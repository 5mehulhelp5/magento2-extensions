/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/form/element/email',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/storage',
    'mage/translate',
    'mage/validation'
], function (
    $,
    ko,
    Component,
    quote,
    customer,
    urlBuilder,
    errorProcessor,
    additionalValidators,
    storage,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/form/element/email',
            createAccountLabel: $t('I want to create an account'),
            createAccountAvailable: false,
            createAccount: false,
            createAccountForced: false,
            loginRequired: false,
            minPasswordLength: window.checkoutConfig.minPasswordLength,
            minCharacterSets: window.checkoutConfig.minCharacterSets,
            listens: {
                email: 'emailHasChanged',
                emailFocused: 'validateEmail'
            },
            ignoreTmpls: {
                email: true
            }
        },

        observableProperties: [
            'createAccount',
            'password_confirm',
            'password_main',
            'createAccountLabel',
            'createAccountAvailable',
            'createAccountForced',
            'loginRequired'
        ],

        initialize: function () {
            this._super();
            additionalValidators.registerValidator(this);

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(this.observableProperties);
            this.initSubscribers();
            this.setInitialValues();
            this.createAccountVisible = ko.computed(function () {
                console.log('Create account value: ' + !this.isPasswordVisible() && this.createAccount());
                return !this.isPasswordVisible() && this.createAccount();
            }, this);

            return this;
        },

        initSubscribers: function () {
            this.createAccountForced.subscribe(function (forced) {
                this.createAccountAvailable(this.createAccountAvailable() && forced);
                this.createAccount(this.createAccountAvailable() && forced);
            }, this);
        },

        toggleCreateAccount: function (component, event) {
            var isChecked = event.target.checked;
            component.createAccount(isChecked);
            if (isChecked) {
                this.isPasswordVisible(false);
            } else {
                this.isPasswordVisible(true);
            }

            return isChecked;
        },

        setInitialValues: function () {
            this.createAccountAvailable(window.checkoutConfig.createAccountAvailable);
            this.createAccount(window.checkoutConfig.createAccountCheckedByDefault);
            this.createAccountLabel(window.checkoutConfig.createAccountTitle);
            this.createAccountForced(window.checkoutConfig.createAccountForced);
        },

        /**
         * Main password field update handler
         */
        mainPasswordChanged: function () {
            if (this.validate() && this.passwordMatch()) {
                this.savePassword();
            }
        },

        /**
         * "Confirm password" field update handler
         */
        confirmPasswordChanged: function () {
            if (this.validate() && this.passwordMatch()) {
                this.savePassword();
            }
        },

        /**
         * Password match validation
         *
         * @returns {*|boolean}
         */
        passwordMatch: function () {
            return this.password_confirm()
                && this.password_main()
                && this.password_confirm() === this.password_main();
        },

        /**
         * Saving password action
         *
         * @returns {*}
         */
        savePassword: function () {
            var payload = {'password': this.password_main(), 'email': this.email()},
                params = {
                    cartId: quote.getQuoteId()
                };
            return storage.post(
                urlBuilder.createUrl('/guest-carts/:cartId/save-temp-password', params),
                JSON.stringify(payload)
            ).done(function (response) {
            }).fail(function (response) {
                errorProcessor.process(response);
            });
        },

        /**
         * Local email validation.
         *
         * @param {Boolean} focused - input focus.
         * @returns {Boolean} - validation result.
         */
        validateEmail: function (focused) {
            var loginFormSelector = 'form[data-role=email-with-possible-login]:visible',
                usernameSelector = loginFormSelector + ' input[name=username]:visible',
                loginForm = $(loginFormSelector),
                validator,
                valid;

            if (!loginForm.length) {
                return true;
            }

            loginForm.validation();

            if (focused === false && !!this.email()) {
                valid = !!$(usernameSelector).valid();

                if (valid) {
                    $(usernameSelector).parent('.input').removeClass('input--has-error');
                    $(usernameSelector).removeAttr('aria-invalid aria-describedby');
                } else {
                    $(usernameSelector).parent('.input').addClass('input--has-error');
                    $(usernameSelector).attr('aria-invalid aria-describedby');
                }

                return valid;
            }

            validator = loginForm.validate();

            valid = validator.check(usernameSelector);
            if (valid) {
                $(usernameSelector).parent('.input').removeClass('input--has-error');
            } else {
                $(usernameSelector).parent('.input').addClass('input--has-error');
            }

            return valid;
        },

        validate: function (hideErrors) {
            var result = true,
                formName = 'form[data-role=email-with-possible-login]:visible';

            result = this.validateEmail();

            if (!customer.isLoggedIn()) {
                if (this.createAccountVisible()) {
                    result = this.validateCreateAccountPassword() && result;
                }

                if (this.isPasswordVisible() && quote.isVirtual()) {
                    result = false;
                    this.loginRequired(true);
                } else {
                    this.loginRequired(false);
                }
            }

            if (!result) {
                $('html, body').animate(
                    {
                        scrollTop: $(formName).offset().top - 100
                    },
                    500
                );
            }

            return result;
        },

        validateCreateAccountPassword: function () {
            var formName = 'form[data-role=email-with-possible-login]:visible',
                $passwordInput = $(formName + ' input[id=customer-password]:visible'),
                $passwordConfirmInput = $(formName + ' input[name=password_confirmation]:visible');

            if ($passwordInput.valid()) {
                $passwordInput.parent('.input').removeClass('input--has-error');
                if ($passwordConfirmInput.valid()) {
                    $passwordConfirmInput.parent('.input').removeClass('input--has-error');

                    return true;
                } else {
                    $passwordConfirmInput.parent('.input').addClass('input--has-error');

                    return false;
                }
            } else {
                $passwordInput.parent('.input').addClass('input--has-error');

                return false;
            }

            return false;
        }
    });
});
