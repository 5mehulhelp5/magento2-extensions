<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Content;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Menu\Content\Resolver\GetVariableResolver;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;
use Magento\Framework\Data\Tree\Node;

class Resolver
{
    public const CHILD_CATEGORIES = '{{child_categories_content}}';

    public const CHILD_ITEMS = '{{child_items_content}}';

    // @codingStandardsIgnoreLine
    public const CHILD_CATEGORIES_PAGE_BUILDER = '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="ammega_menu_widget" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{child_categories_content}}</div></div></div>';

    // @codingStandardsIgnoreLine
    public const CHILD_ITEMS_PAGE_BUILDER = '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="ammega_menu_widget" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{child_items_content}}</div></div></div>';

    /**
     * @var GetVariableResolver
     */
    private $getVariableResolver;

    public function __construct(GetVariableResolver $getVariableResolver)
    {
        $this->getVariableResolver = $getVariableResolver;
    }

    public function resolve(Node $node): ?string
    {
        $id = $node->getData(ItemInterface::ID);
        if ($node->getIsCategory()) {
            $content = $this->parseVariables($node, $this->getDefaultContent($node));
        } elseif ($id && strpos($id, GetItemData::ADDITIONAL_NODE_PREFIX) !== false) {
            $content = $node->getData('content');
        }

        return $content ?? null;
    }

    private function getDefaultContent(Node $node): string
    {
        return $node->getIsCategory() ? $this->getDefaultCategoryContent() : $this->getDefaultItemContent();
    }

    private function getDefaultCategoryContent(): string
    {
        return self::CHILD_CATEGORIES;
    }

    private function getDefaultItemContent(): string
    {
        return self::CHILD_ITEMS;
    }

    protected function parseVariables(Node $node, ?string $content): string
    {
        if (strpos($content, $this->getDefaultContent($node)) !== false) {
            $result = $node->hasChildren()
                ? $this->getVariableResolver->get('child_categories_content')->execute()
                : '';
            $content = str_replace($this->getDefaultContent($node), $result, $content);
        }

        return $content;
    }
}
