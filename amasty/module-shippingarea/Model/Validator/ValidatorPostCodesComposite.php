<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Validator;

use Amasty\ShippingArea\Model\Area;
use Magento\Quote\Model\Quote\Address;

class ValidatorPostCodesComposite implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        $result = false;
        foreach ($this->validators as $validator) {
            if (($validator instanceof ValidatorInterface) && $validator->isValid($area, $address)) {
                $result = true;
            }
        }

        return $result;
    }
}
