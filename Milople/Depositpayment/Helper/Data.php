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
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $scopeConfig;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Directory\Model\Currency $currency
	) {

		$this -> scopeConfig = $scopeConfig;
		$this -> storeManager = $storeManager;
		$this -> currency = $currency;
	}
	public function getCurrencySymbol()
	{
		return $this->currency->getCurrencySymbol();
	}
	public function getCurrencyCode()
	{
		return $this->currency->getCurrencyCode();
	}
	public function getDomain() {
		$domain = $this->getDomainName($this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB));
		$temp = explode('.', $domain);
		$exceptions = array('co.uk', 'com.au', 'com.hk', 'co.nz', 'co.in', 'com.sg');
		$count = count($temp);
		if ($count === 1) {
			return $domain;
		}
		$last = $temp[($count - 2)] . '.' . $temp[($count - 1)];
		if (in_array($last, $exceptions)) {
			$new_domain = $temp[($count - 3)] . '.' . $temp[($count - 2)] . '.' . $temp[($count - 1)];
		} else {
			$new_domain = $temp[($count - 2)] . '.' . $temp[($count - 1)];
		}

		return $new_domain;
	}
	function getDomainName($url)
	{
        $custom_domain = preg_replace('#^https?://#', '', $url);
        $matchFound = preg_match('@^(?:http://)?([^/]+)@i', $custom_domain,$matches);
        if($matchFound)
        {
            $custom_domain = $matches[1];
        }
        $cmpstr = substr($custom_domain,0, 4);
        if($cmpstr == "www.")
        {
            $custom_domain = str_replace('www.',"",$custom_domain);
        }
        return strtolower($custom_domain);
    }
	public function checkEntry($domain, $serial) {
        return true;
		$key = sha1(base64_decode('TTJQYXJ0aWFsUGF5bWVudFBybw=='));
		if (sha1($key . $domain) == $serial) {
			return true;
		}
		return false;
	}

	public function canRun($temp = '') {

		if ($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1") {
			return true;
		}

		if ($temp == '') {
			$temp = $this -> scopeConfig -> getValue('partialpayment/license/partialpayment_serialkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		}

		$url = $this -> storeManager -> getStore() -> getBaseUrl();
		$parsedUrl = parse_url($url);
		$host = explode('.', $parsedUrl['host']);
		$subdomains = array_slice($host, 0, count($host) - 2);
		if (sizeof($subdomains) && ($subdomains[0] == 'test' || $subdomains[0] == 'demo' || $subdomains[0] == 'dev')) {
			return true;
		}
		$original = $this -> checkEntry($_SERVER['SERVER_NAME'], $temp);
		$wildcard = $this -> checkEntry($this -> getDomain(), $temp);
		if (!$original && !$wildcard) {
			return false;
		}
		return true;
	}

	public function getAdminMessage() {
		return base64_decode('PGRpdj5MaWNlbnNlIG9mIDxiIHN0eWxlPSJmb250LXdlaWdodDogYm9sZGVyICFpbXBvcnRhbnQ7Ij5NaWxvcGxlIERlcG9zaXQgUGF5bWVudDwvYj4gZXh0ZW5zaW9uIGhhcyBiZWVuIHZpb2xhdGVkLiBUbyBnZXQgc2VyaWFsIGtleSBwbGVhc2UgY29udGFjdCB1cyBvbiA8YiBzdHlsZT0iZm9udC13ZWlnaHQ6IGJvbGRlciAhaW1wb3J0YW50OyI+aHR0cHM6Ly93d3cubWlsb3BsZS5jb20vbWFnZW50by1leHRlbnNpb25zL2NvbnRhY3RzLzwvYj48L2Rpdj4=');
	}
}
