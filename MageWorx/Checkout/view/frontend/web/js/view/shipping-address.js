define([
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'MageWorx_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Magento_Checkout/js/model/shipping-save-processor',
        'MageWorx_Checkout/js/model/shipping-save-processor/general',
        'MageWorx_Checkout/js/model/shipping-rate-processor/general',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/customer-data',
        'uiRegistry',
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
    createShippingAddress,
    selectShippingAddress,
    shippingRatesValidator,
    formPopUpState,
    shippingService,
    selectShippingMethodAction,
    rateRegistry,
    setShippingInformationAction,
    stepNavigator,
    additionalValidators,
    shippingRateService,
    shippingSaveProcessor,
    shippingSaveProcessorGeneral,
    shippingRateProcessorGeneral,
    checkoutDataResolver,
    checkoutData,
    customerData,
    registry,
    $t
    ) {
        'use strict';

        /**
         * Register own shipping rate processor;
         */
        shippingSaveProcessor.registerProcessor('mageworx_checkout', shippingSaveProcessorGeneral);
        shippingSaveProcessor.registerProcessor('default', shippingSaveProcessorGeneral);

        shippingRateService.registerProcessor('default', shippingRateProcessorGeneral);
        shippingRateService.registerProcessor('mageworx_checkout', shippingRateProcessorGeneral);

        /**
         * Add shipping methods to collection;
         */
        shippingService.setShippingRates(window.checkoutConfig.shippingMethods);

        var countryData = customerData.get('directory-data');

        return Component.extend({
            defaults: {
                template: 'MageWorx_Checkout/container/address/shipping-address',
                shippingFormTemplate: 'MageWorx_Checkout/shipping-address/form',
                shippingMethodListTemplate: 'Magento_Checkout/shipping-address/shipping-method-list',
                shippingMethodItemTemplate: 'Magento_Checkout/shipping-address/shipping-method-item',
                addressListPopUpTemplate: '',
                isAddressDetailsVisible: true,
                selectAddressPopUpVisible: false,
                newAddressPopUpTemplate: 'MageWorx_Checkout/shipping-address/new-address-popup',
                shippingFormVisible: true,
                label: $t('Shipping Address'),
                currentShippingAddress: {},
                sectionIndex: 1,
                saveInAddressBook: 1,
                exports: {
                    saveInAddressBook: '${ $.provider }.shippingAddress:save_in_address_book'
                }
            },
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length === 0,
            isNewAddressAdded: ko.observable(false),
            quoteIsVirtual: quote.isVirtual(),
            currentMethod: null,

            /**
             * Shipping Method View
             */
            rates: shippingService.getShippingRates(),
            isLoading: shippingService.isLoading,

            isSelectAddressAvailable: ko.computed(function () {
                return addressList().length > 0;
            }),

            observableProperties: [
                'currentShippingAddress',
                'isAddressDetailsVisible',
                'selectAddressPopUpVisible',
                'shippingFormVisible',
                'activeTab',
                'label',
                'sectionIndex',
                'saveInAddressBook'
            ],

            initialize: function () {
                var self = this,
                    hasNewAddress,
                    fieldsetName = [
                        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset',
                        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.firstname-lastname-group.field-group',
                        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id-region-region_id-group.field-group',
                        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city-postcode-group.field-group',
                        'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.company-telephone-group.field-group'
                    ];

                this._super();

                // resolve shipping address on init
                checkoutDataResolver.resolveShippingAddress();
                // Set shipping address by default
                if (quote.shippingAddress()) {
                    this.currentShippingAddress(quote.shippingAddress());
                }

                hasNewAddress = addressList.some(function (address) {
                    return address.getType() == 'new-customer-address'; //eslint-disable-line eqeqeq
                });

                this.isNewAddressAdded(hasNewAddress);

                quote.shippingMethod.subscribe(function () {
                    self.errorValidationMessage(false);
                });

                quote.shippingAddress.subscribe(function (address) {
                    address['save_in_address_book'] = self.saveInAddressBook() ? 1 : 0;
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData(),
                        timeoutId;

                    if (shippingAddressData &&
                        shippingAddressData['city'] !== "" &&
                        shippingAddressData['region'] !== "" &&
                        shippingAddressData['telephone'] !== ""
                    ) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                    checkoutProvider.on('shippingAddress', function (shippingAddrData) {
                        if (shippingAddrData && !customer.isLoggedIn) {
                            checkoutData.setShippingAddressFromData(shippingAddrData);
                            clearTimeout(timeoutId);
                            timeoutId = setTimeout(function (addressFlat) {
                                selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(addressFlat));
                                shippingSaveProcessor.saveShippingInformation('mageworx_checkout');
                            }, 1500, shippingAddrData);
                        }

                    });
                    fieldsetName.forEach(function (currentFieldset) {
                        shippingRatesValidator.initFields(currentFieldset);
                    });
                });

                stepNavigator.steps.removeAll();
                additionalValidators.registerValidator(this);

                return this;
            },

            initObservable: function () {
                this._super();

                this.observe(this.observableProperties);

                if (window.checkoutConfig.labels.shipping_address) {
                    this.label(window.checkoutConfig.labels.shipping_address);
                }

                quote.shippingAddress.subscribe(function (currentShippingAddress) {
                    this.currentShippingAddress(currentShippingAddress);
                }, this);

                return this;
            },

            validate: function () {
                if (quote.isVirtual()) {
                    return true;
                }

                var shippingAddressValidationResult = true,
                    loginFormSelector = 'form[data-role=email-with-possible-login]:visible',
                    emailValidationResult = customer.isLoggedIn(),
                    result = false;

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    // Validate pickup form if active
                    if (this.activeTab() === 'pickup') {
                        shippingAddressValidationResult = registry.get('index = pickupInformation').validate();
                    } else {
                        // Else do regoular validation
                        this.source.set('params.invalid', false);
                        this.source.trigger('shippingAddress.data.validate');

                        if (this.source.get('shippingAddress.custom_attributes')) {
                            this.source.trigger('shippingAddress.custom_attributes.data.validate');
                        }

                        if (this.source.get('params.invalid')) {
                            shippingAddressValidationResult = false;
                        }
                    }
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();
                }

                result = shippingAddressValidationResult && emailValidationResult;

                return result;
            },

            saveShippingAddress: function () {
                var shippingAddress = quote.shippingAddress(),
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                //Copy form data to quote shipping address object
                for (var field in addressData) {
                    if (addressData.hasOwnProperty(field) &&
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' && !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress.save_in_address_book = 1;
                }

                selectShippingAddress(shippingAddress);
            },

            saveNewAddress: function () {
                this.source.set('params.invalid', false);
                if (this.source.get('shippingAddress.custom_attributes')) {
                    this.source.trigger('shippingAddress.custom_attributes.data.validate');
                }

                // A _super part start
                if (!this.source.get('params.invalid')) {
                    var addressData,
                        newShippingAddress;

                    this.source.set('params.invalid', false);
                    this.triggerShippingDataValidateEvent();

                    if (!this.source.get('params.invalid')) {
                        addressData = this.source.get('shippingAddress');
                        // if user clicked the checkbox, its value is true or false. Need to convert.
                        addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;

                        // New address must be selected as a shipping address
                        newShippingAddress = createShippingAddress(addressData);
                        selectShippingAddress(newShippingAddress);
                        checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                        checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                        this.isFormPopUpVisible(false);
                        this.isNewAddressAdded(true);
                    }
                }
                // _super part end
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

            toggleSelectAddressPopUp: function () {
                this.selectAddressPopUpVisible(!this.selectAddressPopUpVisible());
            },

            closeSelectAddressPopUp: function () {
                this.selectAddressPopUpVisible(false);
            },

            getAddressTemplate: function () {
                return 'MageWorx_Checkout/container/address/shipping-address';
            },

            getAddressListPopUpTemplate: function () {
                return this.addressListPopUpTemplate || 'MageWorx_Checkout/shipping-address/pop-up';
            },

            getNewAddressPopUpTemplate: function () {
                return this.newAddressPopUpTemplate || 'MageWorx_Checkout/shipping-address/new-address-popup';
            },

            /**
             * @param {Number} countryId
             * @return {*}
             */
            getCountryName: function (countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
            },

            /**
             * Show address form popup
             */
            showFormPopUp: function () {
                this.isFormPopUpVisible(true);
            },

            cancelNewAddress: function () {
                this.isFormPopUpVisible(false);
            },

            submitNewAddress: function () {
                this.saveNewAddress();
            }
        });
    }
);
