/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Tax/js/view/checkout/summary/subtotal'
], function (
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/totals/subtotal'
        }
    });
});
