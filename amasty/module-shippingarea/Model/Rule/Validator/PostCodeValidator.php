<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Validator;

use Amasty\ShippingArea\Model\Area;
use Amasty\ShippingArea\Model\Validator\ValidatorInterface;
use Amasty\ShippingArea\Model\Validator\ValidatorPostCodesComposite;
use Magento\Quote\Model\Quote\Address;

class PostCodeValidator implements ValidatorInterface
{
    /**
     * @var ValidatorPostCodesComposite
     */
    private $postCodeValidator;

    public function __construct(ValidatorPostCodesComposite $postCodeValidator)
    {
        $this->postCodeValidator = $postCodeValidator;
    }

    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool
    {
        return $this->postCodeValidator->isValid($area, $address);
    }
}
