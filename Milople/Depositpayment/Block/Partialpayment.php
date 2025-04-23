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
# use with wholecart

namespace Milople\Depositpayment\Block;
use Magento\Framework\View\Element\Template;
class Partialpayment extends \Magento\Framework\View\Element\Template
{
	public $partialpayment_helper;
	public $helper;
	protected $_coreRegistry = null;
	public $_request;
	public function __construct(
		\Milople\Depositpayment\Helper\Partialpayment $helper,
		\Milople\Depositpayment\Helper\Data $data_helper,
		\Milople\Depositpayment\Model\Calculation $calculation_model,
		Template\Context $context, 
		\Magento\Directory\Model\Currency $currency,
		\Magento\Framework\Registry $registry,
		\Magento\Checkout\Model\Session $checkoutSession,
		array $data = [])
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
		$this->partialpayment_helper = $helper;
		$this->helper = $data_helper;
		$this->_request = $context->getRequest();
		$this->_storeManager = $context->getStoreManager();
		$this->_currency = $currency;
		$this ->_coreRegistry = $registry;
		$this->calculation_model = $calculation_model;
		$this->checkoutSession = $checkoutSession;
    }
	public function getPartialpaymentConfig()
	{
		$partialpaymentData = array();
		$partialpaymentData['installmentFeeLabel'] = $this->calculation_model->getInstallmentLabel();
		return $partialpaymentData;
	}
}