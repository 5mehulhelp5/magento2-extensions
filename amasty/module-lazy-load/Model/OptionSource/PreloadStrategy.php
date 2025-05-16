<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\OptionSource;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class PreloadStrategy implements OptionSourceInterface
{
    public const SMART_OPTIMIZATION = 0;
    public const SKIP_IMAGES = 1;

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        return [
            self::SMART_OPTIMIZATION => __('Smart Optimization'),
            self::SKIP_IMAGES => __('Original Format of Images'),
        ];
    }
}
