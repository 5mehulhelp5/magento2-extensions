<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

/**
 * Class Cart
 *
 * Change checkout URL on the cart page
 */
class Cart
{
    /**
     * @var \MageWorx\Checkout\Api\CheckoutConfigInterface
     */
    private $checkoutConfig;

    /**
     * Cart constructor.
     *
     * @param \MageWorx\Checkout\Api\CheckoutConfigInterface $checkoutConfig
     */
    public function __construct(
        \MageWorx\Checkout\Api\CheckoutConfigInterface $checkoutConfig
    ) {
        $this->checkoutConfig = $checkoutConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Onepage\Link $subject
     * @param string $result
     * @return string
     */
    public function afterGetCheckoutUrl($subject, $result): string
    {
        if ($this->checkoutConfig->isEnabled()) {
            $result = $subject->getUrl('mageworx_checkout/onepage');
        }

        return $result;
    }
}
