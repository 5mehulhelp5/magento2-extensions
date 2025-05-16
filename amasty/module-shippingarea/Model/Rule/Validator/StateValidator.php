<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Validator;

use Amasty\ShippingArea\Model\Area;
use Amasty\ShippingArea\Model\Rule\Validator\Value\Comparer;
use Amasty\ShippingArea\Model\System\ConditionOptionProvider;
use Amasty\ShippingArea\Model\Validator\ValidatorInterface;
use Magento\Quote\Model\Quote\Address;

class StateValidator implements ValidatorInterface
{
    /**
     * @var Comparer
     */
    private $comparer;

    public function __construct(Comparer $comparer)
    {
        $this->comparer = $comparer;
    }

    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        $result = true;
        if ($area->getStateCondition()) {
            if ($area->getStateSetListing() && is_array($area->getStateSetListing())) {
                $result = in_array($address->getRegionId(), $area->getStateSetListing());
            } else {
                $result = $this->comparer->compareValues($address->getRegionCode(), $area->getStateSet());
            }

            if ((int)$area->getStateCondition() === ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }
}
