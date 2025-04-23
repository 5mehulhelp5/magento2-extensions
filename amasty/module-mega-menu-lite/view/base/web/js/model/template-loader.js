define([
    'jquery',
    'Magento_Ui/js/lib/knockout/template/loader'
], function ($, templateLoader) {
    let templatesToLoad = [];

    return {
        /**
         *
         * @param {string[]} templates
         * @return {void}
         */
        addTemplates(templates) {
            templatesToLoad.push(...templates);
        },

        /**
         * @return {void}
         */
        setLoadEvent: function () {
            $(document).ready(() => {
                templatesToLoad.forEach((template) => {
                    templateLoader.loadTemplate(template);
                });

                templatesToLoad = [];
            });
        }
    };
});
