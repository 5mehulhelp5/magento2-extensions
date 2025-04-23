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
namespace Milople\Depositpayment\Model;

class PartialpaymentCron
{
	protected $checkoutSession;
	protected $sessionQuote;
	
	public function __construct(
		\Milople\Depositpayment\Helper\Partialpayment $partialpaymentHelper,
		\Magento\Catalog\Model\Product $product,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Milople\Depositpayment\Model\PartialpaymentInstallmentsFactory $installmentsFactory,
		\Milople\Depositpayment\Model\PartialpaymentOrdersFactory $partialpaymentOrdersFactory,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\Pricing\Helper\Data $priceHelperData,
		\Milople\Depositpayment\Helper\EmailSender $emailSender
		//\Magento\Framework\App\State $state
	)
	{	
		$this->partialpaymentHelper = $partialpaymentHelper;
		$this->product = $product;
		$this->dateTime = $dateTime;
		$this->installmentsFactory = $installmentsFactory;
		$this->partialpaymentOrdersFactory = $partialpaymentOrdersFactory;
		$this->orderFactory = $orderFactory;
		$this->priceHelper = $priceHelperData;
		$this->emailSender = $emailSender;
		//$state->setAreaCode('frontend');//set cust aread code		
	}
	public function runFunctions()
	{
		$this->sendReminderMails();//send installment reminder mails
		$this->captureInstallmentAndOverDueMails();//send installment over due mails
	}
	//this function will send installment reminder mails
	public function sendReminderMails()
	{
		$date = $this->dateTime->date('m/d/Y');
		$newDate = date('Y-m-d', strtotime($date ."+2 day"));// get dates for send reminder mail
		// fatch installment collection for which reminder mail need to send
		$installments = $this->installmentsFactory->create()->getCollection()->addFieldToFilter('installment_due_date',$newDate)->addFieldToFilter('installment_status','Remaining')->addFieldToFilter('installment_reminder_email_sent',0);
		
		//check installment for send reminder mail
		foreach($installments as $installment)
		{
			$partialpaymentOrder = $this->partialpaymentOrdersFactory->create()->load($installment->getPartialPaymentId());//fetch partial payment order
			$order = $this->orderFactory->create()->load($partialpaymentOrder->getOrderId());//fetch order
			$customerName = $order->getCustomerFirstName()." ".$order->getCustomerLastName();//fetch customer name
			// call email sending function to send reminder mail
			$this->emailSender->sendInstallmentReminerEmail($customerName,$order->getCustomerEmail(),$order->getIncrementId(), $installment->getPartialPaymentId(), $order->getOrderCurrencyCode(), $installment->getInstallmentDueDate());
			//set installment reminder email sent to 1 for perticular installment
			$installment->setInstallmentReminderEmailSent(1)->save();
		}
	}
	//this function will send installment over due mails
	public function captureInstallmentAndOverDueMails()
	{
		$date = $this->dateTime->date('m/d/Y');
		$ndate = date('Y-m-d', strtotime($date));
		$captureDate[0] = date('Y-m-d', strtotime($ndate ."-2 day"));// get dates for send reminder mail
		$captureDate[1] = date('Y-m-d', strtotime($ndate ."-4 day"));// get dates for send reminder mail
		$captureDate[2] = date('Y-m-d', strtotime($ndate ."-6 day"));// get dates for send reminder mail
		
		//fet installments records to process
		$installments = $this->installmentsFactory->create()->getCollection()->addFieldToFilter('installment_due_date',array('in'=>$captureDate))->addFieldToFilter('installment_status','Remaining')->addFieldToFilter('installment_over_due_notice_email_sent',0);
		
		foreach($installments as $installment)
		{			
			$partialpaymentOrder = $this->partialpaymentOrdersFactory->create()->load($installment->getPartialPaymentId());//fetch partial payment order
			$order = $this->orderFactory->create()->load($partialpaymentOrder->getOrderId());//fetch order
			$customerName = $order->getCustomerFirstName()." ".$order->getCustomerLastName();//fetch customer name
			$installmentAmount = $this->priceHelper->currency($this->partialpaymentHelper->convertBaseToAnyCurrencyAmount($installment->getInstallmentAmount(), $order->getOrderCurrencyCode()));
			//send installment over due mail
			$this->emailSender->sendOverDueMail($customerName,$order->getCustomerEmail(),$order->getIncrementId(), $installment->getPartialPaymentId(), $installment->getInstallmentDueDate(),$installmentAmount);
			$installment->setInstallmentOverDueNoticeEmailSent(1)->save();
		}
	}
}	
