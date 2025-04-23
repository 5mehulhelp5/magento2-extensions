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
 
class Ipn extends \Magento\Framework\App\Action\Action
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
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
		$this->PaypalNvp = $PaypalNvp;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        try {
            $data = $this->getRequest()->getPostValue();
            //save Data somewhere
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
