<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Rule\Condition;

use Amasty\Label\Model\Di\Wrapper as DefaultStockProvider;
use Amasty\Label\Model\Di\Wrapper as StockIndexTableNameResolver;
use Amasty\Label\Model\Di\Wrapper as StockResolver;
use Amasty\Label\Model\Source\Rules\Operator\Qty as QtyOptionSource;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Phrase;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Sql\ExpressionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Qty extends AbstractCondition
{
    public const JOIN_NAME = 'amasty_qty_stock';
    public const QTY = 'am_qty';
    public const MSI_QUANTITY = 'quantity';

    /**
     * @var QtyOptionSource
     */
    private $qtyOptionSource;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var StockIndexTableNameResolverInterface
     */
    private $stockIndexTableNameResolver;

    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProviderInterface;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    /**
     * @var bool
     */
    private $isMsiUsed = false;

    public function __construct(
        Context $context,
        QtyOptionSource $qtyOptionSource,
        ModuleManager $moduleManager,
        StockResolver $stockResolver,
        StockIndexTableNameResolver $stockIndexTableNameResolver,
        DefaultStockProvider $defaultStockProviderInterface,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        array $data = [],
        ExpressionFactory $expressionFactory = null // TODO move to not optional
    ) {
        $this->qtyOptionSource = $qtyOptionSource;
        $this->moduleManager = $moduleManager;
        $this->stockResolver = $stockResolver;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
        $this->defaultStockProviderInterface = $defaultStockProviderInterface;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->expressionFactory = $expressionFactory ?? ObjectManager::getInstance()->get(ExpressionFactory::class);

        parent::__construct($context, $data);
    }

    public function collectValidatedAttributes(ProductCollection $collection): void
    {
        $select = $collection->getSelect();

        if (!$this->isDataAlreadyJoined($select)) {
            $stockId = $this->getStockId();

            if ($stockId === null || $stockId === $this->defaultStockProviderInterface->getId()) {
                $this->addDefaultStockJoin($select);
            } else {
                $this->addMsiStockJoin($select, $stockId);
            }
        }
    }

    /**
     * @return string
     */
    public function getMappedSqlField()
    {
        if ($this->isMsiUsed) {
            $qtyColumn = self::MSI_QUANTITY;
        } else {
            $qtyColumn = 'qty';
        }

        return $this->expressionFactory->create(['expression' => sprintf(
            'IF(e.type_id = \'%s\', %s.%s, NULL)',
            ProductType::TYPE_SIMPLE,
            self::JOIN_NAME,
            $qtyColumn
        )]);
    }

    /**
     * @return int
     */
    public function getBindArgumentValue()
    {
        return (int)$this->getValue();
    }

    public function validate(AbstractModel $model)
    {
        /** @var Product $model **/
        if ($model->getTypeId() !== ProductType::TYPE_SIMPLE) {
            return false;
        }

        return parent::validate($model);
    }

    private function isDataAlreadyJoined(Select $select): bool
    {
        $fromTables = $select->getPart(Select::FROM);

        return isset($fromTables[self::JOIN_NAME]);
    }

    public function getAttribute(): string
    {
        return self::QTY;
    }

    private function addMsiStockJoin(Select $select, int $stockId): void
    {
        $stockIndexTableName = $this->stockIndexTableNameResolver->execute($stockId);

        if (!$stockIndexTableName) {
            $this->addDefaultStockJoin($select);
        } else {
            $select->joinLeft(
                [self::JOIN_NAME => $stockIndexTableName],
                sprintf('e.sku = %s.sku', self::JOIN_NAME),
                [self::QTY => self::MSI_QUANTITY]
            );
            $this->isMsiUsed = true;
        }
    }

    public function getStockId(): ?int
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
        $stock = $this->stockResolver->execute(ScopeInterface::SCOPE_WEBSITE, $websiteCode);

        return $stock ? (int) $stock->getStockId() : null;
    }

    public function addDefaultStockJoin(Select $select): void
    {
        $stockStatusTable = $this->resource->getTableName('cataloginventory_stock_item');
        $select->joinLeft(
            [self::JOIN_NAME => $stockStatusTable],
            sprintf('e.entity_id = %s.product_id', self::JOIN_NAME),
            [self::QTY => 'qty']
        );
        $this->isMsiUsed = false;
    }

    public function getAttributeElementHtml(): Phrase
    {
        return __('Qty');
    }

    protected function _getAttributeCode()
    {
        return 'qty';
    }

    public function getInputType(): string
    {
        return 'string';
    }

    public function getValueElementType(): string
    {
        return 'text';
    }

    public function getOperatorSelectOptions(): array
    {
        return $this->qtyOptionSource->toOptionArray();
    }
}
