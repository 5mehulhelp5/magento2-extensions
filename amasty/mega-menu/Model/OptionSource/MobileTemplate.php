<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

class MobileTemplate implements \Magento\Framework\Option\ArrayInterface
{
    public const ACCORDION = 'accordion';

    public const DRILL_DOWN = 'drill';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACCORDION,
                'label' => __('Accordion')
            ],
            [
                'value' => self::DRILL_DOWN,
                'label' => __('Drill Down')
            ]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::ACCORDION => __('Accordion'), self::DRILL_DOWN => __('Drill Down')];
    }
}
