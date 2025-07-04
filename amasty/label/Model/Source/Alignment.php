<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Source;

/**
 * Class Alignment
 * @package Amasty\Label\Model\Source
 */
class Alignment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Vertical')
            ],
            [
                'value' => 1,
                'label' => __('Horizontal')
            ]
        ];
    }
}
