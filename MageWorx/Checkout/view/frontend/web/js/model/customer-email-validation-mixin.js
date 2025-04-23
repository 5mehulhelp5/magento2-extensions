/**
 * Copyright © MageWorx. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Customer/js/model/customer',
    'mage/validation'
], function ($, _, customer) {
    'use strict';

    return function (originalValidatorObject) {
        var updateValidator = {};

        if (window.isMageWorxCheckout) {
            updateValidator.validate = function () {
                var emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]:visible';

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                return emailValidationResult;
            }
        }

        return _.extend(originalValidatorObject, updateValidator);
    };
});
