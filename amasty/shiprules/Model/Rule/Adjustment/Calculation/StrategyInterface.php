<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment\Calculation;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

interface StrategyInterface
{
    /**
     * @param Method $method
     * @param RuleInterface $rule
     * @param float $amount
     * @return float
     */
    public function getAdjustmentValue(Method $method, RuleInterface $rule, float $amount): float;
}
