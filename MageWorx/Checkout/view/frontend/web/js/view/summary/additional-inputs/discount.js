/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_SalesRule/js/view/payment/discount',
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
            contentVisible: false,
            label: $t('Discount Code'),
            fieldLabel: $t('Enter discount code'),
            fieldPlaceholder: $t('Enter discount code'),
            tooltipMessage: $t('Tooltip text example')
        },

        observableProperties: [
            'contentVisible',
            'tooltipMessage',
            'label',
            'fieldLabel',
            'fieldPlaceholder'
        ],

        /**
         * @returns {exports}
         */
        initObservable: function () {
            this._super();

            this.observe(this.observableProperties);
            this.initLabels();

            if (this.isApplied()) {
                this.contentVisible(true);
            }

            return this;
        },

        /**
         * Set labels from config
         */
        initLabels: function () {
            if (window.checkoutConfig.labels.discount) {
                this.label(window.checkoutConfig.labels.discount);
            }

            if (window.checkoutConfig.labels.discount_code_label) {
                this.fieldLabel(window.checkoutConfig.labels.discount_code_label);
            }

            if (window.checkoutConfig.labels.discount_field_placeholder) {
                this.fieldPlaceholder(window.checkoutConfig.labels.discount_field_placeholder);
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
            const $item = $('#discount-tooltip .tooltip__activator');

            setTimeout(function () {
                $(function () {

                    const showTooltip = ($tooltip) => {
                        $tooltip.addClass('tooltip__content--visible');
                    }

                    const hideTooltip = ($tooltip) => {
                        $tooltip.removeClass('tooltip__content--visible');
                    }

                    $('#discount-container').find('.tooltip').each((_, item) => {
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
