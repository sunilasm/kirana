define([
    'jquery',
    'Magento_Checkout/js/view/minicart',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm'
], function ($ ,Component, alert, confirm) {
    'use strict';

    return Component.extend({
        confirmMessage: $.mage.__('Are you sure you would like to remove all items from the shopping cart?'),
        emptyCartUrl: window.checkout.emptyMiniCart,

        emptyCartAction: function (element) {
            var self = this,
                href = self.emptyCartUrl;
            $(element).on('click', function () {
                var el = this;
                confirm({
                    content: self.confirmMessage,
                    actions: {
                        confirm: function () {
                            self._removeAllItems(href, el);
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            });
        },

        _removeAllItems: function (href, elem) {
            $.ajax({
                url: href,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $(elem).attr('disabled', 'disabled');
                },
                complete: function () {
                    $(elem).attr('disabled', null);
                }

            }).done(function (response) {
                if (!response.errors) {

                } else {
                    var msg = response.message;

                    if (msg) {
                        alert({
                            content: msg
                        });
                    }
                }
            }).fail(function (error) {
                console.log(JSON.stringify(error));
            });
        }
    });
});