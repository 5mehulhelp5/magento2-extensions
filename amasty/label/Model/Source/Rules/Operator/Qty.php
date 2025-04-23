<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Source\Rules\Operator;

use Magento\Framework\Data\OptionSourceInterface;

class Qty implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('is'),
                'value' => '=='
            ],
            [
                'label' => __('is not'),
                'value' => '!='
            ],
            [
                'label' => __('equals or greater than'),
                'value' => '>='
            ],
            [
                'label' => __('equals or less than'),
                'value' => '<='
            ],
            [
                'label' => __('greater than'),
                'value' => '>'
            ],
            [
                'label' => __('less than'),
                'value' => '<'
            ]
        ];
    }
}
