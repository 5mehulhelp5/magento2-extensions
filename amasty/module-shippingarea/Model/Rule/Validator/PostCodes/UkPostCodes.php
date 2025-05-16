<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Validator\PostCodes;

use Amasty\ShippingArea\Model\Area;
use Amasty\ShippingArea\Model\System\ConditionOptionProvider;
use Amasty\ShippingArea\Model\Validator\ValidatorInterface;
use Amasty\ShippingArea\Model\Rule\Validator\Value\GeneralPostCodeValidation;
use Magento\Quote\Model\Quote\Address;

class UkPostCodes implements ValidatorInterface
{
    /**
     * @var GeneralPostCodeValidation
     */
    private $generalPostCodeValidation;

    public function __construct(GeneralPostCodeValidation $generalPostCodeValidation)
    {
        $this->generalPostCodeValidation = $generalPostCodeValidation;
    }

    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        $result = false;
        $countrySet = $area->getCountrySet();
        if (is_array($countrySet) && !in_array('GB', $countrySet)) {
            return false;
        }
        if ($area->getPostcodeCondition()) {
            $result = $this->generalPostCodeValidation
                ->comparePostcode(
                    $area,
                    $this->convertPostCode($address->getPostcode() ?? '')
                );

            if ((int)$area->getPostcodeCondition() === ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }

    /**
     * @param string $postCode
     * @return string
     */
    public function convertPostCode(string $postCode): string
    {
        if (empty($postCode)) {
            return $postCode;
        }

        $postCode = preg_replace('/\s+/', '', $postCode);
        $postSplit = str_split($postCode);
        if (!is_numeric(end($postSplit)) && strlen($postCode) > 2) {
            $postCode = substr($postCode, 0, -3);
        }

        return $postCode;
    }
}
