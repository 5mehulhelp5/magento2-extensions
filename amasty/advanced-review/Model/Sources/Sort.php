<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Data\OptionSourceInterface;

class Sort implements OptionSourceInterface
{
    public const HELPFUL = 'helpful';

    public const HELPFUL_ALIAS = 'helpful';

    public const TOP_RATED = 'top_rated';

    public const TOP_RATED_ALIAS = 'rating_summary';

    public const NEWEST = 'newest';

    public const NEWEST_ALIAS = 'main_table.created_at';

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
                'value' => self::NEWEST,
                'label' => __('Date')
            ],
            [
                'value' => self::TOP_RATED,
                'label' => __('Rating')
            ]
        ];

        if ($this->config->isAllowHelpful()) {
            $data[] =  [
               'value' => self::HELPFUL,
               'label' => __('Helpfulness')
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
