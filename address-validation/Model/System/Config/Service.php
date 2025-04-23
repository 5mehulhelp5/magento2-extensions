<?php
/**
 * @author Azaleasoft Team
 * @copyright Copyright (c) 2018 Azaleasoft (https://www.azaleasoft.com)
 * @package Azaleasoft_Asaddressvalidation
 */
namespace Azaleasoft\Asaddressvalidation\Model\System\Config;
 
class Service implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
        	['value' => 'ups', 'label' => __('UPS')],
        	['value' => 'usps', 'label' => __('USPS')],
            ['value' => 'fedex', 'label' => __('FedEx')]
        ];
 
        return $options;
    }
}