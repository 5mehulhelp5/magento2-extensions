/**
 *  Amasty ammenu_submenu_mobile UI Component
 */

define([
    'jquery',
    'uiComponent',
    'ammenu_helpers'
], function ($, Component, helpers) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_MegaMenuPremium/submenu/mobile/wrapper',
            imports: {
                color_settings: 'index = ammenu_wrapper:color_settings',
                mobile_class: 'index = ammenu_wrapper:mobile_class'
            },
            links: {
                'actionAnimation': 'index = ammenu_drill_wrapper:actionAnimation'
            }
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    actionAnimation: ''
                });

            return this;
        },

        /**
         * Set content visibility based on menu type
         *
         * @param {Boolean} state - menu item visibility state
         * @returns {Boolean}
         */
        isContentVisible: function (state) {
            return this.isTypeDrill() ? true : state;
        },

        /**
         * Check if menu has type 'drill'
         *
         * @returns {Boolean}
         */
        isTypeDrill: function () {
            return this.mobile_class === 'drill';
        },

        /**
         * Content Block Init
         *
         * @params {Object} node content node
         * @params {Object} item
         *
         * @desc Start method after render content block
         * @returns {void}
         */
        initContent: function (node, item) {
            helpers.updateFormKey(node);
            helpers.sliderResizeSubscribe(node, item.isActive);
            $(node).trigger('contentUpdated');
        }
    });
});
