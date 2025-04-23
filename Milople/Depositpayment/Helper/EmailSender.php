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
namespace Milople\Depositpayment\Helper;
use \AllowDynamicProperties;

#[AllowDynamicProperties]
/**
 * Catalog data helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EmailSender extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\Translate\Inline\StateInterface $stateInterface,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Magento\Framework\Pricing\Helper\Data $priceHelperData,
		\Magento\Directory\Model\Currency $currency,
		\Milople\Depositpayment\Helper\Partialpayment $partialpaymentHelper,
		\Milople\Depositpayment\Model\PartialpaymentInstallments $partialPaymentInstallments,
		\Magento\Theme\Block\Html\Header\Logo $logo,
		\Psr\Log\LoggerInterface $logger
    ){
        parent::__construct($context);
		$this->storeManager = $storeManager;
		$this->_transportBuilder = $transportBuilder;
		$this->inlineTranslation = $stateInterface;
		$this->dateTime = $dateTime;
		$this->partialpaymentHelper = $partialpaymentHelper;
		$this->objectManager = $objectManager;
		$this->priceHelper = $priceHelperData;
		$this->partialPaymentInstallments=$partialPaymentInstallments;
		$this->_currency = $currency;
		$this->logger = $logger;
		$this->_logo = $logo;
    }
	public function getLogoUrl()	
	{	
		return $this->_logo->getLogoSrc();	
	}
	/* public function getLogoUrl()
	{
		return $this->storeManager->getStore()->getBaseUrl().$this->scopeConfig->getValue(
                'design/header/logo_src',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	} */
	public function getLogoAlt()
	{
		return $this->scopeConfig->getValue(
			'design/header/logo_alt',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	// function will return Url of User Installment Page
	public function getUserPartialpaymentUrl($partialPaymentId)
	{
		return $this->storeManager->getStore()->getUrl('depositpayment/customer/installments',array('partialpayment_id'=>$partialPaymentId));
	}
	
	# Prepare Installment Grid Of Partial Payment Order Function
	protected function getInstallmentGrid($partialpaymentId,$currencyCode)
	{
		try
		{
			$installments = $this->partialPaymentInstallments->getCollection()->addFieldToFilter('partial_payment_id',$partialpaymentId);
			$partialpayment_installment_grid="";
			if (sizeof($installments)) 
			{
				$partialpayment_installment_grid .= '<table style="width:100%; border-collapse: collapse; margin-bottom:20px" align="center" cellspacing="0" cellpadding="1" border="0">';
				$partialpayment_installment_grid .= '<tr>';
				# Prepare Header Row
				$partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;">Installment</th>';
				$partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;">Installment Amount</th>';
				$partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;">Due Date</th>';
				$partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;">Paid Date</th>';
				$partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;">Installment Status</th>';
				$partialpayment_installment_grid .= '</tr>';
				$i=1;
				# Prepare Installments Row
				foreach($installments as $installment)
				{
					$due_date = $this->dateTime->date('m-d-Y',$installment->getInstallmentDueDate());
					$paid_date=NULL;
					if($installment->getInstallmentPaidDate()!="" && $installment->getInstallmentStatus()=='Paid')
					{
						$paid_date = $this->dateTime->date('m-d-Y',$installment->getInstallmentPaidDate());
					}
					$partialpayment_installment_grid .= '<tr>';
					$partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="right">' . $this->partialpaymentHelper->addOrdinalNumberSuffix($i) . '</td>';
					$partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="right">' . $this->priceHelper->currency($this->partialpaymentHelper->convertBaseToAnyCurrencyAmount($installment->getInstallmentAmount(), $currencyCode)) . '</td>';
					$partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="center">' . $due_date . '</td>';
					$partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="center">' . $paid_date . '</td>';
					$partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="left">' . $installment->getInstallmentStatus() . '</td>';
					$partialpayment_installment_grid .= '</tr>';
					$i++;
				}
				$partialpayment_installment_grid .= '</table>';
			}
			return $partialpayment_installment_grid;
		}
		catch(\Exception $e)
		{
			return "error";
		}
	}
	
	#Actuall Mail Sender Function
	public function sendMail($customerEmailId,$template,$templateVars,$mailCc)
	{
		
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
		$from = $this->partialpaymentHelper->getEmailSender();
		//$this->inlineTranslation->suspend();
		$to = array($customerEmailId);
		$transport = $this->_transportBuilder->setTemplateIdentifier($template)
						->setTemplateOptions($templateOptions)
						->setTemplateVars($templateVars)
						->setFrom($from)
						->addTo($to);		
		if($mailCc==2 && !empty($this->partialpaymentHelper->getEmailCC()))
		{
			$transport->addBcc($this->partialpaymentHelper->getEmailCC());
		}
		$transport->getTransport()->sendMessage();
		
	}
	
	#Order Success Mail Sender Function
	public function sendOrderSuccessEmail($customerName,$customerEmailId, $incrementId, $partialPaymentId, $currencyCode)
	{
		$this->logger->log(100,print_r("sendOrderSuccessEmail",true));
		try{
			if($this->partialpaymentHelper->getSendPartiallyPaidOrdersConfirmationEmail())
			{
				$templateVars = array(
									'storeName' => $this->storeManager->getStore()->getName(),
									'logo_url' => $this->getLogoUrl(),
									'logo_alt' => $this->getLogoAlt(),
									'customer_name' => $customerName,
									'store_url'   => $this->storeManager->getStore()->getBaseUrl(),
									'brand_label' => $this->partialpaymentHelper->getPartialpaymentLabel(),
									'order_id'    => $incrementId,
									'partialpayment_installment_grid' => $this->getInstallmentGrid($partialPaymentId,$currencyCode),
									'user_partial_payment' => $this->getUserPartialpaymentUrl($partialPaymentId)
								);
				$this->sendMail(
					$customerEmailId,
					$this->partialpaymentHelper->getSendPartiallyPaidOrdersConfirmationTemplate(),
					$templateVars,
					$this->partialpaymentHelper->getSendPartiallyPaidOrdersConfirmationEmail()
				);
			}
		}catch(\Exception $e){	
			\Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($e->getMessage());	
			//throw new \Exception($e->getMessage());
		}
	}
	# Installment Reminder Mail Sender Function
	public function sendInstallmentReminerEmail($customerName,$customerEmailId, $incrementId, $partialPaymentId, $currencyCode, $nextInstallmentDueDate)
	{
		try
		{
			if($this->partialpaymentHelper->getSendInstallmentReminderEmail())
			{
				$templateVars = array(
									'storeName' => $this->storeManager->getStore()->getName(),
									'logo_url' => $this->getLogoUrl(),
									'logo_alt' => $this->getLogoAlt(),
									'customer_name' => $customerName,
									'next_installment_date' => $nextInstallmentDueDate,
									'store_url'   => $this->storeManager->getStore()->getBaseUrl(),
									'brand_label' => $this->partialpaymentHelper->getPartialpaymentLabel(),
									'order_id'    => $incrementId,
									'partialpayment_installment_grid' => $this->getInstallmentGrid($partialPaymentId,$currencyCode),
									'user_partial_payment' => $this->getUserPartialpaymentUrl($partialPaymentId)
								);
				$this->sendMail(
					$customerEmailId,
					$this->partialpaymentHelper->getInstallmentReminderEmailTemplate(),
					$templateVars,
					$this->partialpaymentHelper->getSendInstallmentReminderEmail()
				);
			}
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	}
	
	#Installment Payment Confirmation Mail Sender Function
	public function sendInstallmentPaymentConfirmationMail($customerName,$customerEmailId, $incrementId, $partialPaymentId, $installmentAmount,$currencyCode)
	{
		
		try
		{
			if($this->partialpaymentHelper->getSendInstallmentPaymentConfirmationEmail())
			{
				$templateVars = array(
									'storeName' => $this->storeManager->getStore()->getName(),
									'logo_url' => $this->getLogoUrl(),
									'logo_alt' => $this->getLogoAlt(),
									'customer_name' => $customerName,
									'store_url'   => $this->storeManager->getStore()->getBaseUrl(),
									'order_id'    => $incrementId,
									'brand_label' => $this->partialpaymentHelper->getPartialpaymentLabel(),
									'installment_amount' => $this->priceHelper->currency($this->partialpaymentHelper->convertBaseToAnyCurrencyAmount($installmentAmount, $currencyCode)),
									'partialpayment_installment_grid' => $this->getInstallmentGrid($partialPaymentId,$currencyCode),
									'user_partial_payment' => $this->getUserPartialpaymentUrl($partialPaymentId)
								);
				$this->sendMail(
					$customerEmailId,
					$this->partialpaymentHelper->getInstallmentPaymentConfirmationEmailTemplate(),
					$templateVars,
					$this->partialpaymentHelper->getSendInstallmentPaymentConfirmationEmail()
				);
			}
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	}
	
	#Installment Payment Failure Mail Sender Function
	public function sendInstallmentPaymentFailureMail($customerName,$customerEmailId, $incrementId, $partialPaymentId, $installmentAmount,$currencyCode)
	{
		try
		{
			if($this->partialpaymentHelper->getSendInstallmentPaymentFailureEmail())
			{
				$templateVars = array(
									'storeName' => $this->storeManager->getStore()->getName(),
									'logo_url' => $this->getLogoUrl(),
									'logo_alt' => $this->getLogoAlt(),
									'customer_name' => $customerName,
									'store_url'   => $this->storeManager->getStore()->getBaseUrl(),
									'brand_label' => $this->partialpaymentHelper->getPartialpaymentLabel(),
									'order_id'    => $incrementId,
									'installment_amount' => $this->priceHelper->currency($this->partialpaymentHelper->convertBaseToAnyCurrencyAmount($installmentAmount, $currencyCode)),
									'user_partial_payment' => $this->getUserPartialpaymentUrl($partialPaymentId)
								);
				$this->sendMail(
					$customerEmailId,
					$this->partialpaymentHelper->getInstallmentPaymentFailureEmailTemplate(),
					$templateVars,
					$this->partialpaymentHelper->getSendInstallmentPaymentFailureEmail()
				);
			}
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	}
	
	#Installment Over Due Mail Sender Function
	public function sendOverDueMail($customerName,$customerEmailId, $incrementId, $partialPaymentId, $installmentDueDate,$installmentAmount)
	{
		try
		{
			if($this->partialpaymentHelper->getSendInstallmentOverDueNoticeEmail())
			{
				$templateVars = array(
									'storeName' => $this->storeManager->getStore()->getName(),
									'logo_url' => $this->getLogoUrl(),
									'logo_alt' => $this->getLogoAlt(),
									'customer_name' => $customerName,
									'due_date' => $installmentDueDate,
									'store_url'   => $this->storeManager->getStore()->getBaseUrl(),
									'order_id'    => $incrementId,
									'installment_amount' => $installmentAmount,
									'user_partial_payment' => $this->getUserPartialpaymentUrl($partialPaymentId)
								);
				$this->sendMail(
					$customerEmailId,
					$this->partialpaymentHelper->getInstallmentOverDueNoticeEmailTemplate(),
					$templateVars,
					$this->partialpaymentHelper->getSendInstallmentOverDueNoticeEmail()
				);
			}
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	}
}
