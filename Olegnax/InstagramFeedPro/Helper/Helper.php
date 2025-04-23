<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Helper;

use Exception;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Olegnax\Core\Helper\Helper as CoreHelper;
use Olegnax\InstagramFeedPro\Model\Api;
use Olegnax\InstagramFeedPro\Model\ClientOx as Client;
use Olegnax\InstagramFeedPro\Model\Encryption;
use Olegnax\InstagramFeedPro\Model\LicenseRequest;
use stdClass;

class Helper extends CoreHelper
{
    const CONFIG_MODULE = 'olegnax_instagram_pro';
    const XML_PATH_ENABLE = 'general/enabled';
    const XML_PATH_TOKEN = 'oauth/token';
    const PRODUCT_CODE = 'ox-inst-pro-m2';
    const STORE_CODE = '89MBca8Id0G8P61';
    const OPT_PREFIX = 'olegnax_instagram_license/general/';

    private $_get;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getModuleConfig(static::XML_PATH_ENABLE);
    }

    /**
     * @param bool $update
     * @param int $storeId
     * @return string
     */
    public function generateToken($storeId = 0, $update = false)
    {
        $token = $this->getModuleConfig(static::XML_PATH_TOKEN, $storeId);
        if (empty($token) || $update) {
            $token = md5('' . rand(1, PHP_INT_MAX));
            $this->setModuleConfig(
                static::XML_PATH_TOKEN,
                $token,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                0
            );
        }

        return $token;
    }

    /**
     * @param $license_key
     * @return object|stdClass
     * @throws Exception
     */
    public function activate($license_key)
    {
        if (empty($license_key)) {
            throw new Exception(__('License Key can not be empty.')->getText());
        }
        $store_code = static::STORE_CODE;
        $sku = static::PRODUCT_CODE;
        $frequency = LicenseRequest::DAILY_FREQUENCY;
        $domain = parse_url($this->getSystemDefaultValue('web/unsecure/base_url'), PHP_URL_HOST);
        $domain = preg_replace('#^www\.#i', '', $domain);

        $response = Api::activate(
            Client::instance(),
            function () use (&$store_code, &$sku, &$license_key, &$frequency, &$domain) {
                return LicenseRequest::create(
                    'https://olegnax.com/wp-admin/admin-ajax.php',
                    $store_code,
                    $sku,
                    $license_key,
                    $domain,
                    $frequency
                );
            },
            [&$this, 'encrypt_save']
        );

        return $response;
    }

    /**
     * @return bool|object|stdClass
     * @throws Exception
     */
    public function deactivate()
    {
        $license = $this->load_decrypt();
        if ($license === false) {
            return false;
        }
        $domain = parse_url($this->getSystemDefaultValue('web/unsecure/base_url'), PHP_URL_HOST);
        $domain = preg_replace('#^www\.#i', '', $domain);
        // Validate
        $response = Api::deactivate(
            Client::instance(),
            function () use (&$license) {
                return new LicenseRequest($license);
            },
            [&$this, 'encrypt_save'],
            $domain
        );
        if ($response && $response->error === true) {
            $this->encrypt_save('');
            // Force deactivation
            $response->error = false;
            $response->message = __('Deactivated.');
        }

        return $response;
    }

    /**
     * @return false|string
     */
    protected function load_decrypt()
    {
        // Load
        $license = $this->getSystemDefaultValue(Helper::OPT_PREFIX . 'license');
        // Decrypt
        if (!empty($license)) {
            $store_code = static::STORE_CODE;
            return Encryption::decode(
                $license,
                $store_code
            );
        } else {
            $license = false;
        }

        return $license;
    }

    /**
     * @param $license
     */
    public function encrypt_save($license)
    {
        // Check for downloadbles and updates
        if (!empty($license)) {
            $new = json_decode((string)$license);
        } else {
            $new = null;
        }
        $license_key = $this->load_decrypt();
        $old = $license_key !== false ? json_decode((string)$license_key) : $license_key;
        $store_code = static::STORE_CODE;
        // Save license string
        $this->configFactory()->saveConfig(
            Helper::OPT_PREFIX . 'license',
            is_string($license) ? Encryption::encode($license, $store_code) : ''
        );
        $this->configFactory()->saveConfig(
            Helper::OPT_PREFIX . 'avaible_update',
            ($old !== false
                && isset($new->data->downloadable)
                && isset($old->data->downloadable)
                && $new->data->downloadable->name !== $old->data->downloadable->name
            ) ? 1 : 0
        );
        $this->clearCache();
    }

    /**
     * @return ConfigInterface
     */
    public function configFactory()
    {
        return $this->_loadObject(ConfigInterface::class);
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function get()
    {
        if (is_null($this->_get)){
            $license_key = $this->load_decrypt();
            $this->_get = $license_key !== false && $this->validate() ? json_decode($license_key) : false;
        }

        return $this->_get;
    }

    /**
     * @param bool $force
     * @return bool
     * @throws Exception
     */
    public function validate($force = false)
    {
        return true;
        $license = $this->load_decrypt();
        if ($license === false) {
            return false;
        }
        // Prepare connection retries
        $allow_retry = null;
        $retry_attempts = 0;
        $retry_frequency = null;
        $domain = parse_url($this->getSystemDefaultValue('web/unsecure/base_url'), PHP_URL_HOST);
        $domain = preg_replace('#^www\.#i', '', $domain);

        // Validate
        return Api::validate(
            Client::instance(),
            function () use (&$license) {
                return new LicenseRequest($license);
            },
            [&$this, 'encrypt_save'],
            $domain,
            $force, // Force
            $allow_retry === null ? true : $allow_retry,
            $retry_attempts ? $retry_attempts : 2,
            $retry_frequency ? $retry_frequency : '+4 hour'
        );
    }

    /**
     * @return bool|object|stdClass|null
     * @throws Exception
     */
    public function check()
    {
        $license = $this->load_decrypt();
        if ($license === false) {
            return false;
        }
        $domain = parse_url($this->getSystemDefaultValue('web/unsecure/base_url'), PHP_URL_HOST);
        $domain = preg_replace('#^www\.#i', '', $domain);
        $allow_retry = null;
        $retry_attempts = 0;
        $retry_frequency = null;

        // Validate and return response
        return Api::check(
            Client::instance(),
            function () use (&$license) {
                return new LicenseRequest($license);
            },
            [&$this, 'encrypt_save'],
            $domain,
            $allow_retry === null ? true : $allow_retry,
            $retry_attempts ? $retry_attempts : 2,
            $retry_frequency ? $retry_frequency : '+1 hour'
        );
    }
}
