<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class HelpAndSettings implements OptionSourceInterface
{
    public const NO = 0;
    public const BOTH = 3;
    
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::BOTH,
                'label' => __('Both Desktop and Mobile')
            ],
            [
                'value' => self::NO,
                'label' => __('No')
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            self::BOTH => __('Both Desktop and Mobile'),
            self::NO => __('No')
        ];
    }
}
