define([
    'jquery',
    'Magento_Ui/js/form/provider',
    'uiRegistry'
], function ($, Provider, uiRegistry) {
    'use strict';

    return Provider.extend({
        fieldsToRemoveIfNotVisible: [],

        /**
         * Saves currently available data.
         *
         * @param {Object} [options] - Addtitional request options.
         * @returns {Provider} Chainable.
         */
        save: function (options) {
            let data = this.get('data');

            $.each(data.use_default, function (key, value) {
                if (this.fieldsToRemoveIfNotVisible[key] !== undefined
                    && !uiRegistry.get('index = ' + key).visible()
                ) {
                    delete data.use_default[key];
                }
            }.bind(this));

            this.client.save(data, options);

            return this;
        }
    });
});
