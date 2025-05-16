<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class AddNewConditionHandle implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = $additional->getConditions();

        if (!is_array($conditions)) {
            $conditions = [];
        }

        $conditions[] = [
            'label' => __('Shipping Areas'),
            'value' => [
                [
                    'value' => 'Amasty\ShippingArea\Model\Rule\Condition\Area',
                    'label' => __('Shipping Areas'),
                ]
            ]
        ];
        $additional->setConditions($conditions);
    }
}
