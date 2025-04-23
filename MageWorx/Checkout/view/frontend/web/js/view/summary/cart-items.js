/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magento_Checkout/js/model/totals',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote'
], function (
    ko,
    totals,
    Component,
    stepNavigator,
    quote
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/cart-items'
        },
        totals: totals.totals(),
        items: ko.observable([]),
        maxCartItemsToDisplay: window.checkoutConfig.maxCartItemsToDisplay,
        cartUrl: window.checkoutConfig.cartUrl,
        collapsed: false,
        observableProperties: [
            'collapsed'
        ],

        /**
         * @deprecated Please use observable property (this.items())
         */
        getItems: totals.getItems(),

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            // Set initial items to observable field
            this.setItems(totals.getItems()());
            // Subscribe for items data changes and refresh items in view
            totals.getItems().subscribe(function (items) {
                this.setItems(items);
            }.bind(this));
        },

        /**
         *
         * @returns {exports}
         */
        initObservable: function () {
            this._super();

            this.observe(this.observableProperties);
            this.collapsed(true);

            return this;
        },

        /**
         * Returns cart items qty
         *
         * @returns {Number}
         */
        getItemsQty: function () {
            return parseFloat(this.totals['items_qty']);
        },

        /**
         * Returns count of cart line items
         *
         * @returns {Number}
         */
        getCartLineItemsCount: function () {
            return parseInt(totals.getItems()().length, 10);
        },

        /**
         * Returns shopping cart items summary (includes config settings)
         *
         * @returns {Number}
         */
        getCartSummaryItemsCount: function () {
            return useQty ? this.getItemsQty() : this.getCartLineItemsCount();
        },

        /**
         * Set items to observable field
         *
         * @param {Object} items
         */
        setItems: function (items) {
            this.items(items);
        },

        /**
         * Returns bool value for items block state (expanded or not)
         *
         * @returns {*|Boolean}
         */
        isItemsBlockExpanded: function () {
            return quote.isVirtual() || stepNavigator.isProcessed('shipping');
        },

        toggleVisibility: function () {
            this.collapsed(!this.collapsed());
        }
    });
});
