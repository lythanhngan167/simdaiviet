;(function ($) {
    $(function () {
        var store_id = $("#post_ID").val() || 0;
        $(".rtcl-store-banner-wrap .rtcl-media-action").on('click', 'span.remove', function () {
            var self = $(this),
                banner_wrap = self.parents(".rtcl-store-banner"),
                banner_holder = $('.banner', banner_wrap),
                data = {
                    store_id: store_id,
                    action: 'rtcl_admin_ajax_store_banner_delete',
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

        $(".rtcl-store-banner-wrap .rtcl-media-action").on('click', 'span.add', function () {
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
                        form.append('store_id', store_id);
                        form.append('action', 'rtcl_admin_ajax_store_banner_upload');
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

        $(".rtcl-store-logo-wrap .rtcl-media-action").on('click', 'span.remove', function () {
            var self = $(this),
                logo_wrap = self.parents(".rtcl-store-logo"),
                logo_holder = $('.logo', logo_wrap),
                data = {
                    store_id: store_id,
                    action: 'rtcl_admin_ajax_store_logo_delete',
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

        $(".rtcl-store-logo-wrap .rtcl-media-action").on('click', 'span.add', function () {
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
                        form.append('store_id', store_id);
                        form.append('action', 'rtcl_admin_ajax_store_logo_upload');
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

        $("#oh-type-wrap").on('change', "input[name='oh-type']", function () {
            var self = $(this),
                oh_type = self.val();
            if ('selected' === oh_type) {
                $("#oh-list").slideDown();
            } else {
                $("#oh-list").slideUp();
            }
        });

        $('.open-hour').timepicker(rtcl_store.store_time_options)
            .on('show.timepicker', function (e) {
                $('body').addClass('rtcl');
            })
            .on('hide.timepicker', function (e) {
                $('body').removeClass('rtcl');
            });
        $('.close-hour').timepicker(rtcl_store.store_time_options)
            .on('show.timepicker', function (e) {
                $('body').addClass('rtcl');
            })
            .on('hide.timepicker', function (e) {
                $('body').removeClass('rtcl');
            });
    });
}(jQuery));