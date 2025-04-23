<?php /** @noinspection Annotator */

/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Model;

use Closure;
use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Olegnax\InstagramFeedPro\Helper\Helper;
use stdClass;

/**
 * License Keys's API wrapper.
 */
class Api
{
    const MODULE_PATH = 'Olegnax_InstagramFeedPro';

    /**
     * Activates a license key.
     * Returns call response.
     *
     * @param ClientOx $client Client to use for api calls.
     * @param Closure $getRequest Callable that returns a LicenseRequest.
     * @param $setRequest Callable that sets a LicenseRequest casted as string.
     *
     * @return object|stdClass
     * @throws Exception when LicenseRequest is not present.
     *
     * @since 1.0.0
     *
     */
    public static function activate(ClientOx $client, Closure $getRequest, $setRequest)
    {
        // Prepare
        $license = $getRequest();
        if (!is_a($license, LicenseRequest::class)) {
            throw new Exception(__('Closure must return an object instance of LicenseRequest.')->getText());
        }
        // Call
        if (!array_key_exists('domain', $license->request) || empty($license->request['domain'])) {
            $license->request['domain'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown';
        }
        $license->request['domain'] = preg_replace('/^(www|dev)\./i', '', $license->request['domain']);
        $license->request['meta'] = static::getMeta();
        $license->request['meta']['activate_user_ip'] = $license->request['meta']['user_ip'];
        unset($license->request['meta']['user_ip']);
        static::log(1000);
        $response = $client->call('license_key_activate', $license);
        if ($response && isset($response->error)
            && $response->error === false
        ) {
            if (isset($response->notices)) {
                $license->notices = (array)$response->notices;

            }
            $license->data = (array)$response->data;
            $license->touch();
            call_user_func($setRequest, (string)$license);
            static::log(2000);
        } elseif ($response) {
            static::log(4000, [(array)$response->errors]);
        } else {
            static::log(3000);
        }

        return $response;
    }

    /**
     * @return array
     * @throws FileSystemException
     * @throws ValidatorException
     */
    private static function getMeta()
    {
        return [
            'user_ip' => static::getClientIp(),
            'magento_version' => static::getMagentoVersion(),
            'module_version' => static::getModuleVersion(static::MODULE_PATH),
        ];
    }

    /**
     * @return string
     */
    public static function getClientIp()
    {
        return isset($_SERVER['HTTP_CLIENT_IP'])
            ? $_SERVER['HTTP_CLIENT_IP']
            : (
            isset($_SERVER['REMOTE_ADDR'])
                ? $_SERVER['REMOTE_ADDR']
                : (
            isset($_SERVER['REMOTE_HOST'])
                ? $_SERVER['REMOTE_HOST']
                : 'UNKNOWN'
            )
            );
    }

    /**
     * @return string
     */
    private static function getMagentoVersion()
    {
        return ObjectManager::getInstance()->get(ProductMetadataInterface::class)->getVersion();
    }

    /**
     * @param string $name
     *
     * @return string
     * @throws FileSystemException
     * @throws ValidatorException
     */
    private static function getModuleVersion($name)
    {
        /** @var ComponentRegistrarInterface $componentRegistrar */
        $componentRegistrar = ObjectManager::getInstance()->get(ComponentRegistrarInterface::class);
        $path = $componentRegistrar->getPath(ComponentRegistrar::MODULE, $name);
        if ($path) {
            /** @var ReadFactory $readFactory */
            $readFactory = ObjectManager::getInstance()->get(ReadFactory::class);
            $dirReader = $readFactory->create($path);
            if ($dirReader->isExist('composer.json')) {
                $data = (string)$dirReader->readFile('composer.json');
                $data = json_decode($data, true);
                if (isset($data['version'])) {
                    return $data['version'];
                }
            }
        }
        return '';
    }

    protected static function log($message, array $context = [])
    {
        $level = (int)(floor($message / 1000) * 100);
        static::_log($level, $message, $context);
    }

    protected static function _log($level, $message, array $context = [])
    {
        ObjectManager::getInstance()
            ->get(Helper::class)
            ->log($level, 'InstagramFeedPro', $message, $context);
    }

    /**
     * Validates a license key.
     * Returns flag indicating if license key is valid.
     *
     * @param ClientOx $client Client to use for api calls.
     * @param Closure $getRequest Callable that returns a LicenseRequest.
     * @param $setRequest Callable that sets (updates) a LicenseRequest casted as string.
     * @param null $domain
     * @param bool $force Flag that forces validation against the server.
     * @param bool $allowRetry Allow to connection retries.
     * @param int $retryAttempts Retry attempts.
     * @param string $retryFrequency Retry frequency.
     *
     * @return bool
     * @throws FileSystemException
     * @throws ValidatorException
     * @throws Exception
     * @throws Exception
     * @since 1.0.0
     */
    public static function validate(
        ClientOx $client,
        Closure $getRequest,
        $setRequest,
        $domain = null,
        $force = false,
        $allowRetry = false,
        $retryAttempts = 2,
        $retryFrequency = '+1 hour'
    ) {
        // Prepare
        $license = $getRequest();
        if (!is_a($license, LicenseRequest::class)) {
            static::log(3010);
            throw new Exception(' \Closure must return an object instance of LicenseRequest.');
        }
        $license->updateVersion();
        // Check license data

        if ($license->isEmpty || empty($license->data['the_key'])) {
            static::log(3020);
            return false;
        }
        // No need to check if license already expired.
        if ('active' != $license->data['status']) {
            static::log(1010);
            return false;
        }
        // Validate cached license data
        if (!$force
            && time() < $license->nextCheck
            && $license->isValid
        ) {
            return true;
        }
        // Call
        if (empty($domain)) {
            $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown';
        }
        $license->request['domain'] = preg_replace('/^(www|dev)\./i', '', $domain);
        $license->request['meta'] = static::getMeta();
        $response = null;
        try {
            static::log(1000);
            $response = $client->call('license_key_validate', $license);
        } catch (Exception $e) {
            static::_log(400, $e->getMessage());
            $response = null;
        }
        if (empty($response)) {
            static::log(3000);
            if ($allowRetry && $license->retries < $retryAttempts) {
                $license->addRetryAttempt($retryFrequency);
                static::log(3020,
                    [$license->retries . '/' . $retryAttempts]);
                call_user_func($setRequest, (string)$license);
                return true;
            } else {
                static::log(3030);
            }
        } elseif (isset($response->error)) {
            $license->data = isset($response->data) ? (array)$response->data : [];
            if ($response->error && isset($response->errors)) {
                static::log(4010, [(array)$response->errors]);
                $license->data = ['errors' => $response->errors];
            } else {
                static::log(2020);
            }
            $license->touch();
            call_user_func($setRequest, (string)$license);
            return $response->error === false;
        }

        return false;
    }

    /**
     * Deactivates a license key.
     * Returns call response.
     *
     * @param ClientOx $client Client to use for api calls.
     * @param Closure $getRequest Callable that returns a LicenseRequest.
     * @param $setRequest Callable that updates a LicenseRequest casted as string.
     *
     * @param null $domain
     *
     * @return object|stdClass
     * @throws FileSystemException
     * @throws ValidatorException
     * @throws Exception
     * @throws Exception
     * @since 1.0.0
     */
    public static function deactivate(ClientOx $client, Closure $getRequest, $setRequest, $domain = null)
    {
        // Prepare
        $license = $getRequest();
        if (!is_a($license, LicenseRequest::class)) {
            static::log(3010);
            throw new Exception(' \Closure must return an object instance of LicenseRequest.');
        }
        $license->updateVersion();
        // Call
        if (empty($domain)) {
            $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown';
        }
        $license->request['domain'] = preg_replace('/^(www|dev)\./i', '', $domain);
        $license->request['meta'] = static::getMeta();
        static::log(1000);
        $response = $client->call('license_key_deactivate', $license);
        // Remove license
        if ($response && isset($response->error)) {
            if ($response->error === false) {
                call_user_func($setRequest, null);
                static::log(2010);
            } else {
                if (isset($response->errors)) {
                    static::log(4010, [(array)$response->errors]);
                    foreach ($response->errors as $key => $message) {
                        if ($key === 'activation_id') {
                            call_user_func($setRequest, null);
                            static::log(2010);
                            break;
                        }
                    }
                }
            }
        } else {
            static::log(3000);
        }

        return $response;
    }

    /**
     * Validates a license key (NO SERVER VALIDATION).
     *
     * @param Closure $getRequest Callable that returns a LicenseRequest.
     *
     * @return bool
     * @throws Exception when LicenseRequest is not present.
     *
     * @since 1.0.9
     *
     */
    public static function softValidate(Closure $getRequest)
    {
        // Prepare
        $license = $getRequest();
        if (!is_a($license, LicenseRequest::class)) {
            static::log(3010);
            throw new Exception(' \Closure must return an object instance of LicenseRequest.');
        }
        $license->updateVersion();
        // Check license data
        if ($license->isEmpty || !$license->data['the_key']) {
            static::log(3020);
            return false;
        }
        if ('active' != $license->data['status']) {
            static::log(1010);
            return false;
        }
        // Validate cached license data
        return $license->isValid;
    }

    /**
     * Returns validate endpoint's response.
     *
     * @param ClientOx $client Client to use for api calls.
     * @param Closure $getRequest Callable that returns a LicenseRequest.
     * @param $setRequest Callable that updates a LicenseRequest casted as string.
     * @param null $domain
     * @param bool $allowRetry
     * @param int $retryAttempts
     * @param string $retryFrequency
     *
     * @return bool|object|stdClass
     * @throws Exception when LicenseRequest is not present.
     * @since 1.0.10
     */
    public static function check(
        ClientOx $client,
        Closure $getRequest,
        $setRequest,
        $domain = null,
        $allowRetry = false,
        $retryAttempts = 2,
        $retryFrequency = '+1 hour'
    ) {
        // Prepare
        $license = $getRequest();
        if (!is_a($license, LicenseRequest::class)) {
            static::log(3010);
            throw new Exception(' \Closure must return an object instance of LicenseRequest.');
        }
        $license->updateVersion();
        // Check license data
        if ($license->isEmpty || !$license->data['the_key']) {
            static::log(3020);
            return false;
        }
        if ('active' != $license->data['status']) {
            static::log(1010);
            return false;
        }
        // Call
        if (empty($domain)) {
            $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown';
        }
        $license->request['domain'] = preg_replace('/^(www|dev)\./i', '', $domain);
        $response = null;
        try {
            static::log(1000);
            $response = $client->call('license_key_validate', $license);
        } catch (Exception $e) {
            static::_log(400, $e->getMessage());
            $response = null;
        }
        if (empty($response)) {
            static::log(3000);
            if ($allowRetry && $license->retries < $retryAttempts) {
                $license->addRetryAttempt($retryFrequency);
                static::log(3020,
                    [$license->retries . '/' . $retryAttempts]);
                call_user_func($setRequest, (string)$license);
                return true;
            } else {
                static::log(3030);
            }
        } elseif (isset($response->error)) {
            $license->data = isset($response->data) ? (array)$response->data : [];
            if ($response->error && isset($response->errors)) {
                static::log(4010, [(array)$response->errors]);
                $license->data = ['errors' => $response->errors];
            } else {
                static::log(2020);
            }
            $license->touch();
            call_user_func($setRequest, (string)$license);
            return $response->error === false;
        }

        return $response;
    }
}
