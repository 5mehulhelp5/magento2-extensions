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
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class PayInstallment extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Milople\Depositpayment\Model\PartialpaymentOrdersFactory $partialpaymentOrdersFactory,
        \Milople\Depositpayment\Model\PartialpaymentInstallmentsFactory $installmentFactory,
        \Milople\Depositpayment\Model\Calculation $calculation,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Milople\Depositpayment\Model\PartialpaymentOrders $partialPaymentOrder,
        \Magento\Sales\Model\Order $order,
        \Milople\Depositpayment\Model\PartialpaymentInstallments $partialInstallment,
        \Milople\Depositpayment\Model\InstallmentPaymentHandler $installmentPaymentHandler,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Module\Manager $moduleManager,
        \Milople\Depositpayment\Model\BraintreeInstallments $braintreeInstallments
    ) {

        $this->orderFactory = $orderFactory->create();
        $this->httpContext=$httpContext;
        $this->_request = $context->getRequest();
        $this->partialpaymentOrdersFactory = $partialpaymentOrdersFactory->create();
        $this->installmentFactory = $installmentFactory->create();
        $this->calculation = $calculation;
        $this->partialPaymentOrder=$partialPaymentOrder;
        $this->paymentHelper = $paymentHelper;
        $this->resultFactory =$context->getResultFactory();
        $this->order=$order;
        $this->partialInstallment=$partialInstallment;
        $this->installmentPaymentHandler = $installmentPaymentHandler;
        $this->installmentSession = $session;
        $this->_moduleManager = $moduleManager;
        $this->braintreeInstallments = $braintreeInstallments;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        # check customer is loging or not
        //$postData = $this->_request->getPost();
        $postData = $this->getRequest()->getPost();
        /* print_r($postData);
        exit; */
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if(!$isLoggedIn)//if user is not loggin redirect to loggin page
        {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('customer/account/login');
            return $resultRedirect;
        }

        # check that partial payment id is set or not in post data
        if(!isset($postData['partialPaymentId']))
        {
            $this->messageManager->addError(__('Unable to edit partially paid order\'s information.'));
            $this->_redirect('*/*/partiallypaidorders',['_current'=>true]);
            return;
        }

        $installments = $postData['installment_ids'];
        # check that any installment is selected or not
        if($installments == NULL)
        {
            $this->messageManager->addError(__('Please select an installment.'));
            $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
            return;
        }
        # check that any payment method is selected or not
        if(!isset($postData['payment']['method']))
        {
            $this->messageManager->addError(__('Please select a Paymet Method.'));
            $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
            return;
        }

        # fatch increment id or current order
        $incrementId = $this->orderFactory->load($this->partialpaymentOrdersFactory->load($postData['partialPaymentId'])->getOrderId())->getIncrementId();
        $payment = $postData['payment'];
        $totalInstallmentPaid = 0;
        $paymentAmount = 0;
        $paymentMethod = $this->paymentHelper->getMethodInstance($postData['payment']['method']);
        if($paymentMethod->isOffline())
        {
            if ($postData['payment']['method']=='paytracevault') {
                if($this->_moduleManager->isEnabled('Elsnertech_Paytrace')){
                    foreach($postData['installment_ids'] as $installmentId){
                        $installmentData = $this->partialInstallment->load($installmentId);
                        if($installmentData->getInstallmentStatus() != 'Paid'){//check installment is paid or not
                            $paymentAmount += $installmentData->getInstallmentAmount();
                        }
                    }

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $paytraceVaultModel = $objectManager->create('\Elsnertech\Paytrace\Model\Paytracevault');

                    $payment = $postData['payment'];
                    $payment['amount'] = $paymentAmount;


                    $partialpaymentOrder = $this->partialPaymentOrder->load($postData['partialPaymentId']);
                    $order = $this->orderFactory->load($partialpaymentOrder->getOrderId());

                    try {
                        $result = $paytraceVaultModel->installmentPayment($order, $payment, $payment['amount']);

                        if ($result) {
                            foreach($postData['installment_ids'] as $installmentId){
                                $this->calculation->setInstallmentSuccessData($installmentId, 'paytracevault');
                            }
                        } else {
                            $this->messageManager->addError(__('PayTrace vault payment failed. Please try another payment method.'));
                            $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addError(__('PayTrace vault payment error: %1', $e->getMessage()));
                        $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    }
                } else {
                    $this->messageManager->addError(__('PayTrace is not enabled. Please try another payment method.'));
                    $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    return;
                }
            }
            # repeate a loop to proccess installments
//            foreach($installments as $installmentId)
//            {
//                $installment = $this->installmentFactory->load($installmentId);
//                # does installment already paid?
//                if($installment->getInstallmentStatus() != 'Paid')
//                {
//                    # set installment success data in table
//                    $this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
//                }
//            }
        }
        else
        {

            if($postData['payment']['method']=='authorizenet_directpost')
            {
                $payment['installments'] = implode("-",$postData['installment_ids']);
                foreach($postData['installment_ids'] as $installmentId)
                {
                    $installmentData = $this->partialInstallment->load($installmentId);
                    if($installmentData->getInstallmentStatus() != 'Paid')//check installment is paid or not
                    {
                        $payment['amount'] += $installmentData->getInstallmentAmount();
                        $response = $this->processPayment($payment,$installmentId);
                        $totalInstallmentPaid++;
                    }

                }

                if($response['success'] == true){
                    $this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
                }else{
                    if(isset($response['message']) && $response['message']!='')
                        $this->messageManager->addError(__($response['message']));
                    else
                        $this->messageManager->addError(__('Installment payment failed.'));
                }
            }
            elseif($postData['payment']['method']=='paypal_express'){
                $payment['installments'] = implode("-",$postData['installment_ids']);
                foreach($postData['installment_ids'] as $installmentId)
                {
                    $installmentData = $this->partialInstallment->load($installmentId);
                    if($installmentData->getInstallmentStatus() != 'Paid')//check installment is paid or not
                    {
                        $payment['amount'] += $installmentData->getInstallmentAmount();
                        $response = $this->processPayment($payment,$installmentId);
                        $totalInstallmentPaid++;
                    }

                }
                if($response['success'] == true){
                    return $this->resultRedirectFactory->create()->setUrl($response['redirect_url']);
                    //$this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
                }else{
                    if(isset($response['message']) && $response['message']!='')
                        $this->messageManager->addError(__($response['message']));
                    else
                        $this->messageManager->addError(__('Installment payment failed.'));
                }
            }
            elseif($postData['payment']['method']=='paytm') {
                if($this->_moduleManager->isEnabled('Milople_Partialpaymentpaytm')){
                    $payment['installments'] = implode("-",$postData['installment_ids']);
                    foreach($postData['installment_ids'] as $installmentId)
                    {
                        $installmentData = $this->partialInstallment->load($installmentId);
                        if($installmentData->getInstallmentStatus() != 'Paid')//check installment is paid or not
                        {
                            $payment['amount'] += $installmentData->getInstallmentAmount();
                            //$response = $this->processPayment($payment,$installmentId);
                            $totalInstallmentPaid++;
                            $installmentids[] = $installmentId;
                        }
                    }
                    $installmentId = implode("-",$postData['installment_ids']);
                    $partialpaymentOrder = $this->partialPaymentOrder->load($postData['partialPaymentId']);
                    $order = $this->orderFactory->load($partialpaymentOrder->getOrderId());
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $model = $objectManager->create('\One97\Paytm\Model\Paytm');
                    $this->installmentSession->setInstallmentId($installmentId);
                    $this->installmentSession->setInstallmentOrderId($partialpaymentOrder->getOrderId());
                    return $this->resultRedirectFactory->create()->setUrl($model->buildPaytmInstallmentRequest($order,$payment['amount']));
                }
                else{
                    $this->messageManager->addError(__('paytm is not supported to pay installment, please try another payment method.'));
                    $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    return;
                }
            }
            elseif($postData['payment']['method']=='braintree'){
                foreach($postData['installment_ids'] as $installmentId){
                    $installmentData = $this->partialInstallment->load($installmentId);
                    if($installmentData->getInstallmentStatus() != 'Paid'){//check installment is paid or not
                        $payment['amount'] += $installmentData->getInstallmentAmount();
                    }
                }
                $expiry_month = str_pad ($payment['cc_exp_month'], 2,0,STR_PAD_LEFT);
                $expiry_year = substr(trim($payment['cc_exp_year']),-2);
                $payment['expiration_date']= $expiry_month.'/'.$expiry_year;
                $response = $this->braintreeInstallments->InstallmentCreditCard($payment);
                if($response['status']){
                    foreach($postData['installment_ids'] as $installmentId){
                        $this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
                    }
                }else{
                    $this->messageManager->addError(__('Installment payment failed.'));
                    $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    return;
                }

            }elseif($postData['payment']['method']=='braintree_paypal'){
                foreach($postData['installment_ids'] as $installmentId){
                    $installmentData = $this->partialInstallment->load($installmentId);
                    if($installmentData->getInstallmentStatus() != 'Paid'){//check installment is paid or not
                        $payment['amount'] += $installmentData->getInstallmentAmount();
                    }
                }
                $response = $this->braintreeInstallments->InstallmentPaymentNonceCapture($payment);
                if($response['status']){
                    foreach($postData['installment_ids'] as $installmentId){
                        $this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
                    }
                }else{
                    $this->messageManager->addError(__('Installment payment failed.'));
                    $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    return;
                }
            }elseif($postData['payment']['method']=='braintree_cc_vault' || $postData['payment']['method']=='braintree_paypal_vault'){
                foreach($postData['installment_ids'] as $installmentId){
                    $installmentData = $this->partialInstallment->load($installmentId);
                    if($installmentData->getInstallmentStatus() != 'Paid'){//check installment is paid or not
                        $payment['amount'] += $installmentData->getInstallmentAmount();
                    }
                }
                if($postData['payment']['method']=='braintree_paypal_vault'){
                    $payment['token'] = $payment['paypal_token'];
                }
                $response = $this->braintreeInstallments->InstallmentTokenCapture($payment);
                if($response['status']){
                    foreach($postData['installment_ids'] as $installmentId){
                        $this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
                    }
                }else{
                    $this->messageManager->addError(__('Installment payment failed.'));
                    $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    return;
                }
            }elseif($postData['payment']['method']=='stripe') {
                if($this->_moduleManager->isEnabled('Milople_Stripe')){
                    foreach($postData['installment_ids'] as $installmentId){
                        $installmentData = $this->partialInstallment->load($installmentId);
                        if($installmentData->getInstallmentStatus() != 'Paid'){//check installment is paid or not
                            $payment['amount'] += $installmentData->getInstallmentAmount();
                            $totalInstallmentPaid++;
                        }
                    }
                    $installmentIds = implode("-",$postData['installment_ids']);
                    $payment['installments'] = $installmentIds;
                    $partialpaymentOrder = $this->partialPaymentOrder->load($postData['partialPaymentId']);
                    $order = $this->orderFactory->load($partialpaymentOrder->getOrderId());
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $stripeModel = $objectManager->create('\Milople\Stripe\Model\Payment');
                    if(array_key_exists("lastfour",$payment)){
                        if($payment["lastfour"]==="new"){
                            $charge = $stripeModel->cardInstallmentPayment($order,$payment);
                        }else{
                            $charge=$stripeModel->savedcardInstallmentPayment($payment["lastfour"],$order,$payment['amount'],$payment['installments']);
                        }
                    }else{
                        $charge = $stripeModel->cardInstallmentPayment($order,$payment);
                    }
                    if($charge->id){
                        foreach($postData['installment_ids'] as $installmentId){
                            $this->calculation->setInstallmentSuccessData($installmentId,$postData['payment']['method']);
                        }
                    }else{
                        $this->messageManager->addError(__('Installment payment failed.'));
                        $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                        return;
                    }
                }else{
                    $this->messageManager->addError(__('Stripe is not supported to pay installment, please try another payment method.'));
                    $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                    return;
                }
            }
            else
            {
                $this->messageManager->addError(__('Payment method is not supported to pay installment, please try another payment method.'));
                $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
                return;
            }
        }
        #set installment payment success message
        $incrementIdStr = "<strong>#".$incrementId."</strong>";
        if(count($installments) == 1)//if only one installment paid
        {
            $this->messageManager->addSuccess(__('Installment of order %1 is paid successfully.',$incrementIdStr));
        }
        else //more than one installment paid
        {
            $this->messageManager->addSuccess(__('Installments of order %1 is paid successfully.',$incrementIdStr));
        }
        $this->_redirect('*/*/installments', ['partialpayment_id' => $postData['partialPaymentId'], '_current' => true]);
        return;
    }

    protected function processPayment($payment,$partialId)
    {
        $response = array();
        $partialpaymentOrder = $this->partialPaymentOrder->load($partialId);
        $order = $this->orderFactory->load($partialpaymentOrder->getOrderId());
        $response = $this->installmentPaymentHandler->payInstallments($order,$payment);
        return $response;
    }
}
