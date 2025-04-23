<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Text;

interface VariableProcessorInterface
{
    /**
     * @param string $text
     * @return string[]
     */
    public function extractVariables(string $text): array;

    public function insertVariable(string $text, string $variable, string $variableValue): string;
}
