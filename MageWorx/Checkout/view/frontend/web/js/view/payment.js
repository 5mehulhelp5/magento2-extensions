define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (
    $,
    _,
    Component,
    ko,
    quote,
    stepNavigator,
    paymentService,
    methodConverter,
    getPaymentInformation,
    checkoutDataResolver,
    additionalValidators,
    customerData,
    $t
) {
    'use strict';

    /** Set payment methods to collection */
    paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/container/payment',
            label: $t('Payment Method'),
            sectionIndex: 3
        },
        isLoading: false,
        errorValidationMessage: ko.observable(false),
        isVisible: ko.observable(quote.isVirtual()),
        quoteIsVirtual: quote.isVirtual(),
        isPaymentMethodsAvailable: ko.computed(function () {
            return paymentService.getAvailablePaymentMethods().length > 0;
        }),

        observableProperties: [
            'label',
            'sectionIndex'
        ],

        initialize: function () {
            var self = this;

            this._super();

            stepNavigator.steps.removeAll();

            additionalValidators.registerValidator(this);

            quote.paymentMethod.subscribe(function (value) {
                self.errorValidationMessage(false);
            });

            if ($('.page.messages')) {
                setTimeout(function () {
                    $('.page.messages').remove()
                }, 8000);
            }

            this.customer = customerData.get('cart');

            //setTimeout(function () {
                getPaymentInformation();
            //}, 1000);

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);

            if (window.checkoutConfig.labels.billing_methods) {
                this.label(window.checkoutConfig.labels.billing_methods);
            }

            if (quote.isVirtual()) {
                this.sectionIndex(2);
            }

            return this;
        },

        validate: function () {
            if (!quote.paymentMethod()) {
                this.errorValidationMessage($.mage.__('Please specify a payment method.'));

                return false;
            }

            return true;
        },

        /**
         * @return {*}
         */
        getFormKey: function () {
            return window.checkoutConfig.formKey;
        }
    });
});
