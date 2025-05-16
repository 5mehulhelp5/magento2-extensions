<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\System;

class ConditionOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    public const CONDITION_ALL = 0;
    public const CONDITION_INCLUDE = 1;
    public const CONDITION_EXCLUDE = 2;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                ['value' => self::CONDITION_ALL, 'label' => __('All')],
                ['value' => self::CONDITION_INCLUDE, 'label' => __('Include')],
                ['value' => self::CONDITION_EXCLUDE, 'label' => __('Exclude')]
            ];
        }

        return $this->options;
    }
}
