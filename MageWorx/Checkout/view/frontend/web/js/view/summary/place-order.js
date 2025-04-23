/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Checkout/js/model/payment/additional-validators',
    'MageWorx_Checkout/js/model/shipping-save-processor/general',
    'uiRegistry',
    'Magento_Checkout/js/model/address-converter',
    'MageWorx_Checkout/js/action/select-shipping-address',
    'MageWorx_Checkout/js/action/select-billing-address',
    'Magento_Customer/js/model/customer',
    'MageWorx_Checkout/js/view/billing-address',
    'jquery',
    'mage/translate'
], function (
    Component,
    placeOrderAction,
    quote,
    redirectOnSuccessAction,
    additionalValidators,
    shippingSaveProcessorGeneral,
    uiRegistry,
    addressConverter,
    selectShippingAddress,
    selectBillingAddressAction,
    customer,
    billingAddressView,
    jQuery,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/place-order',
            selectedPaymentMethod: null,
            label: $t('Agree & Place Order'),
            visible: true,
            notApplicableMethods: [
                'braintree_paypal'
            ]
        },

        observableProperties: [
            'label',
            'visible'
        ],

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);

            if (window.checkoutConfig.labels.place_order_button) {
                this.label(window.checkoutConfig.labels.place_order_button);
            }

            this.initSubscribers();

            return this;
        },

        initSubscribers: function () {
            let self = this;

            // Show/hide the place order button for specific payments
            quote.paymentMethod.subscribe(function (value) {
                if (value && value.method
                    && self.notApplicableMethods && self.notApplicableMethods.includes(value.method)
                ) {
                    self.visible(false);
                } else {
                    self.visible(true);
                }
            });
        },

        setSelectedPaymentMethod: function (methodInstance) {
            this.selectedPaymentMethod = methodInstance;
        },

        getSelectedPaymentMethod: function () {
            let paymentMethodData = quote.paymentMethod(),
                paymentMethodCode = '',
                paymentMethodInstance;

            if (paymentMethodData && paymentMethodData.method) {
                paymentMethodCode = paymentMethodData.method;
                paymentMethodInstance = uiRegistry.get('ns = checkout, index = ' + paymentMethodCode + ' , parent = checkout.steps.billing-step.payment.payments-list');

                this.selectedPaymentMethod = paymentMethodInstance;
            }

            return this.selectedPaymentMethod;
        },

        /**
         * Place order action
         */
        placeOrder: function () {
            var self = this;
            window.checkoutConfig.isPlacingOrder = true;

            if (this.getSelectedPaymentMethod()) {
                function placeOrderThroughPaymentMethod() {
                    if (typeof self.getSelectedPaymentMethod().beforePlaceOrder == 'function') {
                        self.getSelectedPaymentMethod().beforePlaceOrder();
                    } else if (self.getSelectedPaymentMethod().component == 'Ebizmarts_SagePaySuite/js/view/payment/method-renderer/pi-method'
                               && self.getSelectedPaymentMethod().dropInEnabled()) {
                        self.getSelectedPaymentMethod().tokenise();
                    } else if (self.getSelectedPaymentMethod().component == 'Magento_Braintree/js/view/payment/method-renderer/cc-form') {
                        self.getSelectedPaymentMethod().placeOrderClick();
                    } else if (self.getSelectedPaymentMethod().component == 'PayPal_Braintree/js/view/payment/method-renderer/hosted-fields') {
                        self.getSelectedPaymentMethod().isProcessing = false;
                        self.getSelectedPaymentMethod().placeOrderClick();
                    } else if (self.getSelectedPaymentMethod().component == 'Magento_Paypal/js/view/payment/method-renderer/paypal-express') {
                        self.getSelectedPaymentMethod().continueToPayPal();
                    } else if (self.getSelectedPaymentMethod().component == 'Swarming_SubscribePro/js/view/payment/method-renderer/cc-form') {
                        self.getSelectedPaymentMethod().startPlaceOrder();
                    } else if (self.getSelectedPaymentMethod().component == 'Klarna_Kp/js/view/payments/kp') {
                        self.getSelectedPaymentMethod().authorize();
                    } else if (typeof self.getSelectedPaymentMethod().placeOrder == 'function') {
                        if (self.getSelectedPaymentMethod().component == 'StripeIntegration_Payments/js/view/payment/method-renderer/stripe_payments') {
                            self.getSelectedPaymentMethod().useQuoteBillingAddress(true);
                        }
                        self.getSelectedPaymentMethod().placeOrder();
                    } else {
                        placeOrderAction(self.getCleanPaymentMethodData()).done(
                            function (response) {
                                redirectOnSuccessAction.execute();
                            }
                        )
                    }
                }

                if (additionalValidators.validate()) {
                    if (quote.isVirtual()) {
                        placeOrderThroughPaymentMethod();
                    } else {
                        if (!customer.isLoggedIn() && quote.shippingMethod() && quote.shippingMethod().method_code !== 'mageworxpickup') {
                            //save all shipping address fields from form before place order
                            var addressFlat = uiRegistry.get('checkoutProvider').shippingAddress;
                            var address = addressConverter.formAddressDataToQuoteAddress(addressFlat);
                            selectShippingAddress(address);
                            //save all billing address fields from form before place order
                            if (jQuery('#billing-address-different-share').is(":checked")) {
                                addressFlat = uiRegistry.get('checkoutProvider').billingAddress;
                                address = addressConverter.formAddressDataToQuoteAddress(addressFlat);
                                selectBillingAddressAction(address);
                            }
                        }

                        jQuery('body').trigger('processStart');
                        if (self.getSelectedPaymentMethod()
                            && self.getSelectedPaymentMethod().component
                            === 'StripeIntegration_Payments/js/view/payment/method-renderer/stripe_payments')
                        {
                            // Do not reload Stripe form if it is already initialized
                            self.getSelectedPaymentMethod().isInitializing(true);
                        }
                        shippingSaveProcessorGeneral.saveShippingInformation('mageworx_checkout').done(function (response) {
                            jQuery('body').trigger('processStop');
                            placeOrderThroughPaymentMethod();
                        }).fail(function (response) {
                            additionalValidators.validate();
                            jQuery('body').trigger('processStop');
                        });
                    }
                }
            } else {
                additionalValidators.validate();
            }

            window.checkoutConfig.isPlacingOrder = false;
        },

        /**
         * Get clean payment method data, without properties without accessors in Payment interface on backend
         * @returns {*}
         */
        getCleanPaymentMethodData: function () {
            var paymentMethodData = quote.getPaymentMethod()();
            delete paymentMethodData.__disableTmpl;
            delete paymentMethodData.title;

            return paymentMethodData;
        }
    });
});
