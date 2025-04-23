<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup;

use Amasty\Label\Model\ResourceModel\Label;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public const FLAT_LABEL_TABLE = 'am_label';
    public const AMASTY_LABEL_STORE_TABLE = 'amasty_label_store';
    public const AMASTY_LABEL_CUSTOMER_GROUP_TABLE = 'amasty_label_customer_group';
    public const AMASTY_LABEL_CATALOG_PARTS_TABLE = 'amasty_label_catalog_parts';
    public const AMASTY_LABEL_INDEX_TABLE = 'amasty_label_index';
    public const AMASTY_LABEL_TOOLTIP_TABLE = 'amasty_label_tooltip';

    public const MODULE_TABLES = [
        self::AMASTY_LABEL_STORE_TABLE,
        self::AMASTY_LABEL_CUSTOMER_GROUP_TABLE,
        self::AMASTY_LABEL_CATALOG_PARTS_TABLE,
        self::AMASTY_LABEL_INDEX_TABLE,
        self::AMASTY_LABEL_TOOLTIP_TABLE,
        Label::TABLE_NAME
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        foreach (self::MODULE_TABLES as $table) {
            $tableName = $setup->getTable($table);

            if ($connection->isTableExists($tableName)) {
                $connection->dropTable($tableName);
            }
        }

        $setup->endSetup();
    }
}
