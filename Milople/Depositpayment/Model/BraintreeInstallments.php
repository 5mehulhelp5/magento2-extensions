<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Partialpayment
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/
namespace Milople\Depositpayment\Model;


use Braintree\Configuration;
use Braintree\CreditCard;
use Braintree\PaymentMethodNonce;
use Braintree\Transaction;
use Braintree\ClientToken;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class BraintreeInstallments{
	
	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
		\Psr\Log\LoggerInterface $logger
	) {
		$this -> scopeConfig = $scopeConfig;
		$this->logger = $logger;
	}
	public function genrateClientToken(){
		Configuration::environment($this->scopeConfig->getValue('payment/braintree/environment'));
		Configuration::merchantId($this->scopeConfig->getValue('payment/braintree/merchant_id'));
		Configuration::publicKey($this->scopeConfig->getValue('payment/braintree/public_key'));
		Configuration::privateKey($this->scopeConfig->getValue('payment/braintree/private_key'));
		$token = ClientToken::generate();
		return $token;
	}
	public function InstallmentCreditCard($data){
		
		Configuration::environment($this->scopeConfig->getValue('payment/braintree/environment'));
		Configuration::merchantId($this->scopeConfig->getValue('payment/braintree/merchant_id'));
		Configuration::publicKey($this->scopeConfig->getValue('payment/braintree/public_key'));
		Configuration::privateKey($this->scopeConfig->getValue('payment/braintree/private_key'));
		//BrainTree payment process
		$result = Transaction::sale(array(
			'amount' => $data['amount'],
			'creditCard' => array(
			'number' => $data['cc_number'],			
			'expirationDate' => $data['expiration_date'],
			'cvv' => $data['cc_cid']
			)
		));
		$response = [];
		if ($result->success)
		{
			if($result->transaction->id)
			{
				$response["status"] = 1;
				$response["transaction"] = $result->transaction->id;
			}
		}
		else if ($result->transaction)
		{
			$response["status"] = 0;
		}
		else 
		{
			$response["status"] = 0;
		}
		return $response;
	}
	public function InstallmentPaymentNonceCapture($data){
		Configuration::environment($this->scopeConfig->getValue('payment/braintree/environment'));
		Configuration::merchantId($this->scopeConfig->getValue('payment/braintree/merchant_id'));
		Configuration::publicKey($this->scopeConfig->getValue('payment/braintree/public_key'));
		Configuration::privateKey($this->scopeConfig->getValue('payment/braintree/private_key'));
		$result = Transaction::sale([
			'amount' => $data['amount'],
			'paymentMethodNonce' => $data['payment_method_nonce'],
			'options' => [
				'submitForSettlement' => True
			]
		]);
		$response = [];
		if ($result->success)
		{
			if($result->transaction->id)
			{
				$response["status"] = 1;
				$response["transaction"] = $result->transaction->id;
			}
		}
		else if ($result->transaction)
		{
			$response["status"] = 0;
		}
		else 
		{
			$response["status"] = 0;
		}
		return $response;
	}
	public function InstallmentTokenCapture($data){
		Configuration::environment($this->scopeConfig->getValue('payment/braintree/environment'));
		Configuration::merchantId($this->scopeConfig->getValue('payment/braintree/merchant_id'));
		Configuration::publicKey($this->scopeConfig->getValue('payment/braintree/public_key'));
		Configuration::privateKey($this->scopeConfig->getValue('payment/braintree/private_key'));
		//BrainTree payment process
		$result = Transaction::sale(array(
			'amount' => $data['amount'],
			'paymentMethodToken'=>$data['token'],
			'options' => [
				'submitForSettlement' => True
			]
			)
		);
		$response = [];
		if ($result->success)
		{
			if($result->transaction->id)
			{
				$response["status"] = 1;
				$response["transaction"] = $result->transaction->id;
			}
		}
		else if ($result->transaction)
		{
			$response["status"] = 0;
		}
		else 
		{
			$response["status"] = 0;
		}
		return $response;
	}
}