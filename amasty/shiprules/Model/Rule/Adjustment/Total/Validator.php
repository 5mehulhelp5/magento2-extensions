<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment\Total;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Total;

class Validator
{
    /**
     * @param RuleInterface|Rule $rule
     * @param Total $total
     * @return bool
     */
    public function validate(RuleInterface $rule, Total $total): bool
    {
        if ($rule->getIgnorePromo()) {
            $totalData = [
                'price' => $total->getPrice(),
                'qty' => $total->getQty(),
                'weight' => $total->getWeight()
            ];
        } else {
            $totalData = [
                'price' => $total->getNotFreePrice(),
                'qty' => $total->getNotFreeQty(),
                'weight' => $total->getNotFreeWeight(),
            ];
        }

        foreach ($totalData as $key => $value) {
            $ruleFromValue = $rule->getData($key . '_from');

            if ($ruleFromValue > 0 && $value < $ruleFromValue) {
                return false;
            }

            $ruleToValue = $rule->getData($key . '_to');

            if ($ruleToValue > 0 && $value > $ruleToValue) {
                return false;
            }
        }

        return true;
    }
}
