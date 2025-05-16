<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class Layouts implements ArrayInterface
{
    public const LAYOUT_2COLUMNS_LEFT_SIDEBAR = '2columns-left';
    public const LAYOUT_2COLUMNS_RIGHT_SIDEBAR = '2columns-right';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LAYOUT_2COLUMNS_LEFT_SIDEBAR, 'label' => __('2 columns with left sidebar')],
            ['value' => self::LAYOUT_2COLUMNS_RIGHT_SIDEBAR, 'label' => __('2 columns with right sidebar')]
        ];
    }
}
