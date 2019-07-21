define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/payment-validation-invoker',
        'mage/validation'
    ],
    function (
        $,
        _,
        registry,
        quote,
        paymentValidationInvoker
    ) {
        'use strict';

        return {

            /**
             * Perform overall checkout data validation
             *
             * @returns {Deferred}
             */
            validate: function () {
                var deferred = $.Deferred(),
                    isValid = true;

                if (!this._validateAddresses(isValid)) {
                    isValid = false;
                }
                if (!this._validateShippingMethod(isValid)) {
                    isValid = false;
                }
                if (!this._validateDeliveryDateFormData(isValid)) {
                    isValid = false;
                }

                // Check validate confirm email
                if (!this._validateConfirmEmail()) {
                    isValid = false;
                }

                if (!isValid) {
                    $('#ErrorPlaceOrder').show();
                } else {
                    $('#ErrorPlaceOrder').hide();
                }

                this._validatePaymentMethod(isValid).done(function () {
                    if (isValid) {
                        deferred.resolve();
                    }
                });

                return deferred;
            },

            /**
             * Validate confirm email
             *
             * @returns {*}
             * @private
             */
            _validateConfirmEmail: function () {
                let loginFormSelector = 'form[data-role=email-with-possible-login]';
                let confirmEmailSelector = loginFormSelector + ' input[name=confirmemail]';
                let loginForm = $(loginFormSelector);

                loginForm.validation();
                // var validator = loginForm.validate();
                return $(confirmEmailSelector).valid();
            },

            /**
             * Validate addresses data
             *
             * @param {boolean} isValid
             * @returns {Boolean}
             */
            _validateAddresses: function (isValid) {
                var provider = registry.get('checkoutProvider');

                _.each(['checkout.shippingAddress', 'checkout.paymentMethod.billingAddress'], function (query) {
                    var addressComponent = registry.get(query);

                    addressComponent.validate();
                    if (isValid && provider.get('params.invalid')) {
                        isValid = false;
                        addressComponent.focusInvalid();
                    }
                }, this);

                return isValid;
            },

            /**
             * Validate shipping method
             *
             * @param {boolean} isValid
             * @returns {boolean}
             */
            _validateShippingMethod: function (isValid) {
                var shippingMethodComponent = registry.get('checkout.shippingMethod'),
                    provider = registry.get('checkoutProvider');

                shippingMethodComponent.validate();
                if (isValid && provider.get('params.invalid')) {
                    isValid = false;
                    shippingMethodComponent.scrollInvalid();
                }

                return isValid;
            },

            /**
             * Validate delivery date form data
             *
             * @param {boolean} isValid
             * @returns {boolean}
             */
            _validateDeliveryDateFormData: function (isValid) {
                var deliveryDateComponent = registry.get('checkout.shippingMethod.delivery-date'),
                    provider = registry.get('checkoutProvider');

                deliveryDateComponent.validate();
                if (isValid && provider.get('params.invalid')) {
                    isValid = false;
                    deliveryDateComponent.focusInvalid();
                }

                return isValid;
            },

            /**
             * Validate payment method
             *
             * @param {boolean} isValid
             * @returns {Deferred}
             */
            _validatePaymentMethod: function (isValid) {
                var methodListComponent = registry.get('checkout.paymentMethod.methodList'),
                    methodCode,
                    methodRenderer;

                if (quote.paymentMethod()) {
                    methodCode = quote.paymentMethod().method;
                    methodRenderer = methodListComponent.getChild(methodCode);

                    return paymentValidationInvoker.invokeValidate(methodRenderer, methodCode);
                } else {
                    if (isValid && !methodListComponent.validate()) {
                        isValid = false;
                        methodListComponent.scrollInvalid();
                    }

                    return isValid
                        ? $.Deferred().resolve()
                        : $.Deferred().reject();
                }
            }
        };
    }
);
