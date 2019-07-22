require([
    'jquery',
    'Magento_Customer/js/customer-data',
    'domReady!'
], function ($, customerData) {
    'use strict';

    var selectors = {
        qtySelector: '#product_addtocart_form [name="qty"]',
        productIdSelector: '#product_addtocart_form [name="product"]'
    },
    cartData = customerData.get('cart'),
    productId = $(selectors.productIdSelector).val(),
    productQty,
    productQtyInput,

    /**
    * Updates product's qty input value according to actual data
    */
    updateQty = function () {

        if (productQty || productQty === 0) {
            productQtyInput = productQtyInput || $(selectors.qtySelector);

            if (productQtyInput && productQty.toString() !== productQtyInput.val()) {
                productQtyInput.val(productQty);
            }
        }
    },

    /**
    * Sets productQty according to cart data from customer-data
    *
    * @param {Object} data - cart data from customer-data
    */
    setProductQty = function (data) {
        var product;

        if (!(data && data.items && data.items.length && productId)) {
            return;
        }
        product = data.items.find(function (item) {
            return item['product_id'] === productId ||
                item['item_id'] === productId;
        });

        if (!product) {
            return;
        }
        productQty = product.qty;
    };

    cartData.subscribe(function (updateCartData) {
        setProductQty(updateCartData);
        updateQty();
    });

    setProductQty(cartData());
    updateQty();

    // https://tc39.github.io/ecma262/#sec-array.prototype.find
    if (!Array.prototype.find) {
        Object.defineProperty(Array.prototype, 'find', {
            value: function(predicate) {
                // 1. Let O be ? ToObject(this value).
                if (this == null) {
                    throw new TypeError('"this" is null or not defined');
                }

                var o = Object(this);

                // 2. Let len be ? ToLength(? Get(O, "length")).
                var len = o.length >>> 0;

                // 3. If IsCallable(predicate) is false, throw a TypeError exception.
                if (typeof predicate !== 'function') {
                    throw new TypeError('predicate must be a function');
                }

                // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
                var thisArg = arguments[1];

                // 5. Let k be 0.
                var k = 0;

                // 6. Repeat, while k < len
                while (k < len) {
                    // a. Let Pk be ! ToString(k).
                    // b. Let kValue be ? Get(O, Pk).
                    // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
                    // d. If testResult is true, return kValue.
                    var kValue = o[k];
                    if (predicate.call(thisArg, kValue, k, o)) {
                        return kValue;
                    }
                    // e. Increase k by 1.
                    k++;
                }

                // 7. Return undefined.
                return undefined;
            },
            configurable: true,
            writable: true
        });
    }
});
