/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent'
], function (
    ko,
    $,
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            isVisible: false
        },

        observableProperties: [
            'isVisible',
            'visible'
        ],

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);
            this.isVisible = ko.computed(function () {
                var elems = this.elems() ?? [];

                if (elems.length === 1 && elems[0]['index'] === 'storeCredit') {
                    return elems[0].isAvailable;
                }

                return elems.length;
            }, this);

            return this;
        },

        processVisibility: function () {
            var $container = $('#additional-inputs-container'),
                self = this;

            $container.bind('DOMSubtreeModified', function () {
                var content = $container.html(),
                    contentWithoutKOTags = content.replaceAll(/<!--.+-->/g, ''),
                    trimContent = contentWithoutKOTags.replaceAll(/\s+|\n+/g, '');

                if (trimContent) {
                    self.visible(true);
                } else {
                    self.visible(false);
                }
            });
        }
    });
});
