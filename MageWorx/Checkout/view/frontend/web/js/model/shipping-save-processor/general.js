/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/error-processor',
    'MageWorx_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/shipping-save-processor/payload-extender',
    'MageWorx_Checkout/js/model/default-shipping-method'
], function (
    ko,
    $,
    quote,
    resourceUrlManager,
    storage,
    paymentService,
    methodConverter,
    errorProcessor,
    selectBillingAddressAction,
    payloadExtender,
    defaultShippingMethod
) {
    'use strict';

    return {
        /**
         * @return {jQuery.Deferred}
         */
        saveShippingInformation: function () {
            var payload,
                methodCode,
                carrierCode;

            if (!quote.billingAddress() && quote.shippingAddress().canUseForBilling()) {
                selectBillingAddressAction(quote.shippingAddress());
            }

            let shippingAddress = quote.shippingAddress(),
                billingAddress = quote.billingAddress();

            if (shippingAddress && shippingAddress['customerAddressId']) {
                shippingAddress['save_in_address_book'] = 0;
            }

            if (billingAddress && billingAddress['customerAddressId']) {
                billingAddress['save_in_address_book'] = 0;
            }

            payload = {
                addressInformation: {
                    'shipping_address': shippingAddress,
                    'billing_address': billingAddress
                }
            };

            methodCode = quote.shippingMethod() ? quote.shippingMethod()['method_code'] : defaultShippingMethod.getMethodCode();
            carrierCode = quote.shippingMethod() ? quote.shippingMethod()['carrier_code'] : defaultShippingMethod.getCarrierCode();
            if (methodCode && carrierCode) {
                payload.addressInformation.shipping_method_code = methodCode;
                payload.addressInformation.shipping_carrier_code = carrierCode;
            } else {
                return $.Deferred();
            }

            payloadExtender(payload);

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
        }
    };
});
