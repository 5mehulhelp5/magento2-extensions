<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Text;

use Amasty\Label\Api\Data\LabelInterface;
use Magento\Catalog\Api\Data\ProductInterface;

interface ProcessorInterface
{
    public const ALL_VARIABLES_FLAG = '*';

    /**
     * @return string[]
     */
    public function getAcceptableVariables(): array;

    public function getVariableValue(
        string $variable,
        LabelInterface $label,
        ProductInterface $product
    ): string;
}
