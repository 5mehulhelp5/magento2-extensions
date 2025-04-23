/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'MB_ThreeStepsCheckout/js/model/payment-validator/braintree-hosted-fields'
], function (braintreeHostedFields) {
    'use strict';

    return {
        braintree: braintreeHostedFields
    }
});