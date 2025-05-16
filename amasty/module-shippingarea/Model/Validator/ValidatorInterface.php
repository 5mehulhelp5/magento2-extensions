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

interface ValidatorInterface
{
    /**
     * @param Area $area
     * @param Address $address
     * @return bool
     */
    public function isValid(Area $area, Address $address): bool;
}
