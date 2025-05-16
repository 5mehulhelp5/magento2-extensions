<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class WidgetAlign implements ArrayInterface
{
    public const LEFT = 'am-widget-left';
    public const CENTER = 'am-widget-center';
    public const RIGHT = 'am-widget-right';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::CENTER, 'label' => __('Center')],
            ['value' => self::RIGHT, 'label' => __('Right')]
        ];
    }
}
