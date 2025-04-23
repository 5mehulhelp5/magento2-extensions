/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/model/address-list',
    'uiRegistry'
], function (
    $,
    ko,
    Component,
    quote,
    customerData,
    addressListOriginal,
    registry
) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/billing-address/address-renderer/default'
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
                if (addressList) {
                    self.addressListInstance(addressList);
                }
                if (quote.billingAddress() && quote.billingAddress().getCacheKey().indexOf('new-') === -1) {
                    self.setPreselectedAddress(quote.billingAddress());
                } else {
                    _.each(addressListOriginal, function (address) {
                        if (address.isDefaultBilling()) {
                            quote.billingAddress(address);
                        }
                    });
                }
            });

            self.isSelected = ko.computed(function () {
                var isSelected = false,
                    billingAddress = self.getPreselectedAddress() ?
                        self.getPreselectedAddress() :
                        null;

                if (billingAddress) {
                    isSelected = billingAddress.getKey() == self.address().getKey();
                }

                return isSelected;
            }, self);

            return this;
        },

        /**
         * Obtain preselected billing address instance from parent
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
         * @param {Object} address
         */
        onAddressChange: function (address) {
            this.addressListInstance().isNewAddressSelected(address === newAddressOption);
        }
    });
});
