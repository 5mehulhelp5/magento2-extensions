<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Model\Renderer;

use Amasty\MegaMenuLite\Model\Menu\Content\Resolver;
use Magento\Framework\Data\Tree\Node;
use Amasty\MegaMenuLite\Model\Menu\TreeResolver;
use Magento\PageBuilder\Model\Stage\RendererInterface;
use Magento\Store\Model\StoreManagerInterface;

class Widget implements RendererInterface
{
    /**
     * @var TreeResolver
     */
    private $treeResolver;

    /**
     * @var Node|null
     */
    private $menu = null;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Resolver
     */
    private $contentResolver;

    public function __construct(
        TreeResolver $treeResolver,
        StoreManagerInterface $storeManager,
        Resolver $contentResolver
    ) {
        $this->treeResolver = $treeResolver;
        $this->storeManager = $storeManager;
        $this->contentResolver = $contentResolver;
    }

    /**
     * Render a state object for the specified block for the stage preview
     *
     * @param array $params
     * @return array
     */
    public function render(array $params): array
    {
        $categoryId = $params['category_id'] ?? null;
        $result['content'] = __('Empty Child Categories')->render();
        if ($categoryId) {
            foreach ($this->getMenuTree()->getChildren() as $mainNode) {
                if ($mainNode->getId() == 'category-node-' . $categoryId) {
                    if ($categoryHtml = $this->contentResolver->resolve($mainNode)) {
                        $result['content'] = $categoryHtml;
                    }
                    break;
                }
            }
        }

        return $result;
    }

    private function getMenuTree(): ?Node
    {
        if ($this->menu === null) {
            $this->menu = $this->treeResolver->get(
                (int) $this->storeManager->getStore()->getId()
            );
        }

        return $this->menu;
    }
}
