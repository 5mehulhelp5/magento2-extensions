/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Customer/js/customer-data',
    'uiRegistry'
], function (
    $,
    ko,
    Component,
    quote,
    formPopUpState,
    customerData,
    registry
) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/shipping-address/address-renderer/default'
        },
        observableProperties: [
            'addressListInstance'
        ],

        /** @inheritdoc */
        initObservable: function () {
            this._super()
                .observe(this.observableProperties);

            var self = this;

            registry.async(this.parent)(function (addressList) {
                self.addressListInstance(addressList);
                self.setPreselectedAddress(quote.shippingAddress());
            });

            self.isSelected = ko.computed(function () {
                var isSelected = false,
                    shippingAddress = self.getPreselectedAddress() ?
                        self.getPreselectedAddress() :
                        null;

                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == self.address().getKey();
                }

                return isSelected;
            }, self);

            return this;
        },

        /**
         * Obtain preselected shipping address instance from parent
         *
         * @returns {*}
         */
        getPreselectedAddress: function () {
            return this.addressListInstance() ? this.addressListInstance().preSelectedAddress() : null;
        },

        setPreselectedAddress: function (address) {
            this.addressListInstance().preSelectedAddress(address);
        },

        /**
         * @param {String} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /** Set selected customer shipping address  */
        selectAddress: function () {
            this.addressListInstance().preSelectedAddress(this.address());
        },

        submitPreSelectedAddress: function () {
            this.addressListInstance().submitSelectedAddress();
        },

        /**
         * Edit address.
         */
        editAddress: function () {
            formPopUpState.isVisible(true);
            this.showPopup();

        },

        /**
         * Show popup.
         */
        showPopup: function () {
            $('[data-open-modal="opc-new-shipping-address"]').trigger('click');
        }
    });
});
