define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Customer/js/action/login',
    'Magento_Checkout/js/model/quote',
    'Aheadworks_OneStepCheckout/js/model/checkout-data',
    'Aheadworks_OneStepCheckout/js/model/newsletter/subscriber',
    'Aheadworks_OneStepCheckout/js/action/check-if-subscribed-by-email',
    'Magento_Checkout/js/model/full-screen-loader',
    'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
    'mage/validation'
], function (
    $,
    Component,
    ko,
    customer,
    checkEmailAvailabilityAction,
    loginAction,
    quote,
    checkoutData,
    newsletterSubscriber,
    checkIfSubscribedByEmailAction,
    fullScreenLoader,
    completenessLogger
) {
    'use strict';

    var validatedEmail = checkoutData.getValidatedEmailValue(),
        newsletterSubscribeConfig = window.checkoutConfig.newsletterSubscribe,
        verifiedIsSubscribed = checkoutData.getVerifiedIsSubscribedFlag();

    if (validatedEmail && !customer.isLoggedIn()) {
        quote.guestEmail = validatedEmail;
        if (newsletterSubscribeConfig.isGuestSubscriptionsAllowed) {
            newsletterSubscriber.subscriberEmail = validatedEmail;
            if (verifiedIsSubscribed !== undefined) {
                newsletterSubscriber.isSubscribed(verifiedIsSubscribed);
                newsletterSubscriber.subscribedStatusVerified(true);
            }
        }
    }

    /**
     * Save guest customer to quote
     * @param email
     * @param firstName
     * @param lastName
     */
    function saveEmailToQuote(email = "", firstName = "", lastName = "") {
        if (!email || email === "") {
            email = $('form.form-login input[name=username]').val();
        }

        if (!firstName || firstName === "") {
            firstName = $('form.form input[name=firstname]').val();
        }

        if (!lastName) {
            lastName = $('form.form input[name=lastname]').val();
        }

        console.log(email + " - " + firstName + " - " + lastName);

        // save to database
        $.ajax({
            url: "/onestepcheckout/index/savequote",
            data: {
                customerEmail: email,
                firstName: firstName,
                lastName: lastName
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                // show some loading icon
            },
            success: function (data, status) {
                console.log(status);
                console.log(data);
            },
            error: function (xhr, status, errorThrown) {
                console.log('Error happens. Try again.');
                console.log(errorThrown);
            }
        });

        // push into api
        let postURL = 'https://checkoutcarteventregistration-qa.azurewebsites.net/api/v1/eventregistration'; // QA
        if (window.location.href.indexOf('infinityscrubs.com') > -1) {
            postURL = 'https://checkoutcarteventregistration.azurewebsites.net/api/v1/eventregistration'; // production
        }
        // Production https://checkoutcarteventregistration.azurewebsites.net/api/v1/eventregistration
        // QA https://checkoutcarteventregistration-qa.azurewebsites.net/api/v1/eventregistration

        let quoteCartId = $('#checkout').attr('data-quote-id');
        console.log(quoteCartId);
        if (quoteCartId && quoteCartId > 0) {
            let ajaxData = {
                cartId: quoteCartId,
                source: 'infinityscrubs' // "infinityscrubs" or "heartsoulscrubs"
            };

            $.ajax({
                url: postURL,
                data: JSON.stringify(ajaxData),
                type: 'POST',
                contentType: "application/json; charset=UTF-8",
                dataType: 'json',
                beforeSend: function (xhr) {
                    console.log("abadoncart infinityscrubs api");
                },
                success: function (data) {
                    console.log(data);
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                    console.log(errorThrown);
                }
            });
        }
    }

    /**
     * Listen first name / last name is changed and save to quote
     */
    function listenCustomerNameIsChanged() {
        $('form.form input[name=firstname]').change(function () {
            saveEmailToQuote();
        });
        $('form.form input[name=lastname]').change(function () {
            saveEmailToQuote();
        });
    }

    $(document).ready(function () {
        listenCustomerNameIsChanged();
    });

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/form/email',
            email: checkoutData.getInputFieldEmailValue(),
            emailFocused: false,
            confirmEmailFocused: false,
            isLoading: false,
            isPasswordVisible: false,
            listens: {
                email: 'emailHasChanged',
                emailFocused: 'validateEmail',
                // confirmEmailFocused: 'validateConfirmEmail'
            }
        },
        checkDelay: 5000,
        checkAvailabilityRequest: null,
        checkIfSubscribedRequest: null,
        isCustomerLoggedIn: customer.isLoggedIn,
        forgotPasswordUrl: window.checkoutConfig.forgotPasswordUrl,
        emailCheckTimeout: 0,

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            completenessLogger.bindField('email', this.email);
            // console.log("8");
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe([
                    'email',
                    'emailFocused',
                    'confirmEmailFocused',
                    'isLoading',
                    'isPasswordVisible'
                ]);
            return this;
        },

        /**
         * Process email value change
         */
        emailHasChanged: function () {
            var self = this;

            clearTimeout(this.emailCheckTimeout);

            if (self.validateEmail()) {
                quote.guestEmail = self.email();
                newsletterSubscriber.subscriberEmail = self.email();
                checkoutData.setValidatedEmailValue(self.email());
            }
            this.emailCheckTimeout = setTimeout(function () {
                if (self.validateEmail()) {
                    // Save email guest for abandon cart
                    saveEmailToQuote(quote.guestEmail);
                    self.checkEmailAvailability();
                    if (newsletterSubscribeConfig.isGuestSubscriptionsAllowed) {
                        self.checkIfSubscribedByEmail();
                    }
                } else {
                    self.isPasswordVisible(false);
                    newsletterSubscriber.subscribedStatusVerified(false);
                }
            }, self.checkDelay);

            checkoutData.setInputFieldEmailValue(self.email());
        },

        /**
         * Check email availability
         */
        checkEmailAvailability: function () {
            var self = this,
                isEmailCheckComplete = $.Deferred();

            this._validateRequest(this.checkAvailabilityRequest);
            this.isLoading(true);
            this.checkAvailabilityRequest = checkEmailAvailabilityAction(isEmailCheckComplete, this.email());

            $.when(isEmailCheckComplete).done(function () {
                self.isPasswordVisible(false);
            }).fail(function () {
                self.isPasswordVisible(true);
            }).always(function () {
                self.isLoading(false);
            });
        },

        /**
         * Check if subscribed by email
         */
        checkIfSubscribedByEmail: function () {
            var isEmailCheckComplete = $.Deferred();

            this._validateRequest(this.checkIfSubscribedRequest);
            this.checkIfSubscribedRequest = checkIfSubscribedByEmailAction(isEmailCheckComplete, this.email());

            $.when(isEmailCheckComplete).done(function () {
                newsletterSubscriber.isSubscribed(true);
                checkoutData.setVerifiedIsSubscribedFlag(true);
            }).fail(function () {
                newsletterSubscriber.isSubscribed(false);
                checkoutData.setVerifiedIsSubscribedFlag(false);
            }).always(function () {
                newsletterSubscriber.subscribedStatusVerified(true);
            });
        },

        /**
         * If request has been sent abort it
         *
         * @param {XMLHttpRequest} request
         */
        _validateRequest: function (request) {
            if (request != null && $.inArray(request.readyState, [1, 2, 3])) {
                request.abort();
                request = null;
            }
        },

        /**
         * Local email validation
         *
         * @param {Boolean} focused
         * @returns {Boolean}
         */
        validateEmail: function (focused) {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=username]',
                loginForm = $(loginFormSelector),
                validator;

            loginForm.validation();

            if (focused === false && !!this.email()) {
                return !!$(usernameSelector).valid();
            }
            validator = loginForm.validate();

            return validator.check(usernameSelector);
        },

        /**
         * Confirm Email validation
         * @returns {boolean|*}
         */
        validateConfirmEmail: function (focused) {
            let loginFormSelector = 'form[data-role=email-with-possible-login]',
                confirmEmailSelector = loginFormSelector + ' input[name=confirmemail]',
                loginForm = $(loginFormSelector),
                validator;

            loginForm.validation();

            if (focused === false && !!this.email()) {
                return !!$(confirmEmailSelector).valid();
            }
            validator = loginForm.validate();

            return validator.check(confirmEmailSelector);
        },

        /**
         * Perform login action
         *
         * @param {Object} loginForm
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            $.each(formDataArray, function () {
                loginData[this.name] = this.value;
            });

            if (this.isPasswordVisible()
                && $(loginForm).validation()
                && $(loginForm).validation('isValid')
            ) {
                fullScreenLoader.startLoader();
                loginAction(loginData).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }
        }
    });
});
