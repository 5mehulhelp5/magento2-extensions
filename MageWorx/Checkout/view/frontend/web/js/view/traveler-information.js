define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function ($, ko, Component, quote, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_Checkout/traveler-information/traveler-information'
        },

        initialize: function () {
            this._super();
            return this;
        },

        totalRoomsAndTravelers: function() {
            const items = this.parsedItems();
            let totalRooms = 0;
            let totalTravelers = 0;

            if (items && items.length) {
                items.forEach(item => {
                    totalRooms += item.rooms.length;
                    item.rooms.forEach(room => {
                        totalTravelers += room.travelers.length;
                    });
                });
            }

            return {
                rooms: totalRooms,
                travelers: totalTravelers
            };
        },

        parsedItems: ko.pureComputed(function () {
            const items = quote.getItems() || [];
            const parsed = [];

            items.forEach(item => {
                const name = item.name;
                const itemId = item.item_id;
                const options = item.options || [];
                let occupancy = '';
                let travelersPerRoom = 1;
                let qty = 1;

                const match = options.find(opt => opt.label === 'Select Occupancy');
                if (match) {
                    occupancy = match.value;

                    const qtyMatch = occupancy.match(/^(\d+)\s*x/i);
                    if (qtyMatch) {
                        qty = parseInt(qtyMatch[1]);
                    }

                    if (/single/i.test(occupancy)) travelersPerRoom = 1;
                    else if (/double/i.test(occupancy)) travelersPerRoom = 2;
                    else if (/triple/i.test(occupancy)) travelersPerRoom = 3;
                    else if (/quad/i.test(occupancy)) travelersPerRoom = 4;
                }

                const rooms = [];

                for (let j = 1; j <= qty; j++) {
                    const travelers = [];
                    for (let i = 1; i <= travelersPerRoom; i++) {
                        travelers.push({
                            name: ko.observable(''),
                            inputName: `custom[travelers_${itemId}_${i}_${j}]`,
                            inputId: `custom:travelers_${itemId}_${i}_${j}`
                        });
                    }

                    rooms.push({
                        travelers: travelers,
                        bedType: ko.observable(''),
                        bedTypeName: `custom[bedtype_${itemId}_${j}]`,
                        bedTypeId: `custom:bedtype_${itemId}_${j}`
                    });
                }

                if (rooms.length) {
                    parsed.push({
                        name: name,
                        rooms: rooms
                    });
                }
            });

            return parsed;
        })
    });
});
