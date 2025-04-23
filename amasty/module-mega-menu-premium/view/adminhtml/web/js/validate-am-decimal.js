define([
    'jquery',
    'mage/translate',
    'jquery/validate'
], function ($) {
    'use strict';

    return function (amDecimalValidator) {
        $.validator.addMethod(
            'validate-am-decimal',
            function (value) {
                return (value >= 0) && (value <= 5) && (/^\d{1,1}(\.\d{1,1}){0,1}$/i.test(value));
            },
            $.mage.__('Please enter a number from 0 to 5. You can use up to one decimal place.')
        );

        return amDecimalValidator;
    };
});
