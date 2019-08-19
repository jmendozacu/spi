
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
