<?php
/**
 * @author Azaleasoft Team
 * @copyright Copyright (c) 2017 Azaleasoft (http://www.azaleasoft.com)
 * @package Azaleasoft_Asaddressvalidation
 */
namespace Azaleasoft\Asaddressvalidation\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $configReader;
    private $soapClientFactory;
    private $storeManager;
    private $address;
    private $streetLines;
    private $region;
    private $country;
    private $urlBuilder;
    private $fedexServiceWsdl;

    const ASADDRESSVALIDATION_GENERAL_ENABLE = 'azaleasoft_asaddressvalidation/general/enable';
    const ASADDRESSVALIDATION_GENERAL_ORIGINAL_ADDRESS_CHECKOUT = 'azaleasoft_asaddressvalidation/general/original_address_checkout';
    const ASADDRESSVALIDATION_GENERAL_SERVICE_NAME = 'azaleasoft_asaddressvalidation/general/service_name';

    const ASADDRESSVALIDATION_UPS_MAXIMUM_LIST_SIZE = 'azaleasoft_asaddressvalidation/ups/maximum_list_size';
    const ASADDRESSVALIDATION_UPS_USERNAME = 'azaleasoft_asaddressvalidation/ups/username';
    const ASADDRESSVALIDATION_UPS_PASSWORD = 'azaleasoft_asaddressvalidation/ups/password';
    const ASADDRESSVALIDATION_UPS_ACCESS_KEY = 'azaleasoft_asaddressvalidation/ups/access_key';
    const ASADDRESSVALIDATION_UPS_URL_PRODUCTION = 'https://onlinetools.ups.com/rest/XAV';

    const ASADDRESSVALIDATION_USPS_USERNAME = 'azaleasoft_asaddressvalidation/usps/username';
    const ASADDRESSVALIDATION_USPS_PASSWORD = 'azaleasoft_asaddressvalidation/usps/password';
    const ASADDRESSVALIDATION_USPS_URL_PRODUCTION = 'http://production.shippingapis.com/ShippingAPI.dll';

    const ASADDRESSVALIDATION_FEDEX_KEY = 'azaleasoft_asaddressvalidation/fedex/key';
    const ASADDRESSVALIDATION_FEDEX_PASSWORD = 'azaleasoft_asaddressvalidation/fedex/password';
    const ASADDRESSVALIDATION_FEDEX_ACCOUNT_NUMBER = 'azaleasoft_asaddressvalidation/fedex/account_number';
    const ASADDRESSVALIDATION_FEDEX_METER_NUMBER = 'azaleasoft_asaddressvalidation/fedex/meter_number';
    const ASADDRESSVALIDATION_FEDEX_SANDBOX_MODE = 'azaleasoft_asaddressvalidation/fedex/sandbox_mode';
    const ASADDRESSVALIDATION_FEDEX_URL_PRODUCTION = 'https://ws.fedex.com:443/web-services';
    const ASADDRESSVALIDATION_FEDEX_URL_TEST = 'https://wsbeta.fedex.com:443/web-services';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Framework\Webapi\Soap\ClientFactory $soapClientFactory,
        \Magento\Customer\Helper\Address $address,
        \Magento\Directory\Model\Region $region,
        \Magento\Directory\Model\Country $country,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->configReader = $configReader;
        $this->soapClientFactory = $soapClientFactory;
        $this->storeManager = $storeManager;
        $this->address = $address;
        $this->streetLines = $this->address->getStreetLines();
        $this->region = $region;
        $this->country = $country;
        $this->urlBuilder = $context->getUrlBuilder();

        $wsdlBasePath = $this->configReader->getModuleDir('etc', 'Azaleasoft_Asaddressvalidation') . '/wsdl/';
        $this->fedexServiceWsdl = $wsdlBasePath . 'AddressValidationService_v4.wsdl';
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_GENERAL_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function allowOriginalAddressCheckout()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_GENERAL_ORIGINAL_ADDRESS_CHECKOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getServiceName()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_GENERAL_SERVICE_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUpsMaximumListSize()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_UPS_MAXIMUM_LIST_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUpsUsername()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_UPS_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUpsPassword()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_UPS_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUpsAccessKey()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_UPS_ACCESS_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUspsUsername()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_USPS_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getUspsPassword()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_USPS_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFedexKey()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_FEDEX_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFedexPassword()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_FEDEX_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFedexAccountNumber()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_FEDEX_ACCOUNT_NUMBER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFedexMeterNumber()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_FEDEX_METER_NUMBER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFedexSandboxMode()
    {
        return $this->scopeConfig->getValue(self::ASADDRESSVALIDATION_FEDEX_SANDBOX_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function sendRequest($params = array())
    {
        $result = [];
        $serviceName = $this->getServiceName();
        switch ($serviceName) {
            case 'ups':
                $response = $this->_sendUpsRequest($params);
                break;
            case 'usps':
                if ($params['country'] != 'US') {
                    return [];
                }
                $response = $this->_sendUspsRequest($params);
                break;
            case 'fedex':
                $response = $this->_sendFedexRequest($params);
                break;
        }

        if (empty($response)) {
          return $result;
        }

        if ($serviceName == 'fedex') {
            $result = $this->_parseResponseData($response);
        } else {
            if (!empty($response->getStatus()) && $response->getStatus() == 200) {
                $rs = $this->_parseResponseData($response->getBody());
                $result = $this->_parseResponseData($response->getBody());
            } else {
                $this->_logger->error($response->getBody());
            }
        }

        return $result;
    }

    protected function _sendUpsRequest($params = array())
    {
        $addressLine = '';
        for ($i = 0; $i < $this->streetLines; $i++) {
            $addressLine .= $params['street_'.$i].' ';
        }
        $address = [
            'AddressLine'           => trim($addressLine),
            'PoliticalDivision2'    => $params['city'],
            'PoliticalDivision1'    => $params['region'],
            'PostcodePrimaryLow'    => strlen($params['zip']) >= 5 ? substr($params['zip'], 0, 5) : '',
            'CountryCode'           => $params['country']
        ];

        $request = [
            'UPSSecurity' => [
                'UsernameToken' => [
                    'Username' => $this->getUpsUsername(),
                    'Password' => $this->getUpsPassword()
                ],
                'ServiceAccessToken' => [
                    'AccessLicenseNumber' => $this->getUpsAccessKey()
                ]
            ],
            'XAVRequest' => [
                'Request' => [
                    'RequestOption' => '1'
                ],
                'MaximumListSize' => self::ASADDRESSVALIDATION_UPS_MAXIMUM_LIST_SIZE,
                'AddressKeyFormat' => $address
            ]
        ];

        $client = new \Zend_Http_Client(self::ASADDRESSVALIDATION_UPS_URL_PRODUCTION);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($request), 'application/json');

        try{
            $response = $client->request();
        } catch(\Exception $e) {
            $this->_logger->error($e->getMessage());
            $response = '';
        }

        return $response;
    }

    protected function _sendUspsRequest($params = array())
    {
        $username = $this->getUspsUsername();
        if (empty($username)) {
          return null;
        }
        $xmlData = "<AddressValidateRequest USERID='$username'>".
            "<Address>".
            "<Address1>".(isset($params['street_1']) ? trim($params['street_1']) : '')."</Address1>".
            "<Address2>".(isset($params['street_0']) ? trim($params['street_0']) : '')."</Address2>".
            "<City>".$params['city']."</City>".
            "<State>".$params['region']."</State>".
            "<Zip5>".(strlen($params['zip']) >= 5 ? substr($params['zip'], 0, 5) : '')."</Zip5>".
            "<Zip4></Zip4>".
            "</Address>".
            "</AddressValidateRequest>";

        $client = new \Zend_Http_Client(self::ASADDRESSVALIDATION_USPS_URL_PRODUCTION);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setParameterPost('API', 'Verify');
        $client->setParameterPost('XML', $xmlData);

        try{
            $response = $client->request('POST');
        } catch(\Exception $e) {
            $this->_logger->error($e->getMessage());
            $response = '';
        }

        return $response;
    }

    protected function _sendFedexRequest($params = array())
    {
        $key = $this->getFedexKey();
        $password = $this->getFedexPassword();
        $accountNumber = $this->getFedexAccountNumber();
        $meterNumber = $this->getFedexMeterNumber();
        if (empty($key) || empty($password) || empty($accountNumber) || empty($meterNumber)) {
            return null;
        }

        $soapClient = $this->soapClientFactory->create($this->fedexServiceWsdl, ['trace' => false]);
        if ($this->getFedexSandboxMode()) {
            $location = self::ASADDRESSVALIDATION_FEDEX_URL_TEST . '/addressvalidation';
        } else {
            $location = self::ASADDRESSVALIDATION_FEDEX_URL_PRODUCTION . '/addressvalidation';
        }
        $soapClient->__setLocation($location);
        $request = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $key, 'Password' => $password],
            ],
            'ClientDetail' => ['AccountNumber' => $accountNumber, 'MeterNumber' => $meterNumber],
            'Version' => ['ServiceId' => 'aval', 'Major' => '4', 'Intermediate' => '0', 'Minor' => '0'],
            'AddressesToValidate' => [
                'Address' => [
                    'StreetLines' => $params['street_0'] . (isset($params['street_1']) ? ' '.$params['street_1'] : ''),
                    'City' => $params['city'],
                    'StateOrProvinceCode' => $params['region_code'],
                    'PostalCode' => $params['zip'],
                    'CountryCode' => $params['country'],
                    'Residential' => true
                ]
            ]
        ];

        try {
            $response = $soapClient->addressValidation($request);
//            $this->_logger->info(json_encode($response));
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            $response = '';
        }

        return $response;
    }

    public function getFullAddress($params)
    {
        $fulladdress = '';
        $city       = isset($params['city']) ? $params['city'] : '';
        $region     = isset($params['region']) ? $params['region'] : '';
        $zip        = isset($params['zip']) ? $params['zip'] : '';
        $country    = isset($params['country']) ? $params['country'] : '';
        for ($i = 0; $i < $this->streetLines; $i++) {
            ${'street_'.$i} = isset($params['street_'.$i]) ? $params['street_'.$i] : '';
            if (!empty(${'street_'.$i})) {
                $fulladdress .= (empty($fulladdress) ? '' : ' ').${'street_'.$i};
            }
        }
        if (!empty($city)) {
            $fulladdress .= (empty($fulladdress) ? '' : ', ').$city;
        }
        if (!empty($region)) {
            $fulladdress .= (empty($region) ? '': ' ').$region;
        }
        if (!empty($zip)) {
            $fulladdress .= (empty($region) ? '' : ' ').$zip;
        }
        if (!empty($country)) {
            $fulladdress .= (empty($fulladdress) ? '' : ', ').$this->country->loadByCode($country)->getName();
        }

        return $fulladdress;
    }

    public function getAccountInitData()
    {
        $data = [
            'urlAddressValidation' => $this->urlBuilder->getUrl('asaddressvalidation/ajax/suggest', ['_secure' => true]),
            'allowOriginalAddress' => $this->allowOriginalAddressCheckout(),
            'streetLines'    => $this->streetLines
        ];

        return json_encode($data);
    }

    public function getCheckoutInitData()
    {
        $data = [
            'urlAddressValidation' => $this->urlBuilder->getUrl('asaddressvalidation/ajax/suggest', ['_secure' => true]),
            'allowOriginalAddress' => $this->allowOriginalAddressCheckout(),
            'streetLines'    => $this->streetLines
        ];

        return json_encode($data);
    }

    public function getMultishippingInitData()
    {
        $data = [
            'urlAddressValidation' => $this->urlBuilder->getUrl('asaddressvalidation/ajax/suggest', ['_secure' => true]),
            'allowOriginalAddress' => $this->allowOriginalAddressCheckout(),
            'streetLines'    => $this->streetLines
        ];

        return json_encode($data);
    }

    public function getRegionData($params) {
        $result = [];

        if (isset($params['region_id']) && !empty($params['region_id'])) {
            $region = $this->region->load($params['region_id']);
        } elseif (isset($params['region_code']) && isset($params['country'])) {
            $region = $this->region->loadByCode($params['region_code'], $params['country']);
        } else {
            $region = null;
        }

        if (!empty($region)) {
            $result['region_id']    = $region->getId();
            $result['region_code']  = $region->getCode();
            $result['region']       = $region->getName();
        }

        return $result;
    }

    protected function _parseResponseData($response)
    {
        switch ($this->getServiceName()) {
            case 'ups':
                return $this->_parseResponseDataFromUps($response);

            case 'usps':
                return $this->_parseResponseDataFromUsps($response);

            case 'fedex':
                return $this->_parseResponseDataFromFedex($response);
        }
    }

    protected function _parseResponseDataFromUps($response)
    {
        $result = [];

        $data = json_decode($response, true);
        if (!empty($data['Fault'])) {
            return $result;
        }

        $status = $data['XAVResponse']['Response']['ResponseStatus']['Code'];
        if ($status == '1') {
            if (isset($data['XAVResponse']['NoCandidatesIndicator'])) {
                return $result;
            }

            $candidate = $data['XAVResponse']['Candidate'];
            if (count($candidate) > 1) {
                $maximumListSize = $this->getUpsMaximumListSize();
                $index = 0;
                foreach ($candidate as $row) {
                    $address = $row['AddressKeyFormat'];
                    if ($this->streetLines >= 2) {
                        $result[] = [
                            'street_0'   => is_array($address['AddressLine']) ? $address['AddressLine'][0] : $address['AddressLine'],
                            'street_1'   => is_array($address['AddressLine']) ? $address['AddressLine'][1] : '',
                            'city'       => $address['PoliticalDivision2'],
                            'region_code'     => $address['PoliticalDivision1'],
                            'zip'        => $address['PostcodePrimaryLow'].'-'.$address['PostcodeExtendedLow'],
                            'country'    => $address['CountryCode']
                        ];
                    } else {
                        $result[] = [
                            'street_0'   => is_array($address['AddressLine']) ? implode(' ', $address['AddressLine']) : $address['AddressLine'],
                            'city'      => $address['PoliticalDivision2'],
                            'region_code'    => $address['PoliticalDivision1'],
                            'zip'       => $address['PostcodePrimaryLow'].'-'.$address['PostcodeExtendedLow'],
                            'country'   => $address['CountryCode']
                        ];
                    }
                    $index += 1;
                    if ($maximumListSize <= $index) {
                        break;
                    }
                }
            } else {
                $address = $candidate['AddressKeyFormat'];
                if ($this->streetLines >= 2) {
                    $result[] = [
                        'street_0'   => is_array($address['AddressLine']) ? $address['AddressLine'][0] : $address['AddressLine'],
                        'street_1'   => is_array($address['AddressLine']) ? $address['AddressLine'][1] : '',
                        'city'       => $address['PoliticalDivision2'],
                        'region_code'     => $address['PoliticalDivision1'],
                        'zip'        => $address['PostcodePrimaryLow'].'-'.$address['PostcodeExtendedLow'],
                        'country'    => $address['CountryCode']
                    ];
                } else {
                    $result[] = [
                        'street_0'   => is_array($address['AddressLine']) ? implode(' ', $address['AddressLine']) : $address['AddressLine'],
                        'city'      => $address['PoliticalDivision2'],
                        'region_code'    => $address['PoliticalDivision1'],
                        'zip'       => $address['PostcodePrimaryLow'].'-'.$address['PostcodeExtendedLow'],
                        'country'   => $address['CountryCode']
                    ];
                }
            }
        }

        return $result;
    }

    protected function _parseResponseDataFromUsps($response)
    {
        $result = [];
        $xmlObject = simplexml_load_string($response);
        $xmlArray = json_decode(json_encode((array)$xmlObject), True);
        if (isset($xmlArray['Description'])) {
          $result[] = [
            'error' => $xmlArray['Description']
          ];

          return $result;
        }

        $address = $xmlArray['Address'];
        // Lookup Zip Code
        if (isset($address['Error'])) {
            $result[] = [
                'error' => isset($address['Error']['Description']) ? $address['Error']['Description'] : 'Invalid Address'
            ];

            return $result;
        }

        if ($this->streetLines >= 2) {
            $result[] = [
                'street_0'  => isset($address['Address2']) ? $address['Address2'] : '',
                'street_1'  => isset($address['Address1']) ? $address['Address1'] : '',
                'city'      => isset($address['City']) ? $address['City'] : '',
                'region_code'   => isset($address['State']) ? $address['State'] : '',
                'zip'       => (isset($address['Zip5']) ? $address['Zip5'] : '').(isset($address['Zip4']) ? '-'.$address['Zip4'] : ''),
                'country'   => 'US',
                'message'   => isset($address['ReturnText']) ? $address['ReturnText'] : ''
            ];
        } else {
            $result[] = [
                'street_0'  => (isset($adderss['Address2']) ? $address['Address2'] : '').(isset($adderss['Address1']) ? $address['Address1'] : ''),
                'city'      => isset($address['City']) ? $address['City'] : '',
                'region_code'   => isset($address['State']) ? $address['State'] : '',
                'zip'       => (isset($address['Zip5']) ? $address['Zip5'] : '').(isset($address['Zip4']) ? '-'.$address['Zip4'] : ''),
                'country'   => 'US',
                'message'   => isset($address['ReturnText']) ? $address['ReturnText'] : ''
            ];
        }

        return $result;
    }

    protected function _parseResponseDataFromFedex($response)
    {
        $result = [];

        $data = json_decode(json_encode($response), true);
        if (isset($data['HighestSeverity']) && $data['HighestSeverity'] == 'SUCCESS') {
            $code = $data['Notifications']['Code'];
            if ($code == '0') {
                $addressResults = $data['AddressResults'];
                if (isset($addressResults['Classification']) && $addressResults['Classification'] != 'UNKNOWN') {
                    $effectiveAddress = $addressResults['EffectiveAddress'];
                    $result[] = [
                        'street_0' => is_array($effectiveAddress['StreetLines']) ? $effectiveAddress['StreetLines'][0] : $effectiveAddress['StreetLines'],
                        'street_1' => is_array($effectiveAddress['StreetLines']) ? $effectiveAddress['StreetLines'][1] : '',
                        'city' => $effectiveAddress['City'],
                        'region_code' => $effectiveAddress['StateOrProvinceCode'],
                        'zip' => $effectiveAddress['PostalCode'],
                        'country' => $effectiveAddress['CountryCode']
                    ];
                }
            }
        }

        return $result;
    }
}
