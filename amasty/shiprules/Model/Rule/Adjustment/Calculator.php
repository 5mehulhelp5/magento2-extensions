<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment;

use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\StrategyComposite;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry as TotalRegistry;
use Magento\Quote\Model\Quote\Address\RateResult\Method as Rate;

/**
 * Adjustment Calculator.
 */
class Calculator
{
    /**
     * @var TotalRegistry
     */
    private $totalRegistry;

    /**
     * @var StrategyComposite
     */
    private $calculationStrategy;

    /**
     * @var array
     */
    private $ratePrice = [];

    public function __construct(
        TotalRegistry $totalRegistry,
        StrategyComposite $calculationStrategy
    ) {
        $this->totalRegistry = $totalRegistry;
        $this->calculationStrategy = $calculationStrategy;
    }

    /**
     * @param Rule $rule
     * @param Rate $rate
     * @param string $hash
     * @return float
     */
    public function calculateByRule(Rule $rule, Rate $rate, string $hash): float
    {
        $cacheKey = $rate->getCarrier() . $rate->getMethod() . $rule->getRuleId();

        if (isset($this->ratePrice[$cacheKey])) {
            return $this->ratePrice[$cacheKey];
        }

        $total = $this->totalRegistry->getByHash($hash);
        $rateValue = 0;

        if (!$total) {
            return $rateValue;
        }

        if ($total->getFreeShipping() && !$rule->getIgnorePromo()) {
            return $this->ratePrice[$cacheKey] = 0;
        }

        if ($rule->getIgnorePromo()) {
            $price = $total->getPrice();
            $qty = $total->getQty();
            $weight = $total->getWeight();
        } else {
            $price = $total->getNotFreePrice();
            $qty = $total->getNotFreeQty();
            $weight = $total->getNotFreeWeight();
        }

        if ($qty > 0) {
            $rateValue = $rule->getRateBase();
        }

        $rateValue += $qty * $rule->getRateFixed();
        $rateValue += $price * $rule->getRatePercent() / 100;
        $rateValue += $weight * $rule->getWeightFixed();
        $rateValue += $rate->getPrice() * $rule->getHandling() / 100;
        $rateValue = $this->checkChangeBoundary($rule, $rateValue);

        return $this->ratePrice[$cacheKey] = $this->calculationStrategy->getAdjustmentValue($rate, $rule, $rateValue);
    }

    /**
     * @param Rule $rule
     * @param float $rateValue
     * @return float
     */
    private function checkChangeBoundary(Rule $rule, float $rateValue): float
    {
        $max = (float)$rule->getRateMax();
        $min = (float)$rule->getRateMin();

        if ($max && abs($rateValue) > $max) {
            $rateValue = $max;
        }
        if (abs($rateValue) < $min) {
            $rateValue = $min;
        }

        return $rateValue;
    }
}
