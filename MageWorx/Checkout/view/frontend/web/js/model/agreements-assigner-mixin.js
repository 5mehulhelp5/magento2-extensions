/**
 * Copyright © MageWorx. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    /**
     * Assigning the agreement ids to the paymentMethod data using correct selector
     * because the agreements has been moved from the active payment method block to the sidebar.
     */
    return function (originalAction) {
        return wrapper.wrap(originalAction, function (originalAssignAgreementIdsFunction, paymentData) {
            originalAssignAgreementIdsFunction(paymentData);
            if (window.isMageWorxCheckout) {
                var agreementsConfig = window.checkoutConfig.checkoutAgreements,
                    agreementForm,
                    agreementData,
                    agreementIds;

                if (!agreementsConfig.isEnabled) {
                    return;
                }

                agreementForm = $('div[data-role=checkout-agreements] input');
                agreementData = agreementForm.serializeArray();
                agreementIds = [];

                agreementData.forEach(function (item) {
                    agreementIds.push(item.value);
                });

                if (paymentData['extension_attributes'] === undefined) {
                    paymentData['extension_attributes'] = {};
                }

                paymentData['extension_attributes']['agreement_ids'] = agreementIds;
            }
        });
    };
});
