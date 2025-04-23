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
 
class ReturnAction extends \Magento\Framework\App\Action\Action
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
		\Magento\Framework\App\Request\Http $request,
		\Milople\Depositpayment\Model\Api\Nvp $PaypalNvp
    )
    {
        $this->resultPageFactory = $resultPageFactory;
		$this->PaypalNvp = $PaypalNvp;
		$this->request = $request;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
		if($this->request->getParam('token') && $this->request->getParam('PayerID')){
			$this->_forward('success');
		}else{
			$this->_forward('cancel');
		}
    }
}
