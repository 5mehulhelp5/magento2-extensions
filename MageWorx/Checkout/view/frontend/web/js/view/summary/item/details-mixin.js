define([
    'mage/utils/wrapper',
    'ko',
    'jquery',
    'Magento_Catalog/js/price-utils'
], function (wrapper, ko, $,priceUtils) {
    'use strict';

    return function (targetModule) {
        return targetModule.extend({
            defaults: {
                template: 'MageWorx_Checkout/summary/item/details'
            },

            /**
             * Calculate rooms and guests based on the item options
             *
             * @param {Object} parentItem - The parent item object
             * @returns {Object} Object with rooms and guests count
             */
            getRoomsAndGuests: function(parentItem) {
                var result = {
                    rooms: 2,
                    guests: 4
                };

                try {
                    if (!parentItem || !parentItem.options) {
                        return result;
                    }

                    var options = JSON.parse(parentItem.options);
                    var occupancyOption = options.find(function(opt) {
                        return opt.label === 'Select Occupancy';
                    });

                    if (occupancyOption) {
                        var occupancy = occupancyOption.value;
                        var qtyMatch = occupancy.match(/^(\d+)\s*x/i);
                        var rooms = 1;

                        if (qtyMatch) {
                            rooms = parseInt(qtyMatch[1]);
                        }

                        var guestsPerRoom = 1;
                        if (/single/i.test(occupancy)) guestsPerRoom = 1;
                        else if (/double/i.test(occupancy)) guestsPerRoom = 2;
                        else if (/triple/i.test(occupancy)) guestsPerRoom = 3;
                        else if (/quad/i.test(occupancy)) guestsPerRoom = 4;

                        result.rooms = rooms;
                        result.guests = rooms * guestsPerRoom;
                    }
                } catch (e) {
                    console.error('Error calculating rooms and guests:', e);
                }

                return result;
            },

            /**
             * Get formatted rooms and guests text
             *
             * @param {Object} parentItem - The parent item object
             * @returns {String} Formatted rooms and guests text
             */
            getRoomsAndGuestsText: function(parentItem) {
                var data = this.getRoomsAndGuests(parentItem);
                return data.rooms + ' Rooms, ' + data.guests + ' Guests';
            },

            /**
             * Format price
             *
             * @param {String} price - The price to format
             * @returns {String} Formatted price
             */
            formatPriceWithCurrency: function (value) {
                try {
                    const prices = (value.match(/[\-\+]{0,1}[\$€¥]?[0-9,]+\.\d{2}/g) || []);
                    if (!prices.length) return '$0.00';

                    return prices
                        .map(raw => {
                            const clean = parseFloat(raw.replace(/[^0-9\.\-]/g, ''));
                            return priceUtils.formatPrice(clean, window.checkoutConfig.priceFormat);
                        })
                        .join(', ');
                } catch (e) {
                    console.error('Error formatting price with currency:', e);
                    return '$0.00';
                }
            },/**
             * Remove prices and tags from value to get clean text
             *
             * @param {String} value
             * @returns {String}
             */
            formatOptionDetail: function (value) {
                try {
                    // Use full_view if present
                    if (typeof value === 'object' && value.full_view) {
                        value = value.full_view;
                    }

                    // Remove price pattern and HTML tags
                    return value
                        .replace(/[\-\+]{0,1}[\$€¥]?[0-9,]+\.\d{2}/g, '')
                        .replace(/<[^>]*>/g, '')
                        .replace(/\s{2,}/g, ' ')
                        .trim();
                } catch (e) {
                    console.error('Error formatting option detail:', e);
                    return value;
                }
            },
            getOptionDetails: function (value) {
                try {
                    return value
                        .replace(/<[^>]+>/g, '') // strip HTML tags
                        .replace(/[\-\+]{0,1}[\$€¥]?[0-9,]+\.\d{2}/g, '') // remove price
                        .split(',')
                        .map(text => text.trim())
                        .filter(Boolean);
                } catch (e) {
                    console.error('Error parsing option details:', e);
                    return [];
                }
            }

        });
    };
});
