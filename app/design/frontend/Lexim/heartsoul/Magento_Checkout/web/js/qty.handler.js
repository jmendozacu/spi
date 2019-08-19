function qtyHandler($bodyReferenceClass, $subtractButton, $addButton) {
    var refClass = jQuery($bodyReferenceClass);
    var minusQty = jQuery($subtractButton);
    var addQty = jQuery($addButton);

    if (refClass.length > 0) {
        var inputVal = 0;
        minusQty.click(function () {
            var input = jQuery(this).next('input[type="number"]');
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
                jQuery(this).next('input[type="number"]').val(inputVal);
            }
        });

        addQty.click(function () {
            var input = jQuery(this).prev('input[type="number"]');
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
                jQuery(this).prev('input[type="number"]').val(inputVal);
            }

            if (itemDefaultQty == input.val()) {
                jQuery('#update-cart-item-' + itemId).hide('fade', 300);
            }
        });
    }

    jQuery('[data-role="cart-item-qty"]').on('input keypress', function () {
        if (this.value.length > 4) {
            this.value = this.value.slice(0,4);
        }
    });
}
