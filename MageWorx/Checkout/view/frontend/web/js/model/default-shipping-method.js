/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([], function () {
    'use strict';

    return {
        methodCode: null,
        carrierCode: null,

        /**
         * @returns {string|null}
         */
        getMethodCode: function () {
            return this.methodCode;
        },

        /**
         * @returns {string|null}
         */
        getCarrierCode: function () {
            return this.carrierCode;
        }
    }
});
