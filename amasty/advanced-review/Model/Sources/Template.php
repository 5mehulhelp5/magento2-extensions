<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    public const DEFAULT_TEMPLATE = 'Amasty_AdvancedReview::widget/review/content/main.phtml';
    public const LIST_DEFAULT = 'Amasty_AdvancedReview::widget/review/sidebar/sidebar.phtml';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::DEFAULT_TEMPLATE,
                'label' => __('Review Grid Template')
            ],
            [
                'value' => self::LIST_DEFAULT,
                'label' => __('Products Images and Names Template (vert.)')
            ]
        ];

        return $options;
    }
}
