<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\Mostviewed\Block\Widget\Related;

use Amasty\Mostviewed\Block\Widget\Related as MostviewedRelated;
use Amasty\Xnotif\Model\Frontend\ProductList\GenerateSubscribeHtml;
use Amasty\Xnotif\Model\Frontend\ProductList\IfSubscriptionEnabled;
use Amasty\Xnotif\Model\Frontend\ProductList\ProcessedProductsRegistry;
use Amasty\XnotifSubscriptionFunctionality\Model\ConfigProvider;
use Magento\Catalog\Model\Product;

class AddSubscriptionBlock
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var IfSubscriptionEnabled
     */
    private $ifSubscriptionEnabled;

    /**
     * @var ProcessedProductsRegistry
     */
    private $processedProductsRegistry;

    /**
     * @var GenerateSubscribeHtml
     */
    private $generateSubscribeHtml;

    public function __construct(
        ConfigProvider $configProvider,
        IfSubscriptionEnabled $ifSubscriptionEnabled,
        ProcessedProductsRegistry $processedProductsRegistry,
        GenerateSubscribeHtml $generateSubscribeHtml
    ) {
        $this->configProvider = $configProvider;
        $this->ifSubscriptionEnabled = $ifSubscriptionEnabled;
        $this->processedProductsRegistry = $processedProductsRegistry;
        $this->generateSubscribeHtml = $generateSubscribeHtml;
    }

    /**
     * @param MostviewedRelated $subject
     * @param string $priceHtml
     * @param Product $product
     * @return string
     */
    public function afterGetProductPriceHtml(MostviewedRelated $subject, $priceHtml, Product $product)
    {
        if (!$this->configProvider->isSubscriptionForRecommendedBlocksEnabled()
            || !$this->ifSubscriptionEnabled->execute()
        ) {
            return $priceHtml;
        }

        if ($this->processedProductsRegistry->checkIfProductProcessed(
            $subject->getNameInLayout(),
            (int)$product->getId()
        )) {
            return $priceHtml;
        }

        $priceHtml .= $this->generateSubscribeHtml->execute($product);

        $this->processedProductsRegistry->markProcessedProduct($subject->getNameInLayout(), (int)$product->getId());

        return $priceHtml;
    }
}
