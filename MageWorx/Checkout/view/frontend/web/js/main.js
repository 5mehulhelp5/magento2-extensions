define([
        "jquery"
    ],
    function ($) {
        "use strict";

        function debounce(callback, wait) {
            let timeout;

            return (...args) => {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => callback.apply(context, args), wait);
            };
        }

        const closeModal = ($modal) => {
            $('body').removeClass('body--scroll-locked');
            $modal.removeClass('modal--is-visible').delay(100).queue(function () {
                $(this).hide().dequeue();
            });
        };

        const Elements = {
            $window: $(window),
            $body: $('.body'),
            $radiogroupControl: $('.radiogroup__control'),
            $collapsibleHeader: $('.collapsible__header'),
            $tooltip: $('.tooltip'),
            $modalActivator: $('[data-modal]'),
            $modal: $('.modal--on-load')
        };

        Elements.$tooltip.each((_, item) => {
            const $item = $(item);

            new Tooltip($item, {
                placement: 'top',
                offset: '0 12px 0 0',
                title: $item.data('content'),
                template: '<div class="tooltip__content" role="tooltip"><div class="tooltip-arrow tooltip__arrow"></div><div class="tooltip-inner"></div></div>'
            });
        });

        Elements.$modalActivator.on('click', function (e) {
            e.preventDefault();

            const $this = $(this);
            const $modalId = $this.data('modal');
            const $modal = $(`[data-modal-id="${$modalId}"`);

            Elements.$body.addClass('body--scroll-locked');
            $modal.css('display', 'flex').delay(100).queue(function () {
                $(this).addClass('modal--is-visible').dequeue();
                $(this).find('input:first').focus();
            })
        });

        Elements.$modal.on('click', '.modal__close', function (e) {
            closeModal($(e.delegateTarget));
        });

        Elements.$modal.on('click', function (e) {
            if (e.target === this) {
                closeModal($(this));
            }
        });

        Elements.$radiogroupControl.on('change', function () {
            const $this = $(this);
            const isSelectedClassName = 'radiogroup__header--is-selected';
            const $radiogroup = $this.parents('.radiogroup');
            const $header = $this.parent();
            const $radiogroupHeaders = $radiogroup.children().not($header);

            $radiogroupHeaders.removeClass(isSelectedClassName);
            $header.addClass(isSelectedClassName);
        });

        Elements.$collapsibleHeader.on('click', function () {
            $(this)
                .parent()
                .toggleClass('collapsible--expanded');
        });

        Elements.$window.on('keydown', function (e) {
            if (e.code === 'Escape') {
                closeModal(Elements.$modal);
            }
        });
    }
);
