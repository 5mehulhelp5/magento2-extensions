define([
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'MageWorx_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Magento_Checkout/js/model/shipping-save-processor',
        'MageWorx_Checkout/js/model/shipping-rate-processor/general',
        'rjsResolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'Magento_Catalog/js/price-utils',
        'mage/translate'
    ], function (
    $,
    _,
    Component,
    ko,
    customer,
    addressList,
    addressConverter,
    quote,
    selectShippingAddress,
    formPopUpState,
    shippingService,
    selectShippingMethodAction,
    setShippingInformationAction,
    stepNavigator,
    additionalValidators,
    shippingRateService,
    shippingSaveProcessor,
    shippingRateProcessorGeneral,
    resolver,
    checkoutData,
    registry,
    priceUtils,
    $t
    ) {
        'use strict';

        /**
         * Add shipping methods to collection;
         */
        shippingService.setShippingRates(window.checkoutConfig.shippingMethods);

        return Component.extend({
            defaults: {
                template: 'MageWorx_Checkout/container/shipping-methods',
                shippingMethodListTemplate: 'MageWorx_Checkout/shipping-method/list',
                shippingMethodItemTemplate: 'MageWorx_Checkout/shipping-method/item',
                label: $t('Shipping Method'),
                sectionIndex: 2,
                imports: {
                    shippingAddress: 'checkout.steps.shipping-step.shippingAddress',
                    address: '${ $.parentName }.shippingAddress'
                }
            },
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length === 0,
            isNewAddressAdded: ko.observable(false),
            saveInAddressBook: 1,
            quoteIsVirtual: quote.isVirtual(),
            currentMethod: null,
            isLoading: shippingService.isLoading,
            rates: shippingService.getShippingRates(),
            isDisplayShippingPriceExclTax: window.checkoutConfig.isDisplayShippingPriceExclTax,
            isDisplayShippingBothPrices: window.checkoutConfig.isDisplayShippingBothPrices,
            shippingRateCountry: function () {
                let shippingAddress = quote.shippingAddress();
                if (shippingAddress.country === 'US'){
                    return 'United States';
                }

                return 'International';
            },
            isSelected: ko.computed(function () {
                    if (quote.shippingMethod()) {
                        return quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code;
                    } else if (window.checkoutConfig.defaultShippingMethod) {
                        return window.checkoutConfig.defaultShippingMethod;
                    } else {
                        return null;
                    }
                }
            ),

            observableProperties: [
                'shippingAddress',
                'address',
                'label',
                'sectionIndex'
            ],

            initialize: function () {
                this._super();

                stepNavigator.steps.removeAll();
                additionalValidators.registerValidator(this);
                resolver(this.afterResolveDocument.bind(this));

                return this;
            },

            initObservable: function () {
                this._super();

                this.observe(this.observableProperties);

                if (window.checkoutConfig.labels.shipping_methods) {
                    this.label(window.checkoutConfig.labels.shipping_methods);
                }

                var self = this;
                registry.async('checkout.steps.shipping-step.shippingAddress')(function (shippingAddress) {
                    self.shippingAddress(shippingAddress);
                });

                quote.shippingMethod.subscribe(function (newValue) {
                    var isMethodChange = ($.type(this.currentMethod) !== 'object') ?
                        true :
                        this.currentMethod.method_code;

                    if ($.type(newValue) === 'object' && (isMethodChange !== newValue.method_code)) {
                        let shippingAddress = quote.shippingAddress();
                        if (shippingAddress.country == 'US'){
                            newValue.country = 'United States';
                        }else{
                            newValue.country = 'International';
                        }
                        this.currentMethod = newValue;
                        setShippingInformationAction();
                    } else if (shippingRateService.isAddressChange) {
                        shippingRateService.isAddressChange = false;
                    }
                }, this);

                // Removes error message whenever a shipping method is set
                quote.shippingMethod.subscribe(function (value) {
                    this.errorValidationMessage(false);
                }, this);

                return this;
            },

            /**
             * Make specified or default shipping method selected after component loaded
             *
             * @param code
             * @param force
             */
            selectSpecificShippingMethod: function (code, force) {
                if (!code) {
                    code = window.checkoutConfig.defaultShippingMethod;
                }

                if (!code) {
                    return;
                }

                if (!quote.shippingMethod() || force) {
                    var rates = shippingService.getShippingRates() ? shippingService.getShippingRates()() : null;
                    if (!rates) {
                        return;
                    }

                    for (var rateKey in rates) {
                        if (!rates.hasOwnProperty(rateKey)) {
                            continue;
                        }
                        var rate = rates[rateKey],
                            key = rate.carrier_code + '_' + rate.method_code;
                        if (key === window.checkoutConfig.defaultShippingMethod) {
                            this.selectShippingMethod(rate);
                            break;
                        }
                    }
                }

                return;
            },

            /**
             * Process something after component loading
             */
            afterResolveDocument: function () {
                this.selectSpecificShippingMethod(false, false);

                var self = this;
                shippingService.getShippingRates().subscribe(function (rates) {
                    self.selectSpecificShippingMethod(false, false);
                });

                if (quote.shippingAddress() && quote.shippingAddress().customerAddressId) {
                    shippingRateProcessorGeneral.getRates(quote.shippingAddress());
                }
            },

            validate: function () {
                if (quote.isVirtual()) {
                    return true;
                }

                var shippingMethodValidationResult = true;

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage('Please specify a shipping method.');

                    shippingMethodValidationResult = false;
                }

                return shippingMethodValidationResult;
            },

            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code']);

                return true;
            },

            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    quote.billingAddress(null);
                    // checkoutDataResolver.resolveBillingAddress();
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
            },

            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn(),
                    field,
                    country = registry.get(this.parentName + '.shippingAddress.shipping-address-fieldset.country_id'),
                    countryIndexedOptions = country.indexedOptions,
                    option = countryIndexedOptions[quote.shippingAddress().countryId],
                    messageContainer = registry.get('checkout.errors').messageContainer;

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage(
                        $t('The shipping method is missing. Select the shipping method and try again.')
                    );

                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.triggerShippingDataValidateEvent();

                    if (emailValidationResult &&
                        this.source.get('params.invalid') ||
                        !quote.shippingMethod()['method_code'] ||
                        !quote.shippingMethod()['carrier_code']
                    ) {
                        this.focusInvalid();

                        return false;
                    }

                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (field in addressData) {
                        if (addressData.hasOwnProperty(field) &&  //eslint-disable-line max-depth
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress['save_in_address_book'] = 1;
                    }
                    selectShippingAddress(shippingAddress);
                } else if (customer.isLoggedIn() &&
                    option &&
                    option['is_region_required'] &&
                    !quote.shippingAddress().region
                ) {
                    messageContainer.addErrorMessage({
                        message: $t('Please specify a regionId in shipping address.')
                    });

                    return false;
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();

                    return false;
                }

                return true;
            },

            /**
             * Trigger Shipping data Validate Event.
             */
            triggerShippingDataValidateEvent: function () {
                this.source.trigger('shippingAddress.data.validate');

                if (this.source.get('shippingAddress.custom_attributes')) {
                    this.source.trigger('shippingAddress.custom_attributes.data.validate');
                }
            },
            getFormattedPrice: function (price) {
                if (price < 0.001) {
                    return $t('Free');
                }

                return priceUtils.formatPrice(price, quote.getPriceFormat());
            }
        });
    }
);
