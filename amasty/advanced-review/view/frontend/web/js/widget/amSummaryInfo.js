define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    $.widget('mage.amSummaryInfo', {
        options: {
            selectors: {
                showMore: '[data-amreview-js="show-more"]',
                percent: '[data-amreview-js="percent"]',
                details: '[data-amreview-js="summary-details"]'
            }
        },
        /**
         * @inheritDoc
         */
        _create: function () {
            $(this.options.selectors.showMore).on('click', function () {
                var button = $(this.options.selectors.showMore);

                $(this.options.selectors.percent).toggle();
                $(this.options.selectors.details).toggle();
                button.text(button.text() === $t('More info') ? $t('Less info') : $t('More info'));
            }.bind(this));
        }
    });

    return $.mage.amSummaryInfo;
});
