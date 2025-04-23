<?php

/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MB\ThreeStepsCheckout\Plugin;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class FixBraintreeVaultPaymentInformation
{
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        ?AddressInterface $billingAddress = null
    ): array {
        if (strpos($paymentMethod->getMethod(), 'braintree_cc_vault') === 0) {
            $paymentMethod->setMethod('braintree_cc_vault');
        }
        return [$cartId, $paymentMethod, $billingAddress];
    }
}
