/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Checkout/js/action/select-shipping-address',
    'MageWorx_Checkout/js/model/update-totals-processor'
], function (originalSelectShippingAddressAction, updateTotalsProcessor) {
    'use strict';

    return function (shippingAddress) {
        var result;

        updateTotalsProcessor.before(shippingAddress);
        result = originalSelectShippingAddressAction(shippingAddress);
        updateTotalsProcessor.after(shippingAddress, result);

        return result;
    };
});
