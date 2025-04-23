<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Partialpaymentauto
* @copyright   Copyright (c) 2019 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/

namespace Milople\Depositpayment\Block\Customer\Payment\Method\Braintree;

class Paypal extends \Magento\Framework\View\Element\Template
{
	public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,
	\Milople\Depositpayment\Model\BraintreeInstallments $braintreeInstallments,
	array $data = []
    )
	{
		$this->braintreeInstallments = $braintreeInstallments;	
		parent::__construct($context, $data);
	}	
	public function getClientToken(){
		return $this->braintreeInstallments->genrateClientToken();
	}
}