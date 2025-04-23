define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/translate'
], function ($, ko, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/checkout-review/checkout-review'
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();
            return this;
        }
    });
});
