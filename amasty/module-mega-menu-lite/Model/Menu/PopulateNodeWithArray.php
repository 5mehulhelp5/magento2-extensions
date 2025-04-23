<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\DataObject;

class PopulateNodeWithArray
{
    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var GetItemData
     */
    private $getItemData;

    public function __construct(
        NodeFactory $nodeFactory,
        GetItemData $getItemData
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->getItemData = $getItemData;
    }

    /**
     * @param DataObject[]|CategoryInterface[]|LinkInterface[] $items
     * @param Node $root
     * @return Node
     */
    public function execute(array $items, Node $root): Node
    {
        $elements = [];
        foreach ($items as $item) {
            $elements[$item->getParentId()][] = $item;
        }
        $this->createTree($elements, $root);

        return $root;
    }

    private function createTree(array $list, Node $parent): void
    {
        if (isset($list[$parent->getEntityId()])) {
            foreach ($list[$parent->getEntityId()] as $item) {
                $item = $this->createNewNode($item, $parent);
                if (isset($list[$item->getEntityId()])) {
                    $this->createTree($list, $item);
                }
            }
        }
    }

    private function createNewNode(DataObject $item, Node $parentNode): Node
    {
        $itemNode = $this->nodeFactory->create(
            [
                'idField' => ItemInterface::ID,
                'data' => $this->getItemData->execute($item),
                'tree' => $parentNode->getTree(),
                'parent' => $parentNode
            ]
        );
        $parentNode->addChild($itemNode);
        $this->updatePath($itemNode);

        return $itemNode;
    }

    private function updatePath(Node $node): void
    {
        if (($node->getData(GetItemData::IS_ACTIVE) || $node->getData(GetItemData::HAS_ACTIVE))
            && !$node->getData(GetItemData::IS_CATEGORY)
        ) {
            $parent = $node->getParent();
            if ($parent) {
                $parent->setData(GetItemData::HAS_ACTIVE, true);
                $this->updatePath($parent);
            }
        }
    }
}
