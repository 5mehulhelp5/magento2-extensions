/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_CustomerBalance/js/view/payment/customer-balance',
    'jquery',
    'mage/translate',
    'popper',
    'jquery/ui',
    'domReady!'
], function (
    Component,
    $,
    $t,
    Popper
) {
    'use strict';

    window.Popper = Popper;

    return Component.extend({
        defaults: {
            storeCreditFormName: 'checkout.sidebar.additionalInputs.storeCredit',
            modules: {
                storeCreditForm: '${ $.storeCreditFormName }'
            },
            contentVisible: false,
            label: $t('Store Credit'),
            tooltipMessage: ''
        },

        observableProperties: [
            'contentVisible',
            'tooltipMessage',
            'label'
        ],

        /**
         * @returns {exports}
         */
        initObservable: function () {
            this._super();

            this.observe(this.observableProperties);
            this.initLabels();

            return this;
        },

        /**
         * Set labels from config
         */
        initLabels: function () {
            if (window.checkoutConfig.labels.customerBalance) {
                this.label(window.checkoutConfig.labels.customerBalance);
            }
        },

        /**
         * Toggle collapsible class state
         */
        toggleCollapsible: function () {
            this.contentVisible(!this.contentVisible());
        },

        /**
         * Initialize tooltip
         */
        initTooltip: function () {
            const $item = $('#customer-balance-tooltip .tooltip__activator');

            setTimeout(function () {
                $(function () {

                    const showTooltip = ($tooltip) => {
                        $tooltip.addClass('tooltip__content--visible');
                    }

                    const hideTooltip = ($tooltip) => {
                        $tooltip.removeClass('tooltip__content--visible');
                    }

                    $('#customer-balance-tooltip').each((_, item) => {
                        const $item = $(item);
                        const $activator = $item.find('.tooltip__activator');
                        const $tooltipContent = $(`
    <div class="tooltip__content" role="tooltip">
      <div data-popper-arrow class="tooltip-arrow tooltip__arrow"></div>
      <div class="tooltip-inner">${$item.data('content')}</div>
    </div>`);

                        $('.body').append($tooltipContent);
                        const popperInstance = Popper.createPopper(
                            $item.get(0),
                            $tooltipContent.get(0),
                            {
                                modifiers: [
                                    {
                                        name: 'preventOverflow',
                                        options: {
                                            padding: 10
                                        }
                                    }
                                ]
                            }
                        );

                        $activator.on('mouseenter focus', () => {
                            showTooltip($tooltipContent);
                            popperInstance.forceUpdate();
                        });
                        $activator.on('mouseleave blur', () => {
                            hideTooltip($tooltipContent);
                        });

                    })
                });
            }, 1500);
        }
    });
});
