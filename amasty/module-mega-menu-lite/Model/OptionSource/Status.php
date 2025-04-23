<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public const DISABLED = 0;

    public const ENABLED = 1;

    public const DESKTOP = 2;

    public const MOBILE = 3;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Enable For Both Desktop and Mobile')
            ],
            [
                'value' => self::DESKTOP,
                'label' => __('Enable for Desktop Only')
            ],
            [
                'value' => self::MOBILE,
                'label' => __('Enable for Mobile Only')
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disable')
            ]
        ];
    }
}
