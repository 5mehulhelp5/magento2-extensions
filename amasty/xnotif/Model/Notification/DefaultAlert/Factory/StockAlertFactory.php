<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Factory;

use Magento\Framework\Data\Collection;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockAlertCollection;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory as StockAlertCollectionFactory;

class StockAlertFactory implements AlertFactoryInterface
{
    /**
     * @var StockAlertCollectionFactory
     */
    private $collectionFactory;

    public function __construct(StockAlertCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function create(): Collection
    {
        /** @var StockAlertCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addStatusFilter(0);
        $collection->setCustomerOrder();
        return $collection;
    }
}
