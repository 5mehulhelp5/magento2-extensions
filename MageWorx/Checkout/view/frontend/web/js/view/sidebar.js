define([
    'Magento_Checkout/js/view/sidebar',
    'mage/translate'
], function (
    Component,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            label: $t('RESERVATION SUMMARY')
        },

        observableProperties: [
            'label'
        ],

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);

            if (window.checkoutConfig.labels.order_summary) {
                this.label(window.checkoutConfig.labels.order_summary);
            }

            return this;
        }
    });
});
