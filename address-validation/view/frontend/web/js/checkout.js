/**
 * @author Azaleasoft Team
 * @copyright Copyright (c) 2017 Azaleasoft (https://www.azaleasoft.com)
 * @package Azaleasoft_Asaddressvalidation
 */
define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "Magento_Checkout/js/model/quote",
    "Magento_Customer/js/model/customer",
    "jquery/ui"
], function($, modal, quote, customer){
    "use strict";

    $.widget("mage.asAddressValidationCheckout", {
        options: {
            formId: "#co-shipping-form",
            popupCancelButtonId: "button.action-hide-popup",
            popupConfirmButtonId: "button.action-save-address",
            nextButtonId: "#shipping-method-buttons-container button.continue",
            popupId: "#address-suggest",
            addressIsChecked: false,
            currentModal: null,
            address: {},
            fields: {},
            mappings: {}
        },

        _create: function() {
            this._initOptions();
            this._bind();
        },

        _initOptions: function() {
            var self = this;
            if (customer.isLoggedIn() && customer.getShippingAddressList().length > 0) {
                self.options.triggerId = "button.action-save-address";
            } else {
                self.options.triggerId = "button.continue";
            }

            var fields = {};
            fields["city"] = "city";
            fields["region_id"] = "region_id";
            fields["region"] = "region";
            fields["postcode"] = "zip";
            fields["country_id"] = "country";
            for(var i=0; i<self.options.streetLines; i++) {
                if (i >= 2) {
                    fields["street[" + i + "]"] = "street_1";
                } else {
                    fields["street[" + i + "]"] = "street_" + i;
                }
            }
            self.options.fields = fields;

            var mappings = {};
            mappings["city"] = "city";
            mappings["region_id"] = "region_id";
            mappings["zip"] = "postcode";
            mappings["country"] = "country_id";
            for(var j=0; j<self.options.streetLines; j++) {
                if (j <= 1) {
                    mappings["street_" + j] = "street[" + j + "]";
                }
            }
            self.options.mappings = mappings;
        },

        _bind: function() {
            var self = this;
            if (customer.isLoggedIn() && customer.getShippingAddressList().length > 0) {
                $(document).on("click", self.options.triggerId, function(event) {
                    if (self.options.addressIsChecked == false) {
                        event.preventDefault();
                    }
                });
                $(document).on("change", self.options.formId + " input, " + self.options.formId + " select", function(event) {
                    $(self.options.popupConfirmButtonId).attr("disabled", "disabled");
                    if ($(".address-check").length < 1) {
                        $(self.options.popupConfirmButtonId).after('<button class="action primary address-check">'+$.mage.__("Check Address")+'</button>');
                    }
                });
                $(document).on("click", "button.address-check", function() {
                    self._readAddress();
                    self._callAjax();
                });
            } else {
                $(document).on("click", self.options.triggerId, function(event) {
                    if (self.options.addressIsChecked == false) {
                        event.preventDefault();
                        $(self.options.triggerId).attr("disabled", "disabled");
                        self._readAddress();
                        self._callAjax();
                    } else {
                        if (self._addressIsChanged()) {
                            self.options.addressIsChecked = false;
                            return;
                        }
                    }
                });
            }
        },

        _addressIsChanged: function() {
            var self = this;
            var flag = false;
            var tmpAddress = {};
            for (var field in self.options.fields) {
                if (tmpAddress[self.options.fields[field]] === undefined) {
                    tmpAddress[self.options.fields[field]] = $(self.options.formId + " [name='" + field + "']").val();
                } else {
                    tmpAddress[self.options.fields[field]] += " " + $(self.options.formId + " [name='" + field + "']").val();
                }
            }
            for (var row in tmpAddress) {
                if (tmpAddress[row] != self.options.address[row]) {
                    flag = true;
                }
            }
            return flag;
        },

        _readAddress: function() {
            var self = this;
            var address = {};
            for (var field in self.options.fields) {
                if (address[self.options.fields[field]] === undefined) {
                    address[self.options.fields[field]] = $(self.options.formId + " [name='" + field + "']").val();
                } else {
                    address[self.options.fields[field]] += " " + $(self.options.formId + " [name='" + field + "']").val();
                }
            }
            return self.options.address = address;
        },

        _callAjax: function() {
            var self = this;
            $.ajax({
                type: "post",
                url: self.options.urlAddressValidation,
                data: self.options.address,
                cache: false,
                dataType: "json"
            }).done(function(data){
                self._parseData(data);
            }).complete(function(){
                self._openModal();
            });
        },

        _parseData: function(data) {
            var self = this;
            var html_orig = "";
            var html_sugg = "";
            data.forEach(function(item, index){
                if (item.type == 'O') {
                    if (self.options.allowOriginalAddress == 1) {
                        html_orig += "<p><input type='radio' name='addresses' data-bind='"+JSON.stringify(item)+"' />"+ item.fulladdress + "</p>";
                    } else if (self.options.allowOriginalAddress == 0) {
                        html_orig += "<p>" + item.fulladdress + "</p>";
                    }
                } else if (item.type == 'S') {
                    if (item.error !== undefined) {
                        html_sugg += "<p><span style='color:#b30000;'>* " + item.error + "</p>";
                    } else {
                        var message = '';
                        if (item.message != null && item.message != '') {
                            message = "<br/><span style='color:#b30000;'>* " + item.message + "</span>";
                        }
                        if (index == 1) {
                            html_sugg += "<p><input type='radio' name='addresses' data-bind='"+JSON.stringify(item)+"' checked='checked' />"+ item.fulladdress + message + "</p>";
                        } else {
                            html_sugg += "<p><input type='radio' name='addresses' data-bind='"+JSON.stringify(item)+"' />"+ item.fulladdress + message + "</p>";
                        }
                    }
                }
            });
            if (html_orig == "") {
                html_orig = "<p>"+$.mage.__("No data found")+"</p>";
            }
            if (html_sugg == "") {
                html_sugg = "<p>"+$.mage.__("No data found")+"</p>";
            }
            $(self.options.popupId).empty().html("<p>"+$.mage.__("Choose original address")+":</p>" + html_orig + "<p>"+$.mage.__("Choose suggested address")+":</p>" + html_sugg);
        },

        _openModal: function() {
            var self = this;
            if (self.options.currentModal == null) {
                var params = {
                    type: "popup",
                    autoOpen: true,
                    title: $.mage.__("Address Validation"),
                    responsive: true,
                    innerScroll: true,
                    modalClass: "as-address-suggest-modal",
                    buttons: [
                        {
                            text: $.mage.__("Cancel"),
                            class: "action",
                            click: function() {
                                self._resetStatus();
                                this.closeModal();
                                self.options.currentModal = null;
                            }
                        },
                        {
                            text: $.mage.__("Confirm"),
                            class: "action primary",
                            click: function() {
                                var selected = $("input[type=radio][name=addresses]:checked");
                                if (selected === undefined || selected.length == 0) {
                                  return;
                                }
                                self._loadAddress();
                                this.closeModal();
                                self.options.currentModal = null;
                            }
                        }
                    ]
                };
                self.options.currentModal = modal(params, $(self.options.popupId));
            }
        },

        _loadAddress: function() {
            var self = this;
            var selected = $("input[type=radio][name=addresses]:checked");
            if (selected.length > 0) {
                var address = JSON.parse(selected.attr("data-bind"));
                for(var i=0; i<self.options.streetLines; i++) {
                    $(self.options.formId + " [name='street["+i+"]']").val("").trigger("change");
                }
                $.each(self.options.mappings, function(key, value){
                    $(self.options.formId + " [name='"+value+"']").val(address[key]).trigger("change");
                });
                if (customer.isLoggedIn() && customer.getShippingAddressList().length > 0) {
                    $(self.options.popupConfirmButtonId).attr("disabled", null);
                    $("button.address-check").remove();
                } else {
                    $(self.options.triggerId).attr("disabled", null);
                }
                self.options.addressIsChecked = true;
            }
        },

        _resetStatus: function() {
            var self = this;
            if (customer.isLoggedIn() && customer.getShippingAddressList().length > 0) {
                self.options.addressIsChecked = false;
                $(self.options.popupConfirmButtonId).attr("disabled", "disabled");
            } else {
                self.options.addressIsChecked = false;
                $(self.options.nextButtonId).attr("disabled", null);
            }
            $(self.options.popupId).empty();
        }
    });

    return $.mage.asAddressValidationCheckout;
});
