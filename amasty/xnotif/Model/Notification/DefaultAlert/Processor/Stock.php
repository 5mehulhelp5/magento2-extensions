<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Processor;

use Amasty\Xnotif\Helper\Config as ConfigHelper;
use Amasty\Xnotif\Helper\Data as Helper;
use Amasty\Xnotif\Model\ConfigProvider;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\ProductAlert\Model\ProductSalability;
use Magento\ProductAlert\Model\Stock as StockAlert;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stock
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var ProductSalability
     */
    private $productSalability;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var array
     */
    private $productSendCache = [];

    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider,
        Helper $helper,
        ConfigHelper $configHelper,
        ProductSalability $productSalability,
        ProductRepositoryInterface $productRepository,
        DateTimeFactory $dateTimeFactory
    ) {
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
        $this->helper = $helper;
        $this->configHelper = $configHelper;
        $this->productSalability = $productSalability;
        $this->productRepository = $productRepository;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    public function execute(ProductInterface $product, StockAlert $alert): ?ProductInterface
    {
        $website = $this->storeManager->getStore($product->getStoreId())->getWebsite();
        if ($this->shouldSkipByLimit($product, $website)) {
            return null;
        }

        return $this->checkStockSubscription($product, $alert, $website);
    }

    private function shouldSkipByLimit(ProductInterface $product, WebsiteInterface $website): bool
    {
        $result = false;
        if ($this->configProvider->isQtyLimitEnabled()) {
            $productId = $product->getId();
            if (isset($this->productSendCache[$productId])) {
                if ($this->productSendCache[$productId]['qty'] <= $this->productSendCache[$productId]['counter']) {
                    //limit- should skip next
                    $result = true;
                } else {
                    $this->productSendCache[$productId]['counter']++;
                }
            } else {
                $websiteId = (int)$website->getId();
                $this->productSendCache[$productId] = [
                    'qty' => $this->isSalable($product, $website)
                        ? $this->getProductQtyForLimit($product, $websiteId)
                        : 0,
                    'counter' => 1
                ];
            }
        }

        return $result;
    }

    private function checkStockSubscription(
        ProductInterface $product,
        StockAlert $alert,
        WebsiteInterface $website
    ): ?ProductInterface {
        if ($this->getIsInStockProduct($product, $website)) {
            if ($alert->getParentId()
                && $alert->getParentId() != $alert->getProductId()
                && !$product->canConfigure()
                && (int) $product->getVisibility() === Visibility::VISIBILITY_NOT_VISIBLE
            ) {
                $product = $this->loadProduct((int)$alert->getParentId(), $product->getStoreId());
            }

            $alert->setSendDate($this->dateTimeFactory->create()->gmtDate());
            $alert->setSendCount($alert->getSendCount() + 1);
            $alert->setStatus(1);
            $alert->save();
            return $product;
        }

        return null;
    }

    private function getIsInStockProduct(ProductInterface $product, WebsiteInterface $website): bool
    {
        $isInStock = $this->isSalable($product, $website);
        if (!$isInStock) {
            return false;
        }

        $minQuantity = $this->configHelper->getMinQty();
        $isInStock = false;

        if ($product->isComposite() && ($allProducts = $this->getUsedProducts($product))) {
            foreach ($allProducts as $simpleProduct) {
                $quantity = $this->helper->getProductQty($simpleProduct, $website->getId());
                $isInStock = $this->isSalable($simpleProduct, $website) && $quantity >= $minQuantity;
                if ($isInStock) {
                    break;
                }
            }
        } else {
            $quantity = $this->helper->getProductQty($product, $website->getId());
            $isInStock = $quantity >= $minQuantity;
        }

        return $isInStock;
    }

    private function isSalable(ProductInterface $product, WebsiteInterface $website): bool
    {
        return $this->productSalability->isSalable($product, $website);
    }

    private function getProductQtyForLimit(ProductInterface $product, int $websiteId): int
    {
        $usedProducts = $this->getUsedProducts($product);

        $quantity = array_reduce(
            $usedProducts,
            function ($sum, $item) use ($websiteId) {
                $qty = $this->helper->getProductQty($item, $websiteId);
                return $qty > 0 ? $sum + $qty : $sum;
            },
            0
        );

        return (int)$quantity;
    }

    /**
     * @return ProductInterface[]
     */
    private function getUsedProducts(ProductInterface $product): array
    {
        switch ($product->getTypeId()) {
            case Configurable::TYPE_CODE:
                $result = $product->getTypeInstance()->getUsedProducts($product);
                break;
            case Grouped::TYPE_CODE:
                $result = $product->getTypeInstance()->getAssociatedProducts($product);
                break;
            case BundleType::TYPE_CODE:
                $result = $product->getTypeInstance()->getSelectionsCollection(
                    $product->getTypeInstance()->getOptionsIds($product),
                    $product
                )->getItems();
                break;
            default:
                $result = [$product];
        }

        return $result;
    }

    private function loadProduct(int $productId, int $storeId): ?ProductInterface
    {
        try {
            $product = $this->productRepository->getById(
                $productId,
                false,
                $storeId
            );
        } catch (NoSuchEntityException $ex) {
            return null;
        }

        if ($product->getStatus() == Status::STATUS_DISABLED) {
            return null;
        }

        return $product;
    }
}
