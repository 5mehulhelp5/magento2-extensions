<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Setup;

use Amasty\GroupedOptions\Api\GroupRepositoryInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    private $tablesToDrop = [
        GroupRepositoryInterface::TABLE,
        GroupRepositoryInterface::TABLE_OPTIONS,
        GroupRepositoryInterface::TABLE_VALUES
    ];

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        foreach ($this->tablesToDrop as $tableToDrop) {
            $setup->getConnection()->dropTable($setup->getTable($tableToDrop));
        }

        $setup->endSetup();
    }
}
