define([
    'jquery',
    'collapsible'
], function ($) {

    $.widget('mage.amFaqCollapsible', $.mage.collapsible, {
        _create: function () {
            // WCAG compatibility
            this.options.content = $(this.element).find(this.options.content);
            this._super();
        }
    });

    return $.mage.amFaqCollapsible;
});
