/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Depositpayment
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/deposit-payment-m2.html
*
**/
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Milople_Depositpayment/checkout/summary/installmentfee'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode();
            },
			getLabel: function() {
				return window.partialpaymentConfig.installmentFeeLabel;
			},
            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('installmentfee').value;
                }
                return this.getFormattedPrice(price);
            },
            getBaseValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_installmentfee;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },
			getPureValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('installmentfee').value;
                }
                return price;
            }
        });
    }
);