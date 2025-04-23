/**
 * Custom shipping address selection component
 */
define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate'
], function (
    $,
    ko,
    Component,
    customer,
    quote,
    selectShippingAddressAction,
    formPopUpState,
    checkoutData,
    createShippingAddress,
    selectShippingMethod,
    shippingRateProcessor,
    shippingService,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/shipping-address-selection'
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();
            this.isActive = ko.observable(true);
            this.selectedShippingAddress = ko.observable(quote.shippingAddress());
            this.shippingAddressList = ko.observableArray(this.getAddressOptions());

            // Subscribe to address changes
            quote.shippingAddress.subscribe(function (address) {
                this.selectedShippingAddress(address);
            }, this);

            // Subscribe to form popup state changes
            var self = this;
            formPopUpState.isVisible.subscribe(function(isVisible) {
                // When popup is closed, refresh the address list
                if (!isVisible) {
                    // Small timeout to ensure the new address is saved
                    setTimeout(function() {
                        self.shippingAddressList(self.getAddressOptions());
                    }, 500);
                }
            });

            return this;
        },

        /**
         * Toggle active state
         */
        toggleActive: function () {
            this.isActive(!this.isActive());
        },

        /**
         * Get customer addresses
         *
         * @returns {Array}
         */
        getAddressOptions: function () {
            var addresses = [];

            if (customer.isLoggedIn()) {
                // Customer is logged in, get saved addresses
                var customerAddresses = customer.getShippingAddressList();

                customerAddresses.forEach(function (address) {
                    addresses.push(address);
                });
            } else {
                // Guest checkout, use the current shipping address if available
                var shippingAddress = quote.shippingAddress();
                if (shippingAddress && !shippingAddress.isAddressEmpty) {
                    addresses.push(shippingAddress);
                }
            }

            return addresses;
        },

        /**
         * Select shipping address
         *
         * @param {Object} address
         */
        selectShippingAddress: function (address) {
            selectShippingAddressAction(address);
            checkoutData.setSelectedShippingAddress(address.getKey());

            // Trigger shipping rates update
            shippingRateProcessor.getRates(quote.shippingAddress());
            shippingService.isLoading(true);
        },

        /**
         * Show address form popup
         */
        showFormPopUp: function () {
            formPopUpState.isVisible(true);
        },

        /**
         * Get country name by country code
         *
         * @param {String} countryId
         * @returns {String}
         */
        getCountryName: function (countryId) {
            // This is a simplified version, in a real implementation you would
            // need to get the country name from a country list
            return countryId;
        }
    });
});
