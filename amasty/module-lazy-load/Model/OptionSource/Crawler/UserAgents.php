<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\OptionSource\Crawler;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class UserAgents implements OptionSourceInterface
{
    public const WEBP = 'webp';
    public const NO_WEBP = 'no_webp';

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        return [
            self::WEBP => __('WebP Support'),
            self::NO_WEBP => __('No WebP Support'),
        ];
    }
}
