define([
    'jquery',
], function ($) {
    'use strict';

    $.widget('mage.xnotifRestockAlert', {
        options: {
            param: 'is_restock'
        },
        selectors: {
            checkbox: '.am-restock-checkbox',
            linkForm: '.product.alert',
            link: '.action.alert',
            xnotifBlock: '.amxnotif-block'
        },
        attributes: {
            action: 'action',
            href: 'href',
            dataAction: 'data-action'
        },

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            this.initHandler();
        },

        /**
         * @returns {void}
         */
        initHandler: function () {
            $(this.element).find(this.selectors.checkbox).on('change', (e) => {
                const data = this.getDataToUpdate();

                if (e.target.checked) {
                    this.addRestockParam(data);
                } else {
                    this.removeRestockParam(data);
                }
            });
        },

        /**
         * @param {Object} data
         * @returns {void}
         */
        addRestockParam: function (data) {
            data.node.attr(data.attribute, this.addRestockToUrl(data.url));
        },

        /**
         * @param {Object} data
         * @returns {void}
         */
        removeRestockParam: function (data) {
            data.node.attr(data.attribute, this.removeRestockFromUrl(data.url));
        },

        /**
         * @returns {Object}
         */
        getDataToUpdate: function () {
            let data = {};

            switch(true) {
                case this.isXnotifBlock():
                    data.node = $(this.element).closest(this.selectors.xnotifBlock);
                    data.attribute = this.attributes.dataAction;
                    data.url = data.node.attr(this.attributes.dataAction);
                    break;
                case this.isLinkForm():
                    data.node = $(this.element).parent(this.selectors.linkForm).find(this.selectors.link);
                    data.attribute = this.attributes.href;
                    data.url = data.node.attr(this.attributes.href);
                    break;
                default:
                    data.node = $(this.element).closest('form');
                    data.attribute = this.attributes.action;
                    data.url = data.node.attr(this.attributes.action);
            }

            return data;
        },

        /**
         * @returns {boolean}
         */
        isLinkForm: function () {
            return !!$(this.element).parent(this.selectors.linkForm).length;
        },

        /**
         * @returns {boolean}
         */
        isXnotifBlock: function () {
            const xnotifBlock = $(this.element).closest(this.selectors.xnotifBlock);

            return !!xnotifBlock.length && !$(xnotifBlock).find('form').length;
        },

        /**
         * @param {string} url
         * @returns {string}
         */
        addRestockToUrl: function (url) {
            const urlObj = new URL(url);
            const searchParams = urlObj.searchParams;

            searchParams.append(this.options.param, 'true');
            urlObj.search = searchParams.toString();

            return urlObj.toString();
        },

        /**
         * @param {string} url
         * @returns {string}
         */
        removeRestockFromUrl: function (url) {
            const urlObj = new URL(url);
            const searchParams = urlObj.searchParams;

            searchParams.delete(this.options.param);
            urlObj.search = searchParams.toString();

            return urlObj.toString();
        }
    });

    return $.mage.xnotifRestockAlert;
});
