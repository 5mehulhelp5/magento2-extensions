<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Cron;

use Amasty\XnotifSubscriptionFunctionality\Model\ResourceModel\StockAlert as StockAlertResource;
use Amasty\XnotifSubscriptionFunctionality\Model\ResourceModel\OutOfStockFilterApplier;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\InventoryApi\Api\Data\SourceItemInterface;

class UpdateProductAlerts
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StockAlertResource
     */
    private $stockAlertResource;

    /**
     * @var StockRegistry
     */
    private $stockRegistry;

    /**
     * @var OutOfStockFilterApplier
     */
    private $outOfStockFilterApplier;

    public function __construct(
        StockAlertResource $stockAlertResource,
        CollectionFactory $collectionFactory,
        StockRegistry $stockRegistry,
        OutOfStockFilterApplier  $outOfStockFilterApplier
    ) {
        $this->stockAlertResource = $stockAlertResource;
        $this->collectionFactory = $collectionFactory;
        $this->stockRegistry = $stockRegistry;
        $this->outOfStockFilterApplier = $outOfStockFilterApplier;
    }

    public function process(): void
    {
        if (!$productIds = $this->stockAlertResource->getRestockProductIds()) {
            return;
        }

        $productsCollection = $this->collectionFactory->create();
        $this->outOfStockFilterApplier->execute($productsCollection);
        $productsCollection->addFieldToFilter('entity_id', ['in' => $productIds]);

        $outOfStockProductIds = $productsCollection->getColumnValues('entity_id');
        $alertIds = $this->stockAlertResource->getAlertIdsByProductIds($outOfStockProductIds);
        if ($alertIds) {
            $this->stockAlertResource->updateAlertStatusById($alertIds, SourceItemInterface::STATUS_OUT_OF_STOCK);
        }
    }
}
