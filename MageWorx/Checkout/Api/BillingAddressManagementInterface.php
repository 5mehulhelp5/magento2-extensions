<?php

namespace MageWorx\Checkout\Api;

interface BillingAddressManagementInterface
{
    /**
     * Save billing address
     *
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveBillingAddress(
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ): \Magento\Checkout\Api\Data\PaymentDetailsInterface;
}
