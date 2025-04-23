/**
 * Copyright © MageWorx. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/validation'
], function ($, _) {
    'use strict';

    return function (originalValidatorObject) {
        var updateValidator = {};

        if (window.isMageWorxCheckout) {
            var checkoutConfig = window.checkoutConfig,
                agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
                agreementsInputPath = 'div.checkout-agreements input';

            updateValidator.validate = function (hideError) {
                var isValid = true;

                if (!agreementsConfig.isEnabled || $(agreementsInputPath).length === 0) {
                    return true;
                }

                $(agreementsInputPath).each(function (index, element) {
                    if (!$.validator.validateSingleElement(element, {
                        errorElement: 'div',
                        hideError: hideError || false
                    })) {
                        isValid = false;
                    }
                });

                return isValid;
            }
        }

        return _.extend(originalValidatorObject, updateValidator);
    };
});
