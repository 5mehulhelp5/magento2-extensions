<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class Sticky implements OptionSourceInterface
{
    public const NO = 0;
    
    public const DESKTOP = 1;

    public const MOBILE = 2;
    
    public const BOTH = 3;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::NO,
                'label' => __('No')
            ],
            [
                'value' => self::DESKTOP,
                'label' => __('On Desktop')
            ],
            [
                'value' => self::MOBILE,
                'label' => __('On Mobile')
            ],
            [
                'value' => self::BOTH,
                'label' => __('On Both Desktop and Mobile')
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            self::NO => __('No'),
            self::DESKTOP => __('On Desktop'),
            self::MOBILE => __('On Mobile'),
            self::BOTH => __('On Both Desktop and Mobile')
        ];
    }
}
