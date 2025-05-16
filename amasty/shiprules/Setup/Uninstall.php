<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Setup;

use Amasty\Shiprules\Model\ResourceModel\Rule;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ): void {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $connection->dropTable($setup->getTable(Rule::TABLE_NAME));
        $connection->dropTable($setup->getTable(Rule::ATTRIBUTE_TABLE_NAME));

        $setup->endSetup();
    }
}
