<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Setup\Patch\Data;

use Amasty\ImageOptimizer\Setup\Patch\Data\UpdateConfigPaths;
use Amasty\ImageOptimizerSpeedSize\Model\ConfigProvider;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateConfig implements DataPatchInterface
{
    private const OLD_SPEEDSIZE_PATH = 'speed_size/speed_size_enabled';
    private const SPEEDSIZE_STRATEGY_KEY = 2;

    /**
     * @var Config
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        Config $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    public static function getDependencies(): array
    {
        return [
            UpdateConfigPaths::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $this->processApiKeyUpdate();
        $this->processPathsUpdate();

        return $this;
    }

    private function processApiKeyUpdate(): void
    {
        $apiKeyPath = UpdateConfigPaths::OPTIMIZER_CONFIG . ConfigProvider::SPEED_SIZE_PUBLIC_KEY;
        $apiKeyValues = $this->getConfigValues($apiKeyPath);
        if (!$apiKeyValues) {
            return;
        }

        $oldValueRecord = current($apiKeyValues); //only global scope left
        $newValue = $this->encryptor->encrypt($oldValueRecord['value']);
        $this->scopeConfig->saveConfig(
            $oldValueRecord['path'],
            $newValue
        );
    }

    private function processPathsUpdate(): void
    {
        $oldPath = UpdateConfigPaths::OPTIMIZER_CONFIG . self::OLD_SPEEDSIZE_PATH;
        $oldPathData = $this->getConfigValues($oldPath);
        if (!$oldPathData) {
            return;
        }

        foreach ($oldPathData as $record) {
            if ((int)$record['value'] === 1) {
                foreach (UpdateConfigPaths::REPLACE_IMAGES_PATHS as $path) {
                    $this->scopeConfig->saveConfig(
                        UpdateConfigPaths::OPTIMIZER_CONFIG . $path . 'replace_strategy',
                        self::SPEEDSIZE_STRATEGY_KEY,
                        $record['scope'],
                        $record['scope_id']
                    );
                }
            }
            $this->scopeConfig->deleteConfig(
                $record['path'],
                $record['scope'],
                $record['scope_id']
            );
        }
    }

    private function getConfigValues(string $path): array
    {
        $connection = $this->scopeConfig->getConnection();
        $select = $connection->select()->from(
            $this->scopeConfig->getMainTable()
        )->where(
            'path = ?',
            $path
        );

        return $connection->fetchAll($select);
    }
}
