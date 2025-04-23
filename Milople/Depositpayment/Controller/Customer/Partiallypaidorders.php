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
namespace Milople\Depositpayment\Controller\Customer;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class Partiallypaidorders extends \Magento\Framework\App\Action\Action implements OrderInterface
{
    
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Http\Context $contextHttp,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->contextHttp=$contextHttp;
        parent::__construct($context);
    }
    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
		$isLoggedIn = $this->contextHttp->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
		if(!$isLoggedIn)//if user is not loggin redirect to loggin page
		{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('customer/account/login');
			return $resultRedirect;
		}
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('this is controlelr');
		$this->_view->loadLayout();
     	$this->_view->renderLayout();
     
    }
}
