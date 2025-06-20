<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Seo for Magento 2 (System)
 */

namespace Amasty\ShopbySeo\Model\Source;

class Canonical implements \Magento\Framework\Option\ArrayInterface
{
    public const CANONICAL_DEFAULT = 'default';
    public const CANONICAL_KEY = 'key';
    public const CANONICAL_CURRENT_URL = 'current_url';
    public const CANONICAL_FIRST_ATTRIBUTE_VALUE = 'first_attribute_value';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $optionValue => $optionLabel) {
            $options[] = [
                'value' => $optionValue,
                'label' => $optionLabel
            ];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::CANONICAL_DEFAULT => __('Do Not Change'),
            self::CANONICAL_KEY => __('Just Url Key'),
            self::CANONICAL_CURRENT_URL => __('Current URL without GET Parameters'),
            self::CANONICAL_FIRST_ATTRIBUTE_VALUE => __('First Attribute Value'),
        ];
    }
}
