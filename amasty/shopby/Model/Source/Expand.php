<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Model\Source;

class Expand implements \Magento\Framework\Option\ArrayInterface
{
    public const AUTO_LABEL = 0;
    public const DESKTOP_AND_MOBILE_LABEL = 1;
    public const DESKTOP_LABEL = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::AUTO_LABEL,
                'label' => __('Auto (based on custom theme)')
            ],
            [
                'value' => self::DESKTOP_AND_MOBILE_LABEL,
                'label' => __('Expand for desktop and mobile')
            ],
            [
                'value' => self::DESKTOP_LABEL,
                'label' => __('Expand for desktop only')
            ]
        ];
    }
}
