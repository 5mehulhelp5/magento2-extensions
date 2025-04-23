/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    var prevHash = null;

    return function(stepNavigator) {
        stepNavigator.handleHash = wrapper.wrap(stepNavigator.handleHash, function(originalFunction) {
            var hash = window.location.hash.replace('#', '');
            if (prevHash) {
                $("body").removeClass('tsc-step-' + prevHash);
            }
            if (hash) {
                $("body").addClass('tsc-step-' + hash);
                prevHash = hash;
            }
            if (hash === 'finalization') {
                $("aside.opc-summary-wrapper").removeClass('custom-slide');
            } else {
                $("aside.opc-summary-wrapper").addClass('custom-slide');
            }
            return originalFunction();
        });
        return stepNavigator;
    }
});