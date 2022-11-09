;(function ($) {
    $(function () {

        $(".store-email-label").on('click', function () {
            var self = $(this);
            self.parents('.store-email').find('#store-email-area').slideDown();
        });

        $(".fade-anchor .fade-anchor-text").on('click', function (e) {
            e.preventDefault();
            $('#store-details-modal').modal('show');
            return false;
        });
        $('.rtcl-promotions-heading').on('click', function () {
            var _self = $(this),
                id = _self.attr('id');
            if ($(this).hasClass('active')) {
                if ('rtcl-regular-promotions-heading' === id) {
                    $("#rtcl-regular-promotions-heading").removeClass('active');
                    $("#rtcl-checkout-form").slideUp();
                    $("#rtcl-membership-promotions-heading").addClass('active');
                    $(".rtcl-membership-promotions-form-wrap").slideDown();
                } else {
                    $("#rtcl-membership-promotions-heading").removeClass('active');
                    $(".rtcl-membership-promotions-form-wrap").slideUp();
                    $("#rtcl-regular-promotions-heading").addClass('active');
                    $("#rtcl-checkout-form").slideDown();
                }
            } else {
                if ('rtcl-regular-promotions-heading' === id) {
                    $("#rtcl-regular-promotions-heading").addClass('active');
                    $("#rtcl-checkout-form").slideDown();
                    $("#rtcl-membership-promotions-heading").removeClass('active');
                    $(".rtcl-membership-promotions-form-wrap").slideUp();
                } else {
                    $("#rtcl-membership-promotions-heading").addClass('active');
                    $(".rtcl-membership-promotions-form-wrap").slideDown();
                    $("#rtcl-regular-promotions-heading").removeClass('active');
                    $("#rtcl-checkout-form").slideUp();
                }
            }
        });

        $(document)
            .on('rtcl_recaptcha_loaded', function () {
                const $storeContactForms = $("form.store-email-form, form#store-email-form");
                if ($storeContactForms.length && typeof grecaptcha !== 'undefined' && $.inArray("store_contact", rtcl.recaptcha.on) !== -1) {
                    $storeContactForms.each((index, form) => {
                        const $form = $(form);
                        if (!$form.data('reCaptchaId')) {
                            const args = {'sitekey': rtcl.recaptcha.site_key};
                            if ($form.find('#rtcl-store-contact-g-recaptcha').length) {
                                $form.data('reCaptchaId', grecaptcha.render($form.find('#rtcl-store-contact-g-recaptcha')[0], args));
                            } else if ($form.find('.rtcl-g-recaptcha-wrap').length) {
                                $form.data('reCaptchaId', grecaptcha.render($form.find('.rtcl-g-recaptcha-wrap')[0], args));
                            }
                        }
                    });
                }
            });

        if ($.fn.validate) {
            // Membership promotion
            $("#rtcl-membership-promotions-form").validate({
                submitHandler: function (form) {
                    var $form = $(form),
                        fromData = new FormData(form);
                    fromData.append('action', 'rtcl_store_ajax_membership_promotion');
                    fromData.append('__rtcl_wpnonce', rtcl_store_public.__rtcl_wpnonce);
                    $.ajax({
                        type: "POST",
                        url: rtcl_store_public.ajaxurl,
                        data: fromData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $form.rtclBlock();
                        },
                        success: function (res) {
                            $form.rtclUnblock();
                            if (res.success) {
                                toastr.success(res.data.message);
                                if (res.data.redirect_url) {
                                    if (res.data.redirect_utl === window.location.href) {
                                        window.location.reload(true);
                                    } else {
                                        window.location = res.data.redirect_url + '?t=' + new Date().getTime();
                                    }
                                }
                            } else {
                                toastr.error(res.data);
                            }
                        },
                        error: function (jqXHR, exception) {
                            $form.rtclUnblock();
                            toastr.error(rtcl_validator.messages.server_error);
                        }
                    });
                }
            });
            // User account form
            $("form.store-email-form, form#store-email-form")
                .each(function () {
                    $(this)
                        .validate({
                            submitHandler: function (form) {
                                var $form = $(form),
                                    targetBtn = $form.find('.sc-submit'),
                                    responseHolder = $form.find('.rtcl-response'),
                                    msgHolder = $("<div class='alert'></div>"),
                                    recaptchId = $form.data('reCaptchaId'),
                                    sc_response = '';
                                // recaptch v2
                                if (rtcl.recaptcha && typeof grecaptcha !== 'undefined' && rtcl.recaptcha.on && $.inArray("store_contact", rtcl.recaptcha.on) !== -1) {
                                    if (rtcl.recaptcha.v === 2 && recaptchId !== undefined) {
                                        const response = grecaptcha.getResponse(recaptchId);
                                        responseHolder.html('');
                                        if (0 === response.length) {
                                            responseHolder.removeClass('text-success').addClass('text-danger').html(rtcl.recaptcha.msg.invalid);
                                            grecaptcha.reset(recaptchId);
                                            return false;
                                        }
                                        submit_form_data_ajax(response);
                                        return false;
                                    } else if (rtcl.recaptcha.v === 3) {
                                        grecaptcha.ready(function () {
                                            $form.rtclBlock();
                                            grecaptcha.execute(rtcl.recaptcha.site_key, {action: 'store_contact'}).then(function (token) {
                                                $form.rtclUnblock();
                                                submit_form_data_ajax(token);
                                            });
                                        });
                                        return false;
                                    }
                                }
                                submit_form_data_ajax();
                                return false;

                                function submit_form_data_ajax(recaptcha_token) {
                                    var formData = new FormData(form);
                                    formData.append('action', 'rtcl_send_mail_to_store_owner');
                                    formData.append('store_id', rtcl_store_public.store_id || 0);
                                    formData.append('__rtcl_wpnonce', rtcl.__rtcl_wpnonce);
                                    if (recaptcha_token) {
                                        formData.append('g-recaptcha-response', recaptcha_token);
                                    }
                                    $.ajax({
                                        url: rtcl_store_public.ajaxurl,
                                        dataType: 'json',
                                        data: formData,
                                        type: 'POST',
                                        processData: false,
                                        contentType: false,
                                        cache: false,
                                        beforeSend: function () {
                                            $form.rtclBlock();
                                            $form.addClass("rtcl-loading");
                                            $form.find('input textarea').prop("disabled", true);
                                            targetBtn.prop("disabled", true);
                                            responseHolder.html('');
                                            $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(targetBtn);
                                        },
                                        success: function (response) {
                                            $form.rtclUnblock();
                                            targetBtn.prop("disabled", false).next('.rtcl-icon-spinner').remove();
                                            $form.find('input textarea').prop("disabled", false);
                                            $form.removeClass("rtcl-loading");
                                            if (response.success) {
                                                msgHolder.removeClass('alert-danger').addClass('alert-success').html(response.data.message).appendTo(responseHolder);
                                                $form[0].reset();
                                                if ($form.parent("#store-email-area").parent().data('hide') !== 0) {
                                                    setTimeout(function () {
                                                        responseHolder.html('');
                                                        $form.parent("#store-email-area").slideUp();
                                                    }, 1000);
                                                }
                                            } else {
                                                msgHolder.removeClass('alert-success').addClass('alert-danger').html(response.data.error).appendTo(responseHolder);
                                            }
                                            if (rtcl.recaptcha && rtcl.recaptcha.v === 2 && recaptchId !== undefined) {
                                                grecaptcha.reset(recaptchId);
                                            }
                                        },
                                        error: function (e) {
                                            $form.rtclUnblock();
                                            $form.find('input textarea').prop("disabled", false);
                                            msgHolder.removeClass('alert-success').addClass('alert-danger').html(e.responseText).appendTo(responseHolder);
                                            targetBtn.prop("disabled", false).next('.rtcl-icon-spinner').remove();
                                            $form.removeClass("rtcl-loading");
                                        }
                                    });
                                }
                            }
                        });
                });
        }
        if ($.fn.owlCarousel) {
            $('.rtcl-store-slider').each(function () {
                var $storeSlider = $(this),
                    settings = $storeSlider.data('settings');
                $storeSlider.addClass("owl-carousel").owlCarousel({
                    responsive: {
                        0: {
                            items: 2
                        },
                        200: {
                            items: 2
                        },
                        400: {
                            items: 2
                        },
                        600: {
                            items: 3
                        },
                        800: {
                            items: settings.items || 4
                        }
                    },
                    margin: 15,
                    rtl: rtcl_store_public.is_rtl ? true : false,
                    nav: true,
                    navText: ['<i class="rtcl-icon-angle-left"></i>', '<i class="rtcl-icon-angle-right"></i>'],
                });
            });
        }

        // Single store ad listing infinity scroll
        var store_ads_wrapper = $(".store-ad-listing-wrapper"), pagination;
        if (store_ads_wrapper.length) {
            var wrapper = $(".rtcl-listing-wrapper", store_ads_wrapper);
            pagination = wrapper.data('pagination') || {};
            pagination.disable = false;
            pagination.loading = false;

            $(window).on('scroll load', function () {
                infinite_scroll(wrapper);
            });
        }

        function infinite_scroll(wrapper) {
            var ajaxVisible = store_ads_wrapper.offset().top + store_ads_wrapper.outerHeight(true),
                ajaxScrollTop = $(window).scrollTop() + $(window).height();
            if (ajaxVisible <= (ajaxScrollTop) && (ajaxVisible + $(window).height()) > ajaxScrollTop) {
                if (pagination.max_num_pages > pagination.current_page && !pagination.loading && !pagination.disable) {
                    var data = {
                        action: "rtcl_store_ad_load_more",
                        current_page: pagination.current_page,
                        max_num_pages: pagination.max_num_pages,
                        found_posts: pagination.found_posts,
                        posts_per_page: pagination.posts_per_page,
                        store_id: rtcl_store_public.store_id
                    }
                    $.ajax({
                        url: rtcl_store_public.ajaxurl,
                        data: data,
                        type: 'POST',
                        beforeSend: function () {
                            pagination.loading = true;
                            $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(wrapper);
                        },
                        success: function (response) {
                            wrapper.next('.rtcl-icon-spinner').remove();
                            pagination.loading = false;
                            pagination.current_page = response.current_page;
                            if (pagination.max_num_pages === response.current_page) {
                                pagination.disable = true;
                            }
                            if (response.complete && response.html) {
                                wrapper.append(response.html)
                            }
                        },
                        error: function (e) {
                            pagination.loading = false;
                            wrapper.next('.rtcl-icon-spinner').remove();
                        }
                    });
                }

            }
        }
    });
}(jQuery));