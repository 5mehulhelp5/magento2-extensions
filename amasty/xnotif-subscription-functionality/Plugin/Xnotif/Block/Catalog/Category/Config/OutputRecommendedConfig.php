<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\Xnotif\Block\Catalog\Category\Config;

use Amasty\Xnotif\Block\Catalog\Category\Config;
use Amasty\Xnotif\Model\Frontend\ProductList\ProcessedProductsRegistry;
use Amasty\XnotifSubscriptionFunctionality\Model\ConfigProvider;

class OutputRecommendedConfig
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ProcessedProductsRegistry
     */
    private $processedProductsRegistry;

    public function __construct(ConfigProvider $configProvider, ProcessedProductsRegistry $processedProductsRegistry)
    {
        $this->configProvider = $configProvider;
        $this->processedProductsRegistry = $processedProductsRegistry;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsOutputEnabled(Config $subject, bool $result): bool
    {
        if (!$result) {
            $result = $this->configProvider->isSubscriptionForRecommendedBlocksEnabled()
                && $this->processedProductsRegistry->isProcessedProductsExists();
        }

        return $result;
    }
}
