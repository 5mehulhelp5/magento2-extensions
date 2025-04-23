/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'ko',
    'mageUtils',
    'uiComponent',
    'uiLayout',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'MageWorx_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function (
    _,
    ko,
    utils,
    Component,
    layout,
    addressList,
    quote,
    selectShippingAddressAction,
    checkoutData,
    registry
) {
    'use strict';

    var defaultRendererTemplate = {
        parent: '${ $.$data.parentName }',
        name: '${ $.$data.name }',
        component: 'MageWorx_Checkout/js/view/shipping-address/address-renderer/default'
    };

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/shipping-address/list',
            visible: addressList().length > 0,
            addressListLength: addressList().length,
            rendererTemplates: []
        },
        observableProperties: [
            'preSelectedAddress'
        ],

        /** @inheritdoc */
        initialize: function () {
            this._super()
                .initChildren()
                .observe(this.observableProperties);

            quote.shippingAddress.subscribe(function (actualAddress) {
                this.preSelectedAddress(actualAddress);
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

        /** @inheritdoc */
        initConfig: function () {
            this._super();
            // the list of child components that are responsible for address rendering
            this.rendererComponents = [];

            return this;
        },

        /** @inheritdoc */
        initChildren: function () {
            _.each(addressList(), this.createRendererComponent, this);

            return this;
        },

        /**
         * Submit address selection: set pre-selected address as quote shipping address
         */
        submitSelectedAddress: function () {
            selectShippingAddressAction(this.preSelectedAddress());
            checkoutData.setSelectedShippingAddress(this.preSelectedAddress().getKey());
            registry.async(this.parentName)(function (parent) {
                parent.toggleSelectAddressPopUp();
            });
        },

        /**
         * Cancel address selection
         */
        cancelSelectedAddress: function () {
            this.preSelectedAddress(quote.shippingAddress());
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
                    name: index
                };
                rendererComponent = utils.template(rendererTemplate, templateData);
                utils.extend(rendererComponent, {
                    address: ko.observable(address)
                });
                layout([rendererComponent]);
                this.rendererComponents[index] = rendererComponent;
            }
        }
    });
});
