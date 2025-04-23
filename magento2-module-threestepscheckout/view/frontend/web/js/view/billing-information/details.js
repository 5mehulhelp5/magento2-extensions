/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'ko',
    'Magento_Checkout/js/view/shipping-information/address-renderer/default',
    'Magento_Checkout/js/model/quote'
], function (ko, Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MB_ThreeStepsCheckout/billing-information/details'
        },
        address: ko.observable(null),
        initialize: function() {
            var self = this;
            quote.billingAddress.subscribe(function(address) {
                 self.address(address);
            });
            this._super();
        }
    });
});
