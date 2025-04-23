define([
    'uiComponent',
    'MageWorx_Checkout/js/action/select-tab',
    'uiRegistry'
], function (
    Component,
    selectTabAction,
    registry
) {
    'use strict';

    return Component.extend({
        defaults: {
            selectedTabIndex: 'delivery',
            exports: {
                'selectedTabIndex': 'checkout.steps.shipping-step.shippingAddress:activeTab'
            }
        },

        observableProperties: [
            'selectedTabIndex'
        ],

        initialize: function () {
            this._super();

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);
            this.initSubscribers();
            return this;
        },

        initSubscribers: function () {
            return this;
        },

        selectTab: function (tab) {
            registry.get('ns = order_tabs, index = tabs', function (tabs) {
                if (tabs.selectedTabIndex() !== tab.index) {
                    var prevTab;
                    if (tabs.selectedTabIndex()) {
                        prevTab = registry.get('ns = order_tabs, index = ' + tabs.selectedTabIndex());
                    }
                    tabs.selectedTabIndex(tab.index);
                    selectTabAction(tab, prevTab);
                }
            });

            return true;
        }
    });
});
