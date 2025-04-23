/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_CheckoutAgreements/js/view/checkout-agreements',
    'mage/translate'
], function (Component, $t) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        agreementManualMode = 1,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {};

    return Component.extend({
        defaults: {
            agreementsMessage: $t('By clicking \'Agree & place order\' you agree to our Terms & Conditions and Privacy Policy')
        },

        observableProperties: [
            'agreementsMessage'
        ],

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);

            return this;
        }
    });
});
