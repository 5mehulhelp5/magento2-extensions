/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'mage/utils/wrapper',
    'mage/translate',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/modal/alert'
], function (wrapper, $t, stepNavigator, alert) {
    'use strict';

    return function (errorProcessor) {
        errorProcessor.process = wrapper.wrap(
            errorProcessor.process,
            function (originalFunction, response, messageContainer) {
                if (stepNavigator.getActiveItemIndex() === 2) {
                    if (response.status !== 401) {
                        var error;
                        try {
                            error = JSON.parse(response.responseText);
                        } catch (exception) {
                            error = {
                                message: $t('Something went wrong with your request. Please try again later.')
                            };
                        }
                        alert({
                            title: $t('Error'),
                            content: error.message
                        });
                    }
                }
                return originalFunction(response, messageContainer);
            }
        );
        return errorProcessor;
    }
});