/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_SalesRule/js/view/summary/discount'
], function (
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/totals/discount'
        }
    });
});
