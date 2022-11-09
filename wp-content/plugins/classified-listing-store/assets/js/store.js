;(function ($) {
    'use strict';

    $(function () {
        $(document)
            .on('click', '#rtcl-store-sub-menu a', function (e) {
                e.preventDefault()
                var _self = $(this),
                    target = _self.data('target');
                if (target) {
                    var contaiers = $("#rtcl-store-content-wrap");
                    contaiers
                        .find('.rtcl-store-content')
                        .hide()
                    $("#rtcl-store-" + target + "-content").show();
                    _self.parents('ul').find('li').removeClass('active');
                    _self
                        .parent('li')
                        .addClass('active');
                }
            })
            .on('click', '.rtcl-store-invite-manager', function (e) {
                var user_inpur_html = $('<div class="rtcl-invite-manager-wrap"><input type="email" id="rtcl-manager-email" class="form-control" autocomplete="off" placeholder="Email address"> <span class="btn btn-success add-manager">Add</span></div>');
                const modal = new RtclModal({
                    footer: false,
                    wrapClass: 'rtcl-store-im-popup no-heading'
                });
                modal
                    .addModal()
                    .content(user_inpur_html);
                var email_input = user_inpur_html.find('#rtcl-manager-email');
                email_input.on('change keyup', function () {
                    var _email = email_input.val();
                    if (!/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(_email) || !_email) {
                        email_input.addClass('is-invalid');
                    } else {
                        email_input.removeClass('is-invalid');
                    }
                })
                user_inpur_html
                    .find('.add-manager')
                    .on('click', function () {
                        var email = email_input.val();
                        if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email)) {
                            $.ajax({
                                url: rtcl_store.ajaxurl,
                                type: "POST",
                                data: {
                                    email: email,
                                    __rtcl_wpnonce: rtcl_store.__rtcl_wpnonce,
                                    action: 'rtcl_ajax_store_send_manager_invitation_by_email'
                                },
                                beforeSend: function () {
                                    modal
                                        .addLoading();
                                    $('.rtcl-store-pp-error').remove();
                                },
                                success: function (res) {
                                    modal.removeLoading();
                                    if (res.success) {
                                        modal.removeModel();
                                        toastr.success(res.data.message);
                                        $("#rtcl-store-managers").append(res.data.html);
                                    } else {
                                        modal.appendContent('<div class="rtcl-store-pp-error">' + res.data + '</div>');
                                    }
                                },
                                error: function (e) {
                                    console.log(e)
                                    modal.removeLoading();
                                    modal.content();
                                }
                            });
                        } else {
                            email_input.addClass('is-invalid');
                        }
                    });
            })
            .on('click', '.rtcl-store-manager-remove', function () {
                var _self = $(this),
                    manager_user_id = parseInt(_self.data('manager_user_id'), 10) || 0,
                    wrap = _self.parents('.rtcl-store-manager');
                if (manager_user_id && confirm(rtcl_store.confirm_text)) {
                    $.ajax({
                        url: rtcl_store.ajaxurl,
                        type: "POST",
                        data: {
                            manager_user_id: manager_user_id,
                            __rtcl_wpnonce: rtcl_store.__rtcl_wpnonce,
                            action: 'rtcl_ajax_store_remove_manager_by_user_id'
                        },
                        beforeSend: function () {
                            wrap.rtclBlock();
                        },
                        success: function (res) {
                            wrap.rtclUnblock();
                            if (res.success) {
                                if (res.data.manager_user_id === manager_user_id) {
                                    wrap.remove();
                                }
                                toastr.success(res.data.message);
                            } else {
                                toastr.error(res.data);
                            }
                        },
                        error: function (e) {
                            wrap.rtclUnblock();
                            toastr.error(rtcl_store.server_error);
                        }
                    });
                }
            });
        $(".rtcl-store-banner-wrap .rtcl-media-action")
            .on('click', 'span.remove', function () {
                var self = $(this),
                    banner_wrap = self.parents(".rtcl-store-banner"),
                    banner_holder = $('.banner', banner_wrap),
                    data = {
                        action: 'rtcl_ajax_store_banner_delete',
                        __rtcl_wpnonce: rtcl_store.__rtcl_wpnonce
                    };
                if (confirm(rtcl_store.confirm_text)) {
                    $.ajax({
                        url: rtcl_store.ajaxurl,
                        data: data,
                        type: 'POST',
                        beforeSend: function () {
                            $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(self);
                        },
                        success: function (response) {
                            self.next('.rtcl-icon-spinner').remove();
                            if (!response.error) {
                                banner_wrap.addClass('no-banner');
                                banner_wrap.removeClass('has-banner');
                                banner_holder.html("");
                            }
                        },
                        error: function (jqXhr, json, errorThrown) {
                            self.next('.rtcl-icon-spinner').remove();
                            console.log('error');
                        }
                    });
                }

            });

        $(".rtcl-store-banner-wrap .rtcl-media-action")
            .on('click', 'span.add', function () {
                var addBtn = $(this),
                    bannerFile = $("<input type='file' style='position:absolute;left:-9999px' />");
                $('body').append(bannerFile);
                if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
                    bannerFile.trigger('change');
                } else {
                    bannerFile.trigger('click');
                }
                bannerFile.on('change', function () {
                    var fileItem = $(this),
                        banner_wrap = addBtn.parents(".rtcl-store-banner"),
                        banner_holder = $('.banner', banner_wrap),
                        form = new FormData(),
                        banner = fileItem[0].files[0],
                        allowed_image_types = rtcl_store.image_allowed_type.map(function (type) {
                            return 'image/' + type;
                        }),
                        max_image_size = parseInt(rtcl_store.max_image_size);
                    if ($.inArray(banner.type, allowed_image_types) !== -1) {
                        if (banner.size <= max_image_size) {
                            form.append('banner', banner);
                            form.append('action', 'rtcl_ajax_store_banner_upload');
                            $.ajax({
                                url: rtcl_store.ajaxurl,
                                data: form,
                                cache: false,
                                contentType: false,
                                processData: false,
                                type: 'POST',
                                beforeSend: function () {
                                    $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(addBtn);
                                },
                                success: function (response) {
                                    addBtn.next('.rtcl-icon-spinner').remove();
                                    if (!response.error) {
                                        banner_wrap.addClass('has-banner');
                                        banner_wrap.removeClass('no-banner');
                                        banner_holder.html("<img class='rtcl-thumbnail' src='" + response.data.src + "'/>");
                                    }
                                },
                                error: function (jqXhr, json, errorThrown) {
                                    addBtn.next('.rtcl-icon-spinner').remove();
                                    console.log('error');
                                }
                            });
                        } else {
                            alert(rtcl_store.error_image_size);
                        }
                    } else {
                        alert(rtcl_store.error_image_extension);
                    }
                });
            });

        $(".rtcl-store-logo-wrap .rtcl-media-action")
            .on('click', 'span.remove', function () {
                var self = $(this),
                    logo_wrap = self.parents(".rtcl-store-logo"),
                    logo_holder = $('.logo', logo_wrap),
                    data = {
                        action: 'rtcl_ajax_store_logo_delete',
                        __rtcl_wpnonce: rtcl_store.__rtcl_wpnonce
                    };
                if (confirm(rtcl_store.confirm_text)) {
                    $.ajax({
                        url: rtcl_store.ajaxurl,
                        data: data,
                        type: 'POST',
                        beforeSend: function () {
                            $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(self);
                        },
                        success: function (response) {
                            self.next('.rtcl-icon-spinner').remove();
                            if (!response.error) {
                                logo_wrap.addClass('no-logo');
                                logo_wrap.removeClass('has-logo');
                                logo_holder.html("");
                            }
                        },
                        error: function (jqXhr, json, errorThrown) {
                            self.next('.rtcl-icon-spinner').remove();
                            console.log('error');
                        }
                    });
                }

            });

        $(".rtcl-store-logo-wrap .rtcl-media-action")
            .on('click', 'span.add', function () {
                var addBtn = $(this),
                    logoFile = $("<input type='file' style='position:absolute;left:-9999px' />");
                $('body').append(logoFile);
                if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
                    logoFile.trigger('change');
                } else {
                    logoFile.trigger('click');
                }
                logoFile.on('change', function () {
                    var fileItem = $(this),
                        logo_wrap = addBtn.parents(".rtcl-store-logo"),
                        logo_holder = $('.logo', logo_wrap),
                        form = new FormData(),
                        logo = fileItem[0].files[0],
                        allowed_image_types = rtcl_store.image_allowed_type.map(function (type) {
                            return 'image/' + type;
                        }),
                        max_image_size = parseInt(rtcl_store.max_image_size);
                    if ($.inArray(logo.type, allowed_image_types) !== -1) {
                        if (logo.size <= max_image_size) {
                            form.append('logo', logo);
                            form.append('action', 'rtcl_ajax_store_logo_upload');
                            $.ajax({
                                url: rtcl_store.ajaxurl,
                                data: form,
                                cache: false,
                                contentType: false,
                                processData: false,
                                type: 'POST',
                                beforeSend: function () {
                                    $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(addBtn);
                                },
                                success: function (response) {
                                    console.log(response);
                                    addBtn.next('.rtcl-icon-spinner').remove();
                                    if (!response.error) {
                                        logo_wrap.addClass('has-logo');
                                        logo_wrap.removeClass('no-logo');
                                        logo_holder.html("<img class='rtcl-thumbnail' src='" + response.data.src + "'/>");
                                    }
                                },
                                error: function (jqXhr, json, errorThrown) {
                                    addBtn.next('.rtcl-icon-spinner').remove();
                                    console.log('error');
                                }
                            });
                        } else {
                            alert(rtcl_store.error_image_size);
                        }
                    } else {
                        alert(rtcl_store.error_image_extension);
                    }
                });
            });

        $("#oh-type-wrap")
            .on('change', "input[name='meta[oh_type]']", function () {
                var self = $(this),
                    oh_type = self.val();
                if ('selected' === oh_type) {
                    $("#oh-list").removeClass('rtcl-hide');
                } else {
                    $("#oh-list").addClass('rtcl-hide');
                }
            });
        // Store category action
        $('#rtcl-store-category').on('change', function () {
            var _self = $(this),
                term_id = _self.val(),
                msgHolder = $("<div class='alert rtcl-response'></div>"),
                target = _self.parents('#rtcl-store-category-wrap'),
                sub_category_wrap = $('#rtcl-store-sub-category-holder'),
                sub_cat_row = $('#rtcl-store-sub-cat-row'),
                data = {
                    'action': 'rtcl_store_get_child_category',
                    'term_id': term_id
                };
            if (term_id) {
                $.ajax({
                    url: rtcl_store.ajaxurl,
                    data: data,
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function beforeSend() {
                        $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(_self);
                        sub_cat_row.addClass("rtcl-hide");
                        target.find('.alert.rtcl-response').remove();
                    },
                    success: function success(response) {
                        _self.next('.rtcl-icon-spinner').remove();

                        if (response.success) {
                            if (response.child_cats) {
                                sub_category_wrap.html($('<select name="store-sub-cat[0]" class="form-control" required />').append(response.child_cats));
                                sub_cat_row.removeClass("rtcl-hide");
                            } else {
                                sub_category_wrap.html('');
                                sub_cat_row.addClass("rtcl-hide");
                            }
                        } else {
                            sub_cat_row.addClass("rtcl-hide");
                            if (response.message.length) {
                                var msg = '';
                                response.message.map(function (message) {
                                    msg += "<p>" + message + "</p>";
                                });

                                if (msg) {
                                    msgHolder.removeClass('alert-success').addClass('alert-danger').html(msg).appendTo(target);
                                }
                            }
                        }
                    },
                    error: function error(e) {
                        _self.next('.rtcl-icon-spinner').remove();
                        msgHolder.removeClass('alert-success').addClass('alert-danger').html(e.responseText).appendTo(target);
                    }
                });
            } else {
                sub_category_wrap.html('');
                sub_cat_row.addClass("rtcl-hide");
            }
        });
        // Store sub category action
        $(document).on('change', '#rtcl-store-sub-category-holder select', function () {
            let self = $(this),
                target = self.parents('#rtcl-store-sub-category-holder'),
                term_id = $(this).val(),
                msgHolder = $("<div class='alert rtcl-response'></div>"),
                data = {
                    'action': 'rtcl_store_get_child_category',
                    'term_id': term_id
                };
            if (term_id) {
                $.ajax({
                    url: rtcl_store.ajaxurl,
                    data: data,
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function beforeSend() {
                        $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(self);
                        target.find('.alert.rtcl-response').remove();
                        self.nextAll('select').remove();
                    },
                    success: function success(response) {
                        target.find('.rtcl-icon-spinner').remove();
                        if (response.success) {
                            if (response.child_cats) {
                                target.append($('<select class="form-control" required />').append(response.child_cats));
                            } else {
                                if (response.message.length) {
                                    var msg = '';
                                    response.message.map(function (message) {
                                        msg += "<p>" + message + "</p>";
                                    });

                                    if (msg) {
                                        msgHolder.removeClass('alert-success').addClass('alert-danger').html(msg).appendTo(target);
                                    }
                                }
                            }
                        } else {
                            self.nextAll('select').remove();
                            if (response.message.length) {
                                let msg = '';
                                response.message.map(function (message) {
                                    msg += "<p>" + message + "</p>";
                                });
                                if (msg) {
                                    msgHolder.removeClass('alert-success').addClass('alert-danger').html(msg).appendTo(target);
                                }
                            }
                        }
                    },
                    error: function error(e) {
                        target.find('.rtcl-icon-spinner').remove();
                        msgHolder.removeClass('alert-success').addClass('alert-danger').html(e.responseText).appendTo(target);
                    }
                });
            } else {
                self.nextAll('select').remove();
            }
        });

        // Set category term id
        $(document).on('change', '.rtcl-store-category-wrap select', function () {
            let self = $(this),
                target = self.parents('.rtcl-store-category-wrap'),
                term_id = $(this).val();
            if (term_id) {
                target.find('#rtcl-store-cat-id').val(term_id);
            } else {
                target.find('#rtcl-store-cat-id').val('');
            }
        });

        $('.open-hour').timepicker(rtcl_store.store_time_options).on('show.timepicker', function (e) {
            $('body').addClass('rtcl');
        }).on('hide.timepicker', function (e) {
            $('body').removeClass('rtcl');
        });
        $('.close-hour').timepicker(rtcl_store.store_time_options).on('show.timepicker', function (e) {
            $('body').addClass('rtcl');
        }).on('hide.timepicker', function (e) {
            $('body').removeClass('rtcl');
        });

        $(".rtcl-self-rm-store-manager")
            .on('click', function (e) {
                e.preventDefault();
                if (confirm(rtcl_store.confirm_text)) {
                    var wrap = $(this).parent();
                    $.ajax({
                        url: rtcl_store.ajaxurl,
                        type: "POST",
                        data: {
                            __rtcl_wpnonce: rtcl_store.__rtcl_wpnonce,
                            action: 'rtcl_ajax_store_self_rm_store_manager'
                        },
                        beforeSend: function () {
                            wrap.rtclBlock();
                        },
                        success: function (res) {
                            wrap.rtclUnblock();
                            if (res.success) {
                                toastr.success(res.data.message);
                                wrap.remove();
                            } else {
                                toastr.error(rtcl_store.data);
                            }
                        },
                        error: function (e) {
                            console.log(e);
                            wrap.rtclUnblock();
                            toastr.error(rtcl_store.server_error);
                        }
                    });
                }
            });
    });

    if ($.fn.validate) {
        $.validator.setDefaults({
            rules: {seltype: "required"},
            errorElement: "div",
            errorClass: "with-errors",
            errorPlacement: function (error, element) {
                error.addClass("help-block").removeClass('error');

                if (element.prop("type") === "checkbox" || element.prop("type") === "radio") {
                    error.insertAfter(element.parents(".rtcl-check-list"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error has-danger").removeClass("has-success");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-success").removeClass("has-error has-danger");
            }
        });

        // Validate Store settings form
        $('#rtcl-store-settings').validate({
            submitHandler: function (form) {

                var $form = $(form),
                    targetBtn = $form.find('input[type=submit]'),
                    responseHolder = $form.parent().find('.rtcl-response'),
                    msgHolder = $("<div class='alert'></div>"),
                    formData = new FormData(form);
                formData.append('action', 'rtcl_update_store_data');
                formData.append('__rtcl_wpnonce', rtcl_store.__rtcl_wpnonce);

                $.ajax({
                    url: rtcl_store.ajaxurl,
                    data: formData,
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $form.rtclBlock();
                        $form.addClass("rtcl-loading");
                        targetBtn.prop("disabled", true);
                        responseHolder.html('');
                        $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(targetBtn);
                    },
                    success: function (response) {
                        $form.rtclUnblock();
                        targetBtn.prop("disabled", false).next('.rtcl-icon-spinner').remove();
                        $form.removeClass("rtcl-loading");
                        if (!response.error) {
                            msgHolder.removeClass('alert-danger').addClass('alert-success').html(response.message).appendTo(responseHolder);
                            setTimeout(function () {
                                responseHolder.html('');
                            }, 1000);
                        } else {
                            msgHolder.removeClass('alert-success').addClass('alert-danger').html(response.message).appendTo(responseHolder);
                        }
                    },
                    error: function (e) {
                        console.log(e);
                        $form.rtclUnblock();
                        msgHolder.removeClass('alert-success').addClass('alert-danger').html(e.responseText).appendTo(responseHolder);
                        targetBtn.prop("disabled", false).next('.rtcl-icon-spinner').remove();
                        $form.removeClass("rtcl-loading");
                    }
                });

                return false;
            }
        });
    }

})(jQuery);
