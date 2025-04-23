/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_CustomerBalance/js/view/summary/customer-balance'
], function (
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/totals/customer-balance',
            storeCreditFormName: 'checkout.sidebar.additionalInputs.storeCredit',
            modules: {
                storeCreditForm: '${ $.storeCreditFormName }'
            }
        }
    });
});
