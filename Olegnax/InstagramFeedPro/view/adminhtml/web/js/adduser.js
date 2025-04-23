define([
    'jquery',
    'mage/translate'
], function($, $t) {
    'use strict';
    $.widget('mage.OxAddUser', {
        options: {
            url: '',
        },
        _create: function () {
            $('body').on('click', '#ox-inst__add-user', $.proxy(this.click, this));
            this.element.removeClass('disabled');
        },
        click: function (e) {
            e.preventDefault();
            var $that = this;
            if(!this.input){
                this.input = $('#ox-inst__add-user-token');
                if(!this.input.length){
                    console.warn($t('OX Instagram: Token form not found'));
                    return;
                }
            }

            this.showError('');
            let token = this.input[0].value;

            if(!token){
                this.showError('Empty Input');
                return;
            }

            $('body').trigger('processStart');
            $.ajax({
                url: this.options.url,
                type: 'POST',
                data: {token: token},
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        $('body').trigger('processStop');
                        $that.showError(response.message);
                    }
                },
                error: function() {
                    $('body').trigger('processStop');
                    $that.showError($t('Error occurred while adding user.'));
                }
            });
        },
        showError: function(messages){
            const wrapper = $('.ox-inst__add-user-messages');
            if(typeof messages === 'string' && messages.trim().length === 0){
                wrapper.html('');
            } else{
                wrapper.html('<div class="message message-error">' + messages + '</div>');
            }
        }
    });
    return $.mage.OxAddUser;
});
