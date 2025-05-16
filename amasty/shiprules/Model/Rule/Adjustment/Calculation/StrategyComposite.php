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

class StrategyComposite implements StrategyInterface
{
    /**
     * @var StrategyInterface[]
     */
    private $strategies;

    public function __construct(
        array $strategies = []
    ) {
        $this->strategies = $strategies;
    }

    /**
     * @param Method $method
     * @param RuleInterface $rule
     * @param float $amount
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAdjustmentValue(Method $method, RuleInterface $rule, float $amount): float
    {
        $strategy = $this->strategies[$rule->getCalc()] ?? null;

        if ($strategy instanceof StrategyInterface) {
            return $strategy->getAdjustmentValue($method, $rule, $amount);
        }

        throw new \InvalidArgumentException(
            'Invalid strategy class provided. Expected implementation of ' . StrategyInterface::class
        );
    }
}
