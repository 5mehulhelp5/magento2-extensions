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

class DiscountDeductionFrom implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('To be Added in Down Payment or First Installment Amount')],
            ['value' => 1, 'label' => __('To be added Equally in Remaining Installment(s) Amount')]            
        ];
    }
}
