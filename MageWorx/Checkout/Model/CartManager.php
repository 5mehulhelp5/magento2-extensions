<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use MageWorx\Checkout\Api\CartManagerInterface;

class CartManager implements CartManagerInterface
{
    /**
     * @var CartInterface|null
     */
    protected $cart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * CartManager constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param bool $forceRenew
     * @return CartInterface
     */
    public function getCurrentCart($forceRenew = false): ?CartInterface
    {
        if ($forceRenew || !$this->cart) {
            try {
                $this->cart = $this->checkoutSession->getQuote();
            } catch (LocalizedException $exception) {
                $this->cart = null;
            }
        }

        return $this->cart;
    }

    /**
     * @return int|null
     */
    public function getCurrentCartId(): ?int
    {
        $cart = $this->getCurrentCart();
        if ($cart) {
            return $cart->getId();
        }

        return null;
    }
}
