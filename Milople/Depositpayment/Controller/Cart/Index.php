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
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class Index extends \Magento\Checkout\Controller\Cart\Index
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Milople\Depositpayment\Helper\Partialpayment $partialpaymentHelper,		
		\Milople\Depositpayment\Model\Calculation $calculationModel
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
			$resultPageFactory
        );
		$this->checkoutSession = $checkoutSession;
		$this->partialpaymentHelper = $partialpaymentHelper;		
		$this->calculationModel = $calculationModel;
    }

    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {	
		$postData = $this->getRequest()->getParams();
		$canShow = $this->partialpaymentHelper->canShowPartialPayment();		
		$isWholeCart = $this->calculationModel->isAllowWholeCart();
		$isFullPaymentAllow = $this->partialpaymentHelper->isAllowFullPayment();
		$isFlexyAllow = $this->partialpaymentHelper->isAllowFlexyPayment();	
		if ($canShow && $isWholeCart)
		{
			if(isset($postData['allow-partial-payment']))
			{
				$this->checkoutSession->setAllowPartialPayment($postData['allow-partial-payment']);
			}
			else if(!$isFullPaymentAllow && !$isFlexyAllow)
			{
				$this->checkoutSession->setAllowPartialPayment(1);
			}
			$this->checkoutSession->getQuote()->collectTotals()->save();	
		}
		return parent::execute();
		
    }
}