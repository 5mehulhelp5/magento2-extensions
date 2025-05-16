<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\Image\ReplaceAlgorithm;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizerSpeedSize\Model\ConfigProvider;
use Amasty\ImageOptimizerSpeedSize\Model\LazyLoadConfigProvider;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\ReplaceAlgorithmInterface;
use Magento\Store\Model\StoreManagerInterface;

class SpeedSizeReplaceAlgorithm implements ReplaceAlgorithmInterface
{
    public const ALGORITHM_NAME = 'replace_with_speed_size';
    public const SPEEDSIZE_REPLACE_STRATEGY_KEY = 2;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    /**
     * @var LazyLoadConfigProvider
     */
    private $lazyLoadConfig;

    public function __construct(
        ConfigProvider $configProvider,
        LazyLoadConfigProvider $lazyLoadConfig,
        StoreManagerInterface $storeManager,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
        $this->lazyLoadConfig = $lazyLoadConfig;
    }

    public function execute(string $image, string $imagePath): string
    {
        $replaceImagePath = $this->getReplaceImagePath($imagePath);

        return str_replace($imagePath, $replaceImagePath, $image);
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        $publicKey = $this->configProvider->getSpeedSizeKey();
        if (!$publicKey) {
            return $imagePath;
        }
        $this->processImagePath($imagePath);

        return $this->getCdnUrl()
            . $publicKey . '/'
            . $imagePath;
    }

    public function getAlgorithmName(): string
    {
        return self::ALGORITHM_NAME;
    }

    public function isAvailable(): bool
    {
        $isImageOptimizerReplace = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY)
            === self::SPEEDSIZE_REPLACE_STRATEGY_KEY;
        $isLazy = $this->lazyConfigProvider->get()->getData(LazyConfig::IS_LAZY);
        $isLazyLoadReplace = $isLazy && $this->lazyLoadConfig->isLazyLoadSpeedSizeEnabled();

        return ($isLazyLoadReplace || $isImageOptimizerReplace && !$isLazy)
            && $this->configProvider->getSpeedSizeKey()
            && $this->configProvider->getSpeedSizeCdnUrl();
    }

    public function canOverride(): bool
    {
        return true;
    }

    private function processImagePath(string &$imagePath): void
    {
        // compatibility with src without domain.
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        if (!parse_url($imagePath, PHP_URL_HOST)) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $updatedImagePath = $baseUrl . trim($imagePath, '/');
            $imagePath = $updatedImagePath;
        }
    }

    private function getCdnUrl(): string
    {
        return rtrim(trim($this->configProvider->getSpeedSizeCdnUrl()), '/') . '/';
    }
}
