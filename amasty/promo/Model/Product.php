<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Gift Base for Magento 2
 */

namespace Amasty\Promo\Model;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Product Stock provider
 * @sine 2.8.0 MSI compatibility; optimization
 */
class Product
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * MSI have backward compatibility for getStockStatusBySku
     *
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var ResourceModel\GetProductTypesBySkus
     */
    private $getProductTypesBySkus;

    /**
     * @var array
     */
    private $productQty = [];

    /**
     * @var array
     */
    private $productTypes = [];

    public function __construct(
        StoreManagerInterface $storeManager,
        Session $checkoutSession,
        LoggerInterface $logger,
        StockRegistryInterface $stockRegistry,
        ResourceModel\GetProductTypesBySkus $getProductTypes
    ) {
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->stockRegistry = $stockRegistry;
        $this->getProductTypesBySkus = $getProductTypes;
    }

    /**
     * reset local cache
     */
    public function resetStorage()
    {
        $this->productQty = [];
    }

    /**
     * @param string $sku
     *
     * @return float|int
     */
    public function getProductQty($sku)
    {
        if (!isset($this->productQty[$sku])) {
            $productType = $this->getProductType($sku);

            if (!$productType
                || $productType === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
                || $productType === \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
                || $productType === \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL
                || $productType === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
            ) {
                $this->productQty[$sku] = false;
            } else {
                $this->productQty[$sku] = $this->getStockQty($sku);
            }
        }

        return $this->productQty[$sku];
    }

    /**
     * @param string $sku
     *
     * @return float|int
     */
    private function getStockQty($sku)
    {
        try {
            /** backorder and manage stock statuses */
            $stockItem = $this->stockRegistry->getStockItemBySku(
                $sku,
                $this->storeManager->getWebsite()->getId()
            );

            /** MSI compatibility (status and salable qty) */
            $stockStatus = $this->stockRegistry->getStockStatusBySku(
                $sku,
                $this->storeManager->getWebsite()->getId()
            );

            if (!$stockStatus->getStockStatus()) {
                return 0;
            }

            if (!$stockItem->getManageStock() || $stockItem->getBackorders()) {
                return $stockItem->getMaxSaleQty();
            }

            return $stockStatus->getQty();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->critical($e->getTraceAsString());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e->getTraceAsString());
        }

        return 0;
    }

    /**
     * fix qty
     *
     * @param string $sku
     * @param int $qtyRequested
     * @param \Magento\Quote\Model\Quote|null $quote
     *
     * @return float|int
     */
    public function checkAvailableQty(
        $sku,
        $qtyRequested,
        $quote = null
    ) {
        if (!$this->getProductType($sku)) {
            return 0;
        }

        $stockQty = $this->getProductQty($sku);

        if (($stockQty === false) && $this->isProductInStock($sku)) {
            return $qtyRequested;
        }

        if (!$stockQty) {
            return 0;
        }

        $qtyAdded = 0;
        if (!$quote) {
            $quote = $this->checkoutSession->getQuote();
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            //items with custom options may have modified sku
            if ($item->getSku() === $sku
                || ($item->hasData('product') && $item->getProduct()->getSku() == $sku)
            ) {
                $qtyAdded += $item->getQty();
            }
        }

        $totalQty = $qtyRequested + $qtyAdded;

        if ($totalQty > $stockQty) {
            return max($stockQty - $qtyAdded, 0);
        }

        return $qtyRequested;
    }

    public function isProductInStock(string $sku): bool
    {
        try {
            $stockStatus = $this->stockRegistry->getStockStatusBySku(
                $sku,
                $this->storeManager->getWebsite()->getId()
            );

            return (bool)$stockStatus->getStockStatus();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return false;
    }

    public function getProductType(string $sku): ?string
    {
        if (!isset($this->productTypes[$sku])) {
            $skuTypesArray = $this->getProductTypesBySkus->execute([$sku]);
            $this->productTypes[$sku] = $skuTypesArray[$sku] ?? null;
        }

        return $this->productTypes[$sku];
    }
}
