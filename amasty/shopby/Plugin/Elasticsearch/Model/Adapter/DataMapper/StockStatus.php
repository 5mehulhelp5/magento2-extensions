<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;
use Amasty\Shopby\Model\Layer\Filter\Stock as FilterStock;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\ScopeInterface;

class StockStatus implements DataMapperInterface
{
    public const FIELD_NAME = 'stock_status';
    public const DOCUMENT_FIELD_NAME = 'quantity_and_stock_status';
    public const INDEX_DOCUMENT = 'document';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var array
     */
    private $inStockProductIds = [];

    /**
     * @var array
     */
    private $allStockProductIds = [];

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    private $stockStatusResource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatusResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockStatusResource = $stockStatusResource;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $entityId
     * @param array $entityIndexData
     * @param int $storeId
     * @param array $context
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map($entityId, array $entityIndexData, $storeId, $context = []): array
    {
        $value = $context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME] ??
            $this->isProductInStock($entityId, (int) $storeId);
        
        return [self::FIELD_NAME => $value];
    }

    private function isProductInStock(int $entityId, int $storeId): int
    {
        if (in_array($entityId, $this->getInStockProductIds($storeId), true)) {
            return FilterStock::FILTER_IN_STOCK;
        }

        if (in_array($entityId, $this->getAllStockProductIds($storeId), true)) {
            return FilterStock::FILTER_OUT_OF_STOCK;
        }

        return FilterStock::FILTER_DEFAULT;
    }

    /**
     * @return int[]
     */
    private function getInStockProductIds(int $storeId)
    {
        if (!isset($this->inStockProductIds[$storeId])) {
            $collection = $this->getCollectionWithStock($storeId, true);

            $this->inStockProductIds[$storeId] = array_map('intval', $collection->getAllIds());
        }

        return $this->inStockProductIds[$storeId];
    }

    /**
     * @return int[]
     */
    private function getAllStockProductIds(int $storeId): array
    {
        if (!isset($this->allStockProductIds[$storeId])) {
            $collection = $this->getCollectionWithStock($storeId, false);

            $this->allStockProductIds[$storeId] = array_map('intval', $collection->getAllIds());
        }

        return $this->allStockProductIds[$storeId];
    }

    public function isAllowed(): bool
    {
        return $this->scopeConfig->isSetFlag('amshopby/stock_filter/enabled', ScopeInterface::SCOPE_STORE);
    }

    public function getFieldName(): string
    {
        return self::FIELD_NAME;
    }

    /**
     * Get Product Collection with Joined Stock.
     * Resolver compatibility with MSI by store emulation.
     *
     * Full emulation may lead to error "Required parameter 'theme_dir' was not passed".
     */
    private function getCollectionWithStock(int $storeId, bool $withFilter): Collection
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create()->addStoreFilter($storeId);
        
        $currentStore = $this->storeManager->getStore();
        // Emulate store for MSI plugin which gets stock by current store
        $this->storeManager->setCurrentStore($storeId);
        $this->stockStatusResource->addStockDataToCollection($collection, $withFilter);
        $this->storeManager->setCurrentStore($currentStore);
        
        return $collection;
    }
}
