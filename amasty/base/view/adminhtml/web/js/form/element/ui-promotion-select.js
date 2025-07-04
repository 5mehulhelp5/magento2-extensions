define([
    'Magento_Ui/js/form/element/ui-select',
    'mage/translate'
],function (UiSelect, $t) {
    'use strict';

    return UiSelect.extend({
        defaults: {
            elementTmpl: 'Amasty_Base/grid/filters/elements/ui-promo-select',
            optgroupTmpl: 'Amasty_Base/grid/filters/elements/ui-promo-select-optgroup',
            promoConfig: {
                promoIcon: `<svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M10.1734 6.20057V2.48743C10.1734 0.833571 9.056 0 7.33048 0H4.67042C2.94444 0 1.82569 0.832714 1.82569 2.48743V6.20057H0V15H12V6.20057H10.1734ZM3.65222 2.5373C3.65222 1.98572 4.094 1.54944 4.67037 1.54944H7.33043C7.90501 1.54944 8.34768 1.98572 8.34768 2.5373V6.20072H3.65222V2.5373ZM6.99248 12.9986H4.90521L5.35862 10.8241C5.08541 10.6432 4.90521 10.3419 4.90521 9.99865C4.90521 9.44708 5.37248 8.99922 5.94885 8.99922C6.52521 8.99922 6.99248 9.44708 6.99248 9.99865C6.99248 10.3419 6.81273 10.6432 6.53773 10.8241L6.99248 12.9986Z"
                                    fill="#ADADAD"></path>
                            </svg>`,
                badgeText: $t('Subscribe to Unlock'),
                badgeColor: '#523cc0',
                badgeBgColor: 'rgba(123, 97, 255, 0.15)'
            },
        },

        getInitialValue: function () {
            const values = [this.value(), this.default];
            let value = [];

            values.some(function (v) {
                if (v !== null && v !== undefined && v.length !== 0) {
                    value = v;

                    return true;
                }

                return false;
            });

            return this.normalizeData(value);
        },

        toggleOptionSelected: function (data) {
            if (data.isPromo) {
                return this;
            }

            return this._super(data);
        },

        openChildLevel: function (data) {
            return !data?.disableExpand && this._super(data);
        }
    });
});
