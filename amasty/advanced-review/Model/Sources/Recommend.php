<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

class Recommend
{
    public const NOT_SELECTED = 0;
    public const NOT_RECOMMENDED = 2;
    public const RECOMMENDED = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::NOT_RECOMMENDED,
                'label' => __('No')
            ],
            [
                'value' => self::RECOMMENDED,
                'label' => __('Yes')
            ]
        ];

        return $options;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getLabel($value)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return '';
    }
}
