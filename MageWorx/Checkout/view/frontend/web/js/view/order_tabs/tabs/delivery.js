define([
    'uiComponent',
    'uiRegistry',
    'MageWorx_Checkout/js/action/tabs/delivery'
], function (
    Component,
    registry,
    deliveryTabProcess
) {
    'use strict';

    return Component.extend({
        defaults: {
            visible: true,
            previousVisibility: false
        },

        observableProperties: [
            'visible',
            'previousVisibility'
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
            this.visible.subscribe(function (oldValue) {
                this.previousVisibility(oldValue);
            }, this, 'beforeChange');
        },

        process: function (flag) {
            deliveryTabProcess(flag);
        }
    });
});
