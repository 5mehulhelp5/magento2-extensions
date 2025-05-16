<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class CategoriesSort implements ArrayInterface
{
    public const SORT_BY_POSITION = 'position';
    public const SORT_BY_NAME = 'name';
    public const MOST_VIEWED = 'most_viewed';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SORT_BY_POSITION, 'label' => __('Position')],
            ['value' => self::SORT_BY_NAME, 'label' => __('Name')],
            ['value' => self::MOST_VIEWED, 'label' => __('Most Viewed')]
        ];
    }
}
