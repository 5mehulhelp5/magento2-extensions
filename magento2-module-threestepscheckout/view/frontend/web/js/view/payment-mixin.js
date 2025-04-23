/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate'
], function (stepNavigator, $t) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initialize: function() {
                this._super();
                stepNavigator.steps().forEach(function (elem) {
                    if (elem.code === 'payment') {
                        elem.title = $t('Billing');
                    }
                });
            }
        });
    };
});