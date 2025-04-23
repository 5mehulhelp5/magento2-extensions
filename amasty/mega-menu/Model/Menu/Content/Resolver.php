<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Menu\Content;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Amasty\MegaMenuLite\Model\Menu\Content\Resolver as ResolverLite;
use Amasty\MegaMenuLite\Model\Menu\Content\Resolver\GetVariableResolver;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Module\Manager;
use Magento\Widget\Model\Template\Filter;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class Resolver extends ResolverLite
{
    /**
     * @var array
     */
    protected $directivePatterns = [
        'construct' =>'/{{([a-z]{0,10})(.*?)}}/si'
    ];

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var State
     */
    private $state;

    public function __construct(
        Manager $moduleManager,
        GetVariableResolver $getVariableResolver,
        Filter $filter,
        State $state,
        $directivePatterns = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->directivePatterns = array_merge($this->directivePatterns, $directivePatterns);
        $this->filter = $filter;
        $this->state = $state;
        parent::__construct($getVariableResolver);
    }

    public function resolve(Node $node): ?string
    {
        $content = $node->getData(ItemInterface::CONTENT);
        if ($this->isNeedDefaultContent($node)) {
            $content = $this->getDefaultContent($node);
        }

        return $content ? $this->parseContent($node, $content) : $content;
    }

    private function isNeedDefaultContent(Node $node): bool
    {
        return $node->getData(ItemInterface::CONTENT) === null
            && $node->getParent()
            && (int) $node->getParent()->getData(ItemInterface::SUBMENU_TYPE) !== SubmenuType::WITH_CONTENT;
    }

    private function getDefaultContent(Node $node): string
    {
        if ($node->getIsCategory()) {
            $content = $this->getDefaultCategoryContent();
        } elseif (strpos($node->getId(), GetItemData::ADDITIONAL_NODE_PREFIX) !== false) {
            $content = $this->getDefaultItemContent();
        }

        return $content ?? '';
    }

    private function getDefaultCategoryContent(): string
    {
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')
            && $this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')
        ) {
            $content = self::CHILD_CATEGORIES_PAGE_BUILDER;
        } else {
            $content = self::CHILD_CATEGORIES;
        }

        return $content;
    }

    private function getDefaultItemContent(): string
    {
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')
            && $this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')
        ) {
            $content = self::CHILD_ITEMS_PAGE_BUILDER;
        } else {
            $content = self::CHILD_ITEMS;
        }

        return $content;
    }

    private function parseContent(Node $node, string $content): ?string
    {
        if ($content) {
            $content = $this->isSubmenuContentEnabled($node)
                ? $this->removeVariables($content)
                : $this->parseVariables($node, $content);

            if ($this->isDirectivesExists($content)) {
                $content = $this->parseWysiwyg($content);
            }
        }

        return $content;
    }

    private function removeVariables(string $content): string
    {
        return str_replace([self::CHILD_CATEGORIES, self::CHILD_ITEMS], '', $content);
    }

    protected function parseWysiwyg(string $content): string
    {
        return (string)$this->state->emulateAreaCode(Area::AREA_FRONTEND, [$this->filter, 'filter'], [$content]);
    }

    protected function isDirectivesExists(string $html): bool
    {
        $matches = false;
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
            return true;
        }

        foreach ($this->directivePatterns as $pattern) {
            if (preg_match($pattern, $html)) {
                $matches = true;
                break;
            }
        }

        return $matches;
    }

    public function isSubmenuContentEnabled(Node $node): bool
    {
        $mainNode = $this->getParentNode($node, LinkInterface::DEFAULT_LEVEL);

        return  $mainNode->getData(ItemInterface::SUBMENU_TYPE) == SubmenuType::WITH_CONTENT;
    }

    private function getParentNode(Node $node, int $level): Node
    {
        if ($node->getLevel() > $level) {
            $node = $this->getParentNode($node->getParent(), $level);
        }

        return $node;
    }
}
