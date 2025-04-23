/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([], function () {
    'use strict';

    return function (paymentComponent) {
        return paymentComponent.validateFormFields();
    }
});