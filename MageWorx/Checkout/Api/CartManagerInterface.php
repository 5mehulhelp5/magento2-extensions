<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Api;

use Magento\Quote\Api\Data\CartInterface;

interface CartManagerInterface
{
    /**
     * Obtain currently active quote (cart)
     * Force Renew flag is used to overwrite cart in local cache
     *
     * @param bool $forceRenew
     * @return CartInterface
     */
    public function getCurrentCart($forceRenew = false): ?CartInterface;

    /**
     * @return int|null
     */
    public function getCurrentCartId(): ?int;
}
