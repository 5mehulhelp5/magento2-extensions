<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\System;

use Magento\Framework\Option\ArrayInterface;

class StatusOptionProvider implements ArrayInterface
{
    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 0;

    /**
     * @var array|null
     */
    private $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                ['value' => self::STATUS_INACTIVE, 'label' => __('Inactive')],
                ['value' => self::STATUS_ACTIVE, 'label' => __('Active')],
            ];
        }

        return $this->options;
    }
}
