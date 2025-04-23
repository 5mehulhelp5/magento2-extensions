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
namespace Milople\Depositpayment\Block\Customer\Payment\Method;
class Form extends \Magento\Payment\Block\Form\Container
{

	 protected $_sessionQuote;
     public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
   		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Catalog\Model\Product $product,
        \Milople\Depositpayment\Block\Customer\Installments $installmentsBlock,
		\Magento\Vault\Model\CustomerTokenManagement $customerTokenManagement,
        array $data = []
    ) {
		$this->_logger = $context->getLogger();
		$this->storeManager = $context->getStoreManager();
		$this->customerFactory = $customerFactory;
		$this->customerRepository = $customerRepository;
		$this->_product = $product;
		$this->installmentsBlock = $installmentsBlock;
        $this->customerTokenManagement = $customerTokenManagement;
	    parent::__construct($context, $paymentHelper, $methodSpecificationFactory, $data);
    }
	
	/**
     *   Prepare custom quote and return to display payment method in installment page in admin to pay an installment
     *
     * @return \Magento\Quote\Model\Quote
     */
     public function getQuote()
     {
        return $this->installmentsBlock->tempQuote;
     }
	/**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\MethodInterface|null $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
		if($method->getCode()== "braintree_paypal_vault"){			
			$isAvailable = false;
			foreach($this->customerTokenManagement->getCustomerSessionTokens() as $token){		
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
			$isAvailable = false;
			foreach($this->customerTokenManagement->getCustomerSessionTokens() as $token){		
				if ($token->getType() === "card") {
					$isAvailable = true;
					break;
				}
			}
			if(!$isAvailable){
				return false;
			}
		}
        return $method && ($method->isOffline() || $method->getCode()== "braintree_paypal_vault" || $method->getCode()== "braintree_paypal" || $method->getCode()== "braintree_cc_vault" || $method->getCode()== "braintree" || $method->getCode()== "paypal_express" || $method->getCode()== "authorizenet_directpost" || $method->getCode()== "stripe") && parent::_canUseMethod($method);		
        // return $method && ($method->isOffline() || $method->getCode()== "braintree_paypal_vault" || $method->getCode()== "braintree_paypal" || $method->getCode()== "braintree_cc_vault" || $method->getCode()== "braintree" || $method->getCode()== "paypal_express" || $method->getCode()== "authorizenet_directpost") && parent::_canUseMethod($method);
    }

    /**
     * Check existing of payment methods
     *
     * @return bool
     */
    public function hasMethods()
    {
        $methods = $this->getMethods();
        if (is_array($methods) && count($methods)) {
            return true;
        }
        return false;
    }

    /**
     * Get current payment method code or the only available, if there is only one method
     *
     * @return string|false
     */
    public function getSelectedMethodCode()
    {
        // One available method. Return this method as selected, because no other variant is possible.
        $methods = $this->getMethods();
        if (count($methods) == 1) {
            foreach ($methods as $method) {
                return $method->getCode();
            }
        }

        // Several methods. If user has selected some method - then return it.
        $currentMethodCode = $this->getQuote()->getPayment()->getMethod();
        if ($currentMethodCode) {
            return $currentMethodCode;
        }

        // Several methods, but no preference for one of them.
        return false;
    }

    /**
     * Whether switch/solo card type available
     *
     * @return true
     * @deprecated unused
     */
    public function hasSsCardType()
    {
        $availableTypes = explode(',', $this->getQuote()->getPayment()->getMethod()->getConfigData('cctypes'));
        $ssPresenations = array_intersect(['SS', 'SM', 'SO'], $availableTypes);
        if ($availableTypes && count($ssPresenations) > 0) {
            return true;
        }
        return false;
    }
    #Responsible to set payment method at customer account.
    public function setMethodFormTemplate($method = '', $template = '')
    {
        if (!empty($method) && !empty($template)) {
            if ($block = $this->getChildBlock('payment.method.' . $method)) {
                $block->setTemplate($template);
            }
        }
        return $this;
    }
    
}
