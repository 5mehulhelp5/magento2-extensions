<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\Validation\Validators;

use Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\ImageOptimizer\SpeedSizeStrategy;
use Amasty\ImageOptimizerSpeedSize\Model\ConfigProvider;
use Amasty\ImageOptimizerSpeedSize\Model\SpeedSize\Client\HttpClient;
use Laminas\Validator\ValidatorInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Phrase;

class ApiKey implements ValidatorInterface
{
    private const OBSCURED_API_KEY = '******';
    private const CONFIG_PATHS_MAP = [
        'replace_home' => 'replace_images_home/replace_strategy',
        'replace_categories' => 'replace_images_categories/replace_strategy',
        'replace_product' => 'replace_images_products/replace_strategy',
        'replace_cms' => 'replace_images_cms/replace_strategy'
    ];

    /**
     * Validation can be triggered multiple times but state won't change
     * flag to mark validation state (null if not triggered yet)
     *
     * @var bool|null
     */
    private $isValid = null;

    /**
     * @var Phrase[];
     */
    private $messages = [];

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        HttpClient $httpClient,
        ConfigProvider $configProvider
    ) {
        $this->httpClient = $httpClient;
        $this->configProvider = $configProvider;
    }

    /**
     * @param Value $value
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        if ($this->isValid !== null) {
            return $this->isValid;
        }
        $apiKey = $this->retrieveApiKeyValue($value);

        if ($apiKey === self::OBSCURED_API_KEY) {
            $apiKey = $this->configProvider->getSpeedSizeKey();
        } elseif (empty($apiKey)) {
            $this->validateEmptyKey($value);

            return $this->isValid;
        }

        $response = $this->httpClient->request('GET', '/clients/' . $apiKey);
        if (!$this->httpClient->isSuccessResponse($response)) {
            $responseData = $this->httpClient->extractJsonResponse($response);
            if (!empty($responseData['detail'])) {
                $errMsg = __('Invalid public key for SpeedSize connection: %1', $responseData['detail']);
            } else {
                $errMsg = __('Invalid public key for SpeedSize connection.');
            }
            $this->setMessage($errMsg);
        }

        return $this->isValid = empty($this->getMessages());
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    private function setMessage(Phrase $msg): void
    {
        $this->messages = [$msg];
    }

    private function retrieveApiKeyValue($value): ?string
    {
        if ($value->getGroupId() === 'lazy_advanced'
            || $value->getData('groups/speed_size/fields/speed_size_public_key/value') === null
        ) {
            $apiKey = $this->configProvider->getSpeedSizeKey();
        } else {
            $apiKey = $value->getData('groups/speed_size/fields/speed_size_public_key/value');
        }

        return $apiKey;
    }

    /**
     * Separate validation logic, to prevent situations
     * when speedsize disabled but config can't be saved
     */
    private function validateEmptyKey($value): void
    {
        if ($this->isSpeedSizeEnabled($value)) {
            $this->setMessage(__('Invalid public key for SpeedSize connection.'));
            $this->isValid = false;
        } else {
            $this->isValid = true;
        }
    }

    private function isSpeedSizeEnabled($value): bool
    {
        $fieldsData = $value->getData('groups/replace_images');
        if ($fieldsData && ($this->isGeneralSpeedSize($fieldsData) || $this->isCustomSpeedSize($fieldsData))) {
            return true;
        }

        $lazyLoadFieldsData = $value->getData('groups/lazy_advanced');
        if ($lazyLoadFieldsData) {
            $fieldsDataValue = ($lazyLoadFieldsData['fields']['speed_size_enabled']['value'] ?? null);
            if ($fieldsDataValue === '1') {
                return true;
            }
        }

        return false;
    }

    private function isGeneralSpeedSize(array $fieldsData): bool
    {
        if (isset($fieldsData['fields']['replace_strategy']['inherit'])) {
            $fieldsDataValue = $this->configProvider->getImageReplaceStrategy();
        } else {
            $fieldsDataValue = (int)($fieldsData['fields']['replace_strategy']['value'] ?? null);
        }

        return $fieldsDataValue === (int)SpeedSizeStrategy::SPEED_SIZE_STRATEGY_KEY;
    }

    private function isCustomSpeedSize(array $fieldsData): bool
    {
        foreach ($fieldsData['groups'] as $groupName => $group) {
            if (isset($group['fields']['replace_strategy']['inherit'])) {
                $fieldsDataValue = (int)$this->configProvider->getConfig(self::CONFIG_PATHS_MAP[$groupName]);
            } else {
                $fieldsDataValue = (int)($group['fields']['replace_strategy']['value'] ?? null);
            }
            if ($fieldsDataValue === (int)SpeedSizeStrategy::SPEED_SIZE_STRATEGY_KEY) {
                return true;
            }
        }

        return false;
    }
}
