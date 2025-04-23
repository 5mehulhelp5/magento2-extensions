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
namespace Milople\Depositpayment\Model\Api;

use Magento\Payment\Model\Cart;
use Magento\Payment\Model\Method\Logger;

class Nvp extends \Magento\Paypal\Model\Api\Nvp
{
    protected $_urlInterface;

	/**
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Psr\Log\LoggerInterface $logger
     * @param Logger $customLogger
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param ProcessableExceptionFactory $processableExceptionFactory
     * @param \Magento\Framework\Exception\LocalizedExceptionFactory $frameworkExceptionFactory
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Psr\Log\LoggerInterface $logger,
        Logger $customLogger,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Paypal\Model\Api\ProcessableExceptionFactory $processableExceptionFactory,
        \Magento\Framework\Exception\LocalizedExceptionFactory $frameworkExceptionFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
		\Milople\Depositpayment\Model\CalculationFactory $calculationModelFactory,
		\Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($customerAddress, $logger, $customLogger, $localeResolver, $regionFactory,$countryFactory,$processableExceptionFactory,$frameworkExceptionFactory,$curlFactory, $data);
        $this->_countryFactory = $countryFactory;
        $this->_processableExceptionFactory = $processableExceptionFactory;
        $this->_frameworkExceptionFactory = $frameworkExceptionFactory;
        $this->_curlFactory = $curlFactory;
		$this->calculationModel = $calculationModelFactory->create();
		$this->_urlInterface = $urlInterface;

    }
	/**
     * SetExpressCheckout call
     *
     * TODO: put together style and giropay settings
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     */
    public function callSetExpressCheckout()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
        $request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
        $this->_exportLineItems($request);

        // import/suppress shipping address, if any
        $options = $this->getShippingOptions();
        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        } elseif ($options && count($options) <= 10) {
            // doesn't support more than 10 shipping options
            $request['CALLBACK'] = $this->getShippingOptionsCallbackUrl();
            $request['CALLBACKTIMEOUT'] = 6;
            // max value
            $request['MAXAMT'] = $request['AMT'] + 999.00;
            // it is impossible to calculate max amount
            $this->_exportShippingOptions($request);
        }
		// Is Partially Paid Order
		if($this->calculationModel->getPayingNow() > 0){
			$request['AMT'] = number_format((float)$this->calculationModel->getPayingNow(), 2, '.', '');
			$request['ITEMAMT'] = number_format((float)$this->calculationModel->getPayingNow(), 2, '.', '');
			$request['TAXAMT']= 0 ;
			$request['SHIPPINGAMT'] = 0;
		}
        $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }
	/**
     * DoExpressCheckout call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     */
    public function callDoExpressCheckoutPayment()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);

        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        }
		if($this->calculationModel->getPayingNow() > 0)
		{
			$request['AMT'] = number_format((float)$this->calculationModel->getPayingNow(), 2, '.', '');
			$request['ITEMAMT'] = number_format((float)$this->calculationModel->getPayingNow(), 2, '.', '');
			$request['TAXAMT']= 0 ;
			$request['SHIPPINGAMT'] = 0;
		}
        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }
	public function callDoCapture()
    {
        $this->setCompleteType($this->_getCaptureCompleteType());
        $request = $this->_exportToRequest($this->_doCaptureRequest);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($request['INVNUM']);
		$installmentModel = $objectManager->create('Milople\Depositpayment\Model\PartialpaymentInstallments');
		$installments = $installmentModel->getInstallmentsByOrderId($order->getId());
		$request['AMT'] = $installments->getFirstItem()->getInstallmentAmount();

        $response = $this->call(self::DO_CAPTURE, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doCaptureResponse, $response);
    }
	public function callInstallmentSetExpressCheckout($order,$payment)
    {
		try {
			$this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
			$this->_config->setMethod("paypal_express");
			$solutionType = $this->_config->getMerchantCountry() == 'DE'
            ? \Magento\Paypal\Model\Config::EC_SOLUTION_TYPE_MARK
            : $this->_config->getValue('solutionType');

			$this->_api = $this->setConfigObject($this->_config);
			$this->_api->setAmount($payment->getAmount())
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($payment->getIncrementId())
            ->setReturnUrl($this->_urlInterface->getUrl('depositpayment/paypal/return'))
            ->setCancelUrl($this->_urlInterface->getUrl('depositpayment/paypal/cancel'))
            ->setSolutionType($solutionType)
            ->setPaymentAction($this->_config->getValue('paymentAction'))
			->setBillingAddress($order->getBillingAddress())
			->setAddress($order->getShippingAddress())
            ->setBillingAddress($order->getBillingAddress());
			$this->_api->addData(
                [
                    'giropay_cancel_url' => $this->_urlInterface->getUrl('depositpayment/paypal/cancel'),
                    'giropay_success_url' => $this->_urlInterface->getUrl('depositpayment/paypal/success'),
                    'giropay_bank_txn_pending_url' => $this->_urlInterface->getUrl('depositpayment/paypal/success'),
                ]
            );
			$this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
			$request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
			$this->_exportLineItems($request);
			$request['ITEMAMT'] = $payment->getAmount();
			$request['TAXAMT']= 0 ;
			$request['SHIPPINGAMT'] = 0;
			$request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
			$response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
			return $response;
        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            throw $e;
        }
	}
	public function callInstallmentGetExpressCheckout($token){
		$this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
		$this->_config->setMethod("paypal_express");
		$this->_api = $this->setConfigObject($this->_config);
		$this->_api->setToken($token);
		$this->_prepareExpressCheckoutCallRequest($this->_getExpressCheckoutDetailsRequest);
        $request = $this->_exportToRequest($this->_getExpressCheckoutDetailsRequest);
		\Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug("********* Get Call Start ********");
        $response = $this->call(self::GET_EXPRESS_CHECKOUT_DETAILS, $request);
		return $response;
	}
	public function callInstallmentDoExpressCheckoutPayment($token,$payerID,$invNum,$amount,$order){
		$this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
		$this->_config->setMethod("paypal_express");
		$this->_api = $this->setConfigObject($this->_config);
		$this->_api->setAmount($amount)
		->setCurrencyCode($order->getBaseCurrencyCode())
		->setInvNum($invNum)
		->setPaymentAction($this->_config->getValue('paymentAction'))
		->setNotifyUrl($this->_urlInterface->getUrl('depositpayment/paypal/ipn'))
		->setBillingAddress($order->getBillingAddress())
		->setAddress($order->getShippingAddress())
		->setBillingAddress($order->getBillingAddress());
		$this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);
		$request['TOKEN'] = $token;
		$request['PAYERID'] = $payerID;
		$request['ITEMAMT'] = $amount;
		$request['TAXAMT']= 0 ;
		$request['SHIPPINGAMT'] = 0;
		$request = $this->_importAddresses($request);
		$request['ADDROVERRIDE'] = 1;
		$response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
		return $response;
	}
}
