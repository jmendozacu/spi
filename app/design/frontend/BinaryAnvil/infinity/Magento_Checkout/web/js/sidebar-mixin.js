/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     Infinity
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

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