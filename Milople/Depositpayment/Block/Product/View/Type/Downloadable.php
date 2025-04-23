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
namespace Milople\Depositpayment\Block\Product\View\Type; 

class Downloadable extends \Magento\Framework\View\Element\Template
{	
	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Directory\Model\Currency $currency,
		\Milople\Depositpayment\Helper\Data $data_helper,
		\Milople\Depositpayment\Helper\Partialpayment $partialpaymentHelper,
	    array $data = []
  	  ) {
		$this->_coreRegistry = $context->getRegistry();
		$this->helper = $data_helper;
		$this->_currency = $currency;   
		$this->partialpaymentHelper = $partialpaymentHelper;
		   parent::__construct(
            $context,
            $data
        );
    }
	
	public function getProduct()
	{
		return $this->_coreRegistry->registry('product');
	}
}
