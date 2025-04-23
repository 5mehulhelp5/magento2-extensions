<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TooltipStatus implements OptionSourceInterface
{
    public const DISABLED = 1;
    public const ENABLED_FOR_ALL_DEVICES = 2;
    public const ENABLED_FOR_DESKTOP_ONLY = 3;

    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'No',
                'value' => self::DISABLED
            ],
            [
                'label' => 'Yes for Both Desktop and Mobile',
                'value' => self::ENABLED_FOR_ALL_DEVICES
            ],
            [
                'label' => 'Yes for Desktop Only',
                'value' => self::ENABLED_FOR_DESKTOP_ONLY
            ]
        ];
    }
}
