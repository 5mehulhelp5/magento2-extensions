<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

class Group implements \Magento\Framework\Option\ArrayInterface
{
    public const NOT_LOGGED_IN_VALUE = 0;

    /**
     * @var \Magento\Customer\Model\Customer\Attribute\Source\Group
     */
    private $groupSource;

    public function __construct(\Magento\Customer\Model\Customer\Attribute\Source\Group $groupSource)
    {
        $this->groupSource = $groupSource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $groups = [
            [
                'value' => self::NOT_LOGGED_IN_VALUE,
                'label' => __('Not Logged In')
            ]
        ];

        return array_merge(
            $groups,
            $this->groupSource->getAllOptions()
        );
    }

    /**
     * Get options in "key-value" format
     *
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
