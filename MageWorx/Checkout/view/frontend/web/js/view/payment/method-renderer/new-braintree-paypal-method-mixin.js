/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(['uiRegistry', 'Magento_Checkout/js/model/quote'], function (registry, quote) {
    'use strict';

    return function (origComponent) {

        if (window.isMageWorxCheckout) {
            return origComponent.extend({
                selectPaymentMethod: function () {
                    registry.get('checkout.sidebar.place-order').setSelectedPaymentMethod(this);

                    return this._super();
                },

                //fix error on first page load
                getShippingAddress: function () {
                    var address = quote.shippingAddress();
                    if (typeof address.street === 'undefined') {
                        address.street = [];
                        address.street[0] = '';
                        address.street[1] = '';
                        address.street[2] = '';
                    }

                    quote.shippingAddress(address);

                    return this._super();
                },
            });
        }

        return origComponent;
    };
});
