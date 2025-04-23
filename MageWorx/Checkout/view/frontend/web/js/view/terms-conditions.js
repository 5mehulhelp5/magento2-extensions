define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/translate'
], function ($, ko, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/terms-conditions/terms-conditions'
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
