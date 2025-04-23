/**
 * @author Azaleasoft Team
 * @copyright Copyright (c) 2017 Azaleasoft (https://www.azaleasoft.com)
 * @package Azaleasoft_Asaddressvalidation
 */
define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "jquery/ui"
], function($, modal){
    "use strict";

    $.widget("mage.asAddressValidationAccount", {
        options: {
            formId: "#form-validate",
            triggerId: "#form-validate button[data-action='save-address']",
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
            var fields = {};
            fields["city"] = "city";
            fields["region_id"] = "region_id";
            fields["region"] = "region";
            fields["zip"] = "zip";
            fields["country"] = "country";
            for(var i=0; i<self.options.streetLines; i++) {
                if (i >= 2) {
                    fields["street_" + (i+1)] = "street_1";
                } else {
                    fields["street_" + (i+1)] = "street_" + i;
                }
            }
            self.options.fields = fields;

            var mappings = {};
            mappings["city"] = "city";
            mappings["region_id"] = "region_id";
            mappings["zip"] = "zip";
            mappings["country"] = "country";
            for(var j=0; j<self.options.streetLines; j++) {
                if (j <= 1) {
                    mappings["street_" + j] = "street_" + (j+1);
                }
            }
            self.options.mappings = mappings;
        },

        _bind: function() {
            var self = this;
            $(document).on("click", self.options.triggerId, function(event) {
                if (self.options.addressIsChecked == false) {
                    event.preventDefault();

                    $(self.options.triggerId).attr("disabled", "disabled");

                    self._readAddress();
                    self._callAjax();
                } else {
                    if (self._addressIsChanged()) {
                        self.options.addressIsChecked = false;
                        $(self.options.triggerId).trigger("click");
                    }
                }
            });
        },

        _addressIsChanged: function() {
            var self = this;
            var flag = false;
            var tmpAddress = {};
            for (var field in self.options.fields) {
                if (tmpAddress[self.options.fields[field]] === undefined) {
                    tmpAddress[self.options.fields[field]] = $("#" + field).val();
                } else {
                    tmpAddress[self.options.fields[field]] += " " + $("#" + field).val();
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
                    address[self.options.fields[field]] = $("#" + field).val();
                } else {
                    address[self.options.fields[field]] += " " + $("#" + field).val();
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
                        if (item.message !== undefined && item.message != '') {
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
                $("input[name='street[]']").val("");
                $.each(self.options.mappings, function(key, value){
                    $("#"+value).val(address[key]).trigger("change");
                });

                self._readAddress();
                self.options.addressIsChecked = true;
                $(self.options.triggerId).attr("disabled", null);
                $(self.options.triggerId).trigger("click");
            }
        },

        _resetStatus: function() {
            var self = this;
            self.options.addressIsChecked = false;
            $(self.options.triggerId).attr("disabled", null);
            $(self.options.popupId).empty();
        }
    });

    return $.mage.asAddressValidationAccount;
});
