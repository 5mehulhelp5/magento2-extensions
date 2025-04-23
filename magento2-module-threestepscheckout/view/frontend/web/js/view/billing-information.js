/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'Magento_Checkout/js/model/payment/method-list'
], function (
    $,
    ko,
    Component,
    registry,
    $t,
    paymentService,
    quote,
    stepNavigator,
    sidebarModel,
    paymentMethodList
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MB_ThreeStepsCheckout/billing-information'
        },
        paymentMethodTitle: ko.observable(''),
        initialize: function() {
            this._super();
            var self = this;
            quote.paymentMethod.subscribe(this.setPaymentMethodTitle.bind(this));
            paymentMethodList.subscribe(function (methodList) {
                self.setPaymentMethodTitle(quote.paymentMethod());
            })
        },

        setPaymentMethodTitle: function (paymentMethod) {
            if (!paymentMethod) {
                this.paymentMethodTitle('');
                return;
            }
            if (paymentMethod.method.indexOf('braintree_cc_vault_') === 0) {
                // Fix for "fake" Braintree CC Vault methods
                var details = window.checkoutConfig.payment.vault[paymentMethod.method].config.details;
                this.paymentMethodTitle(
                    $t('Saved Credit Card') +
                    '<br />' + $t('ending') + ' ' + details.maskedCC +
                    ', ' + $t('expires') + ' ' + details.expirationDate
                );
                return;
            }
            var selectedPaymentMethod = paymentService.getAvailablePaymentMethods().find(
                function(availablePaymentMethod) {
                    return availablePaymentMethod.method === paymentMethod.method;
                }
            );
            if (selectedPaymentMethod) {
                this.paymentMethodTitle(selectedPaymentMethod.title);
            } else {
                this.paymentMethodTitle('');
            }
        },

        /**
         * @return {Boolean}
         */
        isVisible: function () {
            return stepNavigator.isProcessed('payment');
        },

        /**
         * Back step.
         */
        back: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('payment');
        },

        /**
         * Back to shipping method.
         */
        backToPaymentMethod: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('payment', 'checkout-payment-method-load');
        }
    });
});
