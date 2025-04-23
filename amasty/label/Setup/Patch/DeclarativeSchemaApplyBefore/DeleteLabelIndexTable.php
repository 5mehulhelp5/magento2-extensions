<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\DeclarativeSchemaApplyBefore;

class DeleteLabelIndexTable extends DropIndexTable
{
    public static function getDependencies(): array
    {
        return [
            \Amasty\Label\Setup\Patch\DeclarativeSchemaApplyBefore\DropIndexTable::class
        ];
    }
}
