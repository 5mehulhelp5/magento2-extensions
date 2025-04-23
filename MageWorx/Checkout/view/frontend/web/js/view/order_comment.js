define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'mage/translate'
    ],
    function (
        $,
        Component,
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
                visible: true,
                label: $t('Order Comment'),
                comment: ''
            },

            observableProperties: [
                'visible',
                'label',
                'comment'
            ],

            initObservable: function () {
                this._super();
                this.observe(this.observableProperties);

                return this;
            },

            changeComment: function () {
                var payload = {'comment': this.comment()},
                    params = {
                        cartId: quote.getQuoteId()
                    },
                    url = customer.isLoggedIn() ? '/carts/mine/save-comment' : '/guest-carts/:cartId/save-comment';

                return storage.post(
                    urlBuilder.createUrl(url, params),
                    JSON.stringify(payload)
                ).fail(function (response) {
                    errorProcessor.process(response);
                });
            }
        });
    }
);
