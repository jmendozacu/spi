
define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies'
], function ($, authenticationPopup, customerData, alert, confirm) {
    'use strict';


    return function (widget) {

        $.widget('mage.sidebar', widget, {
            _removeItemAfter: function (elem) {
                var productData = customerData.get('cart')().items.filter(function (item) {
                    return Number(elem.data('cart-item')) === Number(item['item_id']);
                });

                $(document).trigger('ajax:removeFromCart', productData['product_sku']);

                if(location.href.indexOf("checkout/cart/") > -1) {
                    location.reload();
                }
            },

            _updateItemQtyAfter: function (elem) {
                this._hideItemButton(elem);

                if(location.href.indexOf("checkout/cart/") > -1) {
                    location.reload();
                }
            }

        });

        return $.mage.sidebar;
    }
});