<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $removedStatuses = ['pending', 'fraud', 'canceled', 'holded'];
        foreach ($options as $key => $option) {
            if (isset($option['value']) && in_array($option['value'], $removedStatuses)) {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
