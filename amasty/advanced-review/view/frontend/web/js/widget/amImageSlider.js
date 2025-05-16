define([
    'jquery',
    'Amasty_Base/vendor/slick/slick.min',
    'Amasty_AdvancedReview/vendor/fancybox/jquery.fancybox.min'
], function ($) {
    'use strict';

    $.widget('mage.amImageSlider', {
        options: {
            slidesToShow: 3,
            slidesToScroll: 3,
            centerMode: false,
            variableWidth: false,
            responsive: [
                {
                    breakpoint: 460,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 360,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ],
            selectors: {
                imageContainer: '[data-amreview-js="review-images"]',
                sliderItem: '[data-amreview-js="slider-item"]',
                hide: 'hidden',
                active: '-active'
            }
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            var self = this,
                slidesToShow = $(window).width() > 768 ? self.options.slidesToShow : 1,
                $imageContainer = $(self.element);

            if ($imageContainer.length) {
                if (slidesToShow === 1) {
                    delete self.options.responsive;
                }

                $.each($imageContainer, function () {
                    var $element = $(this);

                    $element.find('a').fancybox({
                        loop: true,
                        toolbar: false,
                        baseClass: 'amrev-fancybox-zoom'
                    });

                    if (
                        $element.find(self.options.selectors.sliderItem).length > slidesToShow
                        && self.options.slidesToShow
                    ) {
                        $element.slick(self.options);
                        $element.slick('resize');
                    }
                });
            }
        }
    });

    return $.mage.amImageSlider;
});
