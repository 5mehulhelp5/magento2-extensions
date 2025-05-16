/*
* Amasty Advanced Review
*/

define([
    'jquery',
    'Amasty_Base/vendor/slick/slick.min'
], function ($) {
    'use strict';

    $.widget('mage.amReview', {
        /**
         * @inheritDoc
         */
        _create: function () {
            $('#tab-label-reviews').on('click', function () {
                $('.amreview-images.slick-initialized').slick('setPosition');
            });
        }
    });

    return $.mage.amReview;
});
