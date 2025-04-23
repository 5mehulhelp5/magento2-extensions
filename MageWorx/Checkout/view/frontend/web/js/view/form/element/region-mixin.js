/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'uiLayout',
        'mageUtils'
    ],
    function (
        layout,
        utils
    ) {
        'use strict';

        /**
         * Recursively loops over data to find non-undefined, non-array value
         *
         * @param  {Array} data
         * @return {*} - first non-undefined value in array
         */
        function findFirst(data) {
            var value;

            data.some(function (node) {
                value = node.value;

                if (Array.isArray(value)) {
                    value = findFirst(value);
                }

                return !_.isUndefined(value);
            });

            return value;
        }

        return function (origComponent) {

            if (window.isMageWorxCheckout) {

                var inputNode = {
                    parent: '${ $.$data.parentName }',
                    component: 'Magento_Ui/js/form/element/abstract',
                    template: '${ $.$data.template }',
                    provider: '${ $.$data.provider }',
                    name: '${ $.$data.index }_input',
                    dataScope: '${ $.$data.customEntry }',
                    customScope: '${ $.$data.customScope }',
                    sortOrder: {
                        after: '${ $.$data.name }'
                    },
                    displayArea: 'body',
                    label: '${ $.$data.label }',
                    additionalClasses: 'form__field',
                    value: ''
                };

                return origComponent.extend({
                    /**
                     * Creates input from template, renders it via renderer.
                     *
                     * @returns {Object} Chainable.
                     */
                    initInput: function () {
                        layout([utils.template(inputNode, this)]);

                        return this;
                    },

                    /**
                     * Select first available option
                     *
                     * @returns {Object} Chainable.
                     */
                    clear: function () {
                        var options = typeof this.options == 'function' ? this.options() : this.options,
                            value = this.caption() ? '' : findFirst(options);

                        if (typeof this.value() === 'undefined' && value === '') {
                            return this;
                        }

                        this.value(value);

                        return this;
                    },
                });
            }

            return origComponent;
        };
    });
