define([
    'uiComponent',
    'MageWorx_Checkout/js/view/summary/item/rooms-calculator'
], function (Component, roomsCalculator) {
    'use strict';

    return Component.extend({
        defaults: {
            template: ''
        },

        initialize: function () {
            this._super();
            return this;
        },

        calculate: function(options) {
            return roomsCalculator.calculate(options);
        }
    });
});
