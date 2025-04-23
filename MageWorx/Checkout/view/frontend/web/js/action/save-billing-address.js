/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/error-processor',
    'mage/storage',
    'mage/translate',
    'underscore'
], function (
    messageContainer,
    fullScreenLoader,
    urlBuilder,
    errorProcessor,
    storage,
    $t,
    _
) {
    'use strict';

    return function (billingAddress) {
        if (window.showLoaderDuringBillingAddressSave) {
            fullScreenLoader.startLoader();
        }

        var serviceUrl = urlBuilder.createUrl('/checkout/save-billing-address', {}),
            payload = {
                'billingAddress': billingAddress
            },
            headers = {},
            $deferred = storage.post(
                serviceUrl, JSON.stringify(payload), true, 'application/json', headers
            );

        $deferred.always(
            function () {
                if (window.showLoaderDuringBillingAddressSave) {
                    fullScreenLoader.stopLoader();
                }
            }
        );

        return $deferred;
    };
});
