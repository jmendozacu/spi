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
 * @package     default
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

function qtyHandler($bodyReferenceClass, $subtractButton, $addButton) {

    var refClass = jQuery($bodyReferenceClass);
    var minusQty = jQuery($subtractButton);
    var addQty = jQuery($addButton);



    if (refClass.length > 0) {
        var inputVal = 0;
        minusQty.click(function () {
            var input = jQuery(this).nextAll('input[type="number"]:first');
            var inputVal = input.val();
            if (jQuery(this).hasClass('cart-control')) {
                var itemId = input.attr('data-cart-item');
                var itemDefaultQty = input.attr('data-item-qty');
                if (itemDefaultQty != inputVal - 1 && inputVal > 0) {
                    jQuery('#update-cart-item-' + itemId).show('fade', 300);
                } else {
                    jQuery('#update-cart-item-' + itemId).hide('fade', 300);
                }
            }
            inputVal--;
            if (inputVal > 0) {
                jQuery(this).nextAll('input[type="number"]:first').val(inputVal);
            }
        });

        addQty.click(function () {
            var input = jQuery(this).prevAll('input[type="number"]:first');
            var inputVal = input.val();
            if (jQuery(this).hasClass('cart-control')) {
                var itemId = input.attr('data-cart-item');
                var itemDefaultQty = input.attr('data-item-qty');
                if (itemDefaultQty != inputVal + 1 && inputVal > 0) {
                    jQuery('#update-cart-item-' + itemId).show('fade', 300);
                } else {
                    jQuery('#update-cart-item-' + itemId).hide('fade', 300);
                }
            }
            inputVal++;
            if (inputVal <= parseInt(input.attr('max'))) {
                jQuery(this).prevAll('input[type="number"]:first').val(inputVal);
            }

            if (itemDefaultQty == input.val()) {
                jQuery('#update-cart-item-' + itemId).hide('fade', 300);
            }
        });
    }

    jQuery('#qty').on('input keypress', function () {
        if (this.value.length > 4) {
            this.value = this.value.slice(0,4);
        }
    });
}
