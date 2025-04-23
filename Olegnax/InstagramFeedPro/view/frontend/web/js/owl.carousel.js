/*
 * @author      Olegnax
 * @package     Olegnax_InstagramFeed
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'OXowlCarousel'
], function ($) {
    'use strict';

    $.widget('mage.OXIFowlCarousel', $.mage.OXowlCarousel, {
        owlchanged: function (event) {
            this._super(event);
            if (event.item.count) {
                let index = event.item.index + 1,
                    $hotSpots = this.$element.parent().find('.ox-instagram__related-hotspots').find('.ox-ihs__item');
                $hotSpots.removeClass('-show').addClass('-hide').filter('[data-image-index="' + index + '"], :not([data-image-index])').addClass('-show').removeClass('-hide');
            }
        }
    });
    return $.mage.OXIFowlCarousel;
});
