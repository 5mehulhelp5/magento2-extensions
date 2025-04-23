/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/error-processor',
    'mage/storage',
    'mage/translate'
], function (
    Component,
    $,
    quote,
    customer,
    urlBuilder,
    errorProcessor,
    storage,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/summary/newsletter-subscription',
            isVisible: true,
            label: $t('I want to subscribe for newsletters'),
            value: true,
            isChecked: false
        },

        observableProperties: [
            'isVisible',
            'label',
            'value',
            'isChecked'
        ],

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);
            this.isChecked.subscribe(function (val) {
                this.checkboxChecked();
            }, this);

            return this;
        },

        checkboxChecked: function () {
            var status = $('#email_subscription_checkbox').is(":checked"),
                payload = {'status': status},
                params = {
                    cartId: quote.getQuoteId()
                },
                url = customer.isLoggedIn() ? '/carts/mine/email-subscription' : '/guest-carts/:cartId/email-subscription';

            return storage.post(
                urlBuilder.createUrl(url, params),
                JSON.stringify(payload)
            ).fail(function (response) {
                errorProcessor.process(response);
            });
        }
    });
});
