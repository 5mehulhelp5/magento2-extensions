<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\Validation\Validators;

use Amasty\ImageOptimizerSpeedSize\Model\ConfigProvider;
use Laminas\Validator\ValidatorInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Phrase;

class CdnUrl implements ValidatorInterface
{
    /**
     * @var Phrase[]
     */
    private $messages = [];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Validation can be triggered multiple times but state won't change
     * flag to marks validation state (null if not triggered yet)
     *
     * @var bool|null
     */
    private $isValid = null;

    public function __construct(
        ConfigProvider $configProvider
    ) {
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

        if ($value->getData('groups/speed_size/fields/speed_size_public_key/inherit')) {
            $apiKey = $this->configProvider->getSpeedSizeKey();
        } else {
            $apiKey = $value->getData('groups/speed_size/fields/speed_size_public_key/value');
        }

        if ($value->getData('groups/speed_size/fields/speed_size_cdn/inherit')) {
            $cdnUrl = $this->configProvider->getSpeedSizeCdnUrl();
        } else {
            $cdnUrl = $value->getData('groups/speed_size/fields/speed_size_cdn/value');
        }

        if ($apiKey && !$cdnUrl) {
            $this->setMessage(__(
                'Please make sure that all required fields are fulfilled to connect to SpeedSize.'
            ));
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
}
