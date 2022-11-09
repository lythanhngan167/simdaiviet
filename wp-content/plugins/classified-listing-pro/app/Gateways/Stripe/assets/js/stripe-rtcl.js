/* global rtcl_stripe_params, Stripe, rtcl_make_checkout_request */
(function ($) {
    'use strict';

    try {
        var stripe = Stripe(rtcl_stripe_params.key, {locale: rtcl_stripe_params.locale || 'auto'});
    } catch (error) {
        console.log(error);
        return;
    }
    var stripe_elements_options = Object.keys(rtcl_stripe_params.elements_options).length ? rtcl_stripe_params.elements_options : {},
        elements = stripe.elements(stripe_elements_options),
        stripe_card,
        stripe_exp,
        stripe_cvc;

    /**
     * Object to handle Stripe elements payment form.
     */
    var rtcl_stripe_form = {

        /**
         * Unmounts all Stripe elements when the checkout page is being updated.
         */
        unmountElements: function () {
            if (rtcl_stripe_params.inline_cc_form) {
                stripe_card.unmount('#stripe-card-element');
            } else {
                stripe_card.unmount('#stripe-card-element');
                stripe_exp.unmount('#stripe-exp-element');
                stripe_cvc.unmount('#stripe-cvc-element');
            }
        },

        /**
         * Mounts all elements to their DOM nodes on initial loads and updates.
         */
        mountElements: function () {
            if (!$('#stripe-card-element').length) {
                return;
            }

            if (rtcl_stripe_params.inline_cc_form) {
                stripe_card.mount('#stripe-card-element');
                return;
            }

            stripe_card.mount('#stripe-card-element');
            stripe_exp.mount('#stripe-exp-element');
            stripe_cvc.mount('#stripe-cvc-element');
        },

        /**
         * Creates all Stripe elements that will be used to enter cards or IBANs.
         */
        createElements: function () {
            var elementStyles = {
                base: {
                    iconColor: '#666ee8',
                    color: '#31325f',
                    fontSize: '15px',
                    '::placeholder': {
                        color: '#cfd7e0',
                    }
                }
            };

            var elementClasses = {
                focus: 'focused',
                empty: 'empty',
                invalid: 'invalid',
            };

            elementStyles = rtcl_stripe_params.elements_styling ? rtcl_stripe_params.elements_styling : elementStyles;
            elementClasses = rtcl_stripe_params.elements_classes ? rtcl_stripe_params.elements_classes : elementClasses;

            if (rtcl_stripe_params.inline_cc_form) {
                stripe_card = elements.create('card', {style: elementStyles, hidePostalCode: true});
                console.log(stripe_card);
                stripe_card.addEventListener('change', function (event) {
                    rtcl_stripe_form.onCCFormChange();

                    if (event.error) {
                        $(document.body).trigger('stripeError', event);
                    }
                });
            } else {
                stripe_card = elements.create('cardNumber', {style: elementStyles, classes: elementClasses});
                stripe_exp = elements.create('cardExpiry', {style: elementStyles, classes: elementClasses});
                stripe_cvc = elements.create('cardCvc', {style: elementStyles, classes: elementClasses});

                stripe_card.addEventListener('change', function (event) {
                    rtcl_stripe_form.onCCFormChange();

                    rtcl_stripe_form.updateCardBrand(event.brand);

                    if (event.error) {
                        $(document.body).trigger('stripeError', event);
                    }
                });

                stripe_exp.addEventListener('change', function (event) {
                    rtcl_stripe_form.onCCFormChange();

                    if (event.error) {
                        $(document.body).trigger('stripeError', event);
                    }
                });

                stripe_cvc.addEventListener('change', function (event) {
                    rtcl_stripe_form.onCCFormChange();

                    if (event.error) {
                        $(document.body).trigger('stripeError', event);
                    }
                });
            }
            $(document.body)
                .on('rtcl_updated_checkout rtcl_cc_form_init', function () {
                    // Don't re-mount if already mounted in DOM.
                    if ($('#stripe-card-element').children().length) {
                        return;
                    }

                    // Unmount prior to re-mounting.
                    if (stripe_card) {
                        rtcl_stripe_form.unmountElements();
                    }

                    rtcl_stripe_form.mountElements();
                });
        },

        /**
         * Updates the card brand logo with non-inline CC forms.
         *
         * @param {string} brand The identifier of the chosen brand.
         */
        updateCardBrand: function (brand) {
            var brandClass = {
                'visa': 'stripe-visa-brand',
                'mastercard': 'stripe-mastercard-brand',
                'amex': 'stripe-amex-brand',
                'discover': 'stripe-discover-brand',
                'diners': 'stripe-diners-brand',
                'jcb': 'stripe-jcb-brand',
                'unknown': 'stripe-credit-card-brand'
            };

            var imageElement = $('.stripe-card-brand'),
                imageClass = 'stripe-credit-card-brand';

            if (brand in brandClass) {
                imageClass = brandClass[brand];
            }

            // Remove existing card brand class.
            $.each(brandClass, function (index, el) {
                imageElement.removeClass(el);
            });

            imageElement.addClass(imageClass);
        },

        /**
         * Initialize event handlers and UI state.
         */
        init: function () {

            var $form = $('form#rtcl-checkout-form');
            // checkout page
            if (!$form.length) {
                rtcl_stripe_form.confirmPaymentIntent();
                return
            }
            this.form = $form;
            $form
                .on('change', 'input[name="payment_method"]', function (e) {
                    $form.validate().destroy();
                    if ($(this).val() === 'stripe') {
                        // Unmount prior to re-mounting.
                        if (stripe_card) {
                            rtcl_stripe_form.unmountElements();
                        }

                        rtcl_stripe_form.mountElements();
                        $form.validate({
                            submitHandler: function (form) {

                                rtcl_stripe_form.block();
                                rtcl_stripe_form.createPaymentMethod()

                                // rtcl_stripe_form.block();
                                // rtcl_stripe_form.confirmCardSetup();
                                return false;
                            }
                        });


                    } else {
                        $form.validate({
                            submitHandler: function (form) {
                                rtcl_make_checkout_request(form);
                                return false;
                            }
                        });
                    }
                });
            $form.on('change', this.reset);

            $(document)
                .on('stripeError', this.onError);

            rtcl_stripe_form.createElements();
            $(document.body).trigger('rtcl_cc_form_init');
            rtcl_stripe_form.confirmPaymentIntent();
        },

        /**
         * Blocks payment forms with an overlay while being submitted.
         */
        block: function () {
            rtcl_stripe_form.form && rtcl_stripe_form.form.rtclBlock();
        },

        /**
         * Removes overlays from payment forms.
         */
        unblock: function () {
            rtcl_stripe_form.form && rtcl_stripe_form.form.rtclUnblock();
        },

        /**
         * Returns the selected payment method HTML element.
         *
         * @return {HTMLElement}
         */
        getSelectedPaymentElement: function () {
            return $('#rtcl-payment-methods input[name="payment_method"]:checked');
        },
        /**
         * Initiates the creation of a Source object.
         *
         * Currently this is only used for credit cards and SEPA Direct Debit,
         * all other payment methods work with redirects to create sources.
         */
        createPaymentMethod: function () {
            return stripe
                .createPaymentMethod({
                    type: 'card',
                    card: stripe_card,
                    billing_details: {
                        name: rtcl_stripe_params.billing.name,
                        email: rtcl_stripe_params.billing.email
                    }
                })
                .then(rtcl_stripe_form.paymentMethodResponse);
        },
        paymentMethodResponse: function (response) {
            if (response.error) {
                $(document.body).trigger('stripeError', response);
                return;
            }

            rtcl_stripe_form.reset();
            rtcl_stripe_form.form.append(
                $('<input type="hidden" />')
                    .addClass('stripe-payment-method')
                    .attr('name', 'stripe_payment_method')
                    .val(response.paymentMethod.id)
            );

            rtcl_make_checkout_request(rtcl_stripe_form.form[0], function (res) {
                if (res.result === 'success') {
                    rtcl_stripe_form.block();
                    if (res.requiresAction && res.payment_intent_client_secret) {
                        rtcl_stripe_form.openPaymentIntentModal(res.payment_intent_client_secret, res.redirect_url);
                    } else {
                        if (res.redirect_url) {
                            window.location = res.redirect_url
                        }
                    }
                } else {
                    rtcl_stripe_form.unblock();
                }
            });

        },


        /**
         * If a new credit card is entered, reset sources.
         */
        onCCFormChange: function () {
            rtcl_stripe_form.reset();
        },

        /**
         * Removes all Stripe errors and hidden fields with IDs from the form.
         */
        reset: function () {
            $('.rtcl-stripe-error, .stripe-payment-method').remove();
        },

        /**
         * Displays stripe-related errors.
         *
         * @param {Event}  e      The jQuery event.
         * @param {Object} result The result of Stripe call.
         */
        onError: function (e, result) {
            var message = result.error.message,
                selectedMethodElement = rtcl_stripe_form.getSelectedPaymentElement().closest('.rtcl-payment-method'),
                errorContainer = selectedMethodElement.find('.stripe-source-errors');


            // Notify users that the email is invalid.
            if ('email_invalid' === result.error.code) {
                message = rtcl_stripe_params.i18.email_invalid;
            } else if (
                /*
                 * Customers do not need to know the specifics of the below type of errors
                 * therefore return a generic localizable error message.
                 */
                'invalid_request_error' === result.error.type ||
                'api_connection_error' === result.error.type ||
                'api_error' === result.error.type ||
                'authentication_error' === result.error.type ||
                'rate_limit_error' === result.error.type
            ) {
                message = rtcl_stripe_params.i18.invalid_request_error;
            }

            if ('card_error' === result.error.type && rtcl_stripe_params.i18.hasOwnProperty(result.error.code)) {
                message = rtcl_stripe_params.i18[result.error.code];
            }

            if ('validation_error' === result.error.type && rtcl_stripe_params.i18.hasOwnProperty(result.error.code)) {
                message = rtcl_stripe_params.i18[result.error.code];
            }

            rtcl_stripe_form.reset();
            console.log(result.error.message); // Leave for troubleshooting.
            $(errorContainer).html('<ul class="rtcl-error rtcl-stripe-error"><li /></ul>');
            $(errorContainer).find('li').text(message ? message : result.error.message); // Prevent XSS
            rtcl_stripe_form.unblock();
            if ($('.rtcl-stripe-error').length) {
                $('html, body').animate({
                    scrollTop: ($('.rtcl-stripe-error').offset().top - 200)
                }, 200);
            }
        },

        confirmPaymentIntent: function () {
            var $intentId = $('#stripe-intent-id');
            var $intentReturn = $('#stripe-intent-return');
            if (!$intentId.length || !$intentReturn.length || !$intentId.val() || !$intentReturn.val()) {
                return;
            }

            rtcl_stripe_form.openPaymentIntentModal($intentId.val(), $intentReturn.val());
        },
        openPaymentIntentModal: function (intentSecret, returnURL) {
            stripe
                .handleCardPayment(intentSecret)
                .then(function (result) {
                    if (result.error) {
                        $(document.body).trigger('stripeError', result);
                    } else {
                        $.post({
                            url: rtcl_stripe_params.routes.confirm_payment_intent,
                            dataType: 'json',
                            data: {
                                paymentIntentId: result.paymentIntent.id
                            },
                            error: function () {
                                window.location = returnURL;
                            }
                        }).done(function (serverResponse) {
                        }).always(function (re) {
                            window.location = returnURL;
                        });
                    }
                });
        },

    };

    rtcl_stripe_form.init();
    window.rtcl_stripe_form = rtcl_stripe_form;
})(jQuery);
