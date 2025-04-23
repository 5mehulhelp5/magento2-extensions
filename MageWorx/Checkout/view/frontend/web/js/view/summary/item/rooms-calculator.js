define([
    'ko'
], function (ko) {
    'use strict';

    return {
        /**
         * Calculate rooms and guests based on options
         *
         * @param {Array} options - Product options
         * @returns {Object} Object with rooms and guests count
         */
        calculate: function (options) {
            var result = {
                rooms: 2,
                guests: 4
            };

            if (!options || !options.length) {
                return result;
            }

            try {
                var parsedOptions = typeof options === 'string' ? JSON.parse(options) : options;
                var occupancyOption = parsedOptions.find(function(opt) {
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
        }
    };
});
