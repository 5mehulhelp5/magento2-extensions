define([
    'ko',
    'underscore',
    'uiComponent'
], function (
    ko,
    _,
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                tabs: 'order_tabs.tabs:elems'
            },
            tabs: [],
            visibleTabsCount: 0
        },

        observableProperties: [
            'visible',
            'tabs',
            'visibleTabsCount'
        ],

        initialize: function () {
            this._super();

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);
            this.initSubscriptions();

            return this;
        },

        initSubscriptions: function () {
            var self = this;

            this.visibleTabsCount = ko.computed(function() {
                let counter = 0,
                    tabs = this.tabs();

                _.each(tabs, function (elem) {
                    if (elem.visible()) {
                        counter++;
                    }
                });

                return counter;
            }, this);

            this.visibleTabsCount.subscribe(function (count) {
                if (count > 1) {
                    self.visible(true);
                } else {
                    self.visible(false);
                }
            });
        }
    });
});
