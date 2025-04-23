/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'MageWorx_Checkout/js/model/update-totals-processor',
    'MageWorx_Checkout/js/action/save-billing-address',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'jquery'
], function (
    updateTotalsProcessor,
    saveBillingAddress,
    paymentService,
    paymentMethodsConverter,
    methodList,
    quote,
    customer,
    $
) {
    'use strict';

    var timeoutId,
        billingAddressSave = $.Deferred(),
        isFreePaymentMethod = function (paymentMethod) {
            return paymentMethod.code === 'free';
        },
        getGrandTotal = function () {
            return quote.totals()['grand_total'];
        };

    return function (billingAddress) {
        if (!customer.isLoggedIn() && quote.guestEmail && !billingAddress.email) {
            billingAddress.email = quote.guestEmail;
        }

        updateTotalsProcessor.before(billingAddress);

        var address;

        if (quote.shippingAddress() && billingAddress.getCacheKey() == //eslint-disable-line eqeqeq
            quote.shippingAddress().getCacheKey()
        ) {
            address = $.extend(true, {}, billingAddress);
            if (quote.shippingAddress().sameAsBilling) {
                address.saveInAddressBook = null;
            }
        } else {
            address = billingAddress;
        }
        quote.billingAddress(address);

        // Save billing address in backend
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function (address) {
            if (billingAddressSave.state() === 'pending') {
                // skip previous call when new data obtained
                billingAddressSave.rejectWith ? billingAddressSave.rejectWith() : billingAddressSave.abort();
            }
            billingAddressSave = saveBillingAddress(address);
            billingAddressSave.done(
                function (response) {
                    var totals = response['totals'],
                        paymentMethodsInfo = response['payment_methods'];

                    if (totals && !window.checkoutConfig.isPlacingOrder) {
                        quote.setTotals(response.totals);
                    }

                    if (paymentMethodsInfo) {
                        //paymentService.setPaymentMethods();
                        var freeMethod = _.find(paymentMethodsInfo, isFreePaymentMethod);

                        if (freeMethod && getGrandTotal() <= 0) {
                            paymentMethodsInfo.splice(0, paymentMethodsInfo.length, freeMethod);
                        }

                        var methods = paymentMethodsConverter(paymentMethodsInfo),
                            methodNames = _.pluck(methods, 'method');

                        _.map(methodList(), function (existingMethod) {
                            var existingMethodIndex = methodNames.indexOf(existingMethod.method);

                            if (existingMethodIndex !== -1) {
                                methods[existingMethodIndex] = existingMethod;
                            }
                        });

                        methodList(methods);
                    }

                    // Reload payment methods
                    updateTotalsProcessor.after(billingAddress);
                }
            )
        }, 1500, address);
    };
});
