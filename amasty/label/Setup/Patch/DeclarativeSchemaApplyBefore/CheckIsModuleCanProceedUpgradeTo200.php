<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\DeclarativeSchemaApplyBefore;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\PatchInterface;

class CheckIsModuleCanProceedUpgradeTo200 implements PatchInterface
{
    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply(): PatchInterface
    {
        if (class_exists(\Amasty\Label\Model\AbstractLabels::class)) {
            $message = __('To continue the update, you need to delete the folder with the module files and upload '
                . 'a new one from the downloaded package.');
            throw new LocalizedException($message);
        }

        return $this;
    }
}
