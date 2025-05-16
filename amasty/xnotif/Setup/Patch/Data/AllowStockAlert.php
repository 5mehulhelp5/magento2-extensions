<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AllowStockAlert implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('core_config_data');
        $cols = $connection->fetchCol(
            $connection->select()
                ->from($tableName)
                ->where('path = ?', 'catalog/productalert/allow_stock')
        );

        if ($cols) {
            $connection->update(
                $tableName,
                ['value' => 1],
                'path = \'catalog/productalert/allow_stock\''
            );
        } else {
            $connection->insert(
                $tableName,
                [
                    'scope' => 'default',
                    'scope_id' => 0,
                    'path' => 'catalog/productalert/allow_stock',
                    'value' => '1'
                ]
            );
        }

        return $this;
    }
}
