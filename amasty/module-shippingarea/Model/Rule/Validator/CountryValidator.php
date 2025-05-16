<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Validator;

use Amasty\ShippingArea\Model\Area;
use Amasty\ShippingArea\Model\System\ConditionOptionProvider;
use Amasty\ShippingArea\Model\Validator\ValidatorInterface;
use Magento\Quote\Model\Quote\Address;

class CountryValidator implements ValidatorInterface
{
    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        $result = true;

        if ($area->getCountryCondition() && is_array($area->getCountrySet())) {
            $result = in_array($address->getCountry(), $area->getCountrySet());

            if ((int)$area->getCountryCondition() == ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }
}
