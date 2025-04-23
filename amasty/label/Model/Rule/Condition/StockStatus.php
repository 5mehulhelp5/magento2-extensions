<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Rule\Condition;

use Amasty\Label\Model\Source\Rules\Operator\BooleanOptions;
use Amasty\Label\Model\Source\Rules\Value\StockStatus as StockStatusOptionsProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status as StockStatusResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Phrase;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Sql\ExpressionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class StockStatus extends AbstractCondition
{
    /**
     * @var BooleanOptions
     */
    private $booleanOptions;

    /**
     * @var StockStatusOptionsProvider
     */
    private $stockStatusOptionsProvider;

    /**
     * @var StockStatusResource
     */
    private $stockStatusResource;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var bool
     */
    private $isMsiUsed = false;

    /**
     * @var bool
     */
    private $isStockItemUsed = false;

    public function __construct(
        Context $context,
        ModuleManager $moduleManager,
        BooleanOptions $booleanOptions,
        StockStatusResource $stockStatusResource,
        StockStatusOptionsProvider $stockStatusOptionsProvider,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->stockStatusOptionsProvider = $stockStatusOptionsProvider;
        $this->stockStatusResource = $stockStatusResource;
        $this->booleanOptions = $booleanOptions;
        $this->moduleManager = $moduleManager;
        $this->storeManager = $storeManager;
        $this->resource = $resource;

        parent::__construct(
            $context,
            $data
        );
    }

    public function collectValidatedAttributes(ProductCollection $collection): void
    {
        $select = $collection->getSelect();

        if (!$this->isStockStatusJoined($select)) {
            if ($this->isMsiEnabled()) {
                $this->stockStatusResource->addStockStatusToSelect($select, $this->storeManager->getWebsite());
                $this->prepareSelect($select);
            } else {
                $this->addStockStatusToSelectFromItem($select);
            }

        }
    }

    /**
     * @return string
     */
    public function getMappedSqlField()
    {
        if ($this->isMsiUsed) {
            $stockStatusColumn = 'is_salable';
        } elseif ($this->isStockItemUsed) {
            $stockStatusColumn = 'is_in_stock';
        } else {
            $stockStatusColumn = 'stock_status';
        }

        return 'stock_status.' . $stockStatusColumn;
    }

    /**
     * @return int
     */
    public function getBindArgumentValue()
    {
        return (int)$this->getValue();
    }

    private function addStockStatusToSelectFromItem($select)
    {
        $stockStatusTable = $this->resource->getTableName('cataloginventory_stock_item');
        $select->joinLeft(
            ['stock_status' => $stockStatusTable],
            'e.entity_id = stock_status.product_id',
            ['is_salable' => 'stock_status.is_in_stock']
        );
        $this->isMsiUsed = false;
        $this->isStockItemUsed = true;
    }

    private function prepareSelect(Select $select): void
    {
        $catalogInventoryTable = $this->stockStatusResource->getMainTable();
        $fromTables = $select->getPart(Select::FROM);

        if ($fromTables['stock_status']['tableName'] === $catalogInventoryTable) {
            $fromTables['stock_status']['joinCondition'] = preg_replace(
                '@(?<=stock_status\.website_id=)\d+@',
                (string)Store::DEFAULT_STORE_ID,
                $fromTables['stock_status']['joinCondition']
            );
            $select->setPart(Select::FROM, $fromTables);
            $this->isMsiUsed = false;
        } else {
            $this->isMsiUsed = true;
        }
        $this->isStockItemUsed = false;
    }

    private function isStockStatusJoined($select): bool
    {
        $fromTables = $select->getPart(Select::FROM);

        return isset($fromTables['stock_status']);
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model): bool
    {
        $validatedValue = $model->hasData('is_salable')
            ? $model->getData('is_salable') : $model->getData('stock_status');

        return $this->validateAttribute($validatedValue);
    }

    private function isMsiEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }

    public function getAttributeElementHtml(): Phrase
    {
        return __('Stock Status');
    }

    public function getInputType(): string
    {
        return 'select';
    }

    public function getValueElementType(): string
    {
        return 'select';
    }

    public function getOperatorSelectOptions(): array
    {
        return $this->booleanOptions->toOptionArray();
    }

    public function getValueSelectOptions(): array
    {
        return $this->stockStatusOptionsProvider->toOptionArray();
    }
}
