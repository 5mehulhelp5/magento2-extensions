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
class Partialpayment extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $serialKey;
	protected $status;
	protected $analytics;
	protected $label;
	protected $downPaymentValue;
	protected $downPaymentCalculation;
	protected $customerGroups;
	protected $autocapture;
	protected $applyPartialpaymentTo;
	protected $minimumOrderAmount;
	protected $allowFullPayment;
	protected $captureInstallmentAutomatically;
	protected $shippingAndTaxCalculationOptions;
	protected $discountCalculationOptions;
	protected $paymentPlan;
	protected $numberOfDays;
	protected $emailSender;
	protected $emailCC;
	protected $helper;
	protected $calculationModel;
	protected $timeformat;
	protected $_storeManager;
	protected $customerSession;
	protected $logger;
	protected $_currency;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Milople\Depositpayment\Helper\Data $data_helper,
		\Milople\Depositpayment\Model\Config\Source\Customergroups $customerGroups,
		\Milople\Depositpayment\Model\CalculationFactory $calculationModelFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Directory\Model\Currency $currency,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeformat,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        parent::__construct($context);
		$this->_storeManager 	= $storeManager;
		$this->customerSession	= $customerSession;
		$this->customerGroups	= $customerGroups;
		$this->helper 			= $data_helper;
		$this->logger 			= $context->getLogger();
		$this->calculationModel = $calculationModelFactory;
		$this->timeformat 		= $timeformat;
		$this->_currency		= $currency;
    }

	#***** License section *****
	public function getSerialKey()
	{
		if (empty($this->serialKey))
		{
	       	$this->serialKey = $this->scopeConfig->getValue(
                'partialpayment/license/partialpayment_serialkey',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->serialKey;
	}

	public function getStatus()
	{
		if (empty($this->status))
		{
	       	$this->status = $this->scopeConfig->getValue(
                'partialpayment/license/partialpayment_status',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->status;
	}

	public function getEnableAnalyticsAndReports()
	{
		if (empty($this->analytics))
		{
	       	$this->analytics = $this->scopeConfig->getValue(
                'partialpayment/license/partialpayment_analytics',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->analytics;
	}


	#***** General section ******
	public function getPartialpaymentLabel()
	{
		if (empty($this->label))
		{
	      	$this->label = $this->scopeConfig->getValue(
                'partialpayment/general/partialpayment_brand_label',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->label;
	}
	public function getNumberOfInstallments()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/general/number_of_installments',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function autocapture()
	{
		if (empty($this->autocapture))
		{
	       	$this->autocapture = $this->scopeConfig->getValue(
                'partialpayment/general/capture_installment_automatically',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->autocapture;
	}
	public function getDownPaymentValue($product = NULL)
	{
		if($product!=NULL && $this->isSelectedProducts())// return product specific setting
		{
			if($product->getDownpayment() != '')
				return $product->getDownpayment();
		}
		//return global configuration setting
		return $this->scopeConfig->getValue(
				'partialpayment/payment_calculation_settings/down_payment',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
	}

	public function getCalculateDownpaymentOn($product = NULL)
	{
		if($product!=NULL && $this->isSelectedProducts())// return product specific setting
		{
			if($product->getDownPaymentCalculation()){
				return $product->getDownPaymentCalculation();
			}
		}
		//return global configuration setting
		return $this->scopeConfig->getValue(
			'partialpayment/payment_calculation_settings/calculate_downpayment_on',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	public function getCustomerGroups()
	{
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("getCustomerGroups() function is called",true));
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("getCustomerGroups() function is called",true));
		if (empty($this->applyPartialpaymentTo))
		{
	       	$this->applyPartialpaymentTo = $this->scopeConfig->getValue(
                'partialpayment/general/partialpayment_customergroups',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}

		return explode(',',$this->applyPartialpaymentTo);
	}

	public function getApplyPartialpaymentTo()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/general/apply_partialpayment_to',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	public function getMinimumOrderAmount()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/general/minimum_order_amount',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	public function isAllowFullPayment($isAllow=NULL)
	{
		if($this->isSelectedProducts() && $isAllow!='')// if product specific setting
		{
			if($isAllow==1)
				return true;
			else
				return false;
		}
		//return global configuration setting
		return $this->scopeConfig->getValue(
        	'partialpayment/general/allow_fullpayment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}

	public function isCaptureInstallmentAutomatically()
	{
		if (empty($this->captureInstallmentAutomatically))
		{
	       	$this->captureInstallmentAutomatically = $this->scopeConfig->getValue(
                'partialpayment/general/capture_installment_automatically',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->captureInstallmentAutomatically;
	}

	public function getShippingAndTaxCalculationOptionsForAllProducts()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/general/shipping_tax_and_surcharge_calculation_options_for_all_products',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	public function getDiscountCalculationOptionsForAllProducts()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/general/discount_calculation_options_for_all_products',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getShippingAndTaxCalculationOptionsForWholeCart()
	{
		return $this->shippingTaxAndSurchargeCalculationOptions = $this->scopeConfig->getValue(
			'partialpayment/general/shipping_tax_and_surcharge_calculation_options_for_wholecart',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	public function getDiscountCalculationOptionsForWholecart()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/general/discount_calculation_options_for_wholecart',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getTotalIinstallments ($product=NULL)//Number of Installments
	{	if($product == NULL){
		return $this->scopeConfig->getValue(
				'partialpayment/general/number_of_installments',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
		}
		if($product && $product->getApplyPartialPayment() && $this->isSelectedProducts())//if selected product
		{
			if($product->getNoOfInstallments())
			{
				return $product->getNoOfInstallments();// returns number of installment value from specific product only
			}
		}
		//returns number of installments value from global config
		return $this->scopeConfig->getValue(
                'partialpayment/general/number_of_installments',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function showCategoryImage()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/general/show_category_label',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getCategoryCornerImageUrl()
	{
		$image = $this->scopeConfig->getValue(
			'partialpayment/general/category_corner_image',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
		if($image && $this->showCategoryImage()){
			$media_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$image_url = $media_url."partialpayment/".$image;
			return $image_url;
		}else{
			return null;
		}

	}
	#***** Installment Fee settings *****
	public function installmentFeeStatus()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/payment_calculation_settings/installment_fee',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function installmentFeeLabel()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/payment_calculation_settings/installment_fee_label',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function installmentFeeType()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/payment_calculation_settings/installment_fee_type',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function installmentFeeValue()
	{
		return $this->scopeConfig->getValue(
			'partialpayment/payment_calculation_settings/installment_fee_value',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	#***** payment calculation settings *****
	public function getPartialpaymentType()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/payment_calculation_settings/partialpayment_type',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function isAllowFlexyPayment($product=NULL)
	{
		if($product && $product->getApplyPartialPayment() && $this->isSelectedProducts())//if selected product
		{
			if($product->getAllowFlexyPayment()==1)
			{
				return 1;// returns 1 if flexy payment is yes
			}
			else
			{
				return 0;// if flexy no from product tab
			}
		}
		//returns true if flexy payment is yes in global configuration
		return $this->scopeConfig->getValue(
                'partialpayment/payment_calculation_settings/partialpayment_type',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	public function getPaymentPlan()
	{
		if (empty($this->paymentPlan))
		{
	       	$this->paymentPlan = $this->scopeConfig->getValue(
                'partialpayment/payment_calculation_settings/payment_plan',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->paymentPlan;
	}

	public function getNumberOfDays()
	{
		if (empty($this->numberOfDays))
		{
	       	$this->numberOfDays = $this->scopeConfig->getValue(
                'partialpayment/payment_calculation_settings/number_of_days',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		}
		return $this->numberOfDays;
	}

	#***** Maximum Credit Limit Settings *****#
	public function getMaximumCreditLimit()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/partial_payment_credit_settings/maximum_credit_limit',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getCreditLimitSurpassMessage()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/partial_payment_credit_settings/credit_limit_surpass_message',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	#***** Emails and Notifications Settings *****#
	public function getEmailSender()
	{
		return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/email_sender',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	public function getEmailCC()
	{
		return array_filter(explode(',',
					   $this->scopeConfig->getValue
					   (
                			'partialpayment/emails_and_notifications_settings/email_cc_to',
                			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
            			)
				  ));
	}

	#Order Confirmation
	public function getSendPartiallyPaidOrdersConfirmationEmail()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/send_partially_paid_orders_confirmation_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getSendPartiallyPaidOrdersConfirmationTemplate()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/send_partially_paid_orders_confirmation_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	#Installment Payment Reminder Mail
	public function getSendInstallmentReminderEmail()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/send_installment_reminder_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getInstallmentReminderEmailTemplate()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/installment_reminder_email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	#Installment Payment Confirmation Mail
	public function getSendInstallmentPaymentConfirmationEmail()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/send_installment_payment_confirmation_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getInstallmentPaymentConfirmationEmailTemplate()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/installment_payment_confirmation_email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	#Installment Payment Failure Mail
	public function getSendInstallmentPaymentFailureEmail()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/send_installment_failure_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getInstallmentPaymentFailureEmailTemplate()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/installment_failure_email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}

	#Installment Payment Over Due Mail
	public function getSendInstallmentOverDueNoticeEmail()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/send_installment_over_due_notice_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getInstallmentOverDueNoticeEmailTemplate()
	{
			return $this->scopeConfig->getValue(
                'partialpayment/emails_and_notifications_settings/installment_over_due_notice_email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}


	#***** General Functions *****
	#functions related to configuration
	public function isAllProducts()
	{
		if($this->getApplyPartialpaymentTo()==1)
		{
			return true;
		}
		return false;
	}
	public function isSelectedProducts()
	{
		if($this->getApplyPartialpaymentTo()==0)
		{
			return true;
		}
		return false;
	}
	public function isWholeCart()
	{
		if($this->getApplyPartialpaymentTo()==2)
		{
			return true;
		}
		return false;
	}
	public function isAllowOnProducts($isApply=NULL)
	{
		if($this->isAllProducts() || ($this->isSelectedProducts() && $isApply))
		{
			return true;
		}
		return false;
	}
	public function isShippingAndTaxInPayingNow()
	{
		//return false if add Shipping, Tax and Surcharge add in remaining amount(amount to be paid later)
		if($this->isAllProducts() && !$this->isAllowFullPayment() && $this->getShippingAndTaxCalculationOptionsForAllProducts())//for all products
			return false;
		else if($this->isWholeCart() && $this->getShippingAndTaxCalculationOptionsForWholeCart())// for wholecart
			return false;
		return true;
	}
	public function isDiscountInPayingNow()
	{
		//return false if deduct Shipping, Tax and Surcharge add in remaining amount(amount to be paid later)
		if($this->isAllProducts() && !$this->isAllowFullPayment() && $this->getDiscountCalculationOptionsForAllProducts())
			return false;
		else if($this->isWholeCart() && $this->getDiscountCalculationOptionsForWholeCart())
			return false;
		return true;
	}
	public function getCustomerSession()
    {
        return $this->customerSession;
    }
	public function isAddToCartFail(){
		$isAddToCartFail = 0;
		if($this->getCustomerSession()->getAddCartFail()){

			$isAddToCartFail = $this->getCustomerSession()->getAddCartFail();
			$this->getCustomerSession()->unsAddCartFail();
		}
		return $isAddToCartFail;
	}
	public function isValidCustomer($customerGroupId = NULL)
	{
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('isValidCustomer functino is called');
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($customerGroupId,true));

		$customer_groups = $this->getCustomerGroups();
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($customer_groups,true));
		if($customerGroupId == NULL)
		{
			$_loggedIn = $this->customerSession->isLoggedIn();
			if($this->customerSession->getCustomerGroupId()) {
				$customerGroupId = $this->customerSession->getCustomerGroupId();
			}
			else if(!$_loggedIn)
			{
				$customerGroupId = 0;
			}
			else
			{
				$customerGroupId = $this->customerSession->getCustomerGroupId();
			}
		}
		if(in_array($customerGroupId,$customer_groups))
		{
			return true;
		}
		return false;

	}
	public function isAllowForRegisteredCustomerOnly()
	{
		$allGroups = $this->customerGroups->toOptionArray();
		$selectedGroups = $this->getCustomerGroups();
		if($selectedGroups[0]==0)//if guest is selected
			return false;
		foreach($allGroups as $group)
		{
			if($group['value'] != 0 && !in_array($group['value'],$selectedGroups))//if except guest any other is not selected then return false
			{
				return false;
			}
		}
		return true;//if except guest all customer group is selected in configuration
	}

	#currency conversion functions
	#convert any currency in currenct currency amount
	public function convertToCurrentCurrencyAmount($price,$currentCurrency ='')
	{
		$baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
		if($currentCurrency != '')
		{
			$currentCurrencyCode = $currentCurrency ;
		}
		else
		{
			$currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
		}
		if ($baseCurrencyCode != $currentCurrencyCode)
		{
			$currentCurrencyRate = $this->_storeManager->getStore()->getCurrentCurrencyRate();
			$price = $price / $currentCurrencyRate;
		}
		return $price;
	}
	public function convertBaseToAnyCurrencyAmount($price,$currentCurrency ='')
	{
		$baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
		if($currentCurrency != '')
		{
			$currentCurrencyCode = $currentCurrency ;
		}
		else
		{
			$currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
		}
		if ($baseCurrencyCode != $currentCurrencyCode)
		{
			$currentCurrencyRate = $this->_storeManager->getStore()->getCurrentCurrencyRate();
			$price = $price / $currentCurrencyRate;
		}
		return $price;
	}

	#set suuffix number
	public function addOrdinalNumberSuffix($num)
	{
		if (!in_array(($num % 100),array(11,12,13)))
		{
			switch ($num % 10)
			{
				// Handle 1st, 2nd, 3rd
				case 1:  return $num.'<sup>st</sup>';
				case 2:  return $num.'<sup>nd</sup>';
				case 3:  return $num.'<sup>rd</sup>';
			}
		}
		return $num.'<sup>th</sup>';
	}

	#common function to check partial payment should display or not
	public function canShowPartialPayment($product=null)
	{
		/* check extension status and validate customer and down payment */

		if($this->getStatus() && $this->helper->canRun() && $this->isValidCustomer())
		{

			if($product == null){// if product null means whole cart{

			   return true;
			 }
			else if($product != null && $this->isAllowOnProducts($product->getApplyPartialPayment()))
			{

				//$price = $product->getPriceInfo()->getPrice('final_price')->getValue();
				$price = $product->getFromPrice();

				if($price > $this->calculationModel->create()->getDownPaymentAmount($price, $product))
					return true;

				if($product->getTypeId()=='bundle')
				return true;
			}
		}
		return false;
	}
	#Returnns Last Installment value
	public function adjustLastInstallment($remaining,$installmentAmount,$noOfInstallments){
		$installmentAmount = number_format((float)$installmentAmount, 2, '.', '');
		if($noOfInstallments < 3){
			return $installmentAmount;
		}
		$diff = $remaining - ($installmentAmount * ($noOfInstallments-1));
		$lastInstallment = $installmentAmount + number_format((float)$diff, 2, '.', '');
		return number_format((float)$lastInstallment, 2, '.', '');
	}
	#Returns current currency code
	public function getCurrencyCode() {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
		return $currencyCode;
    }
	#Returns current currency symbol
	public function getCurrencySymbol() {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
		$currency = $this->_currency->load($currencyCode);
		return $currencySymbol = $currency->getCurrencySymbol();
    }
	#Return config value for given path.
	public function getValueByPath($path){
		return $this->scopeConfig->getValue(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	# date formating
	public function formatDate($date)
	{
		return $this->timeformat->formatDate(
                        $date,
                        \IntlDateFormatter::MEDIUM,
                        false
               );
	}
}
