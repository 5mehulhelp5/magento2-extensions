/**
 * Copyright © MageWorx, Inc. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer'
], function ($, wrapper, quote, customer) {
    'use strict';

    return function (selectPaymentMethodAction) {

        return wrapper.wrap(selectPaymentMethodAction, function (originalSelectPaymentMethodAction, paymentMethod) {
            if (!customer.isLoggedIn() && !quote.guestEmail) {
                quote.guestEmail = 'johndoe@example.com';
            }

            // Fix for magento EE store credits (error fires when a customer removes the credits from order)
            if (paymentMethod === null) {
                paymentMethod = {};
            }

            originalSelectPaymentMethodAction(paymentMethod);
        });
    };

});
