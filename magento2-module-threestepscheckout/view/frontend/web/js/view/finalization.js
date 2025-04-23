/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'uiRegistry',
    'underscore',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate'
],function ($, ko, Component, registry, _, getPaymentInformation, checkoutData, quote, stepNavigator, $t) {
    'use strict';

    function getSelectedPaymentMethodComponent() {
        if (quote.paymentMethod()) {
            return registry.get('checkout.steps.billing-step.payment.payments-list.' + quote.paymentMethod().method);
        } else {
            return null;
        }
    }

    return Component.extend({
        defaults: {
            template: 'MB_ThreeStepsCheckout/finalization'
        },
        isVisible: ko.observable(false),
        errorValidationMessage: ko.observable(false),
        initialize: function () {
            this._super();
            // register your step
            stepNavigator.registerStep(
                'finalization',
                null,
                $t('Finalization'),
                this.isVisible,
                _.bind(this.navigate, this),
                30
            );
            if (quote.isVirtual()) {
                $("body").addClass('tsc-virtual-quote');
            }
            return this;
        },
        navigate: function () {
            if (!this.hasPaymentMethod()) {
                this.isVisible(false);
                stepNavigator.setHash('payment');
            } else {
                var self = this;
                getPaymentInformation().done(function (data) {
                    self.isVisible(true);
                });
            }
        },
        hasPaymentMethod: function() {
            return Boolean(checkoutData.getSelectedPaymentMethod());
        },
        placeOrder: function() {
            this.errorValidationMessage(false);
            var selectedPaymentMethodComponent = getSelectedPaymentMethodComponent();
            if (selectedPaymentMethodComponent) {
                selectedPaymentMethodComponent.placeOrder();
            }
        }
    });
});
