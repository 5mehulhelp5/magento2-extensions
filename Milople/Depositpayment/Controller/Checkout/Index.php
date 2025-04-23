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
namespace Milople\Depositpayment\Controller\Checkout;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class Index extends \Magento\Checkout\Controller\Index\Index
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Milople\Depositpayment\Model\Calculation $calculation,
        \Milople\Depositpayment\Helper\Partialpayment $partialpaymentHelper,
        \Milople\Depositpayment\Helper\Data $dataHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->calculation=$calculation;
        $this->checkoutSession = $checkoutSession;
        $this->partialpaymentHelper = $partialpaymentHelper;
	$this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }
    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */
        $calculationModel = $this->calculation;
        $basePaidAmount = $calculationModel->getPayingNow();
        $quote = $this->getOnepage()->getQuote();
        $baseRemainingAmount = $calculationModel->getAmountToBePaidLater();
		$isFlexyAllow = $this->partialpaymentHelper->isAllowFlexyPayment();
		$isFullPaymentAllow = $this->partialpaymentHelper->isAllowFullPayment();
        $message = $calculationModel->checkCreditLimit($this->_customerSession->getCustomer()->getId(),$baseRemainingAmount,$quote->getQuoteCurrencyCode());
        if ($message) {
            $this->messageManager->addError(__($message));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $postData = $this->getRequest()->getParams();
		$status = $this->partialpaymentHelper->getStatus();
		$isValid = $this->dataHelper->canRun();
		$isWholeCart = $calculationModel ->isAllowWholeCart();
		if ($status && $isValid && $isWholeCart)
		{
			if(isset($postData['allow-partial-payment']))
			{
				$this->checkoutSession->setAllowPartialPayment($postData['allow-partial-payment']);
			}
			else if(!$isFullPaymentAllow && !$isFlexyAllow)
			{
				$this->checkoutSession->setAllowPartialPayment(1);
			}			
		}
        return parent::execute();
    }
}
