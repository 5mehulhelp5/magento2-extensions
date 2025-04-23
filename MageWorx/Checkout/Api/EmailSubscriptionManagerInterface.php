<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Api;

/**
 * Interface EmailSubscriptionManagerInterface
 */
interface EmailSubscriptionManagerInterface
{
    /**
     * Save subscription status for guest customer
     *
     * @param bool $status
     * @param string $cartId
     * @return bool
     */
    public function saveSubscriptionGuest(bool $status, string $cartId): bool;

    /**
     * Save subscription status for registered customers
     *
     * @param bool $status
     * @param int $cartId
     * @return bool
     */
    public function saveSubscription(bool $status, int $cartId): bool;

    /**
     * Get subscription status by quote id
     *
     * @param int $quoteId
     * @return bool
     */
    public function getEmailSubscriptionStatusByQuoteId(int $quoteId): bool;
}
