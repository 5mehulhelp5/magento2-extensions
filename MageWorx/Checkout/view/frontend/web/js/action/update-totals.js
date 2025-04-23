define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
], function ($, getTotalsAction) {
    'use strict';

    var callback = function() {};

    return function (deferred) {
        deferred = deferred || $.Deferred();
        getTotalsAction(callback, deferred);

        return deferred;
    };
});
