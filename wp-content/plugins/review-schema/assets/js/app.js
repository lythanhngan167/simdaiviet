/**
 * Featherlight - ultra slim jQuery lightbox
 * Version 1.7.14 - http://noelboss.github.io/featherlight/
 *
 * Copyright 2019, Noël Raoul Bossart (http://www.noelboss.com)
 * MIT Licensed.
**/
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof module&&module.exports?module.exports=function(b,c){return void 0===c&&(c="undefined"!=typeof window?require("jquery"):require("jquery")(b)),a(c),c}:a(jQuery)}(function(a){"use strict";function b(a,c){if(!(this instanceof b)){var d=new b(a,c);return d.open(),d}this.id=b.id++,this.setup(a,c),this.chainCallbacks(b._callbackChain)}function c(a,b){var c={};for(var d in a)d in b&&(c[d]=a[d],delete a[d]);return c}function d(a,b){var c={},d=new RegExp("^"+b+"([A-Z])(.*)");for(var e in a){var f=e.match(d);if(f){var g=(f[1]+f[2].replace(/([A-Z])/g,"-$1")).toLowerCase();c[g]=a[e]}}return c}if("undefined"==typeof a)return void("console"in window&&window.console.info("Too much lightness, Featherlight needs jQuery."));if(a.fn.jquery.match(/-ajax/))return void("console"in window&&window.console.info("Featherlight needs regular jQuery, not the slim version."));var e=[],f=function(b){return e=a.grep(e,function(a){return a!==b&&a.$instance.closest("body").length>0})},g={allow:1,allowfullscreen:1,frameborder:1,height:1,longdesc:1,marginheight:1,marginwidth:1,mozallowfullscreen:1,name:1,referrerpolicy:1,sandbox:1,scrolling:1,src:1,srcdoc:1,style:1,webkitallowfullscreen:1,width:1},h={keyup:"onKeyUp",resize:"onResize"},i=function(c){a.each(b.opened().reverse(),function(){return c.isDefaultPrevented()||!1!==this[h[c.type]](c)?void 0:(c.preventDefault(),c.stopPropagation(),!1)})},j=function(c){if(c!==b._globalHandlerInstalled){b._globalHandlerInstalled=c;var d=a.map(h,function(a,c){return c+"."+b.prototype.namespace}).join(" ");a(window)[c?"on":"off"](d,i)}};b.prototype={constructor:b,namespace:"featherlight",targetAttr:"data-featherlight",variant:null,resetCss:!1,background:null,openTrigger:"click",closeTrigger:"click",filter:null,root:"body",openSpeed:250,closeSpeed:250,closeOnClick:"background",closeOnEsc:!0,closeIcon:"&#10005;",loading:"",persist:!1,otherClose:null,beforeOpen:a.noop,beforeContent:a.noop,beforeClose:a.noop,afterOpen:a.noop,afterContent:a.noop,afterClose:a.noop,onKeyUp:a.noop,onResize:a.noop,type:null,contentFilters:["jquery","image","html","ajax","iframe","text"],setup:function(b,c){"object"!=typeof b||b instanceof a!=!1||c||(c=b,b=void 0);var d=a.extend(this,c,{target:b}),e=d.resetCss?d.namespace+"-reset":d.namespace,f=a(d.background||['<div class="'+e+"-loading "+e+'">','<div class="'+e+'-content">','<button class="'+e+"-close-icon "+d.namespace+'-close" aria-label="Close">',d.closeIcon,"</button>",'<div class="'+d.namespace+'-inner">'+d.loading+"</div>","</div>","</div>"].join("")),g="."+d.namespace+"-close"+(d.otherClose?","+d.otherClose:"");return d.$instance=f.clone().addClass(d.variant),d.$instance.on(d.closeTrigger+"."+d.namespace,function(b){if(!b.isDefaultPrevented()){var c=a(b.target);("background"===d.closeOnClick&&c.is("."+d.namespace)||"anywhere"===d.closeOnClick||c.closest(g).length)&&(d.close(b),b.preventDefault())}}),this},getContent:function(){if(this.persist!==!1&&this.$content)return this.$content;var b=this,c=this.constructor.contentFilters,d=function(a){return b.$currentTarget&&b.$currentTarget.attr(a)},e=d(b.targetAttr),f=b.target||e||"",g=c[b.type];if(!g&&f in c&&(g=c[f],f=b.target&&e),f=f||d("href")||"",!g)for(var h in c)b[h]&&(g=c[h],f=b[h]);if(!g){var i=f;if(f=null,a.each(b.contentFilters,function(){return g=c[this],g.test&&(f=g.test(i)),!f&&g.regex&&i.match&&i.match(g.regex)&&(f=i),!f}),!f)return"console"in window&&window.console.error("Featherlight: no content filter found "+(i?' for "'+i+'"':" (no target specified)")),!1}return g.process.call(b,f)},setContent:function(b){return this.$instance.removeClass(this.namespace+"-loading"),this.$instance.toggleClass(this.namespace+"-iframe",b.is("iframe")),this.$instance.find("."+this.namespace+"-inner").not(b).slice(1).remove().end().replaceWith(a.contains(this.$instance[0],b[0])?"":b),this.$content=b.addClass(this.namespace+"-inner"),this},open:function(b){var c=this;if(c.$instance.hide().appendTo(c.root),!(b&&b.isDefaultPrevented()||c.beforeOpen(b)===!1)){b&&b.preventDefault();var d=c.getContent();if(d)return e.push(c),j(!0),c.$instance.fadeIn(c.openSpeed),c.beforeContent(b),a.when(d).always(function(a){a&&(c.setContent(a),c.afterContent(b))}).then(c.$instance.promise()).done(function(){c.afterOpen(b)})}return c.$instance.detach(),a.Deferred().reject().promise()},close:function(b){var c=this,d=a.Deferred();return c.beforeClose(b)===!1?d.reject():(0===f(c).length&&j(!1),c.$instance.fadeOut(c.closeSpeed,function(){c.$instance.detach(),c.afterClose(b),d.resolve()})),d.promise()},resize:function(a,b){if(a&&b){this.$content.css("width","").css("height","");var c=Math.max(a/(this.$content.parent().width()-1),b/(this.$content.parent().height()-1));c>1&&(c=b/Math.floor(b/c),this.$content.css("width",""+a/c+"px").css("height",""+b/c+"px"))}},chainCallbacks:function(b){for(var c in b)this[c]=a.proxy(b[c],this,a.proxy(this[c],this))}},a.extend(b,{id:0,autoBind:"[data-featherlight]",defaults:b.prototype,contentFilters:{jquery:{regex:/^[#.]\w/,test:function(b){return b instanceof a&&b},process:function(b){return this.persist!==!1?a(b):a(b).clone(!0)}},image:{regex:/\.(png|jpg|jpeg|gif|tiff?|bmp|svg)(\?\S*)?$/i,process:function(b){var c=this,d=a.Deferred(),e=new Image,f=a('<img src="'+b+'" alt="" class="'+c.namespace+'-image" />');return e.onload=function(){f.naturalWidth=e.width,f.naturalHeight=e.height,d.resolve(f)},e.onerror=function(){d.reject(f)},e.src=b,d.promise()}},html:{regex:/^\s*<[\w!][^<]*>/,process:function(b){return a(b)}},ajax:{regex:/./,process:function(b){var c=a.Deferred(),d=a("<div></div>").load(b,function(a,b){"error"!==b&&c.resolve(d.contents()),c.reject()});return c.promise()}},iframe:{process:function(b){var e=new a.Deferred,f=a("<iframe/>"),h=d(this,"iframe"),i=c(h,g);return f.hide().attr("src",b).attr(i).css(h).on("load",function(){e.resolve(f.show())}).appendTo(this.$instance.find("."+this.namespace+"-content")),e.promise()}},text:{process:function(b){return a("<div>",{text:b})}}},functionAttributes:["beforeOpen","afterOpen","beforeContent","afterContent","beforeClose","afterClose"],readElementConfig:function(b,c){var d=this,e=new RegExp("^data-"+c+"-(.*)"),f={};return b&&b.attributes&&a.each(b.attributes,function(){var b=this.name.match(e);if(b){var c=this.value,g=a.camelCase(b[1]);if(a.inArray(g,d.functionAttributes)>=0)c=new Function(c);else try{c=JSON.parse(c)}catch(h){}f[g]=c}}),f},extend:function(b,c){var d=function(){this.constructor=b};return d.prototype=this.prototype,b.prototype=new d,b.__super__=this.prototype,a.extend(b,this,c),b.defaults=b.prototype,b},attach:function(b,c,d){var e=this;"object"!=typeof c||c instanceof a!=!1||d||(d=c,c=void 0),d=a.extend({},d);var f,g=d.namespace||e.defaults.namespace,h=a.extend({},e.defaults,e.readElementConfig(b[0],g),d),i=function(g){var i=a(g.currentTarget),j=a.extend({$source:b,$currentTarget:i},e.readElementConfig(b[0],h.namespace),e.readElementConfig(g.currentTarget,h.namespace),d),k=f||i.data("featherlight-persisted")||new e(c,j);"shared"===k.persist?f=k:k.persist!==!1&&i.data("featherlight-persisted",k),j.$currentTarget.blur&&j.$currentTarget.blur(),k.open(g)};return b.on(h.openTrigger+"."+h.namespace,h.filter,i),{filter:h.filter,handler:i}},current:function(){var a=this.opened();return a[a.length-1]||null},opened:function(){var b=this;return f(),a.grep(e,function(a){return a instanceof b})},close:function(a){var b=this.current();return b?b.close(a):void 0},_onReady:function(){var b=this;if(b.autoBind){var c=a(b.autoBind);c.each(function(){b.attach(a(this))}),a(document).on("click",b.autoBind,function(d){if(!d.isDefaultPrevented()){var e=a(d.currentTarget),f=c.length;if(c=c.add(e),f!==c.length){var g=b.attach(e);(!g.filter||a(d.target).parentsUntil(e,g.filter).length>0)&&g.handler(d)}}})}},_callbackChain:{onKeyUp:function(b,c){return 27===c.keyCode?(this.closeOnEsc&&a.featherlight.close(c),!1):b(c)},beforeOpen:function(b,c){return a(document.documentElement).addClass("with-featherlight"),this._previouslyActive=document.activeElement,this._$previouslyTabbable=a("a, input, select, textarea, iframe, button, iframe, [contentEditable=true]").not("[tabindex]").not(this.$instance.find("button")),this._$previouslyWithTabIndex=a("[tabindex]").not('[tabindex="-1"]'),this._previousWithTabIndices=this._$previouslyWithTabIndex.map(function(b,c){return a(c).attr("tabindex")}),this._$previouslyWithTabIndex.add(this._$previouslyTabbable).attr("tabindex",-1),document.activeElement.blur&&document.activeElement.blur(),b(c)},afterClose:function(c,d){var e=c(d),f=this;return this._$previouslyTabbable.removeAttr("tabindex"),this._$previouslyWithTabIndex.each(function(b,c){a(c).attr("tabindex",f._previousWithTabIndices[b])}),this._previouslyActive.focus(),0===b.opened().length&&a(document.documentElement).removeClass("with-featherlight"),e},onResize:function(a,b){return this.resize(this.$content.naturalWidth,this.$content.naturalHeight),a(b)},afterContent:function(a,b){var c=a(b);return this.$instance.find("[autofocus]:not([disabled])").focus(),this.onResize(b),c}}}),a.featherlight=b,a.fn.featherlight=function(a,c){return b.attach(this,a,c),this},a(document).ready(function(){b._onReady()})});
(function ($, window) {
    'use strict';

    $(function () {
        function rtrs_repeter(repeter_type) {
            // repeter field 
            let repeter_class = ".rtrs-" + repeter_type;
            $(document).on('click', repeter_class + " .rtrs-field-add", function (e) {

                //check pro 
                let field_length = $(repeter_class + " input").length;

                field_length++;
                if (field_length >= rtrs.pro_cons_limit) {
                    $(repeter_class + " .rtrs-field-add").addClass('rtrs-hidden');
                }

                e.preventDefault();
                let new_field = '<div class="rtrs-input-filed"><span class="rtrs-remove-btn">+</span><input type="text" class="form-control" name="rt_' + repeter_type + '[]" placeholder="' + rtrs.write_txt + '"></div>';
                $(repeter_class + " .rtrs-field-add").before(new_field);
            });

            // remove repeter field 
            $(document).on('click', repeter_class + " .rtrs-remove-btn", function (e) {
                e.preventDefault();
                $(this).parent().remove();

                let field_length = $(repeter_class + " input").length;
                field_length++;
                if (field_length <= rtrs.pro_cons_limit) {
                    $(repeter_class + " .rtrs-field-add").removeClass('rtrs-hidden');
                }

            });
        }
        rtrs_repeter('pros');
        rtrs_repeter('cons');

        // google captcha verify  
        if (rtrs.recaptcha_sitekey) {
            $('form.rtrs-form-box').on('submit', function (e) {
                if (!rtrs.recaptcha) return;

                e.preventDefault();
                // cache the current form so you make sure to only have data from this one
                let $form = $(this);

                grecaptcha.ready(function () {
                    grecaptcha.execute(rtrs.recaptcha_sitekey, { action: 'reviewForm' }).then(function (token) {
                        document.getElementById("gRecaptchaResponse").value = token;

                        $form.off('submit').submit();
                        $('.rtrs-review-submit').trigger('click');
                    });
                });
            });
        }

        // featherlight popup 
        function rtrs_featherlight_popup() {
            // $('.rtrs-media-image').featherlight({type: 'image'});  
        }
        rtrs_featherlight_popup();

        //edit review 
        $(document).on('click', '.rtrs-review-edit-btn', function (e) {
            e.preventDefault();

            let comment_post_id = $(this).attr('data-comment-post-id');
            let comment_id = $(this).attr('data-comment-id');
            let $this = $(this);

            $.ajax({
                type: "post",
                dataType: "json",
                url: rtrs.ajaxurl,
                data: {
                    action: "rtrs_review_edit_form",
                    comment_post_id: comment_post_id,
                    comment_id: comment_id,
                },
                beforeSend: function () {
                    $this.html('(' + rtrs.loading + ')');
                },
                success: function (resp) {
                    if (resp.success) {
                        $this.html('(' + rtrs.edit + ')');
                        $('body').prepend(resp.data);
                        //load again video sources
                        video_source_option();
                        $('#rtrs-video-source').on('change', function () {
                            video_source_option();
                        });
                    } else {
                        alert(resp.data);
                    }
                },
            });
        });

        //edit review 
        $(document).on('click', '.rtrs-review-edit-submit', function (e) {
            e.preventDefault();

            let form = $(this).parents('form').serialize();

            $.ajax({
                type: "post",
                dataType: "json",
                url: rtrs.ajaxurl,
                data: form,
                success: function (resp) {
                    if (resp.success) {
                        location.reload();
                    } else {
                        console.log(resp.data);
                    }
                },
            });
        });

        //hide review outside click
        $(document).on('mouseup', function (e) {
            let container = $(".rtrs-review-popup");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $(".rtrs-modal").remove();
            }
        });

        // highlight review 
        $(document).on("click", '.rtrs-review-highlight', function (e) {
            let commentID = $(this).data('comment-id');
            let highlight = $(this).attr('data-highlight');

            if (highlight == 'highlight') {
                $(this).html(rtrs.remove_highlight);
                $(this).attr('data-highlight', 'remove');

                // highlight bg
                $(this).closest(".rtrs-each-review").addClass('rtrs-top-review');
            } else {
                $(this).html(rtrs.highlight);
                $(this).attr('data-highlight', 'highlight');

                // remove highlight
                $(this).closest(".rtrs-each-review").removeClass('rtrs-top-review');
            }

            $.ajax({
                type: "post",
                dataType: "json",
                url: rtrs.ajaxurl,
                data: {
                    action: "rtrs_review_hightlight",
                    comment_id: commentID,
                    highlight: highlight,
                },
                beforeSend: function () {
                },
                success: function (resp) {
                },
            });
        });

        // helpful review  
        $(document).on("click", '.rtrs-review-helpful', function (e) {
            let commentID = $(this).data('comment-id');
            let helpful = $(this).attr('data-helpful');
            let type = $(this).attr('data-type');

            let old_helpful = $(this).find(".helpful-count").html();

            if (helpful == 'helpful') {
                $(this).attr('data-helpful', 'remove');

                old_helpful++;
                $(this).find(".helpful-count").html(old_helpful);

            } else {
                $(this).attr('data-helpful', 'helpful');

                old_helpful--;
                $(this).find(".helpful-count").html(old_helpful)
            }

            // decrement
            let decrement_selector = type == 'like' ? $(this).next() : $(this).prev();
            if (decrement_selector.attr('data-helpful') == 'remove') {
                decrement_selector.attr('data-helpful', 'helpful');

                let old_decrement_selector = decrement_selector.find(".helpful-count").html();
                old_decrement_selector--;
                decrement_selector.find(".helpful-count").html(old_decrement_selector);
            }

            $.ajax({
                type: "post",
                dataType: "json",
                url: rtrs.ajaxurl,
                data: {
                    action: "rtrs_review_helpful",
                    comment_id: commentID,
                    helpful: helpful,
                    type: type,
                    nonce: rtrs.nonce,
                },
                beforeSend: function () {
                },
                success: function (resp) {
                    // console.log(resp.data);
                },
            });
        });

        // share review  
        $(document).on("click", '.rtrs-share-review', function (e) {
            e.preventDefault();
            let url = $(this).attr('data-url');
            let width = 800;
            let height = 600;
            let top = (screen.height / 2) - (height / 2);
            let left = (screen.width / 2) - (width / 2);
            return window.open(url, '', 'location=1,status=1,resizable=yes,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left);
        });

        const url = new URL(window.location.href);

        // review filter
        $('.rtrs_review_filter').on('change', function (e) {
            let select_value = this.value;
            let data_type = $(this).data('type');

            if (data_type === 'sort') {
                url.searchParams.set('sort_by', select_value);
            } else {
                url.searchParams.set('filter_by', select_value);
            }
            window.history.replaceState(null, null, url);

            let sort_by = url.searchParams.get('sort_by');

            let filter_by = url.searchParams.get('filter_by');

            $.ajax({
                type: "post",
                dataType: "json",
                url: rtrs.ajaxurl,
                data: {
                    action: "rtrs_review_filter",
                    post_id: rtrs.post_id,
                    sort_by,
                    filter_by,
                },
                beforeSend: function () {
                    $('.rtrs-paginate').html(rtrs.loading);
                },
                success: function (resp) {
                    if (resp.success) {
                        $('.rtrs-review-list').html(resp.data.review);
                        $('.rtrs-paginate').html(resp.data.pagination);
                    }

                    rtrs_featherlight_popup();
                },
            });
        });

        //upload image
        // Todo: select closest id rtrs-image
        $(document).on('click', '#rtrs-upload-box-image', function () { $('#rtrs-image').trigger('click'); });

        $(document).on('change', '#rtrs-image', function (e) {
            let file_data, form_data;
            file_data = $(this).prop('files')[0];
            form_data = new FormData();
            form_data.append('rtrs-image', file_data);
            form_data.append('nonce', rtrs.nonce);
            form_data.append('action', 'rtrs_image_upload');

            $.ajax({
                url: rtrs.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function () {
                    $('.rtrs-image-error').html('');
                    $('#rtrs-upload-box-image span').html(rtrs.loading);
                },
                success: function (resp) {
                    if (resp.success) {
                        $('.rtrs-preview-imgs').append("<div class='rtrs-preview-img'><img src='" + resp.data.file_info.url + "' /><input type='hidden' name='rt_attachment[imgs][]' value='" + resp.data.file_info.id + "'><span class='rtrs-file-remove' data-id='" + resp.data.file_info.id + "'>x</span></div>");
                    } else {
                        $('.rtrs-image-error').html(resp.data.msg);
                    }
                    $('#rtrs-upload-box-image span').html(rtrs.upload_img);
                }
            });
        });

        //delete image  
        $(document).on('click', '.rtrs-file-remove', function (e) {
            e.preventDefault();
            let attachment_id = $(this).data('id');
            if (confirm(rtrs.sure_txt)) {
                $(this).parent().remove();
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: rtrs.ajaxurl,
                    data: {
                        action: "rtrs_remove_file",
                        attachment_id: attachment_id,
                    },
                    success: function () { },
                });
            }
        });

        //upload video
        $(document).on('click', '#rtrs-upload-box-video', function () { $('#rtrs-video').trigger('click'); });

        $(document).on('change', '#rtrs-video', function (e) {
            let file_data, form_data;
            file_data = $(this).prop('files')[0];
            form_data = new FormData();
            form_data.append('rtrs-video', file_data);
            form_data.append('nonce', rtrs.nonce);
            form_data.append('action', 'rtrs_video_upload');

            $.ajax({
                url: rtrs.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function () {
                    $('.rtrs-video-error').html('');
                    $('#rtrs-upload-box-video span').html(rtrs.loading);
                },
                success: function (resp) {
                    if (resp.success) {
                        $('.rtrs-preview-videos').append("<div class='rtrs-preview-video'><span class='name'>" + resp.data.file_info.name + "</span><input type='hidden' name='rt_attachment[videos][]' value='" + resp.data.file_info.id + "'><span class='rtrs-file-remove'>x</span></div>");
                    } else {
                        $('.rtrs-video-error').html(resp.data.msg);
                    }
                    $('#rtrs-upload-box-video span').html(rtrs.upload_video);
                }
            });
        });

        // video source select  
        function video_source_option() {
            let video_source = $("#rtrs-video-source").val();
            if (video_source == 'self') {
                $('.rtrs-source-video').show();
                $('.rtrs-source-external').hide();
            } else {
                $('.rtrs-source-video').hide();
                $('.rtrs-source-external').show();
            }
        }
        video_source_option();

        $('#rtrs-video-source').on('change', function () {
            video_source_option();
        });

        //self hosted video popup 
        $(document).on('click', '.rtrs-play-self-video', function (e) {
            e.preventDefault();

            let video_url = $(this).attr('data-video-url');

            $.ajax({
                type: "post",
                dataType: "json",
                url: rtrs.ajaxurl,
                data: {
                    action: "rtrs_self_video_popup",
                    video_url
                },
                success: function (resp) {
                    if (resp.success) {
                        $('body').prepend(resp.data);
                    }
                },
            });
        });
        //Ajax load more review
        // we will remove the button and load its new copy with AJAX, that's why $('body').on()
        $('body').on('click', '#rtrs-load-more', function () {
            let max_page = $('#rtrs-load-more').attr('data-max');
            let btn = $('#rtrs-load-more');
            let sort_by = url.searchParams.get('sort_by');

            $.ajax({
                url: rtrs.ajaxurl,
                data: {
                    action: 'rtrs_pagination',
                    post_id: rtrs.post_id,
                    current_page: rtrs.current_page,
                    sort_by,
                },
                type: 'POST',
                dataType: "json",
                beforeSend: function () {
                    btn.text(rtrs.loading);
                },
                success: function (resp) {
                    btn.text('Load More');
                    if (resp.success) {
                        $('.rtrs-review-list').append(resp.data.review);
                    }

                    rtrs.current_page++;
                    if (rtrs.current_page == max_page) {
                        btn.remove();
                    }

                    rtrs_featherlight_popup();
                }
            });
            return false;
        });

        //Ajax pagination with number
        // we will remove the button and load its new copy with AJAX, that's why $('body').on()
        $('body').on('click', '.rtrs-paginate-ajax a', function (e) {
            e.preventDefault();
            let pagi_url = $(this).attr('href');
            let prag_match = pagi_url.match('/comment-page-([0-9]+)/');
            let current_page = 1;
            if (prag_match) {
                current_page = prag_match[1];
            }

            let max_page = $(this).parent().attr('data-max');
            let sort_by = url.searchParams.get('sort_by');

            $.ajax({
                url: rtrs.ajaxurl,
                data: {
                    action: 'rtrs_pagination',
                    post_id: rtrs.post_id,
                    current_page: current_page,
                    max_page: max_page,
                    pagi_num: true,
                    sort_by,
                },
                type: 'POST',
                dataType: "json",
                beforeSend: function () {
                    $('.rtrs-paginate-ajax').html(rtrs.loading);
                },
                success: function (resp) {
                    if (resp.success) {
                        $('.rtrs-review-list').html(resp.data.review);
                        $('.rtrs-paginate-ajax').html(resp.data.pagination);
                        // console.log(resp.data.number);
                    }

                    rtrs_featherlight_popup();
                }
            });
            return false;
        });

        //on scroll pagination
        if ($(".rtrs-paginate-onscroll").length > 0) {
            let onScrollPagi = true;
            $(window).scroll(function () {

                if (!onScrollPagi) return;

                let bottomOffset = 2900; // the distance (in px) from the page bottom when you want to load more posts

                let max_page = $('.rtrs-paginate-onscroll').attr('data-max');
                let sort_by = url.searchParams.get('sort_by');

                if (rtrs.current_page >= max_page) {
                    onScrollPagi = false;
                    return;
                }

                if ($(document).scrollTop() > ($(document).height() - bottomOffset) && onScrollPagi == true) {
                    $.ajax({
                        url: rtrs.ajaxurl,
                        data: {
                            action: 'rtrs_pagination',
                            post_id: rtrs.post_id,
                            current_page: rtrs.current_page,
                            sort_by: sort_by,
                        },
                        type: 'POST',
                        dataType: "json",
                        beforeSend: function () {
                            $('.rtrs-paginate-onscroll').html(rtrs.loading);
                            onScrollPagi = false;
                        },
                        success: function (resp) {
                            if (resp.success) {
                                $('.rtrs-review-list').append(resp.data.review);
                                $('.rtrs-paginate-onscroll').html('');
                                rtrs.current_page++;
                                onScrollPagi = true;

                                rtrs_featherlight_popup();
                            }
                        }
                    });
                }
            });
        }

    });

})(jQuery, window);

// require('../vendor/featherlight/featherlight.min.js');