/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'ko',
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Customer/js/model/customer',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'MB_ThreeStepsCheckout/js/model/payment-validator-list'
],function (
    Component,
    ko,
    $,
    registry,
    setPaymentInformation,
    quote,
    stepNavigator,
    customer,
    globalMessageList,
    $t,
    paymentValidatorList
) {
    'use strict';

    function getBillingAddressComponent() {
        return registry.get('checkout.steps.billing-step.payment.beforeMethods.billing-address-form');
    }

    function getSelectedPaymentMethodComponent() {
        if (quote.paymentMethod()) {
            return registry.get('checkout.steps.billing-step.payment.payments-list.' + quote.paymentMethod().method);
        } else {
            return null;
        }
    }

    function validatePayment(paymentComponent) {
        if (typeof paymentValidatorList[paymentComponent.getCode()] === 'undefined') {
            return true;
        }
        return paymentValidatorList[paymentComponent.getCode()](paymentComponent);
    }

    return Component.extend({
        initialize: function () {
            var self = this;
            this._super();
            quote.paymentMethod.subscribe(function () {
                self.errorValidationMessage(false);
            });
            quote.billingAddress.subscribe(function () {
                self.errorValidationMessage(false);
            });
        },
        errorValidationMessage: ko.observable(false),
        setBillingInformation: function() {
            // @todo Save payment method?
            if (this.validateBillingInformation()) {
                var selectedPaymentMethodComponent = getSelectedPaymentMethodComponent();
                if (selectedPaymentMethodComponent) {
                    if (validatePayment(selectedPaymentMethodComponent)) {
                        setPaymentInformation(globalMessageList, selectedPaymentMethodComponent.getData())
                            .done(function() {
                                stepNavigator.next();
                            });
                    }
                }
            }
        },
        validateBillingInformation: function() {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                emailValidationResult = customer.isLoggedIn();
            if (!getBillingAddressComponent().isAddressDetailsVisible()) {
                this.errorValidationMessage(
                    $t('First you need to save billing address.')
                );
                return false;
            }
            if (!quote.paymentMethod() || !$('#' + quote.paymentMethod().method).is(":checked")) {
                this.errorValidationMessage(
                    $t('The payment method is missing. Select the payment method and try again.')
                );
                return false;
            }
            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
            }
            if (!emailValidationResult) {
                $(loginFormSelector + ' input[name=username]').focus();
                return false;
            }
            return true;
        }
    });
});
