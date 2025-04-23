/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_GiftCardAccount/js/view/payment/gift-card-account'
], function (
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            contentVisible: false
        },
        observableProperties: [
            'contentVisible'
        ],

        /**
         * @returns {exports}
         */
        initObservable: function () {
            this._super();

            this.observe(this.observableProperties);

            if (this.giftCartCode()) {
                this.contentVisible(true);
            }

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
