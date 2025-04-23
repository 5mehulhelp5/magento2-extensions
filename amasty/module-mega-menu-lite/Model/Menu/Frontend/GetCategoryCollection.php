<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Frontend;

use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Category\JoinItemToCategoty;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Store\Model\StoreManagerInterface;

class GetCategoryCollection
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var JoinItemToCategoty
     */
    private $joinItemToCategoty;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        JoinItemToCategoty $joinItemToCategoty,
        FieldsByStore $fieldsByStore
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->joinItemToCategoty = $joinItemToCategoty;
        $this->fieldsByStore = $fieldsByStore;
    }

    public function execute(): CategoryCollection
    {
        $collection = $this->categoryCollectionFactory->create();
        $store = $this->storeManager->getStore();
        $storeId = (int) $store->getId();

        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect('name');
        $collection->addPathFilter(sprintf('.*/%s/[/0-9]*$', $store->getRootCategoryId()));
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);
        $this->joinItemToCategoty->execute(
            $collection,
            $this->getCols(),
            (int) $this->storeManager->getStore()->getId()
        );

        return $collection;
    }

    private function getCols(): array
    {
        $result = [];
        $fieldsets = $this->fieldsByStore->getCategoryFields();
        array_walk_recursive($fieldsets, function ($item, $key) use (&$result) {
            if ($item !== Position::POSITION) {
                $result[] = $item;
            }
        });

        return $result;
    }
}
