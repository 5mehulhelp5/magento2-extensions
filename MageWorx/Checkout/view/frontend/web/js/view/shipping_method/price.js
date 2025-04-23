/**
 * Copyright © MageWorx, Inc. All rights reserved.
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function (
    Component,
    quote,
    priceUtils,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/shipping-method/item/price'
        },
        isDisplayShippingPriceExclTax: window.checkoutConfig.isDisplayShippingPriceExclTax,
        isDisplayShippingBothPrices: window.checkoutConfig.isDisplayShippingBothPrices,

        /**
         * @param {Object} item
         * @return {Boolean}
         */
        isPriceEqual: function (item) {
            return item['price_excl_tax'] != item['price_incl_tax'];
        },

        /**
         * @param {*} price
         * @return {*|String}
         */
        getFormattedPrice: function (price) {
            if (price < 0.001) {
                return $t('Free');
            }

            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    });
});
