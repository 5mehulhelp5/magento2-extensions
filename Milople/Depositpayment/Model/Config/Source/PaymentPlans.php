<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Depositpayment
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/deposit-payment-m2.html
*
**/
namespace Milople\Depositpayment\Model\Config\Source;

class PaymentPlans implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Monthly')],            
            ['value' => 2, 'label' => __('Weekly')],
            ['value' => 3, 'label' => __('Based on Days')]
        ];
    }
}
