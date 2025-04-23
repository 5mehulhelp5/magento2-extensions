<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

/**
 * Class Sidebar
 *
 * Change checkout URL in the mini-cart
 */
class Sidebar
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
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig($subject, $result): array
    {
        if ($this->checkoutConfig->isEnabled()) {
            $result['checkoutUrl'] = $subject->getUrl('mageworx_checkout/onepage');
        }

        return $result;
    }
}
