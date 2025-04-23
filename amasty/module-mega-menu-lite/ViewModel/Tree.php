<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\ViewModel;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;
use Amasty\MegaMenuLite\Model\Menu\Frontend\ModifyNodeData;
use Amasty\MegaMenuLite\Model\Menu\Frontend\ModifyNodeDataInterface;
use Amasty\MegaMenuLite\Model\Menu\TreeResolver;
use Amasty\MegaMenuLite\Model\OptionSource\Status;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManager;

class Tree implements ArgumentInterface
{
    public const CONFIGURATION = 'config';

    public const DATA = 'data';

    /**
     * @var TreeResolver
     */
    private $treeResolver;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var Node|null
     */
    private $menu = null;

    /**
     * @var array
     */
    private $nodesData = [];

    /**
     * @var ModifyNodeDataInterface[]
     */
    private $modifyDataPool;

    public function __construct(
        TreeResolver $treeResolver,
        StoreManager $storeManager,
        array $modifyDataPool = []
    ) {
        $this->treeResolver = $treeResolver;
        $this->storeManager = $storeManager;
        $this->modifyDataPool = $modifyDataPool;
    }

    public function getAllNodesData(): array
    {
        $elems = $this->getNodesData()['elems'] ?? [];
        foreach ($elems as $key => $elem) {
            if ($elem[ItemInterface::STATUS] == Status::MOBILE) {
                unset($elems[$key]);
            }
        }

        return $elems;
    }

    public function getNodesData(): array
    {
        if (!$this->nodesData) {
            $this->nodesData = $this->getNodeData($this->getMenuTree());
            usort(
                $this->nodesData['elems'],
                function (array $firstElement, array $secondElement) {
                    return $firstElement[Position::POSITION] - $secondElement[Position::POSITION];
                }
            );
        }

        return $this->nodesData;
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

    private function getNodeData(Node $node): array
    {
        $data = [];
        if ($node->getChildren()->count()) {
            foreach ($node->getChildren() as $child) {
                $data[] = $this->getNodeData($child);
            }
        }

        return $this->getCurrentNodeData($node, $data);
    }

    public function getHamburgerNodesData(): array
    {
        $nodes = [];
        foreach ($this->getAllNodesData() as $node) {
            if (!$node[GetItemData::IS_CATEGORY]) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    private function getCurrentNodeData(Node $node, array $elems = []): array
    {
        $data = [
            'elems' => $elems,
            '__disableTmpl' => true
        ];

        foreach ($this->modifyDataPool as $modifier) {
            $data = $modifier->execute($node, $data);
        }

        return $data;
    }
}
