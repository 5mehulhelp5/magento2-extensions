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
namespace Milople\Depositpayment\Controller\Paypal;
 
class Express extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
	
	protected $partialpaymentCron;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Milople\Depositpayment\Model\Api\Nvp $PaypalNvp,
		\Magento\Paypal\Model\Config $paypalConfig
    )
    {
        $this->resultPageFactory = $resultPageFactory;
		$this->PaypalNvp = $PaypalNvp;
		$this->paypalConfig = $paypalConfig;
		$this->paypalConfig->setMethod('paypal_express');
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {		
		$response = $this->PaypalNvp->callInstallmentSetExpressCheckout();
		$redirectUrl = $this->paypalConfig->getExpressCheckoutStartUrl($response['TOKEN']);
		return $this->resultRedirectFactory->create()->setUrl($redirectUrl);
    }
}
