/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'mage/utils/wrapper',
    'ko',
    'jquery',
    'Magento_Ui/js/lib/knockout/template/loader',
    'mage/template'
], function (wrapper,ko, $, templateLoader, template) {
    'use strict';

    var blockLoaderTemplatePath = 'MageWorx_Checkout/spinner',
        blockContentLoadingClass = 'layout__pane--loading',
        blockLoaderElement = $.Deferred(),
        loaderImageHref = $.Deferred(),
        blockLoader,
        blockLoaderClass;

    templateLoader.loadTemplate(blockLoaderTemplatePath).done(function (blockLoaderTemplate) {
        loaderImageHref.done(function (loaderHref) {
            blockLoader = template($.trim(blockLoaderTemplate), {
                loaderImageHref: loaderHref
            });

            blockLoader = $(blockLoader);

            blockLoaderClass = '.' + blockLoader.attr('class');
            blockLoaderElement.resolve();
        });
    });

    function isLoadingClassRequired(block) {
        var position = block.css('position');
        if (position === 'fixed' || position === 'absolute') {
            return false;
        }

        return true;
    }

    /**
     * Add mageworx loader to block.
     */
    function addBlockLoader(block) {
        block.find(':focus').blur();

        block.find('input:disabled, select:disabled').addClass('_disabled');
        block.find('input, select').prop('disabled', true);

        if (isLoadingClassRequired(block)) {
            block.addClass(blockContentLoadingClass);
        }
        block.prepend(blockLoader.clone());
    }

    /**
     * Remove mageworx loader from block.
     */
    function removeBlockLoader(block) {
        if (!block.has(blockLoaderClass).length) {
            return;
        }
        block.find(blockLoaderClass).remove();
        block.find('input:not("._disabled"), select:not("._disabled")').prop('disabled', false);
        block.find('input:disabled, select:disabled').removeClass('_disabled');
        block.removeClass(blockContentLoadingClass);
    }

    return function (blockLoaderFunction) {
        return wrapper.wrap(blockLoaderFunction, function (originalBlockLoaderFunctionFunction, loaderHref) {
            if (window.isMageWorxCheckout) {
                loaderImageHref.resolve(loaderHref);
                ko.bindingHandlers.blockLoader = {
                    update: function (element, displayBlockLoader) {
                        element = $(element);

                        if (ko.unwrap(displayBlockLoader())) {
                            blockLoaderElement.done(addBlockLoader(element));
                        } else {
                            blockLoaderElement.done(removeBlockLoader(element));
                        }
                    }
                };
            } else {
                originalBlockLoaderFunctionFunction(loaderHref);
            }
        });
    };
});
