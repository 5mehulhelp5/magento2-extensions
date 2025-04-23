<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Api;

/**
 * Interface OrderCommentsManagementInterface
 */
interface OrderCommentsManagementInterface
{
    /**
     * Save comment for guest customer
     *
     * @param string $comment
     * @param string $cartId
     * @return bool
     */
    public function saveCommentGuest(string $comment, string $cartId): bool;

    /**
     * Save comment for registered customers
     *
     * @param string $comment
     * @param int $cartId
     * @return bool
     */
    public function saveComment(string $comment, int $cartId): bool;

    /**
     * Get comment by quote id
     *
     * @param int $quoteId
     * @return string
     */
    public function getOrderCommentByQuoteId(int $quoteId): string;
}
