<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model;

class ConfigProvider extends \Amasty\ImageOptimizer\Model\ConfigProvider
{
    public const SPEED_SIZE_PUBLIC_KEY = 'speed_size/speed_size_public_key';
    public const SPEED_SIZE_CDN = 'speed_size/speed_size_cdn';

    public function getSpeedSizeKey(): string
    {
        return (string)$this->getValue(self::SPEED_SIZE_PUBLIC_KEY);
    }

    public function getSpeedSizeCdnUrl(): string
    {
        return (string)$this->getValue(self::SPEED_SIZE_CDN);
    }
}
