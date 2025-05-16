<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Factory;

use Magento\Framework\Data\Collection;
use Magento\ProductAlert\Model\ResourceModel\Price\Collection as PriceAlertCollection;
use Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory as PriceAlertCollectionFactory;

class PriceAlertFactory implements AlertFactoryInterface
{
    /**
     * @var PriceAlertCollectionFactory
     */
    private $collectionFactory;

    public function __construct(PriceAlertCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function create(): Collection
    {
        /** @var PriceAlertCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', 0);
        $collection->setCustomerOrder();
        return $collection;
    }
}
