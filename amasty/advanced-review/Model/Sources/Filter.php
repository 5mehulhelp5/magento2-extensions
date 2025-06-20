<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Data\OptionSourceInterface;

class Filter implements OptionSourceInterface
{
    public const RECOMMENDED = 'is_recommended';

    public const VERIFIED = 'verified_buyer';

    public const WITH_IMAGES = 'with_images';

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $config;

    public function __construct(\Amasty\AdvancedReview\Helper\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => self::VERIFIED,
                'label' => __('Verified Buyers')
            ]
        ];

        if ($this->config->isRecommendFieldEnabled()) {
            $data[] =  [
                'value' => self::RECOMMENDED,
                'label' => __('Recommended')
            ];
        }

        if ($this->config->isAllowImages()) {
            $data[] =  [
               'value' => self::WITH_IMAGES,
               'label' => __('With images')
            ];
        }

        return $data;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $optionArray = $this->toOptionArray();
        $labels =  array_column($optionArray, 'label');
        $values =  array_column($optionArray, 'value');
        return array_combine($values, $labels);
    }
}
