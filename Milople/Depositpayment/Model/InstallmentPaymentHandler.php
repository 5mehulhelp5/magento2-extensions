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
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class InstallmentPaymentHandler extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Catalog\Model\Product $product
    * @param Magento\Framework\Data\Form\FormKey $formKey $formkey,
    * @param Magento\Quote\Model\Quote $quote,
    * @param Magento\Customer\Model\CustomerFactory $customerFactory,
    * @param Magento\Sales\Model\Service\OrderService $orderService,
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		//\Magento\Authorizenet\Model\Directpost $directpost,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Product $product,
		\Magento\Framework\Data\Form\FormKey $formkey,
		\Magento\Quote\Model\QuoteFactory $quote,
		\Magento\Quote\Model\QuoteManagement $quoteManagement,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Sales\Model\Service\OrderService $orderService,
	    \Magento\Framework\DataObject $payment,
	    \Milople\Depositpayment\Model\Calculation $calculation,
		\Milople\Depositpayment\Model\Api\Nvp $PaypalNvp,
		\Magento\Paypal\Model\Config $paypalConfig,
		\Milople\Depositpayment\Model\BraintreeInstallments $braintreeInstallments,
		\Magento\Framework\Module\Manager $moduleManager
	  ) {
        	$this->_storeManager = $storeManager;
			//$this->directpost = $directpost;
        	$this->_product = $product;
        	$this->_formkey = $formkey;
        	$this->quote = $quote;
        	$this->quoteManagement = $quoteManagement;
        	$this->customerFactory = $customerFactory;
        	$this->customerRepository = $customerRepository;
        	$this->orderService = $orderService;
	    	$this->payment = $payment;
	       	$this->_logger = $context->getLogger();
	    	$this->calculation = $calculation;
			$this->PaypalNvp = $PaypalNvp;
			$this->paypalConfig = $paypalConfig;
			$this->braintreeInstallments = $braintreeInstallments;
			$this->_moduleManager = $moduleManager;
        	parent::__construct($context);
    }
 
   	public function payInstallments(\Magento\Sales\Model\Order $order,$payment)
	{						
		$this->payment->setAmount($payment['amount']);
		
		// if($payment['method'] == 'authorizenet_directpost')
		// {
		// 	$orderId = $order->getIncrementId().'-'.$payment['installments']."-".$this->calculation->storeDate->date('is');
		// 	$order->setIncrementId($orderId);
		// 	$this->payment->setOrder($order);
		// 	if(isset($payment['cc_number']))
		// 		$this->payment->setCcNumber($payment['cc_number']);
		// 	if(isset($payment['cc_exp_month']))
		// 		$this->payment->setCcExpMonth($payment['cc_exp_month']);
		// 	if(isset($payment['cc_exp_year']))
		// 		$this->payment->setCcExpYear($payment['cc_exp_year']);
		// 	if(isset($payment['cc_cid']))
		// 		$this->payment->setCcCid($payment['cc_cid']);
		// 	$result = $this->directpost->prepareDirectCallForInstallmentPayment($order,$this->payment);
		// 	if($result->getXTransId()>0)
		// 	{
		// 		$response['success'] = true;
		// 	}
		// 	else
		// 	{
		// 		$response['message'] = 'Sorry, Transaction declined by authorizenet.';
		// 		$response['success'] = false;
		// 	}
		// }
		if($payment['method'] == 'paypal_express')
		{
			$orderId = $order->getIncrementId().'-'.$payment['installments'];
			$this->payment->setIncrementId($orderId);
			$this->paypalConfig->setMethod('paypal_express');
			$response = $this->PaypalNvp->callInstallmentSetExpressCheckout($order,$this->payment);			
			if($response['ACK'] == "Success")
			{
				$redirectUrl = $this->paypalConfig->getExpressCheckoutStartUrl($response['TOKEN']);
				//return $this->resultRedirectFactory->create()->setUrl($redirectUrl);
				$response['success'] = true;
				$response['redirect_url'] = $redirectUrl;
			}
			else
			{
				$response['message'] = 'Sorry, Transaction declined by PayPal.';
				$response['success'] = false;
			}
		}
		if($payment['method']=='braintree'){				
			$expiry_month = str_pad ($payment['cc_exp_month'], 2,0,STR_PAD_LEFT);
			$expiry_year = substr(trim($payment['cc_exp_year']),-2);				
			$payment['expiration_date']= $expiry_month.'/'.$expiry_year;				
			$response = $this->braintreeInstallments->InstallmentCreditCard($payment);				
			if($response['status'])
			{
				$response['success'] = true;
			}
			else
			{
				$response['message'] = 'Sorry, Transaction declined by authorizenet.';
				$response['success'] = false;
			}				
		}elseif($payment['method']=='braintree_cc_vault' || $payment['method']=='braintree_paypal_vault'){				
			if($payment['method']=='braintree_paypal_vault'){
				$payment['token'] = $payment['paypal_token'];
			}
			$response = $this->braintreeInstallments->InstallmentTokenCapture($payment);
			if($response['status'])
			{
				$response['success'] = true;
			}
			else
			{
				$response['message'] = 'Sorry, Transaction declined by authorizenet.';
				$response['success'] = false;
			}
		}elseif($payment['method']=='stripe'){
			if($this->_moduleManager->isEnabled('Milople_Stripe')){
				
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$stripeModel = $objectManager->create('\Milople\Stripe\Model\Payment');					
				if(array_key_exists("lastfour",$payment)){
					if($payment["lastfour"]==="new"){							
						$charge = $stripeModel->cardInstallmentPayment($order,$payment);
					}else{
						$charge=$stripeModel->savedcardInstallmentPayment($payment["lastfour"],$order,$payment['amount'],$payment['installments']);
					}												
				}else{
					$charge = $stripeModel->cardInstallmentPayment($order,$payment);						
				}
				if($charge->id)
				{
					$response['success'] = true;
				}
				else
				{
					$response['message'] = 'Sorry, Transaction declined by authorizenet.';
					$response['success'] = false;
				}	
			}else{
				$response['message'] = 'Sorry, Stripe module is not found.';
				$response['success'] = false;
			}
		}
		else
		{
			$response['message'] = 'Unable to pay installment. Payment method is not supported.';
			$response['success'] = false;
		}
		return $response;
	}
}
