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
namespace Milople\Depositpayment\Controller\Adminhtml\Partiallypaidorders;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use \AllowDynamicProperties;

#[AllowDynamicProperties]
class Save extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
	 public function __construct(
			\Magento\Backend\App\Action\Context $context,
        	\Magento\Catalog\Helper\Product $productHelper,
        	\Magento\Framework\Escaper $escaper,
        	PageFactory $resultPageFactory,
        	ForwardFactory $resultForwardFactory,
        	\Milople\Depositpayment\Model\PartialpaymentInstallments $installmentData,
        	\Milople\Depositpayment\Model\PartialpaymentOrders $partialOrders,
        	\Milople\Depositpayment\Model\Calculation $calculationModel,
        	\Magento\Sales\Model\Order $order,
        	\Milople\Depositpayment\Model\InstallmentPaymentHandler $installmentPaymentHandler,
			\Milople\Depositpayment\Model\Order\Payment $payment
    	) 
	{
		parent::__construct($context,$productHelper,$escaper,$resultPageFactory,$resultForwardFactory);
        $this->calculationModel = $calculationModel;
        $this->installmentData=$installmentData;
        $this->partialOrders=$partialOrders;
        $this->order=$order;
		$this->installmentPaymentHandler = $installmentPaymentHandler;
		$this->payment = $payment;
	}
	 /**
	 * @return void
	 */
	protected function getPaid($id)
	{
		$postData = $this->getRequest()->getPost();
		$installmentData = $this->installmentData->load($id);
		if($installmentData->getInstallmentStatus()!='Paid' && $postData['installment_status'][$id] == 'Paid')
			return true;
		else
			return false;
	}
	public function execute()
	{
		$postData = $this->getRequest()->getPost();
		if ($postData)// check data is set or not 
		{
			$partialpayment_id = $this->getRequest()->getParam('partial_payment_id');
			if($partialpayment_id == NULL)//check partialpayment order is proper or not
			{
				$this->messageManager->addError(__('Unable to edit partially paid order\'s information.'));
				$this->_redirect('*/*/');
				return;
			}
			elseif(!isset($postData['installment_id']))//if not any installment selected for edit return back
			{
				$this->messageManager->addError(__('Please select an installment.'));
				$this->_redirect('*/*/edit', ['id' => $partialpayment_id, '_current' => true]);
				return;
			}
			$payment = $postData['payment'];
			$totalInstallmentUpdated = 0;
			$totalInstallmentPaid = 0;
			$payment['amount'] = 0;
			try 
			{
				// check is there any installment selected to pay and payment method is set
				$installmentToPay = array_filter($postData['installment_id'],array($this,"getPaid"));
				//if payment method is not set return back with error message
				if(!empty($installmentToPay) && (!isset($payment['method']) || $payment['method'] == ''))
				{
					$this->messageManager->addError(__('Please select a payment method to pay installment(s).'));
					$this->_redirect('*/*/edit', ['id' => $partialpayment_id, '_current' => true]);
					return;
				}
				unset($installmentToPay);
				$installmentToPay = array();
				foreach($postData['installment_id'] as $installmentId)
				{
					$installmentData = $this->installmentData->load($installmentId);
					if($installmentData->getInstallmentStatus() != 'Paid' && (isset($payment['method'])) )//check installment is paid or not
					{
						$payment['amount'] += $installmentData->getInstallmentAmount();
						$totalInstallmentPaid++;
						array_push($installmentToPay,$installmentData->getId());
					}
					else
					{
						if($installmentData->getInstallmentStatus() == 'Paid' && $postData['installment_status'][$installmentId] != 'Paid')
						{
							$this->calculationModel->setInstallmentUnPaid($installmentId);
						}
						$installmentData->setInstallmentStatus($postData['installment_status'][$installmentId]);
						$installmentData->save();
						$totalInstallmentUpdated++;
					}					
				}
				if($totalInstallmentPaid > 0)
				{
					$payment['installments'] = implode("-",$installmentToPay);
					$response = $this->procesPayment($payment);
					
					if($response['success'] == true)
					{
						foreach($installmentToPay as $installment)
						{
							$this->calculationModel->setInstallmentSuccessData($installmentId,$payment['method']);
						}
						// Display success message
						if($totalInstallmentPaid == 1)
						{
							$successMessage = "1 installment of order #".$postData['order_incrementId']." has been paid successfully.";
							$this->messageManager->addSuccess(__($successMessage));
						}
						else if($totalInstallmentPaid > 1)
						{
							$successMessage = $totalInstallmentPaid." installments of order #".$postData['order_incrementId']." has been paid successfully.";
							$this->messageManager->addSuccess(__($successMessage));
						}
					}
					else
					{
						if(isset($response['message']) && $response['message']!='')
							$this->messageManager->addError(__($response['message']));
						else
							$this->messageManager->addError(__('Installment payment failed.'));
					}
				}
				if($totalInstallmentUpdated == 1)
				{
					$successMessage = "1 installment of order #".$postData['order_incrementId']." has been updated successfully.";
					$this->messageManager->addSuccess(__($successMessage));
				}
				else if($totalInstallmentUpdated > 1)
				{
					$successMessage = $totalInstallmentUpdated." installments of order #".$postData['order_incrementId']." has been updated successfully.";
					$this->messageManager->addSuccess(__($successMessage));
				}
				// Check if 'Save and Continue'
				if ($this->getRequest()->getParam('back')) 
				{
					$this->_redirect('*/*/edit', ['id' => $partialpayment_id, '_current' => true]);
					return;
				}

				// Go to grid page
				$this->_redirect('*/*/');
				return;
			} 
			catch (\Exception $e) 
			{
				$this->messageManager->addError($e->getMessage());
			}
			$this->_getSession()->setFormData($postData);
			$this->_redirect('*/*/edit', ['id' => $partialpayment_id]);
		}
	}
	protected function procesPayment($payment)
	{
		$response = array();
		if($payment['method'] == 'purchaseorder' || $payment['method'] == 'banktransfer' || $payment['method'] == 'checkmo' || $payment['method'] == 'cashondelivery')
		{
			$response['success'] = true;
		}
		else
		{
			$partialpaymentOrder = $this->partialOrders->load($this->getRequest()->getParam('partial_payment_id'));
			$order = $this->order->load($partialpaymentOrder->getOrderId());
			$response = $this->installmentPaymentHandler->payInstallments($order,$payment);
		}
		return $response;
	}
}
