<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Processor;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\ProductAlert\Model\Price as PriceAlert;

class Price
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CatalogData
     */
    private $catalogData;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CatalogData $catalogData,
        DateTimeFactory $dateTimeFactory
    ) {
        $this->productRepository = $productRepository;
        $this->catalogData = $catalogData;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    public function execute(ProductInterface $product, PriceAlert $alert)
    {
        if ($alert->getPrice() > $product->getFinalPrice()) {
            $productPrice = $product->getFinalPrice();

            if ($alert->getParentId()
                && $alert->getParentId() != $alert->getProductId()
                && !$product->canConfigure()
                && (int) $product->getVisibility() === Visibility::VISIBILITY_NOT_VISIBLE
            ) {
                $product = $this->loadProduct((int)$alert->getParentId(), $product->getStoreId());
            } else {
                $product->setFinalPrice(
                    $this->catalogData->getTaxPrice(
                        $product,
                        $productPrice
                    )
                );
                $product->setPrice(
                    $this->catalogData->getTaxPrice(
                        $product,
                        $product->getPrice()
                    )
                );
            }

            $alert->setPrice($productPrice);
            $alert->setLastSendDate($this->dateTimeFactory->create()->gmtDate());
            $alert->setSendCount($alert->getSendCount() + 1);
            $alert->setStatus(1);
            $alert->save();

            return $product;
        }

        return null;
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
