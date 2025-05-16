<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\ProductAlert\Model\Price as PriceAlert;
use Magento\ProductAlert\Model\ResourceModel\Price\Collection as PriceAlertCollection;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockAlertCollection;
use Magento\ProductAlert\Model\Stock as StockAlert;
use Magento\Store\Model\StoreManagerInterface;

class ProductService
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductInterface[]|null
     */
    private $loadedProducts;

    public function __construct(
        StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Supported when all items in giving collection have same store_id & website_id.
     *
     * @param PriceAlertCollection|StockAlertCollection|Collection $alertCollection
     */
    public function preload(Collection $alertCollection): void
    {
        $this->clear();

        /** @var StockAlert|PriceAlert $alert */
        $alert = $alertCollection->getFirstItem();
        if (!$alert->getId()) {
            $this->loadedProducts = [];
            return;
        }

        $storeId = (int)($alert->getStoreId()
            ?: $this->storeManager->getWebsite($alert->getWebsiteId())->getDefaultStore()->getId());

        $productIds = array_map(
            /** @var StockAlert|PriceAlert $alert */
            static function (AbstractModel $alert) {
                return (int)$alert->getProductId();
            },
            $alertCollection->getItems()
        );
        $productIds = array_unique(array_filter($productIds));

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addStoreFilter($storeId);
        $productCollection->addIdFilter($productIds);
        $productCollection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $productCollection->addAttributeToSelect('name');
        $productCollection->addAttributeToSelect('short_description');
        $productCollection->addAttributeToSelect('thumbnail');
        $productCollection->addAttributeToSelect(ProductInterface::VISIBILITY);

        $productCollection->addPriceData();
        $productCollection->addAttributeToSelect('special_price');
        $productCollection->addAttributeToSelect('special_from_date');
        $productCollection->addAttributeToSelect('special_to_date');

        $this->loadedProducts = $productCollection->getItems();
    }

    public function get(int $productId): ?ProductInterface
    {
        return $this->loadedProducts[$productId] ?? null;
    }

    public function clear(): void
    {
        $this->loadedProducts = null;
    }
}
