define([
    'uiComponent'
], function (
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            visible: true
        },

        initObservable: function () {
            this._super();
            this.observe('visible');

            return this;
        }
    });
});
