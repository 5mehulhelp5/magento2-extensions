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
 
class Success extends \Magento\Framework\App\Action\Action
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
		\Milople\Depositpayment\Model\Api\Nvp $PaypalNvp,
		\Milople\Depositpayment\Model\Calculation $calculation,
		\Milople\Depositpayment\Model\PartialpaymentOrders $partialPaymentOrder,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Milople\Depositpayment\Model\PartialpaymentInstallments $partialInstallment
    )
    {
        $this->resultPageFactory = $resultPageFactory;
		$this->PaypalNvp = $PaypalNvp;
		$this->partialPaymentOrder = $partialPaymentOrder;		
		$this->orderFactory = $orderFactory->create();
		$this->partialInstallment = $partialInstallment;
		$this->calculation = $calculation;
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
		$token = $this->request->getParam('token');
		$payerID = $this->request->getParam('PayerID');
		
		$response = $this->PaypalNvp->callInstallmentGetExpressCheckout($token);		
		$invNum = $response['PAYMENTREQUEST_0_INVNUM'];
		$invArray = explode("-",$invNum);		
		$installmentData = $this->partialInstallment->load($invArray[1]);
		$amount = $installmentData->getInstallmentAmount();
		$partialpaymentOrder = $this->partialPaymentOrder->load($installmentData->getPartialPaymentId());
		$order = $this->orderFactory->load($partialpaymentOrder->getOrderId());
		$response = $this->PaypalNvp->callInstallmentDoExpressCheckoutPayment($token,$payerID,$invNum,$amount,$order);
		if($response['ACK']=='Success')//if payment successfully captured
		{
			$incrementId = $order->getIncrementId();
			$incrementIdStr = "<strong>#".$incrementId."</strong>";
			$this->calculation->setInstallmentSuccessData($invArray[1],"paypal_express");
			$this->messageManager->addSuccess(__('Installment of order %1 is paid successfully.',$incrementIdStr));
			$this->_redirect('depositpayment/customer/installments', ['partialpayment_id' => $installmentData->getPartialPaymentId(), '_current' => true]);	
		}else{
			$this->messageManager->addError(__('Payment Transaction request rejected by paypal.'));
			$this->_redirect('depositpayment/customer/installments', ['partialpayment_id' => $installmentData->getPartialPaymentId(), '_current' => true]);
		}
		return $this->resultPageFactory->create();
    }
}
