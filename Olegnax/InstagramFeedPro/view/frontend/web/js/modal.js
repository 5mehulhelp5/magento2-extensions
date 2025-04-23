/*
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

define([

    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'mage/translate',
    'mage/url',
    'jquery-ui-modules/widget',
], function ($, modal, mageTemplate, $t, urlBuilder) {
    // noinspection JSDuplicatedDeclaration
    window.hasOwnProperty('OxCopyToClipboard') || (window.OxCopyToClipboard = function (text, parent, success, error) {
        // noinspection JSDuplicatedDeclaration,ES6ConvertVarToLetConst
        var parent = parent || document.body,
            error = error || function (err) {
                console.error(err)
            },
            $textarea = $('<textarea>').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '2em',
                height: '2em',
                padding: 0,
                border: 'none',
                outline: 'none',
                boxShadow: 'none',
                background: 'transparent',
            }).val(text).appendTo(parent);
        $textarea.get(0).focus();
        $textarea.get(0).select();

        try {
            if (document.execCommand('copy')) {
                "function" == typeof success && success.call();
            } else {
                "function" == typeof error && error.call();
            }
        } catch (err) {
            "function" == typeof error && error.call(null, err);
        }
        $textarea.remove();
    });

    function debouncer(func, timeout) {
        var timeoutID,
            timeout = timeout || 500;
        return function () {
            var scope = this,
                args = arguments;
            clearTimeout(timeoutID);
            timeoutID = setTimeout(function () {
                func.apply(scope, Array.prototype.slice.call(args));
            }, timeout);
        }
    }

    $.widget('mage.OxInstagramFeedModal', {
        options: {
            template: 'Olegnax_InstagramFeedPro/template/modal.html',
            relatedProductClass: 'ox-instagram__related-products',
            relatedHotSpotClass: 'ox-instagram__related-hotspots',
            selectedHotSpotClass: 'ox-inst__hs-active',
            appendTo: 'body',
            dialogOptions: {
                type: 'popup',
                modalClass: 'ox-instagram-modal',
                modalVisibleClass: '_show',
                responsive: false,
                responsiveClass: '',
                innerScroll: false,
                autoOpen: true,
                clickableOverlay: true,
                title: '',
                buttons: [],
            },
            text: {
                'prev': $t('Previous'),
                'next': $t('Next'),
                'close': $t('Close'),
                'likes': $t('Like(s)'),
                'comments': $t('Comment(s)'),
                'follow': $t('follow'),
                'loading': $t('Loading'),
                'hotspot': $t('Hotspot'),
                'share_link': $t('Share URL'),
                'copy': $t('Copy to Clipboard'),
                'copied': $t('URL Copied to Clipboard!'),
                'photo_by': $t('Photo by'),
                'profile_photo': $t('profile photo'),
                'play': $t('Play Video'),
            },
            relatedLayout: 'list',
            showNextPrev: true,
            showShare: true,
            showDate: true,
            showCaption: true,
            showLikes: true,
            showComments: true,
            showFollow: true,
            lazy: true,
            lazy_placeholder: '',
            loop: false,
            disabled: false,
            mediaBreakpoint: 767,
            socialButtons: [
                {
                    name: 'facebook',
                    title: $t('Facebook'),
                    link: 'https://www.facebook.com/sharer/sharer.php?u=<%- location %>'
                },
                {
                    name: 'twitter',
                    title: $t('Twitter'),
                    link: 'https://twitter.com/intent/tweet?url=<%- location %>&text=<%- caption %>'
                },
                {
                    name: 'pinterest',
                    title: $t('Pinterest'),
                    link: 'http://pinterest.com/pin/create/button/?url=<%- location %>&media=<%- media_url %>'
                }
            ]
        },
        _create: function () {
            this.options.disabled = window.self !== window.top;
            if (!window.hasOwnProperty('OxInstagramTemplate')) {
                window.OxInstagramTemplate = {};
            }
            if (!window.hasOwnProperty('OxInstagramRelated')) {
                window.OxInstagramRelated = {};
            }
            let _self = this;
            if (!this.element.data('uniqModalID')) {
                this.element.data('uniqModalID', Math.random());
            }
            this.element.off('click' + this.eventNamespace, '[data-instagram-modal]')
            .on('click' + this.eventNamespace, '[data-instagram-modal]', $.proxy(this._click, this));
            $('body').off('click.OxInstagramFeedModal', '.ox-i-social-button:not(.ox-i-link)')
            .on('click.OxInstagramFeedModal', '.ox-i-social-button:not(.ox-i-link)', function (e) {
                let newWind = window.open($(this).attr('href'), $(this).attr('title'), "width=420,height=320,resizable=yes,scrollbars=yes,status=yes");
                if (newWind) {
                    newWind.focus();
                    e.preventDefault();
                }
            }).off('click.OxInstagramFeedModal', '.ox-i-social-button.ox-i-link')
            .on('click.OxInstagramFeedModal', '.ox-i-social-button.ox-i-link', function (e) {
                e.preventDefault();
                let $this = $(this);
                window.OxCopyToClipboard(window.location.href, _self.element, function () {
                    $this.addClass('-show-tooltip').find('.ox-tooltip').text(_self.options.text.copied);
                    setTimeout(function () {
                        $this.removeClass('-show-tooltip').find('.ox-tooltip').text(_self.options.text.copy);
                    }, 3000);
                });

            });

            this.__openByURL();
        },
        _click: function (e) {
            let $target = $(e.currentTarget || e.target),
                data = $target.data('instagramModal');
            if (data)
                e.preventDefault();
            if ('string' === typeof data && data) {
                data = JSON.parse(data);
            }

            data.nextItem = $target.data('instagramNext');
            data.prevItem = $target.data('instagramPrev');

            this.openModal(data);
        },
        _clickNav: function (e) {
            let $target = $(e.currentTarget),
                parentUniqModalID = $target.closest('.ox-instagram-modal__content').eq(0).data('uniqModalID'),
                uniqModalID = this.element.data('uniqModalID'),
                data = $target.data('instagramId');
            if (data && parentUniqModalID == uniqModalID) {
                let $itemWrapper = this.element.find('#' + data),
                    $itemTarget = $itemWrapper.find('[data-instagram-modal]').first();
                if ($itemTarget.length) {
                    $itemTarget.trigger('click' + this.eventNamespace);
                    e.preventDefault();
                }
            }
        },
        openModal: function (data) {
            if ( this.options.disabled ) return;
            this._prepareNextPrev(data);

            // noinspection JSUnresolvedVariable
            let modalId = 'ox-modal_' + data.full_id;
            let needRelated = false,
                _self = this;
            $('.modals-wrapper aside[role="dialog"]').each(function(){
                let $modal = $(this).find('[data-role="content"]').children().eq(0),
                  modal = $modal.data('mageModal') || $modal.data('mage-Modal')
                if (_self.modal) {
                    if (modal && modal.options.isOpen && !_self.modal.element.is(modal.element)) {
                        modal.closeModal();
                    }
                } else if (modal && modal.options.isOpen) {
                    modal.closeModal();
                }
            });
            if (!this.modal) {
                let $modal = $('.ox-instagram-modal__content');
                if ($modal.length && ($modal.data('mageModal') || $modal.data('mage-Modal'))) {
                    this.modal = $modal.data('mageModal') || $modal.data('mage-Modal');
                }
            }
            if (!this.modal) {
                $(this.options.appendTo).append(
                    $('<div>')
                    .attr({
                        class: 'ox-instagram-modal__content',
                        id: modalId
                    }).data('uniqModalID', this.element.data('uniqModalID'))
                    .html(this._getHtml(data))
                );
                let $modal = $('#' + modalId);

                if ($modal.length) {
                    this.modal = modal(this.options.dialogOptions, $modal);
                    let _ua = this.modal.__proto__._unsetActive;
                    this.modal.__proto__._unsetActive = function () {
                        _ua.call(this);
                        if (this.element.is($modal)) {
                            _self._close();
                        }
                    };
                    this._setModalEvents($modal);
                    $('body').trigger('contentUpdated');
                } else {
                    throw 'Where is the "' + modalId + '" model element?';
                }
                needRelated = true;
            } else {
                if (modalId !== this.modal.element.attr('id')
                    || this.element.data('uniqModalID') !== this.modal.element.data('uniqModalID')
                ) {
                    this.modal.element.attr('id', modalId).data('uniqModalID', this.element.data('uniqModalID')).html(this._getHtml(data));
                    this._setModalEvents(this.modal.element);
                    $('body').trigger('contentUpdated');
                    needRelated = true;
                }
                if (!this.modal.options.isOpen) {
                    this.modal.openModal();
                }
            }
            // noinspection JSUnresolvedVariable
            this._updateHistory(data.full_id);
            // noinspection JSUnresolvedVariable
            if (data.related) {
                if (needRelated) {
                    // noinspection JSUnresolvedVariable
                    this._loadHotspots(data.intspost_id);
                }
            } else {
                this._appendRelateds('', '');
            }
            this._modifyDate(this.modal.element);

            return this.element;
        },
        _setModalEvents: function($modal){
            $modal
            .off('click' + this.eventNamespace, '[data-instagram-id]')
            .on('click' + this.eventNamespace, '[data-instagram-id]', $.proxy(this._clickNav, this))
            .off('mouseenter' + this.eventNamespace, '[data-entity-id]')
            .off('mouseleave' + this.eventNamespace, '[data-entity-id]')
            .on('mouseenter' + this.eventNamespace, '.' + this.options.relatedHotSpotClass + ' [data-entity-id]', $.proxy(this._menterHotSpot, this))
            .on('mouseleave' + this.eventNamespace, '.' + this.options.relatedHotSpotClass + ' [data-entity-id]', $.proxy(this._mleaveHotSpot, this))
            .on('mouseenter' + this.eventNamespace, '.' + this.options.relatedProductClass + ' [data-entity-id]', $.proxy(this._menterProduct, this))
            .on('mouseleave' + this.eventNamespace, '.' + this.options.relatedProductClass + ' [data-entity-id]', $.proxy(this._mleaveProduct, this))
            .off('click' + this.eventNamespace, '.' + this.options.relatedHotSpotClass + '>div')
            .on('click' + this.eventNamespace, '.' + this.options.relatedHotSpotClass + '>div', $.proxy(this._recalcTooltip, this));
            let _self = this;
            $(window).off('resize' + this.eventNamespace)
            .on('resize' + this.eventNamespace, debouncer($.proxy(this._recalcTooltips, _self), 100));
        },
        _recalcTooltips: function (e) {
            if (this.options.mediaBreakpoint > e.currentTarget.innerWidth) {
                this.modal.element.find('.' + this.options.relatedHotSpotClass + ' > div').each($.proxy(function (i, e) {
                    this._recalcTooltip({currentTarget: e});
                }, this));
            }
        },
        _recalcTooltip: function (e) {
            let mediaBreakpoint = this.options.mediaBreakpoint,
                targetId = (e.currentTarget || e.target).id,
                selector = '#' + targetId + ' .ox-hotspot__tooltip',
                $tooltip = this.modal.element.find(selector).eq(0),
                tooltipRect = $tooltip[0].getBoundingClientRect(),
                modalRect = this.modal.element[0].getBoundingClientRect(),
                setTextStyle = (selector, style) => {
                    const styleId = 'ox_inst_modal_styles';
                    const mediaQueryStart = `@media (max-width: ${mediaBreakpoint}px) {`;
                    const selectorStyle = `head style#${styleId}`;
                
                    // Ensure the style tag exists
                    let $styleTag = $(selectorStyle);
                    if (!$styleTag.length) {
                        $styleTag = $('<style>').attr({ id: styleId }).appendTo('head');
                    }
                
                    // Parse existing styles
                    const existingStyles = $styleTag.text()
                        .replace(mediaQueryStart, '')
                        .replace(/}$/, '')
                        .split('}')
                        .filter(rule => rule.trim().length > 0) // Filter out empty rules
                        .reduce((acc, rule) => {
                            const [key, value] = rule.trim().split('{').map(part => part.trim());
                            if (key && value) acc[key] = value;
                            return acc;
                        }, {});
                
                    // Update or add the new style
                    existingStyles[selector.trim()] = style.trim();
                
                    // Rebuild the style string
                    const updatedStyles = Object.entries(existingStyles)
                        .map(([key, value]) => `${key}{${value}}`)
                        .join('');
                
                    $styleTag.text(`${mediaQueryStart}${updatedStyles}}`);
                };
                let tooltipTransform = $tooltip.css('transform').replace('matrix(', '').replace(')', '').split(', ').map(parseFloat),
                     tooltipTY = tooltipTransform[5],  
                     tooltipTX = tooltipTransform[4];
                if (modalRect.left > tooltipRect.left) {
                        tooltipTX = tooltipTransform[4] + modalRect.left - tooltipRect.left;                    
                } else if (modalRect.right < tooltipRect.right) {
                        tooltipTX = tooltipTransform[4] + modalRect.right - tooltipRect.right;                    
                }                
                if (modalRect.top > tooltipRect.top) {
                        tooltipTY = ((tooltipTY + (modalRect.top - tooltipRect.top)) * 100 / tooltipRect.height);
                } else {
                    tooltipTY = tooltipTY * 100 / tooltipRect.height;
                }
                if ( modalRect.width < tooltipRect.width) {
                    setTextStyle(selector, 'max-width: ' +  modalRect.width + 'px');
                }
                if(modalRect.left > tooltipRect.left || modalRect.right < tooltipRect.right || modalRect.top > tooltipRect.top){
                    let beforeTX = tooltipTX + tooltipRect.width / 2 + 6;
                    setTextStyle(selector, 'transform: translateX(' + (tooltipTX * 100 / tooltipRect.width) + '%) translateY(' + tooltipTY + '%)');
                    setTextStyle(selector + ':before', 'left:calc(50% - ' + beforeTX + 'px)');
                }

        },
        _modifyDate:function(parent){
            $('[data-datetime]', parent).each(function () {
                let $this = $(this),
                    datetime = $this.data('datetime'),
                    date = new Date(datetime + ' GMT+0000'),
                    interval = new Date() - date,
                    _interval = Math.round(interval/86400000);
                if (1<_interval) {
                    if (7>=_interval) {
                        $this.html(_interval + $t(' days ago'));
                    }
                    return;
                }
                _interval = Math.round(interval/3600000);
                if (1<_interval) {
                    $this.html(_interval + $t(' hours ago'));
                    return;
                }
                _interval = Math.round(interval/60000);
                if (1<_interval) {
                    $this.html(_interval + $t(' minutes ago'));
                    return;
                }
                _interval = Math.round(interval/1000);
                if (1<_interval) {
                    $this.html(_interval + $t(' seconds ago'));
                    return;
                }
                $this.html('');
            });
        },
        _close: function () {
            this._updateHistory();
        },
        _updateHistory: function (id) {
            if (typeof history.replaceState === 'function') {
                history.replaceState(null, null, this._getPostUrl(id));
            }
        },
        __curentHashTag: function () {
            return (new URL(window.location.href)).hash.toString();
        },
        _getPostUrl: function (id) {
            return window.location.href.replace(this.__curentHashTag(), '') + (id ? '#/p/' + id : '');
        },
        _menterHotSpot: function (e) {
            this._mleaveHotSpot();
            let $target = $(e.currentTarget || e.target),
                id = parseInt($target.data('entityId'), 10);
            this.modal.element.find('.' + this.options.relatedProductClass + ' [data-entity-id="' + id + '"]').addClass(this.options.selectedHotSpotClass);
        },
        _mleaveHotSpot: function () {
            this.modal.element.find('.' + this.options.relatedProductClass + ' [data-entity-id]').removeClass(this.options.selectedHotSpotClass);
        },
        _menterProduct: function (e) {
            this._mleaveProduct();
            let $target = $(e.currentTarget || e.target),
                id = parseInt($target.data('entityId'), 10);
            this.modal.element.find('.' + this.options.relatedHotSpotClass + ' [data-entity-id="' + id + '"]').addClass(this.options.selectedHotSpotClass);
        },
        _mleaveProduct: function () {
            this.modal.element.find('.' + this.options.relatedHotSpotClass + ' [data-entity-id]').removeClass(this.options.selectedHotSpotClass);
        },
        __openByURL: function () {
            let code = this.__curentHashTag().match(/\#\/p\/([0-9_]+)$/i) || [];
            if (true !== window.OxInstagramPreOpened && 2 === code.length) {
                this.element.find('#' + code[1] + ' [data-instagram-modal]').trigger('click');
                window.OxInstagramPreOpened = true;
            }
        },
        _prepareNextPrev: function (data) {
            if (!data.nextItem && !data.prevItem) {
                let ar = [],
                    _self = this;
                this.element.find('[data-instagram-modal]').each(function (key, item) {
                    let $target = $(item),
                        data = $target.data('instagramModal');
                    if ('string' === typeof data && data) {
                        data = JSON.parse(data);
                    }
                    // noinspection JSUnresolvedVariable
                    ar[key] = data.full_id;
                }).each(function () {
                    let $target = $(this),
                        data = $target.data('instagramModal'),
                        key;
                    if ('string' === typeof data && data) {
                        data = JSON.parse(data);
                    }
                    // noinspection JSUnresolvedVariable
                    key = ar.indexOf(data.full_id);
                    if (0 < key) {
                        $target.data('instagramPrev', ar[key - 1]);
                    } else if (_self.options.loop)
                        $target.data('instagramPrev', ar[ar.length - 1]);

                    if (key < ar.length - 1) {
                        $target.data('instagramNext', ar[key + 1]);
                    } else if (_self.options.loop)
                        $target.data('instagramNext', ar[0]);
                });
                // noinspection JSUnresolvedVariable
                let key = ar.indexOf(data.full_id);
                if (0 < key) {
                    data.prevItem = ar[key - 1];
                } else if (_self.options.loop)
                    data.prevItem = ar[ar.length - 1];

                if (key < ar.length - 1) {
                    data.nextItem = ar[key + 1];
                } else if (_self.options.loop)
                    data.nextItem = ar[0];

            }
        },
        _socialShare: function (data) {
            // Extend and encode data
            const encodedData = Object.entries({
                location: this._getPostUrl(data.full_id),
                ...data
            }).reduce((acc, [key, value]) => {
                acc[key] = encodeURIComponent(value);
                return acc;
            }, {});

            // Map social buttons and generate links
            const buttons = this.options.socialButtons.map(button => ({
                ...button,
                link: mageTemplate(button.link, encodedData)
            }));
            return buttons;
        },
        _loadHotspots: function (id) {
            if (this._xhr) {
                this._xhr.abort();
            }
            if (window.OxInstagramRelated.hasOwnProperty(id)) {
                return this.__appendRelateds(window.OxInstagramRelated[id]);
            }
            let _self = this;
            this._xhr = $.ajax({
                url: urlBuilder.build('olegnax_instagram/api/related'),
                method: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (data) {
                    if (data.hasOwnProperty('products') && data.hasOwnProperty('hotSpots')) {
                        window.OxInstagramRelated[id] = data;
                    }
                    _self.__appendRelateds(data);
                },
                complete: function () {
                    _self.modal.element.find('.' + _self.options.relatedProductClass).find('.-loader').remove();
                }
            })
        },
        __appendRelateds: function (data) {
            // noinspection JSUnresolvedVariable
            this._appendRelateds(data.products.trim(), data.hotSpots.trim());
        },
        _appendRelateds: function (products, hotSpots) {
            this.modal.element.find('.' + this.options.relatedProductClass).toggleClass('show', 0 < products.length).html(products);
            this.modal.element.find('.' + this.options.relatedHotSpotClass).toggleClass('show', 0 < hotSpots.length).html(hotSpots);
            $('body').trigger('contentUpdated');
        },
        closeModal: function () {
            if (!this.modal)
                return;
            this.modal.closeModal();
            this._updateHistory();
        },
        _getHtml: function (data) {
            return mageTemplate(this._getTemplate(), $.extend(
                {},
                this.options,
                {
                    data: data,
                    socialButtons: this._socialShare(data)
                }
            ));
        },
        _getTemplate: function () {
            if (
                !window.OxInstagramTemplate.hasOwnProperty(this.options.template)
                || !window.OxInstagramTemplate[this.options.template]
            ) {
                window.OxInstagramTemplate[this.options.template] = $.ajax({
                    url: require.toUrl(this.options.template),
                    type: 'GET',
                    async: false,
                    cache: true,
                }).responseText;
            }

            return window.OxInstagramTemplate[this.options.template];
        }
    });
    return $.mage.OxInstagramFeedModal;
});