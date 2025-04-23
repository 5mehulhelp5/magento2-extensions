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
* @url         https://www.milople.com/magento2-extensions/Deposit-payment-m2.html
*
**/
namespace Milople\Depositpayment\Block\Adminhtml\Partiallypaidorders\Edit\Tab\Renderer;
 
use Magento\Framework\DataObject;
 
class LastName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * get First Name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        /* $All = $row->getData();     
		$data = print_r($All, true);;
        return "<pre>".$data."</pre>"; */ 
		if($row->getLastname()){
			return $row->getLastname();
		}else{
			return $row->getGuestLastname();
		}
    }
}