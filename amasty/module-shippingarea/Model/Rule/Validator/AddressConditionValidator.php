<?php
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

class AddressConditionValidator implements ValidatorInterface
{
    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        $result = true;
        if ($area->getAddressCondition()) {
            $result = false;
            $inputStreet = $address->getStreet();

            foreach ($area->getStreetArray() as $streetLine) {
                foreach ($inputStreet as $inputStreetLine) {
                    if (stripos($inputStreetLine, $streetLine) !== false) {
                        $result = true;
                        break 2;
                    }
                }
            }

            if ((int)$area->getAddressCondition() === ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }
}
