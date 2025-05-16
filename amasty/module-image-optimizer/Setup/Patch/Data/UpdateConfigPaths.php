<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateConfigPaths implements DataPatchInterface
{
    public const OPTIMIZER_CONFIG = 'amoptimizer/';

    public const REPLACE_IMAGES_PATHS = [
        'replace_images_general/',
        'replace_images_home/',
        'replace_images_categories/',
        'replace_images_products/',
        'replace_images_cms/',
        'replace_images_general/'
    ];

    public const CONFIG_SETTINGS_MAP = [
        'webp_resolutions' => 'replace_strategy',
        'webp_resolutions_ignore' => 'replace_ignore'
    ];

    /**
     * @var Config
     */
    private $scopeConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(
        Config $scopeConfig,
        WriterInterface $configWriter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function apply(): UpdateConfigPaths
    {
        foreach (self::REPLACE_IMAGES_PATHS as $path) {
            foreach (self::CONFIG_SETTINGS_MAP as $old => $new) {
                $oldPath = $path . $old;
                $newPath = $path . $new;

                $oldPathData = $this->getConfigValues($oldPath);
                if (!$oldPathData) {
                    continue;
                }

                foreach ($oldPathData as $record) {
                    $this->scopeConfig->saveConfig(
                        self::OPTIMIZER_CONFIG . $newPath,
                        $record['value'],
                        $record['scope'],
                        $record['scope_id']
                    );
                    $this->scopeConfig->deleteConfig(
                        $record['path'],
                        $record['scope'],
                        $record['scope_id']
                    );
                }
            }
        }

        return $this;
    }

    private function getConfigValues(string $path): array
    {
        $connection = $this->scopeConfig->getConnection();
        $select = $connection->select()->from(
            $this->scopeConfig->getMainTable()
        )->where(
            'path = ?',
            self::OPTIMIZER_CONFIG . $path
        );

        return $connection->fetchAll($select);
    }
}
