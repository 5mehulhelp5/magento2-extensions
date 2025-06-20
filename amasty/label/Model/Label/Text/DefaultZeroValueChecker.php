<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Text;

use Amasty\Label\Api\Data\LabelInterface;

class DefaultZeroValueChecker implements ZeroValueCheckerInterface
{
    public function isZeroValue(string $variableValue, LabelInterface $label): bool
    {
        $isZero = $variableValue === '';

        if (!$isZero) {
            preg_match_all('/[\d\.]+/', $variableValue, $matches);
            $matches = array_merge(...$matches);
            $matches = array_map(function (string $matchedValue): int {
                return (int) ceil((float) $matchedValue);
            }, $matches);
            $isZero = !empty($matches) && array_sum($matches) === 0;
        }

        return $isZero;
    }
}
