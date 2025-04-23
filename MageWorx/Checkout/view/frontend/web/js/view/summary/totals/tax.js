/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Tax/js/view/checkout/summary/tax'
], function (
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/totals/tax',
            contentVisible: false
        },

        observableProperties: [
            'contentVisible'
        ],

        initObservable: function () {
            this._super();

            this.observe(this.observableProperties);

            return this;
        },

        /**
         * Toggle collapsible class state
         */
        toggleCollapsible: function () {
            this.contentVisible(!this.contentVisible());
        }
    });
});
