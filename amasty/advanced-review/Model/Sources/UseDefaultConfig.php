<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Option\ArrayInterface;

class UseDefaultConfig implements ArrayInterface
{
    public const USE_DEFAULT = '';

    public const NO = 1;

    public const YES = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::USE_DEFAULT,
                'label' => __('Use Default Config')
            ],
            [
                'value' => self::NO,
                'label' => __('No')
            ],
            [
                'value' => self::YES,
                'label' => __('Yes')
            ]
        ];

        return $options;
    }
}
