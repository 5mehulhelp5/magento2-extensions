<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Model\Stock\Status as StockStatus;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Module\Manager;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventoryCatalog\Model\ResourceModel\AddStockStatusToSelect;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;

class OutOfStockFilterApplier
{
    private const MAGENTO_INVENTORYCATALOG_MODULE_NAME = 'Magento_InventoryCatalog';

    private const DEFAULT_STOCK_TABLE = 'cataloginventory_stock_status';

    private const STOCK_STATUS_ALIAS = 'stock_status';

    private const MSI_IS_SALABLE_FIELD = 'is_salable';

    /**
     * @var bool|null
     */
    private $msiEnabled = null;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite = null;

    /**
     * @var StockIndexTableNameResolverInterface
     */
    private $stockIndexTableNameResolver;

    /**
     * @var AddStockStatusToSelect
     */
    private $addStockStatusToSelect;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var int|null
     */
    private $stockId = null;

    public function __construct(
        Manager $moduleManager,
        StockIndexTableNameResolverInterface $stockIndexTableNameResolver,
        AddStockStatusToSelect $addStockStatusToSelect
    ) {
        $this->moduleManager = $moduleManager;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
        $this->addStockStatusToSelect = $addStockStatusToSelect;

        if ($this->isMSIEnabled()) {
            $this->getStockIdForCurrentWebsite = ObjectManager::getInstance()->get(GetStockIdForCurrentWebsite::class);
        }
    }

    public function execute(AbstractCollection $productsCollection)
    {
        $this->connection = $productsCollection->getConnection();

        $this->joinStockTable($productsCollection);
    }

    private function joinStockTable(AbstractCollection $productsCollection): void
    {
        if ($this->needJoinMsiTable()) {
            $this->joinMsiStockTable($productsCollection);
        } else {
            $this->joinDefaultStockTable($productsCollection);
        }
    }

    private function needJoinMsiTable(): bool
    {
        $this->stockId = $this->getStockIdForCurrentWebsite->execute();

        return $this->getStockIdForCurrentWebsite instanceof GetStockIdForCurrentWebsite
            && $this->isMsiStockTableExists($this->stockId);
    }

    private function joinMsiStockTable(AbstractCollection $productsCollection): void
    {
        $this->addStockStatusToSelect->execute($productsCollection->getSelect(), $this->stockId);
        $productsCollection->getSelect()->where(self::MSI_IS_SALABLE_FIELD . ' = 0');
    }

    private function joinDefaultStockTable(AbstractCollection $productsCollection): void
    {
        $productsCollection->joinField(
            self::STOCK_STATUS_ALIAS,
            self::DEFAULT_STOCK_TABLE,
            StockStatus::KEY_STOCK_STATUS,
            'product_id = entity_id',
            '{{table}}.stock_id = ' . Stock::DEFAULT_STOCK_ID,
            'left'
        )->addFieldToFilter(
            StockStatus::KEY_STOCK_STATUS,
            ['eq' => SourceItemInterface::STATUS_OUT_OF_STOCK]
        );
    }

    private function isMSIEnabled(): bool
    {
        if ($this->msiEnabled === null) {
            $this->msiEnabled = $this->moduleManager->isEnabled(self::MAGENTO_INVENTORYCATALOG_MODULE_NAME);
        }

        return $this->msiEnabled;
    }

    private function isMsiStockTableExists(int $stockId): bool
    {
        return $this->connection->isTableExists($this->stockIndexTableNameResolver->execute($stockId));
    }
}
