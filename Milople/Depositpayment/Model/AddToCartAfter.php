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
namespace Milople\Depositpayment\Model;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NotFoundException;
use Exception;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class AddToCartAfter implements ObserverInterface
{ 
	protected $request;
    public function __construct (
       \Magento\Framework\Registry $registry,
	   \Magento\Framework\App\RequestInterface $request,
 	   \Magento\Catalog\Model\Product $productModel,
       \Magento\Checkout\Model\Session $checkoutSession,
       \Psr\Log\LoggerInterface $logger
	  ) {
        $this->_registry = $registry;
		$this->_request = $request;
		$this->logger=$logger;
		$this->_productModel = $productModel;
		$this->_checkoutSession = $checkoutSession;
	}
 
    public function execute(Observer $observer)
    {
       $postdata = $this->_request->getPost();	   
	   try
	   {
		   if (isset($postdata['super_group'])) 
			{				 
				if (isset($postdata['allow_partial_payment']))
				{
					$session = $this->_checkoutSession;
					$session->setData($postdata['product'], $postdata['allow_partial_payment']);
				}
			}
			if(isset($postdata['allow_partial_payment']) && $postdata['allow_partial_payment']>=1) //check partial payment is selected or not in frontend
			{
				$product = $this->_productModel->load($postdata['product']);
				if ($product->getTypeId() === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {					
         			$childProductCollection = $product->getTypeInstance()->getAssociatedProducts($product);
         			$child = array();
         			$numberOfInstallment = array();
         			foreach($childProductCollection as $childProduct) 
					{
						$child[]=$childProduct->getId();
						$numberOfInstallment[$childProduct->getId()] = $postdata['allow_partial_payment'];

					}
					$this->_checkoutSession->setGroupIds($child);
					$this->_checkoutSession->setGroupInstallmentNumber($numberOfInstallment);
      		 	}else{
      				$product->addCustomOption('allow_partial_payment', $postdata['allow_partial_payment']); //set value of partial payment allow in infoByRequest	 		
      		 	}
				
			}
	   }
	   catch(\Exception $e)
       {
			$this->logger->debug($e->getMessage()); 
			return $e->getMessage();		 
	   }
		
		
    }
     
}