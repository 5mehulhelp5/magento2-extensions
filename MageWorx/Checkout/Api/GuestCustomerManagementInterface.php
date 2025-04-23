<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface GuestCustomerManagementInterface
{
    /**
     * Save temporary password for guest customer.
     * Would be used later for create account purposes,
     * after order was successfully placed.
     *
     * @param string $cartId
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function saveTempPassword(string $cartId, string $email, string $password): bool;

    /**
     * Get password hash for cart
     *
     * @param $cartId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTempPasswordHash($cartId): string;
}
