/**
 * Copyright © MageWorx. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/action/select-shipping-address',
], function ($, _, wrapper, quote, addressList, selectShippingAddress) {
    'use strict';

    return function (originalDataResolver) {
        var updateObject = {};

        if (window.isMageWorxCheckout) {

            /**
             * Wrap shipping address resolver to set first available address as a shipping address
             * in case when customer has multiple addressees in the address book but set no one as
             * a default shipping address.
             *
             * Prevents fatal error of the js initialization in the billing address component.
             *
             * @type {Function|(function(): *)}
             */
            updateObject.resolveShippingAddress = wrapper.wrap(
                originalDataResolver.resolveShippingAddress,
                function (originalResolveShippingAddressFunction) {
                    originalResolveShippingAddressFunction();
                    if (!quote.shippingAddress() && addressList.length > 0) {
                        selectShippingAddress(addressList()[0]);
                    }
                }
            )
        }

        return _.extend(originalDataResolver, updateObject);
    };
});
