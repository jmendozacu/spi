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

require([
    'jquery',
    'Magento_Customer/js/customer-data',
    'domReady!'
], function ($, customerData) {
    'use strict';

    var selectors = {
            productIdSelector: '#product_addtocart_form [name="product"]',
            itemIdSelector: '#product_addtocart_form'
        },
        cartData = customerData.get('cart'),
        productId = $(selectors.productIdSelector).val(),
        itemId = $(selectors.itemIdSelector).attr('action').split('id/').pop().slice(0, -1),
        productAttr,

        /**
         * Gets productAttr according to cart data from customer-data
         *
         * @param {Object} data - cart data from customer-data
         */
        getProductAttr = function (data) {
            if (!(data && data.items && data.items.length && productId)) {
                return;
            }

            productAttr = data.items.find(function (item) {
                return item['product_id'] === productId &&
                    item['item_id'] === itemId;
            });

            if (!productAttr) {
                return;
            }
        },

        selectCurrentAttributes = function () {
            if (productAttr) {
                $.each(productAttr.options, function () {
                    var $currentSwatchOption = $('.swatch-option[option-id="' + this.option_value + '"]');

                    if (!$currentSwatchOption.hasClass('selected')) {
                        $currentSwatchOption.trigger('click');
                    }
                });
            }
        };

    cartData.subscribe(function (updateCartData) {
        getProductAttr(updateCartData);
        selectCurrentAttributes();
    });

    getProductAttr(cartData());

    selectCurrentAttributes();
});
