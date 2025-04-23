/**
 * Custom onepage view component
 */
define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/customer'
], function (
    $,
    ko,
    Component,
    customer
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/onepage'
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();

            // Make customer object available to the template
            this.customer = customer;

            return this;
        }
    });
});
