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
define([
    'jquery',
    'Milople_Depositpayment/js/customer/order/scripts'
], function (jQuery) {
    'use strict';
	
    var $el = jQuery('#edit_form'),
        config,
        baseUrl,
        order,
        payment;

    if (!$el.length || !$el.data('order-config')) {
        return;
    }
	config = $el.data('order-config');
    baseUrl = $el.data('load-base-url');

    order = new AdminOrder(config);
    order.setLoadBaseUrl(baseUrl);
  
    payment = {
        switchMethod: order.switchPaymentMethod.bind(order)
    };

    window.order = order;
    window.payment = payment;
});
