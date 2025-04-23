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
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetCategoryCollection;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetLinkCollection;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Store\Model\StoreManagerInterface;

class TreeResolver
{
    public const AFTER_SORT_ORDER = 99;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var Node[]
     */
    private $menu;

    /**
     * @var GetLinkCollection
     */
    private $getLinkCollection;

    /**
     * @var GetCategoryCollection
     */
    private $getCategoryCollection;

    /**
     * @var PopulateNodeWithArray
     */
    private $populateNodeWithArray;

    public function __construct(
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        StoreManagerInterface $storeManager,
        DataObjectFactory $dataObjectFactory,
        GetLinkCollection $getLinkCollection,
        GetCategoryCollection $getCategoryCollection,
        PopulateNodeWithArray $populateNodeWithArray
    ) {
        $this->storeManager = $storeManager;
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->getLinkCollection = $getLinkCollection;
        $this->getCategoryCollection = $getCategoryCollection;
        $this->populateNodeWithArray = $populateNodeWithArray;
    }

    /**
     * @param int $storeId
     * @return Node
     */
    public function get(int $storeId): Node
    {
        if (!isset($this->menu[$storeId])) {
            $this->menu[$storeId] = $this->getMenu($storeId);
        }

        return $this->menu[$storeId];
    }

    private function getMenu(int $storeId): Node
    {
        $rootCategoryId = $this->getCategoryRootId($storeId);
        $links = $this->getLinkCollection->execute()->getItems();
        $categories = $this->getCategoryCollection->execute()->getItems();
        $additionalLinks = $this->prepareAdditionalLinks($rootCategoryId, $this->getBeforeAdditionalLinks());

        $root = $this->populateNodeWithArray->execute($additionalLinks, $this->getRootMenuNode($storeId));
        $root = $this->populateNodeWithArray->execute($categories, $root);

        $root->setData(ItemInterface::ENTITY_ID, 0);
        $root = $this->populateNodeWithArray->execute($links, $root);
        $root->setData(ItemInterface::ENTITY_ID, $this->getCategoryRootId($storeId));

        $additionalLinks = $this->prepareAdditionalLinks($rootCategoryId, $this->getAdditionalLinksWrapper());
        $root = $this->populateNodeWithArray->execute($additionalLinks, $root);

        return $root;
    }

    private function prepareAdditionalLinks(int $rootCategoryId, array $links = []): array
    {
        foreach ($links as &$link) {
            if (is_array($link)) {
                $link = $this->dataObjectFactory->create(['data' => $link]);
            }
            $link->setData(LinkInterface::PARENT_ID, $rootCategoryId);
        }

        return $links;
    }

    /**
     * public method for creating plugins
     * @return array
     */
    public function getBeforeAdditionalLinks(): array
    {
        return [];
    }

    /**
     * public method for creating plugins
     * @return array
     */
    public function getAdditionalLinks(): array
    {
        return [];
    }

    public function getAdditionalLinksWrapper(): array
    {
        $links = $this->getAdditionalLinks();
        foreach ($links as &$link) {
            $sort = $link['sort_order'] ?? null;
            if (!$sort) {
                $link['sort_order'] = self::AFTER_SORT_ORDER;
            }
        }

        return $links;
    }

    private function getRootMenuNode(int $storeId): Node
    {
        return $this->nodeFactory->create(
            [
                'data' => ['entity_id' => $this->getCategoryRootId($storeId)],
                'idField' => 'entity_id',
                'tree' => $this->treeFactory->create()
            ]
        );
    }

    private function getCategoryRootId(int $storeId): int
    {
        return (int) $this->storeManager->getStore($storeId)->getRootCategoryId();
    }
}
