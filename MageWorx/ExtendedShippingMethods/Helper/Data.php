<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ExtendedShippingMethods\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Serialize\Serializer\Json as SerializeJson;
use Psr\Log\LoggerInterface as Logger;

class Data extends AbstractHelper
{
    const XML_PATH_SHIPPING_METHODS_CONFIG = 'mageworx_checkout/shipping_methods/configuration';

    const XML_PATH_SHIPPING_METHODS_ICONS  = 'mageworx_checkout/shipping_methods/add_icons';

    const IMAGE_URL_PATH = '/mageworx/extended_shipping_methods/icons/';

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var SerializeJson
     */
    protected $serializer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Random $mathRandom
     * @param SerializeJson $serializer
     * @param StoreManagerInterface $storeManager
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        Random $mathRandom,
        SerializeJson $serializer,
        StoreManagerInterface $storeManager,
        Logger $logger
    ) {
        $this->mathRandom   = $mathRandom;
        $this->serializer   = $serializer;
        $this->storeManager = $storeManager;
        $this->logger       = $logger;
        parent::__construct($context);
    }

    /**
     * Parse comma separated string of ids and convert it to array ( int[] )
     *
     * @param string $string
     * @return array
     */
    private function convertCommaSeparatedStringToIntArray(string $string): array
    {
        $result = explode(',', $string);
        array_walk(
            $result,
            function (&$value) {
                $value = (int)trim($value);
            }
        );
        $result = array_filter($result);

        return $result;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getShippingMethodsConfiguration(int $storeId = null): array
    {
        $result = $this->scopeConfig->getValue(
            static::XML_PATH_SHIPPING_METHODS_CONFIG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (empty($result)) {
            return [];
        }

        if (is_string($result)) {
            $result = $this->unserializeValue($result);
        }

        foreach ($result as $methodCode => $values) {
            if (empty($values['image'])) {
                continue;
            }

            try {
                $fullImageUrl                 = $this->getBaseImagePath($storeId) . $values['image'];
                $result[$methodCode]['image'] = $fullImageUrl;
            } catch (LocalizedException $exception) {
                $result[$methodCode]['image'] = '';
            }
        }

        return $result;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function getShippingMethodsIcon(int $storeId = null): bool
    {
        $addIcon = $this->scopeConfig->isSetFlag(
            static::XML_PATH_SHIPPING_METHODS_ICONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $addIcon;
    }

    /** FOR METHODS SETTING IN STORE CONFIG */

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    public function unserializeValue($value): array
    {
        if (is_string($value) && !empty($value)) {
            try {
                return $this->serializer->unserialize($value);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());

                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    public function serializeValue($value)
    {
        if (is_array($value)) {
            $data = [];
            foreach ($value as $methodId => $title) {
                if (!array_key_exists($methodId, $data)) {
                    $data[$methodId] = $title;
                }
            }

            try {
                return $this->serializer->serialize($data);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());

                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    public function isEncodedArrayFieldValue($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('methods_id', $row)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     * @throws LocalizedException
     */
    public function encodeArrayFieldValue(array $value): array
    {
        $result = [];
        foreach ($value as $methodId => $data) {
            if (!is_array($data)) {
                continue;
            }
            $resultId          = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = $data;
        }

        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    public function decodeArrayFieldValue(array $value): array
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('methods_id', $row)
            ) {
                continue;
            }
            $methodId          = $row['methods_id'];
            $result[$methodId] = $row;
        }

        return $result;
    }

    /**
     * Returns base url for images
     *
     * @param int|null $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseImagePath(int $storeId = null): string
    {
        $store        = $this->storeManager->getStore($storeId);
        $baseMediaUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        $fullBaseUrl = $baseMediaUrl . static::IMAGE_URL_PATH;

        return $fullBaseUrl;
    }
}
