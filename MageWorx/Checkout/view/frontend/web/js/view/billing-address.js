define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/billing-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/create-billing-address',
        'MageWorx_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messageList',
        'uiRegistry',
        'mage/translate',
        'rjsResolver'
    ],
    function ($,
              ko,
              Component,
              quote,
              checkoutData,
              createBillingAddress,
              selectBillingAddress,
              setBillingAddressAction,
              addressConverter,
              additionalValidators,
              globalMessageList,
              registry,
              $t,
              resolver
    ) {
        'use strict';

        var observedElements = [];

        return Component.extend({
            defaults: {
                template: 'MageWorx_Checkout/billing-address/main',
                formTemplate: 'MageWorx_Checkout/billing-address/form',
                detailsTemplate: 'MageWorx_Checkout/billing-address/details',
                isAddressSameAsShipping: true,
                isAddressFormVisible: false,
                visible: true,
                label: $t('Billing Address'),
                sectionIndex: 1,
                fieldsInitialized: false,
                imports: {
                    isAddressSameAsShipping: '${ $.parentName.split(\'.\').slice(0, -1).join(\'.\', 2) }:isAddressSameAsShipping',
                    isAddressFormVisible: '${ $.parentName.split(\'.\').slice(0, -1).join(\'.\', 2) }:isAddressFormVisible'
                }
            },

            observableProperties: [
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
                this._super();

                additionalValidators.registerValidator(this);
                this.initSubscribers();
                resolver(this.afterResolveDocument.bind(this));

                return this;
            },

            initSubscribers: function () {

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

            afterResolveDocument: function () {
                if (!this.fieldsInitialized) {
                    this.initFields();
                }
            },

            /**
             * Make fields value observable by saving data
             * @returns {exports}
             */
            initFields: function () {
                var self = this,
                    fieldsContainerName
                        = this.name + '.form-fields',
                    container = registry.get(fieldsContainerName),
                    bindFunction = function (element, index, list) {
                        if (!element.isBinded) {
                            self.bindHandler(element);
                        }
                    };

                container.elems.subscribe(function (elements) {
                    _.each(
                        elements,
                        bindFunction
                    );
                });

                _.each(container.elems(), bindFunction);

                this.fieldsInitialized = true;

                return this;
            },

            bindHandler: function (element) {
                var self = this;

                if (element.component.indexOf('/group') !== -1) {
                    $.each(element.elems(), function (index, elem) {
                        registry.async(elem.name)(function () {
                            self.bindHandler(elem);
                        });
                    });
                } else if (_.isString(element.index) && element.index.indexOf('-group') !== -1) {
                    $.each(element.elems(), function (index, elem) {
                        registry.async(elem.name)(function () {
                            self.bindHandler(elem);
                        });
                    });
                } else {
                    element.on('value', this.saveBillingAddress.bind(this, element.index));
                    var exists = false;
                    $.each(observedElements, function (elementInStack) {
                        if (elementInStack.name === element.name) {
                            exists = true;
                            return false;
                        }
                    });

                    if (!exists) {
                        observedElements.push(element);
                    }
                    element.isBinded = true;
                }
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

                return observedValues;
            },

            saveBillingAddress: function (fieldName) {
                if (!this.isAddressSameAsShipping()) {
                    if (this.isAddressFormVisible()) {
                        var addressFlat = addressConverter.formDataProviderToFlatData(
                                this.collectObservedData(),
                                'billingAddress'
                            ),
                            newBillingAddress;

                        addressFlat.save_in_address_book = this.saveInAddressBook() ? 1 : 0;
                        newBillingAddress = createBillingAddress(addressFlat);

                        // New address must be selected as a billing address
                        selectBillingAddress(newBillingAddress);
                        checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                        checkoutData.setNewCustomerBillingAddress(addressFlat);
                    }
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

            getAddressTemplate: function () {
                return 'MageWorx_Checkout/container/address/billing-address';
            }
        });
    }
);
