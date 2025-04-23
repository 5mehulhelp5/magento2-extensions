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
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/
namespace Milople\Depositpayment\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class AddToCartBefore implements ObserverInterface
{
	protected $messageManager;
	
	public function __construct(
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Request\Http $request,
		\Milople\Depositpayment\Model\Calculation $calculationModel,
		\Milople\Depositpayment\Helper\Partialpayment $partialHelper,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Catalog\Model\Product $product
		) {
        $this->request = $request;
		$this->calculationModel = $calculationModel;
		$this->partialHelper = $partialHelper;
		$this->_customerSession  = $customerSession;
 		$this->messageManager = $messageManager;		
		$this->_product = $product;
	 }
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$product = $this->_product->load($observer->getRequest()->getParam('product'));
		if( $this->partialHelper->canShowPartialPayment($product)){			
			$requestParms = $observer->getRequest()->getParams();			
			if(!array_key_exists('allow_partial_payment',$requestParms)){
				$observer->getRequest()->setParam('product', false);
				$observer->getRequest()->setParam('return_url', $product->getProductUrl());				
				$this->_customerSession->setAddCartFail($requestParms['product']);
			}
		}		
	}
}