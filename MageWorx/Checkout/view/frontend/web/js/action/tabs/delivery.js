/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'uiRegistry',
], function (
    registry
) {
    'use strict';

    return function (flag) {
        var pickupInfo = registry.get('checkout.steps.shipping-step.shippingAddress.shippingAdditional.mageworxpickup');

        if (flag === true) {
            enableTab();
        } else {
            disableTab();
        }

        function enableTab() {
            if (pickupInfo) {
                pickupInfo.isVisible(false);
            }
        }

        function disableTab() {
            if (pickupInfo) {
                pickupInfo.isVisible(true);
            }
        }
    };
});
