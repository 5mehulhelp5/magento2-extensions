<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Setup;

use Amasty\Shopby\Api\CmsPageRepositoryInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Delete tables manually, because Amasty_Base restricts to delete Amasty tables by Declarative Scheme.
 *
 * @see \Amasty\Base\Plugin\Framework\Setup\Declaration\Schema\Diff\Diff\RestrictDropTables
 */
class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $defaultConnection = $setup->getConnection();
        $defaultConnection->dropTable(
            $setup->getTable(CmsPageRepositoryInterface::TABLE)
        );
    }
}
