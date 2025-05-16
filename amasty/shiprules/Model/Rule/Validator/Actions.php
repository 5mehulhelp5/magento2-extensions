<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Validator;

use Amasty\CommonRules\Model\OptionProvider\Provider\CalculationOptionProvider;
use Amasty\Shiprules\Model\Rule;
use Magento\Framework\Validator\AbstractValidator;

class Actions extends AbstractValidator
{
    /**
     * @param Rule $rule
     * @return bool
     */
    public function isValid($rule): bool
    {
        $errors = [];

        if ((int)$rule->getCalc() === CalculationOptionProvider::CALC_REPLACE_PRODUCT
            && empty($rule->getActions()->getActions())
        ) {
            $errors[] = __('Specify the product conditions or select another Calculation type.');
        }

        if (empty($errors)) {
            return true;
        }

        $this->_addMessages($errors);

        return false;
    }
}
