<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

use Magento\Store\Model\ScopeInterface;

/**
 * Class AllowForcedRegistrationForVirtualOrders
 *
 * Make available for use checkout with virtual quotes and forced customer registration
 *
 */
class AllowForcedRegistrationForVirtualOrders
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
     * @return \Magento\Framework\Event\ObserverInterface
     */
    public function afterExecute($subject, $result, \Magento\Framework\Event\Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $resultFromObserver = $observer->getEvent()->getResult();

        if (!$resultFromObserver->getIsAllowed()) {
            if (!$this->checkoutConfig->isEnabled($store)) {
                return $result;
            }

            if (!$this->checkoutConfig->isCreateAccountEnabled($store)) {
                return $result;
            }

            if ($this->checkoutConfig->isForcedCreateAccountEnabled($store)) {
                $resultFromObserver->setIsAllowed(true);
            }
        }

        return $result;
    }
}
