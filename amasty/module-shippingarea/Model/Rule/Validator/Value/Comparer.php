<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Validator\Value;

class Comparer
{
    /**
     * Case and type insensitive comparison of values
     *
     * @param string $validatedValue
     * @param string $value
     *
     * @return bool
     */
    public function compareValues(string $validatedValue, string $value): bool
    {
        $validatePattern = preg_quote($validatedValue, '~');
        $value = str_replace(["\r\n", "\r"], "\n", $value);

        return (bool)preg_match('~^' . $validatePattern . '$~miu', $value);
    }
}
