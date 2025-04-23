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
namespace Milople\Depositpayment\Block\Product;

use Magento\Framework\View\Element\Template;

class Price extends \Magento\Framework\View\Element\Template
{
	protected $_coreRegistry = null;
	protected $scopeConfig;	
	protected $planmodelFactory;
	protected $_logger; 
	public function __construct(
		\Milople\Depositpayment\Helper\Data $helper,
		Template\Context $context, 
		\Magento\Directory\Model\Currency $currency,
		\Magento\Catalog\Block\Product\View\abstractView $product,
		array $data = [])
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;		
		$this->_storeManager = $context->getStoreManager();
		$this->_currency = $currency;
		$this -> _coreRegistry =$context->getRegistry();
		$this->partialpaymentHelper = $helper;
		$this->scopeConfig = $context->getScopeConfig();
		$this->product = $product;
		$this->logger = $context->getLogger();

		
    }
	public function getproduct()
	{
		return $this->product->getProduct();
	}
}