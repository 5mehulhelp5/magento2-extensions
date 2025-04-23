/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/model/address-list',
    'mage/translate',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'MageWorx_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mageUtils',
    'ko',
    'uiLayout'
], function (
    Component,
    addressList,
    $t,
    customer,
    quote,
    selectBillingAddressAction,
    checkoutData,
    registry,
    utils,
    ko,
    layout
) {
    'use strict';

    var newAddressOption = {
            /**
             * Get new address label
             * @returns {String}
             */
            getAddressInline: function () {
                return $t('New Address');
            },
            customerAddressId: null
        },
        addressOptions = addressList().filter(function (address) {
            return address.getType() === 'customer-address';
        });

    var defaultRendererTemplate = {
        parent: '${ $.$data.parentName }',
        name: '${ $.$data.name }',
        component: 'MageWorx_Checkout/js/view/billing-address/address-renderer/default'
    };

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/billing-address/list',
            selectedAddress: null,
            isNewAddressSelected: false,
            isAddressDifferent: false,
            addressOptions: addressOptions,
            visible: addressList().length > 0,
            addressListLength: addressList().length,
            rendererTemplates: [],
            exports: {
                selectedAddress: '${ $.parentName }:selectedAddress'
            },
            imports: {
                isAddressDifferent: '${ $.parentName }:isAddressDifferent'
            }
        },

        observableProperties: [
            'selectedAddress',
            'isAddressDifferent',
            'isNewAddressSelected',
            'visible',
            'preSelectedAddress'
        ],

        /** @inheritdoc */
        initialize: function () {
            this._super()
                .initChildren();

            quote.billingAddress.subscribe(function (actualAddress) {
                if ((this.isAddressDifferent() || quote.isVirtual()) && actualAddress) {
                    this.preSelectedAddress(actualAddress);
                } else {
                    this.preSelectedAddress(quote.shippingAddress());
                }
            }, this);

            addressList.subscribe(function (changes) {
                    var self = this;

                    changes.forEach(function (change) {
                        if (change.status === 'added') {
                            self.createRendererComponent(change.value, change.index);
                        }
                    });
                },
                this,
                'arrayChange'
            );

            return this;
        },

        /**
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();
            // the list of child components that are responsible for address rendering
            this.rendererComponents = [];
            // @TODO Remove address options when list is completed
            this.addressOptions.push(newAddressOption);

            return this;
        },

        /**
         * @return {exports.initObservable}
         */
        initObservable: function () {
            this._super()
                .observe(this.observableProperties)
                .observe({
                    isNewAddressSelected: !customer.isLoggedIn() || !addressOptions.length
                });

            return this;
        },


        /** @inheritdoc */
        initChildren: function () {
            _.each(addressList(), this.createRendererComponent, this);

            return this;
        },


        /**
         * Submit address selection: set pre-selected address as quote billing address
         */
        submitSelectedAddress: function () {
            checkoutData.setSelectedBillingAddress(this.preSelectedAddress().getKey());
            selectBillingAddressAction(this.preSelectedAddress());
            registry.async(this.parentName)(function (parent) {
                parent.toggleSelectAddressPopUp();
            });
            registry.get('checkout.steps.shipping-step.billingAddress').isAddressFormVisible(false);
        },

        /**
         * Cancel address selection
         */
        cancelSelectedAddress: function () {
            this.preSelectedAddress(quote.billingAddress());
            registry.async(this.parentName)(function (parent) {
                parent.toggleSelectAddressPopUp();
            });
        },

        /**
         * Create new component that will render given address in the address list
         *
         * @param {Object} address
         * @param {*} index
         */
        createRendererComponent: function (address, index) {
            var rendererTemplate, templateData, rendererComponent;

            if (index in this.rendererComponents) {
                this.rendererComponents[index].address(address);
            } else {
                // rendererTemplates are provided via layout
                rendererTemplate = address.getType() != undefined && this.rendererTemplates[address.getType()] != undefined ? //eslint-disable-line
                    utils.extend({}, defaultRendererTemplate, this.rendererTemplates[address.getType()]) :
                    defaultRendererTemplate;
                templateData = {
                    parentName: this.name,
                    name: 'billing-address-' + index,
                    displayArea: 'billing-addresses'
                };
                rendererComponent = utils.template(rendererTemplate, templateData);
                utils.extend(rendererComponent, {
                    address: ko.observable(address),
                    addressListInstance: ko.observable(this)
                });
                layout([rendererComponent]);
                this.rendererComponents[index] = rendererComponent;
            }
        },

        /**
         * @param {Object} address
         * @return {*}
         */
        addressOptionsText: function (address) {
            return address.getAddressInline();
        },

        /**
         * @param {Object} address
         */
        onAddressChange: function (address) {
            this.isNewAddressSelected(address === newAddressOption);
        }
    });
});
