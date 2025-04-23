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
namespace Milople\Depositpayment\Controller\Adminhtml\Reports;
class MassInstallmentsRemind extends \Magento\Backend\App\Action{
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Milople\Depositpayment\Model\PartialpaymentInstallments $installmentsFactory,
		\Milople\Depositpayment\Model\PartialpaymentOrders $partialpaymentOrdersFactory,
		\Milople\Depositpayment\Helper\EmailSender $emailSender,
		\Magento\Sales\Model\Order $orderFactory
    ) {
        parent::__construct($context);
		$this->installmentsFactory = $installmentsFactory;
		$this->partialpaymentOrdersFactory = $partialpaymentOrdersFactory;
		$this->emailSender = $emailSender;
		$this->orderFactory = $orderFactory;
    }
    public function execute()
    {
		try{	
			$allInstallments = $this->_request->getParams();
			$allInstallments = $allInstallments['partialpaymentInstallments'];		
			// call email sending function to send reminder mail
			foreach($allInstallments as $installmentId){			
				$installmentModel = $this->installmentsFactory->load($installmentId);
				$partialPaymentModel = $this->partialpaymentOrdersFactory->load($installmentModel->getPartialPaymentId());
				$orderModel = $this->orderFactory->load($partialPaymentModel->getOrderId());
				if($orderModel->getCustomerFirstname()){
					//$customerName = $orderModel->getCustomerFirstname()." ".$orderModel->getCustomerLastname();
					$billingAddress = $orderModel->getBillingAddress();
					$customerName = $billingAddress->getFirstname()." ".$billingAddress->getLastname();
				}else{
					$billingAddress = $orderModel->getBillingAddress();
					$customerName = $billingAddress->getFirstname()." ".$billingAddress->getLastname();
				}			
				$this->emailSender->sendInstallmentReminerEmail($customerName,$orderModel->getCustomerEmail(),$orderModel->getIncrementId(), $installmentModel->getPartialPaymentId(), $orderModel->getOrderCurrencyCode(), $installmentModel->getInstallmentDueDate());			
			}
			$this->messageManager->addSuccess(__("Reminder Email Successfully Send."));
		}
		catch(\Exception $e)
		{
			$this->messageManager->addError($e->getMessage());			
		}
		$this->_redirect('*/*/installments');
    }
}