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
namespace Milople\Depositpayment\Block\Adminhtml\Payment\Method;
class Form extends \Magento\Payment\Block\Form\Container
{
     public function __construct(
	\Magento\Catalog\Model\Product $product,
    \Magento\Payment\Helper\Data $paymentHelper,
    \Magento\Quote\Model\QuoteFactory $quote,
	\Magento\Customer\Model\CustomerFactory $customerFactory,
    \Magento\Framework\View\Element\Template\Context $context,
	\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
    \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
	\Milople\Depositpayment\Block\Adminhtml\Partiallypaidorders\Edit\Tab\AllInformation $installmentsBlock,
	\Magento\Vault\Model\PaymentTokenManagement $paymentTokenManagement,
	\Magento\Framework\App\Request\Http $request,
	\Magento\Sales\Model\Order $order,
	\Milople\Depositpayment\Model\PartialpaymentOrders $partialpaymentOrders,
	array $data = []
    ) {
		$this->storeManager = $context->getStoreManager();
		$this->customerFactory = $customerFactory;
		$this->customerRepository = $customerRepository;
		$this->_product = $product;
		$this->quote = $quote;
		$this->installmentsBlock = $installmentsBlock;
		$this->paymentTokenManagement = $paymentTokenManagement;
		$this->request = $request;
		$this->order = $order;
		$this->partialpaymentOrders = $partialpaymentOrders;
        parent::__construct($context, $paymentHelper, $methodSpecificationFactory, $data);
    }
	
	# Prepare custom quote and return to display payment method in installment page in admin to pay an installment
	public function getQuote()
	{
		return $this->installmentsBlock->tempQuote;
	}
	protected function _canUseMethod($method)
    {
		if($method->getCode()== "braintree_paypal_vault"){
			$partialOderId = $this->request->getParam('id');			
			$partialOrder = $this->partialpaymentOrders->load($partialOderId);			
			$order = $this->order->load($partialOrder->getOrderId());
			$isAvailable = false;
			foreach($this->paymentTokenManagement->getListByCustomerId($order->getCustomerId()) as $token){		
				if ($token->getType() === "account") {
					$isAvailable = true;
					break;
				}
			}
			if(!$isAvailable){
				return false;
			}
		}
		if($method->getCode()== "braintree_cc_vault"){
			$partialOderId = $this->request->getParam('id');			
			$partialOrder = $this->partialpaymentOrders->load($partialOderId);			
			$order = $this->order->load($partialOrder->getOrderId());
			$isAvailable = false;
			foreach($this->paymentTokenManagement->getListByCustomerId($order->getCustomerId()) as $token){		
				if ($token->getType() === "card") {
					$isAvailable = true;
					break;
				}
			}
			if(!$isAvailable){
				return false;
			}
		}
        return $method && ($method->canUseInternal()) && parent::_canUseMethod($method);
    }
}
