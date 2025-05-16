<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment\Calculation\Strategy;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\StrategyInterface;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class Replace implements StrategyInterface
{
    /**
     * @param Method $method
     * @param RuleInterface $rule
     * @param float $amount
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAdjustmentValue(Method $method, RuleInterface $rule, float $amount): float
    {
        return $amount - $method->getPrice();
    }
}
