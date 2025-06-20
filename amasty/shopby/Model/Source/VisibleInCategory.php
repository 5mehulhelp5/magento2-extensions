<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Model\Source;

use Magento\Framework\Data\CollectionDataSourceInterface;

class VisibleInCategory implements \Magento\Framework\Option\ArrayInterface, CollectionDataSourceInterface
{
    /**
     * filter visibility modes
     */
    public const VISIBLE_EVERYWHERE = 'visible_everywhere';
    public const ONLY_IN_SELECTED_CATEGORIES = 'only_in_selected_categories';
    public const HIDE_IN_SELECTED_CATEGORIES = 'hide_in_selected_categories';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::VISIBLE_EVERYWHERE => __('Visible Everywhere'),
            self::ONLY_IN_SELECTED_CATEGORIES => __('Only in Selected Categories'),
            self::HIDE_IN_SELECTED_CATEGORIES => __('Hide in Selected Categories'),
        ];
    }
}
