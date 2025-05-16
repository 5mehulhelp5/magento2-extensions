<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

class AdminNotifications
{
    public const DISABLED = 0;

    public const INSTANTLY = 2;

    public const DAILY = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::DISABLED,
                'label' => __('No')
            ],
            [
                'value' => self::INSTANTLY,
                'label' => __('Yes (Instantly)')
            ],
            [
                'value' => self::DAILY,
                'label' => __('Yes (Daily)')
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
