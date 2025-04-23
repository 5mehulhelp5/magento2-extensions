/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/action/login',
    'Magento_Customer/js/model/customer',
    'mage/validation',
    'Magento_Checkout/js/model/authentication-messages',
    'ko'
], function (
    $,
    Component,
    loginAction,
    customer,
    validation,
    messageContainer,
    ko
) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return Component.extend({
        isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
        isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
        registerUrl: checkoutConfig.registerUrl,
        forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
        autocomplete: checkoutConfig.autocomplete,
        defaults: {
            template: 'MageWorx_Checkout/authentication',
            visible: false
        },
        observableProperties: [
            'visible'
        ],

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);
            this.visible(false);

            this.popupDisplayStyle = ko.computed(function () {
                if (this.visible()) {
                    return 'flex';
                } else {
                    return 'none';
                }
            }, this);

            return this;
        },

        togglePopUp: function () {
            this.visible(!this.visible());
        },

        closePopUp: function () {
            this.visible(false);
        },

        /**
         * Is login form enabled for current customer.
         *
         * @return {Boolean}
         */
        isActive: function () {
            return !customer.isLoggedIn();
        },

        /**
         * Provide login action.
         *
         * @param {HTMLElement} loginForm
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if ($(loginForm).validation() &&
                $(loginForm).validation('isValid')
            ) {
                $('body').trigger('processStart');
                loginAction(loginData, checkoutConfig.mageworxCheckoutUrl, undefined, messageContainer)
                    .done(function () {
                        setTimeout($('body').trigger('processStop'), 1500);
                    })
                    .fail(function () {
                        $('body').trigger('processStop');
                    })
                    .always(function () {
                        setTimeout($('body').trigger('processStop'), 3000);
                    });
            }
        }
    });
});
