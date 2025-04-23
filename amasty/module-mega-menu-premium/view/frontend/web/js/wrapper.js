/**
 *  Extend Amasty MegaMenuLite Wrapper UI Component
 */

define([], function () {
    'use strict';

    return function (Wrapper) {
        return Wrapper.extend({
            defaults: {
                components: {
                    ammenu_submenu_mobile: {
                        name: 'ammenu_submenu_mobile',
                        component: 'Amasty_MegaMenuPremium/js/submenu/mobile',
                        deps: ['index = ammenu_wrapper'],
                        enable_condition: function () {
                            return this.isMobile();
                        }
                    }
                }
            },

            /**
             * @inheritDoc
             */
            initialize: function () {
                return this._super();
            },

            /**
             * Init root submenu element
             *
             * @param {Object} item
             */
            _initRoot: function (item) {
                this._super();

                if (item.submenu_animation) {
                    item.additionalClasses.push('-animation-' + item.submenu_animation);
                }
            }
        });
    };
});
