<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\Data;

class InvalidateLabelsIndex extends InvalidateLabelIndex
{
    public static function getDependencies(): array
    {
        return [
            \Amasty\Label\Setup\Patch\Data\InvalidateLabelIndex::class
        ];
    }
}
