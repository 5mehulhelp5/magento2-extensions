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
use Amasty\ShippingArea\Model\Rule\Validator\Value\Comparer;
use Amasty\ShippingArea\Model\Rule\Validator\Value\GeneralPostCodeValidation;
use Magento\Quote\Model\Quote\Address;

class CityValidator implements ValidatorInterface
{
    /**
     * @var Comparer
     */
    private $comparer;

    /**
     * @var GeneralPostCodeValidation
     */
    private $generalPostCodeValidation;

    public function __construct(
        Comparer $comparer,
        GeneralPostCodeValidation $generalPostCodeValidation
    ) {
        $this->comparer = $comparer;
        $this->generalPostCodeValidation = $generalPostCodeValidation;
    }

    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        $result = true;
        if (((int)$area->getCityCondition() === ConditionOptionProvider::CONDITION_EXCLUDE)
            && ((int)$area->getPostcodeCondition() === ConditionOptionProvider::CONDITION_EXCLUDE)
            && $this->comparer->compareValues((string)$address->getCity(), $area->getCitySet())
            && !$this->generalPostCodeValidation->comparePostcode($area, $address->getPostcode() ?? '')
        ) {
            return false;
        }

        if ($area->getCityCondition()) {
            $result = $this->comparer->compareValues((string)$address->getCity(), $area->getCitySet());

            if ((int)$area->getCityCondition() === ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }
}
