define([
    'jquery',
    'underscore',
    'mage/utils/wrapper'
],function ($, _, Wrapper) {
    "use strict";

    return function (origRules) {
        origRules.getObservableFields = Wrapper.wrap(
            origRules.getObservableFields,
            function (originalAction) {
                var fields = originalAction(),
                    additionalCheckoutFields = [
                        'telephone'
                    ];

                fields = fields.concat(additionalCheckoutFields);

                return fields;
            }
        );

        return origRules;
    };
});
