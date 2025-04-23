/* eslint-disable max-len */
/**
 *  Amasty Account UI Component
 */

define([
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko, _, Component, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            tab_index: 1,
            imports: {
                mobile_class: 'index = ammenu_wrapper:color_settings',
                color_settings: 'index = ammenu_wrapper:color_settings',
                welcome_message: 'index = ammenu_wrapper:welcome_message',
                settings: 'index = ammenu_wrapper:settings',
                root_templates: 'index = ammenu_wrapper:templates',
                icons: 'index = ammenu_wrapper:icons',
                activeTab: 'index = ammenu_tabs_wrapper:activeTab'
            },
            listens: {
                activeTab: 'tabChange'
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();
            this.customer(customerData.get('customer')());
            this.initWishlist();
            this._initElems(this.items, 0);
            this.elems(this.items);

            return this;
        },

        initWishlist: function () {
            const wishlistIndex = this.items.findIndex((item) => item.id === 'wishlist');
            if (wishlistIndex === -1) {
                return;
            }

            this.items[wishlistIndex].counter = customerData.get('wishlist')().counter;
        },

        /**
         * Check for match account tab by index
         *
         * @return {Boolean}
         */
        matchTab: function () {
            return this.activeTab() === this.tab_index;
        },

        /**
         * Listener for 'activeTab' value changes
         *
         * @return {void}
         */
        tabChange: _.once(function () {
            this.rendered(this.matchTab());
        }),

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    elems: [],
                    customer: false,
                    welcome_message: false,
                    wishlist: false,
                    activeTab: 0,
                    rendered: false
                });

            return this;
        },

        /**
         *  Init account elements
         *
         * @param {Object} elems
         * @param {Number} level
         * @param {Object} [parent]
         * @return {void}
         */
        _initElems: function (elems, level, parent) {
            var self = this;

            _.each(elems, function (elem) {
                elem.isVisible = ko.observable(_.isUndefined(elem.isVisible) ? true : elem.isVisible);
                elem.isActive = ko.observable(false);
                elem.isFocused = ko.observable(false);
                elem.level = ko.observable(level);
                elem.all_link = false;
                elem.color = ko.observable(self.getElementColor(elem.id));
                elem.base_color = elem.color();
                elem.hide_content = false;
                elem.url = elem.url || '';
                elem.additionalClasses = '';
                elem.column_count = ko.observable(1);
                elem.content = '<!-- ko scope: "index = ammenu_columns_wrapper" --><!-- ko template: getTemplate() --><!-- /ko --><!-- /ko -->';
                elem.isContentActive = ko.observable(false);
                elem.submenu_type = 0;
                elem.parent = parent;
                elem.width = 1;
                elem.elems = elem.elems || [];
                elem.isSubmenuVisible = ko.observable(elem.elems.length);
                elem.rendered = ko.observable(false);

                if (elem.elems && elem.elems.length) {
                    self._initElems(elem.elems, level + 1, elem);
                }

                if (level === 0) {
                    self._initRoot(elem);
                }
            });
        },

        /**
         *  Get highlight or common account link color
         *
         * @param {String} type
         * @returns {String} color
         */
        getElementColor: function (type) {
            return this._isLinkActive(type)
                ? this.color_settings.current_category_color
                : this.color_settings.main_menu_text;
        },

        /**
         *  Compare link with a href
         *
         * @param {String} type
         * @returns {Boolean}
         */
        _isLinkActive: function (type) {
            return window.location.href.indexOf(type) !== -1;
        },

        /**
         * Init root submenu element
         *
         * @param {Object} elem
         * @return {void}
         */
        _initRoot: function (elem) {
            elem.submenu_position = {
                top: ko.observable(),
                bottom: ko.observable()
            };

            elem.nodes = {};
        }
    });
});
