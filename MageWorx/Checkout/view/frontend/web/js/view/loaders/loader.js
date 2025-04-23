/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'mage/template',
    'jquery',
    'mage/translate',
    'jquery-ui-modules/widget'
], function ( mageTemplate, $) {
    'use strict';

    $.widget('mage.loader', {
        options: {
            texts: {
                loaderText: $.mage.__('Please wait...'),
                imgAlt: $.mage.__('Loading...')
            },
            icon: '',
            template:
                '<div class="loading-mask" data-role="loader">' +
                '<div class="loader">' +
                '<img alt="<%- data.texts.imgAlt %>" src="<%- data.icon %>">' +
                '<p><%- data.texts.loaderText %></p>' +
                '</div>' +
                '</div>',
            mwTemplate:
                '<div class="spinner" data-role="loader">' +
            '<figure class="spinner__figure"><svg viewBox="0 0 104 104" preserveAspectRatio="none">' +
                    '    <circle cx="52" cy="52" r="50" stroke="currentColor" stroke-width="4" fill="transparent"></circle>' +
                '</svg>' +
                '</figure>' +
                '</div>'
        },
        loaderStarted: 0,

        _bind: function () {
            this._on({
                'show.loader': 'show',
                'hide.loader': 'hide',
                'processStop': 'hide',
                'processStart': 'show',
                'contentUpdated.loader': '_contentUpdated'
            });
        },

        _create: function () {
            this._bind();
        },

        _contentUpdated: function (event) {
            this.show(event);
        },

        /** Show loader */
        show: function (e, ctx) {
            this._render();

            this.loaderStarted++;
            this.spinner.show();

            if (ctx) {
                var css = {
                    width: ctx.outerWidth(),
                    height: ctx.outerHeight(),
                    position: 'absolute'
                };

                var position = {
                    my: 'top left',
                    at: 'top left',
                    of: ctx
                };
                this.spinner.css(css).position(position);
            }

            return false;
        },

        /** Hide loader */
        hide: function () {
            if (this.loaderStarted > 0) {
                this.loaderStarted--;
                if (this.loaderStarted === 0) {
                    this.spinner.hide();
                }
            }

            return false;
        },

        /** Render loader */
        _render: function () {
            var html;

            if (this.spinnerTemplate) {
                return;
            }

            if (window.isMageWorxCheckout) {
                this.spinnerTemplate = mageTemplate(this.options.mwTemplate);
                html = $(this.spinnerTemplate({data: this.options}));
            } else {
                this.spinnerTemplate = mageTemplate(this.options.template);
                html = $(this.spinnerTemplate({data: this.options}));
            }

            html.prependTo(this.element);
            this.spinner = html;
        },

        /** Destroy loader */
        _destroy: function () {
            this.spinner.remove();
        }
    });

    /**
     * loaderAjax widget
     */
    $.widget('mage.loaderAjax', {
        options: {
            loadingClass: 'ajax-loading',
            defaultContainer: '[data-container=body]'
        },

        _create: function () {
            this._bind();

            if (window.console && !this.element.is(this.options.defaultContainer) && $.mage.isDevMode(undefined)) {
                console.warn('This widget is intended to be attached to the body, not below.');
            }
        },

        _bind: function () {
            $(document).on({
                'ajaxComplete': this._onAjaxComplete.bind(this),
                'ajaxSend': this._onAjaxSend.bind(this)
            });
        },

        _getJqueryObj: function (loaderContextObject) {
            var ctxObject;

            if (loaderContextObject) {
                if (loaderContextObject.jquery) {
                    ctxObject = loaderContextObject;
                } else {
                    ctxObject = $(loaderContextObject);
                }
            } else {
                ctxObject = $('[data-container="body"]');
            }

            return ctxObject;
        },

        _onAjaxSend: function (e, jqxhr, settings) {
            var ctxObject;

            $(this.options.defaultContainer).addClass(this.options.loadingClass)
                .attr({'aria-busy': true});

            if (settings && settings.showLoader) {
                ctxObject = this._getJqueryObj(settings.loaderContext);
                ctxObject.trigger('processStart');

                if (window.console && !ctxObject.parents('[data-role="loader"]').length) {
                    console.warn(
                        'Expected to start loader but did not find one in the dom'
                    );
                }
            }
        },

        _onAjaxComplete: function (e, jqxhr, settings) {
            $(this.options.defaultContainer).removeClass(this.options.loadingClass)
                .attr('aria-busy', false);
            if (settings && settings.showLoader) {
                this._getJqueryObj(settings.loaderContext)
                    .trigger('processStop');
            }
        }
    });

    return {
        loaderAjax: $.mage.loaderAjax,
        loader: $.mage.loader
    };
});
