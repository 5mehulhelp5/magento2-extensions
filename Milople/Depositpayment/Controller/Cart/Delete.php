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
namespace Milople\Depositpayment\Controller\Cart;

class Delete extends \Magento\Checkout\Controller\Cart\Delete
{
    protected $calculationModel;
    /**
     * Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
		\Milople\Depositpayment\Model\Calculation $calculationModel
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
		$this->calculationModel = $calculationModel;
    }

    public function execute()
    {
        if($this->cart->getItemsCount() == 1)
		{
			$this->_checkoutSession->unsAllowPartialPayment();
            $this->_checkoutSession->unsGroupIds();
		}
		else if($this->_checkoutSession->getAllowPartialPayment() == 1){
			if($this->calculationModel->getQuote()->getSubtotal() <= $this->calculationModel->getDownPaymentAmount($this->calculationModel->getQuote()->getSubtotal()))
			{
				$this->_checkoutSession->unsAllowPartialPayment();
			}
		}
		return parent::execute();
    }
}
