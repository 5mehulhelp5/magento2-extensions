/**
 * Custom Reservation Summary component
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/reservation-summary'
        },

        /**
         * @returns {*}
         */
        isVisible: function () {
            return true;
        },

        /**
         * @returns {*}
         */
        getItems: function () {
            return quote.getItems();
        }
    });
});
