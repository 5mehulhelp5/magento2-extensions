<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Source\Rules\Value;

class StockStatus
{
    public const IN_STOCK = 1;
    public const OUT_OF_STOCK = 0;

    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('In Stock'),
                'value' => self::IN_STOCK
            ],
            [
                'label' => __('Out of Stock'),
                'value' => self::OUT_OF_STOCK
            ]
        ];
    }
}
