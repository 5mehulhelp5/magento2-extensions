/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'jquery',
    'mage/template',
    'text!MageWorx_Checkout/template/spinner.html',
    'jquery/ui'
], function (Component, $, mageTemplate, spinnerTemplate) {
    'use strict';

    return {
        showSpinnerClass: 'layout__pane--loading',

        addSpinner: function (block) {
            var spinnerHtml = mageTemplate(
                spinnerTemplate
            );

            $(block).prepend(spinnerHtml);
        },

        showSpinner: function (block) {
            this.addSpinner(block);
            $(block).addClass(this.showSpinnerClass);
            $(block + '.spinner').css('display', 'flex');
        },

        hideSpinner: function (block) {
            $(block).removeClass(this.showSpinnerClass);
            $(block + '.spinner').hide();
        }
    };
});
