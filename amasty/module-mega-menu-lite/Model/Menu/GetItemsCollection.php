<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu;

use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Collection as ItemCollection;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Store\Model\Store;

class GetItemsCollection
{
    /**
     * @var ItemCollectionFactory
     */
    protected $collectionFactory;

    public function __construct(ItemCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(int $storeId): ItemCollection
    {
        /** @var ItemCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('store_id', [$storeId, Store::DEFAULT_STORE_ID]);
        $collection->addOrder('store_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }
}
