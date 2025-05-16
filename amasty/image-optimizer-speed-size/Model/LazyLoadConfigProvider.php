<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model;

class LazyLoadConfigProvider extends \Amasty\LazyLoad\Model\ConfigProvider
{
    public const LAZY_LOAD_SPEED_SIZE = 'lazy_load_general/speed_size_enabled';

    public function isLazyLoadSpeedSizeEnabled(): bool
    {
        return $this->isSetFlag(self::LAZY_LOAD_SPEED_SIZE);
    }

    /**
     * If SpeedSize in Lazy Load enabled we must behave similar to user agent replace functionality
     */
    public function isReplaceImagesUsingUserAgent(): bool
    {
        return $this->isLazyLoadSpeedSizeEnabled() || parent::isReplaceImagesUsingUserAgent();
    }

    public function skipImagesCount(string $type = ''): int
    {
        if ($this->isLazyLoadSpeedSizeEnabled()) {
            $type = ''; //must use default config
        }

        return parent::skipImagesCount($type);
    }

    public function customSkipImagesCount(string $pageType, string $deviceType = ''): int
    {
        if ($this->isLazyLoadSpeedSizeEnabled()) {
            $deviceType = ''; //must use default config
        }

        return parent::customSkipImagesCount($pageType, $deviceType);
    }

    public function getReplaceImagesUsingUserAgentIgnoreList(): array
    {
        return $this->isLazyLoadSpeedSizeEnabled() ? [] : parent::getReplaceImagesUsingUserAgentIgnoreList();
    }
}
