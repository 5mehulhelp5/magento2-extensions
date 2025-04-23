define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/billing-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/create-billing-address',
        'MageWorx_Checkout/js/action/select-billing-address',
        'MageWorx_Checkout/js/view/billing-address/list',
        'Magento_Customer/js/model/address-list',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'MageWorx_Checkout/js/model/billing-address/form-popup-state',
        'uiRegistry',
        'mage/translate'
    ],
    function ($,
              ko,
              Component,
              quote,
              checkoutData,
              createBillingAddress,
              selectBillingAddress,
              billingAddressList,
              addressList,
              customer,
              setBillingAddressAction,
              addressConverter,
              additionalValidators,
              globalMessageList,
              checkoutDataResolver,
              formPopUpState,
              registry,
              $t
    ) {
        'use strict';

        var observedElements = [],
            addressOptions = addressList().filter(function (address) {
                return address.getType() === 'customer-address';
            });

        return Component.extend({
            defaults: {
                template: 'MageWorx_Checkout/billing-address/main',
                formTemplate: 'MageWorx_Checkout/billing-address/form',
                isAddressSameAsShipping: true,
                isAddressDifferent: false,
                isNewAddressAdded: false,
                selectAddressPopUpVisible: false,
                addressListPopUpTemplate: 'MageWorx_Checkout/billing-address/address-list-popup',
                newAddressPopUpTemplate: 'MageWorx_Checkout/billing-address/new-address-popup',
                detailsTemplate: 'MageWorx_Checkout/billing-address/details',
                isAddressFormVisible: false,
                visible: true,
                label: $t('Billing Address'),
                sectionIndex: 1,
                fieldsInitialized: false,
                saveInAddressBook: 1,
                exports: {
                    saveInAddressBook: '${ $.provider }.billingAddress:save_in_address_book'
                }
            },

            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            quoteIsVirtual: quote.isVirtual(),

            canUseShippingAddress: ko.computed(function () {
                return quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
            }),

            isSelectAddressAvailable: ko.computed(function () {
                return addressList().length > 0;
            }),

            observableProperties: [
                'addressOptions',
                'isAddressDifferent',
                'isNewAddressAdded',
                'selectAddressPopUpVisible',
                'visible',
                'label',
                'sectionIndex'
            ],

            currentBillingAddress: ko.computed(function () {
                return quote.billingAddress();
            }),

            /**
             * @return {exports}
             */
            initialize: function () {
                var self = this;

                if (!quote.isVirtual() && !quote.shippingAddress()) {
                    checkoutDataResolver.resolveShippingAddress();
                }

                this._super();

                additionalValidators.registerValidator(this);

                // Set default billing address for logged in customers
                if (this.isCustomerLoggedIn) {
                    _.each(addressList, function (address) {
                        if (address.isDefaultBilling()) {
                            quote.billingAddress(address);
                            if (address.getCacheKey() !== quote.shippingAddress().getCacheKey()) {
                                self.useDifferentAddress();
                            }
                        }
                    });
                }

                if (checkoutData.getBillingAddressFromData()) {
                    checkoutData.setNewCustomerBillingAddress(checkoutData.getBillingAddressFromData());
                }

                // Resolve billing address before processing any methods
                checkoutDataResolver.resolveBillingAddress();

                this.initSubscribers();

                this.isAddressFormVisible(quote.isVirtual() && (!customer.isLoggedIn() || !addressOptions.length));

                if (quote.shippingAddress() && !quote.isVirtual()) {
                    this.isAddressDifferent(false);
                    this.isAddressSameAsShipping(true);
                    selectBillingAddress(quote.shippingAddress());
                }

                return this;
            },

            initSubscribers: function () {
                var self = this._super() || this,
                    billingAddressData;

                // Hide form only when checkbox "I have different billing address" is not checked
                this.isAddressDetailsVisible.subscribe(function (value) {
                    if (Boolean(value) === true && self.isAddressDifferent() === true) {
                        self.isAddressDetailsVisible(false);
                    }
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    billingAddressData = checkoutData.getBillingAddressFromData();
                    checkoutProvider.on('billingAddress', function (billingAddressData) {
                        checkoutData.setBillingAddressFromData(billingAddressData);
                        if (quote.shippingAddress() && !quote.isVirtual()) {
                            var address = addressConverter.formAddressDataToQuoteAddress(
                                billingAddressData
                            );
                            if (!self.isAddressSameAsShipping()) {
                                selectBillingAddress(address);
                            }
                        }
                    });

                    /**
                     * If there was no billing address selected
                     * we must set actual billing address data as new billing address
                     */
                    if (billingAddressData && !(quote.billingAddress() && quote.billingAddress().customerAddressId)) {
                        checkoutProvider.set(
                            'billingAddress',
                            $.extend({}, checkoutProvider.get('billingAddress'), billingAddressData)
                        );
                    }
                });

                quote.shippingAddress.subscribe(function (newAddress) {
                    if (self.isAddressSameAsShipping()) {
                        selectBillingAddress(newAddress);
                    }
                });

                this.isAddressDifferent.subscribe(function (value) {
                    self.isAddressSameAsShipping(!value);
                });

                this.selectedAddress.subscribe(function (value) {
                    if (!value) {
                        self.isAddressFormVisible(true);
                    } else {
                        if (!value.customerAddressId) {
                            self.isAddressFormVisible(true);
                        } else {
                            self.isAddressFormVisible(false);
                        }
                    }
                });

                quote.billingAddress.subscribe(function (address) {
                    address['save_in_address_book'] = self.saveInAddressBook() ? 1 : 0;
                });
            },

            /**
             *
             * @returns {exports}
             */
            initObservable: function () {
                this._super();
                this.observe(this.observableProperties);

                if (window.checkoutConfig.labels.billing_address) {
                    this.label(window.checkoutConfig.labels.billing_address);
                }

                return this;
            },

            useDifferentAddress: function () {
                if (this.isAddressDifferent()) {
                    this.isAddressFormVisible(true);
                    this.updateAddress();
                } else {
                    this.isAddressFormVisible(false);
                    this.isAddressSameAsShipping(true);
                    this.useShippingAddress();
                }

                return true;
            },

            /**
             * @return {Boolean}
             */
            useShippingAddress: function () {
                if (!quote.shippingAddress()) {
                    return false;
                }

                if (this.isAddressSameAsShipping()) {
                    selectBillingAddress(quote.shippingAddress());
                    checkoutData.setSelectedBillingAddress(null);
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                } else {
                    this.updateAddress();
                }

                return true;
            },

            /**
             *
             * @param address
             */
            onAddressChange: function (address) {
                this._super(address);
                if (!this.isAddressSameAsShipping()) {
                    this.updateAddress();
                }
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                // Important: Trigger address select
                this.selectedAddress(quote.billingAddress());

                if (this.selectedAddress() && !this.isAddressFormVisible()) {
                    newBillingAddress = createBillingAddress(this.selectedAddress());

                    var self = this,
                        selectedAddress = {
                            customerAddressId: this.selectedAddress().customerAddressId,
                            customerId: this.selectedAddress().customerId,
                            sameAsBilling: this.selectedAddress().sameAsBilling,
                            regionId: this.selectedAddress().regionId,
                            getAddressInline: function () {
                                return self.selectedAddress().getAddressInline();
                            }
                        };

                    selectBillingAddress($.extend(newBillingAddress, selectedAddress));
                    checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                } else {
                    var addressData = this.source.get('billingAddress'),
                        newBillingAddress;

                    if (customer.isLoggedIn() && this.customerHasAddresses) {
                        _.each(addressList(), function (address) {
                            if (address.isDefaultBilling()) {
                                newBillingAddress = address;
                            }
                        });
                    } else {
                        addressData.save_in_address_book = this.saveInAddressBook() ? 1 : 0;
                        newBillingAddress = createBillingAddress(addressData);
                    }

                    // New address must be selected as a billing address
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(newBillingAddress);
                }

                if (window.checkoutConfig.reloadOnBillingAddress) {
                    setBillingAddressAction(globalMessageList);
                }
            },

            validate: function () {

                if (this.isAddressSameAsShipping()) {
                    return true;
                }

                if (!this.isAddressFormVisible()) {
                    return true;
                }

                this.source.set('params.invalid', false);
                this.source.trigger('billingAddress.data.validate');

                if (this.source.get('billingAddress.custom_attributes')) {
                    this.source.trigger('billingAddress.custom_attributes.data.validate');
                }

                return !this.source.get('params.invalid');
            },

            saveNewAddress: function () {
                this.source.set('params.invalid', false);
                if (this.source.get('billingAddress.custom_attributes')) {
                    this.source.trigger('billingAddress.custom_attributes.data.validate');
                }

                // A _super part start
                if (!this.source.get('params.invalid')) {
                    var addressData,
                        newBillingAddress;

                    this.source.set('params.invalid', false);
                    this.validate();

                    if (!this.source.get('params.invalid')) {
                        addressData = this.source.get('billingAddress');
                        // if user clicked the checkbox, its value is true or false. Need to convert.
                        addressData['save_in_address_book'] = this.saveInAddressBook ? 1 : 0;

                        // New address must be selected as a shipping address
                        newBillingAddress = createBillingAddress(addressData);
                        selectBillingAddress(newBillingAddress);
                        checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                        checkoutData.setNewCustomerBillingAddress($.extend(true, {}, addressData));
                        this.isFormPopUpVisible(false);
                        this.isNewAddressAdded(true);
                    }
                }
                // _super part end
            },

            /**
             * Collect observed fields data to object
             *
             * @returns {*}
             */
            collectObservedData: function () {
                var observedValues = {};

                $.each(observedElements, function (index, field) {
                    if (typeof field.value == 'function') {
                        observedValues[field.dataScope] = field.value();
                    }
                });

                observedValues['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;

                return observedValues;
            },

            getAddressTemplate: function () {
                return 'MageWorx_Checkout/container/address/billing-address';
            },

            getAddressListPopUpTemplate: function () {
                return this.addressListPopUpTemplate || 'MageWorx_Checkout/billing-address/address-list-popup';
            },

            getNewAddressPopUpTemplate: function () {
                return this.newAddressPopUpTemplate || 'MageWorx_Checkout/billing-address/new-address-popup';
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
            },

            toggleSelectAddressPopUp: function () {
                this.selectAddressPopUpVisible(!this.selectAddressPopUpVisible());
            },

            closeSelectAddressPopUp: function () {
                this.selectAddressPopUpVisible(false);
            },
        });
    }
);
