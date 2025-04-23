/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(['underscore'], function (_) {
    'use strict';

    return function (origComponent) {

        if (window.isMageWorxCheckout) {
            if (typeof origComponent.street === 'undefined') {
                origComponent.street = [];
            }

            return origComponent;
        }

        return origComponent;
    };
});
