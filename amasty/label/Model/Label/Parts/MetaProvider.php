<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Parts;

use Amasty\Label\Api\Data\LabelExtensionInterface;
use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use RuntimeException as RuntimeException;

class MetaProvider
{
    public const FRONTEND_SETTINGS_PART = LabelFrontendSettingsInterface::PART_CODE;
    public const TABLE_NAME = 'table_name';
    public const PART_CODE = 'part_code';
    public const PART_INTERFACE = 'part_interface';

    /**
     * @var array[]
     *
     * @example [
     *      'part_name' [
     *          'part_config_1' => $value
     *      ]
     * ]
     */
    private $config;

    public function __construct(
        $config = []
    ) {
        $this->config = $this->parseConfig($config);
    }

    /**
     * @return string[]
     */
    public function getAllPartsCodes(): array
    {
        return array_keys($this->config);
    }

    /**
     * @return array[]
     */
    public function getPartsConfig(): array
    {
        return $this->config;
    }

    private function generateAccessMethod(string $partCode, string $type): string
    {
        $method = $type . join('', array_map('ucfirst', explode('_', $partCode)));

        if (!method_exists(LabelExtensionInterface::class, $method)) {
            $exceptionMessage = __('DI config is outdated or invalid. Try execute php bin/magento setup:di:compile');
            throw new RuntimeException($exceptionMessage->render());
        }

        return $method;
    }

    public function getSetter(string $partCode): string
    {
        return $this->generateAccessMethod($partCode, 'set');
    }

    public function getGetter(string $partCode): string
    {
        return $this->generateAccessMethod($partCode, 'get');
    }

    private function getValue(string $partKey, string $identifier): string
    {
        if (!isset($this->config[$partKey][$identifier])) {
            throw new RuntimeException(
                __('Invalid DI configuration. Required parameter %1 for label part %2 wasn\'t passed')->render()
            );
        }

        return $this->config[$partKey][$identifier];
    }

    /**
     * @param string $partCode
     * @return string
     * @throws RuntimeException
     */
    public function getInterface(string $partCode): string
    {
        return $this->getValue($partCode, self::PART_INTERFACE);
    }

    public function getTable(string $partCode): string
    {
        return $this->getValue($partCode, self::TABLE_NAME);
    }

    private function parseConfig(array $config): array
    {
        return array_reduce($config, function (array $carry, array $config): array {
            if (isset($config[self::PART_CODE])) {
                $carry[$config[self::PART_CODE]] = $config;
            }

            return $carry;
        }, []);
    }
}
