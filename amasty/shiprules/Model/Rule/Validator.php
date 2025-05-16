<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule;

use Amasty\CommonRules\Model\Modifiers\Address;
use Amasty\CommonRules\Model\Validator\SalesRule;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Validator as TotalValidator;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Validator
{
    /**
     * @var Address
     */
    private $addressModifier;

    /**
     * @var SalesRule
     */
    private $salesRuleValidator;

    /**
     * @var TotalValidator
     */
    private $totalValidator;

    public function __construct(
        Address $addressModifier,
        SalesRule $salesRuleValidator,
        TotalValidator $totalValidator
    ) {
        $this->addressModifier = $addressModifier;
        $this->salesRuleValidator = $salesRuleValidator;
        $this->totalValidator = $totalValidator;
    }

    /**
     * @param RuleInterface|Rule $rule
     * @param RateRequest $request
     * @param Total $total
     * @return bool
     */
    public function validateRule(RuleInterface $rule, RateRequest $request, Total $total): bool
    {
        $allItems = $request->getAllItems();

        if (empty($allItems)) {
            return false;
        }

        $modifiedAddress = $this->addressModifier->modify(
            current($allItems)->getAddress(),
            $request
        );

        return $this->salesRuleValidator->validate($rule, $allItems) // Validate rule by coupon code and conditions
            && $rule->validate($modifiedAddress, $allItems)
            && $this->totalValidator->validate($rule, $total);
    }
}
