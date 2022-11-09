/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/admin.js":
/*!*************************!*\
  !*** ./src/js/admin.js ***!
  \*************************/
/***/ (() => {

(function ($) {
  "use strict";

  var rtApp = {
    /**
     * Meta tab
     * @since 1.0.0
     * @return {mixed} 
     */
    metaTab: function metaTab() {
      $(".rtrs-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var $this = $(this),
            container = $this.parents('.rtrs-tab-container'),
            nav = container.children('.rtrs-tab-nav'),
            content = container.children(".rtrs-tab-content"),
            $id = $this.attr('href');

        switch ($id) {
          case '#sc-review':
            $('#_rtrs_sc_tab').val('review');
            break;

          case '#sc-schema':
            $('#_rtrs_sc_tab').val('schema');
            break;

          case '#sc-settings':
            $('#_rtrs_sc_tab').val('setting');
            break;

          case '#sc-affiliate':
            $('#_rtrs_sc_tab').val('affiliate');
            break;

          case '#sc-style':
            $('#_rtrs_sc_tab').val('style');
            break;

          case '#sc-preview':
            $('#_rtrs_sc_tab').val('preview');
            break;
        }

        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
      }); // auto check tab by radio support

      $(document).on('change', '#rtrs-support input[type=radio]', function () {
        var rtrs_support = $("#rtrs-support input[name='rtrs_support']:checked").val();

        switch (rtrs_support) {
          case 'review':
          case 'review-schema':
            //save meta
            $('#_rtrs_sc_tab').val('review'); //show tab and content

            $('.rtrs-tab-nav').find('li').removeClass('active');
            $('.rtrs-tab-nav').find('li.review-tab').addClass('active');
            $('.rtrs-tab-container .rtrs-tab-content').hide();
            $('#sc-review').show();
            break;

          case 'schema':
            //save meta
            $('#_rtrs_sc_tab').val('schema'); //show tab and content

            $('.rtrs-tab-nav').find('li').removeClass('active');
            $('.rtrs-tab-nav').find('li.schema-tab').addClass('active');
            $('.rtrs-tab-container .rtrs-tab-content').hide();
            $('#sc-schema').show();
            break;
        }
      });
    },

    /**
     * Alert box
     * @since 1.0.0
     * @return {mixed} 
     */
    alertBox: function alertBox() {
      //radio_image pro alert
      $('.rtrs-radio-image input[type="radio"]').on('click', function () {
        if (!rtrs.pro && $(this).data('pro') == 'yes') {
          $('.rtrs-pro-alert').show();
          $('.rtrs-pro-alert p span').html('');
          return false;
        }
      }); //pagination_type pro alert

      $("#rtrs-pagination_type").on('change', function (e) {
        var pagination_type = $("#rtrs-pagination_type option:selected").text();

        if (pagination_type.indexOf("[Pro]") >= 0) {
          if (!rtrs.pro) {
            $('#rtrs-pagination_type').val("number").select2();
            $('.rtrs-pro-alert').show();
            return false;
          }
        }
      }); //schema_type pro alert

      $("#rtrs-rich_snippet_cat_back").on('change', function (e) {
        var pagination_type = $("#rtrs-rich_snippet_cat_back option:selected").text();

        if (pagination_type.indexOf("[Pro]") >= 0) {
          if (!rtrs.pro) {
            $('#rtrs-rich_snippet_cat_back').val("article").select2();
            $('.rtrs-pro-alert').show();
            return false;
          }
        }
      });
      $("#rtrs_meta").on('click', '.pro-field', function (e) {
        e.preventDefault();
        $('.rtrs-pro-alert').show();
      }); //pro alert close

      $('.rtrs-pro-alert-close').on('click', function (e) {
        e.preventDefault();
        $('.rtrs-pro-alert').hide();
      }); //already exist alert close

      $('.rtrs-post-type-close').on('click', function (e) {
        e.preventDefault();
        $('.rtrs-post-type').hide();
      });
    },

    /**
     * Library support
     * @since 1.0.0
     * @return {mixed} 
     */
    libSupport: function libSupport() {
      //support select2 js
      if ($(".rtrs-select2").length && $.fn.select2) {
        $(".rtrs-select2").select2({
          dropdownAutoWidth: true
        });
      } //color picker


      if ($.fn.wpColorPicker) {
        $('.rt-color').wpColorPicker();
      }
    },

    /**
     * Schema data auto fill
     * @since 1.0.0
     * @return {mixed} 
     */
    autoFillSchema: function autoFillSchema() {
      function auto_fill_ajax(snippet_cat, post_id) {
        $.ajax({
          type: "POST",
          dataType: "json",
          url: rtrs.ajaxurl,
          data: {
            action: 'rtrs_auto_fill_schema',
            post_id: post_id,
            snippet_cat: snippet_cat
          },
          success: function success(resp) {
            if (resp.success) {
              var data = resp.data;

              if (data.hasOwnProperty('category')) {
                snippet_cat = data.category;
              }

              switch (snippet_cat) {
                case 'article':
                case 'news_article':
                case 'blog_posting':
                  var past_val = $('#rtrs-rich_snippet_cat').val();
                  past_val.push(snippet_cat);
                  $('#rtrs-rich_snippet_cat').val(past_val); // $('#rtrs-rich_snippet_cat').val([snippet_cat]);

                  $('#rtrs-rich_snippet_cat').trigger('change');

                  for (var key in data) {
                    if (key == 'image' || key == 'publisherImage') {
                      $('#rtrs_' + snippet_cat + '_schema_holder #rtrs_' + snippet_cat + '_schema_0_' + key).find('.rtrs-preview-imgs').html("<div class='rtrs-preview-img'><img src='" + data[key].url + "' /><input type='hidden' name='rtrs_" + snippet_cat + "_schema[0][" + key + "]' value='" + data[key].id + "'><button class='rtrs-file-remove' data-id='" + data[key].id + "'>x</button></div>");
                    } else if (key === 'description' || key === 'articleBody') {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('textarea[name="rtrs_' + snippet_cat + '_schema[0][' + key + ']"]').val(data[key]);
                    } else {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][' + key + ']"]').val(data[key]);
                    }
                  }

                  break;

                case 'product':
                  for (var _key in data) {
                    if (_key == 'image') {
                      $('#rtrs_' + snippet_cat + '_schema_holder #rtrs_' + snippet_cat + '_schema_0_' + _key).find('.rtrs-preview-imgs').html("<div class='rtrs-preview-img'><img src='" + data[_key].url + "' /><input type='hidden' name='rtrs_" + snippet_cat + "_schema[0][" + _key + "]' value='" + data[_key].id + "'><button class='rtrs-file-remove' data-id='" + data[_key].id + "'>x</button></div>");
                    } else if (_key === 'offers') {
                      for (var sub_key in data[_key][0]) {
                        $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][' + sub_key + ']"]').val(data[_key][0][sub_key]);
                      }
                    } else if (_key === 'aggregateRating') {
                      for (var _sub_key in data[_key]) {
                        $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][' + _sub_key + ']"]').val(data[_key][_sub_key]);
                      }
                    } else if (_key === 'description') {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('textarea[name="rtrs_' + snippet_cat + '_schema[0][' + _key + ']"]').val(data[_key]);
                    } else {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][' + _key + ']"]').val(data[_key]);
                    }
                  }

                  break;

                default:
                  var old_val = $('#rtrs-rich_snippet_cat').val();
                  old_val.push(snippet_cat);
                  $('#rtrs-rich_snippet_cat').val(old_val);
                  $('#rtrs-rich_snippet_cat').trigger('change');

                  for (var _key2 in data) {
                    if (_key2 === 'headline') {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][name]"]').val(data[_key2]);
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][title]"]').val(data[_key2]);
                    } else if (_key2 === 'articleBody') {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('textarea[name="rtrs_' + snippet_cat + '_schema[0][description]"]').val(data[_key2]);

                      if (snippet_cat == 'special_announcement') {
                        $('#rtrs_' + snippet_cat + '_schema_holder').find('textarea[name="rtrs_' + snippet_cat + '_schema[0][text]"]').val(data[_key2]);
                      }
                    } else if (_key2 === 'image') {
                      $('#rtrs_' + snippet_cat + '_schema_holder #rtrs_' + snippet_cat + '_schema_0_' + _key2).find('.rtrs-preview-imgs').html("<div class='rtrs-preview-img'><img src='" + data[_key2].url + "' /><input type='hidden' name='rtrs_" + snippet_cat + "_schema[0][" + _key2 + "]' value='" + data[_key2].id + "'><button class='rtrs-file-remove' data-id='" + data[_key2].id + "'>x</button></div>");
                    } else {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][' + _key2 + ']"]').val(data[_key2]);
                    } //changed key


                    if (_key2 === 'mainEntityOfPage') {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][url]"]').val(data[_key2]);
                    } else if (_key2 === 'datePublished') {
                      $('#rtrs_' + snippet_cat + '_schema_holder').find('input[name="rtrs_' + snippet_cat + '_schema[0][dateCreated]"]').val(data[_key2]);
                    }
                  }

                  break;
              }
            }
          }
        });
      }

      function get_post_type() {
        var attrs, postType;
        attrs = $('body').attr('class').split(' ');
        $(attrs).each(function () {
          if ('post-type-' === this.substr(0, 10)) {
            postType = this.split('post-type-');
            postType = postType[postType.length - 1];
            return;
          }
        });
        return postType;
      }

      var post_id = $('#post_ID').val();
      var rich_snippet_cat = $("#rtrs-rich_snippet_cat").val();
      var url_string = window.location.href;
      var url = new URL(url_string);
      var c = url.searchParams.get("action"); //auto fill up cat and data in edit screen

      if (rich_snippet_cat && !rich_snippet_cat.length && c && c == 'edit') {
        var post_type = get_post_type();

        if (post_type == 'post') {
          auto_fill_ajax('post', post_id);
        } else if (post_type == 'product' || post_type == 'download') {
          $('#rtrs-rich_snippet_cat').val(['product']);
          $('#rtrs-rich_snippet_cat').trigger('change');
          auto_fill_ajax('product', post_id);
        } else {
          //others post type from settings
          $('#rtrs-rich_snippet_cat').val([post_type]);
          $('#rtrs-rich_snippet_cat').trigger('change');
          auto_fill_ajax(post_type, post_id);
        }
      }

      $('#rtrs-rich_snippet_cat').on('select2:select', function (e) {
        var snippet_cat = e.params.data.id;
        auto_fill_ajax(snippet_cat, post_id);
      }); //auto fill data by button click

      $('.rtrs-auto_fill button').on('click', function (e) {
        e.preventDefault();
        var schema_type = $(this).data('type');
        auto_fill_ajax(schema_type, post_id);
      });
    },

    /**
     * Conditional meta field
     * @since 1.0.0
     * @return {mixed} 
     */
    conditionalField: function conditionalField() {
      $(document).on('change', '#rtrs_meta input[type=checkbox], #rtrs_meta input[type=radio], #rtrs_meta select', function () {
        showHideMeta();
      });
      $(document).on('change', '.rtrs-settings input[type=checkbox], .rtrs-settings input[type=radio], .rtrs-settings select', function () {
        showHideMeta();
      });
      $("#rtrs-rich_snippet_cat").on("change", function (e) {
        showHideMeta();
      });
      showHideMeta();

      function showHideMeta() {
        var rt_support_review_schema = $("#rtrs-support-review-schema").is(':checked');
        var rt_support_review = $("#rtrs-support-review").is(':checked');

        if (rt_support_review || rt_support_review_schema) {
          $(".rtrs-tab-nav.rt-back li:nth-child(1)").show();
          $(".rtrs-tab-nav.rt-back li:nth-child(2)").show();
          $(".rtrs-tab-nav.rt-back li:nth-child(4)").show();
        } else {
          $(".rtrs-tab-nav.rt-back li:nth-child(1)").hide();
          $(".rtrs-tab-nav.rt-back li:nth-child(2)").hide();
          $(".rtrs-tab-nav.rt-back li:nth-child(4)").hide();
        }

        var rt_support_schema = $("#rtrs-support-schema").is(':checked');

        if (rt_support_schema || rt_support_review_schema) {
          $(".rtrs-tab-nav.rt-back li:nth-child(3)").show();
        } else {
          $(".rtrs-tab-nav.rt-back li:nth-child(3)").hide();
        } // review custom page


        var custom_page = $("#rtrs-post-type").val();

        if (custom_page == 'page') {
          $('#rtrs_page_id_holder').show(); //schema tab

          $('#page_schema_holder').show();
          $('#rich_snippet_holder').hide();
          $('#rich_snippet_cat_holder').hide();
        } else {
          $('#rtrs_page_id_holder').hide(); //schema tab

          $('#page_schema_holder').hide();
          $('#rich_snippet_holder').show();
          $('#rich_snippet_cat_holder').show();
        }
        /* Schema Tab */


        var auto_snippet = $("#rtrs-auto_rich_snippet").is(':checked');

        if (auto_snippet && custom_page != 'page') {
          $("#rich_snippet_cat_holder").show();
        } else {
          $("#rich_snippet_cat_holder").hide();
        } // schema custom page


        var schema_custom_page = $("#rtrs-schema-post-type").val();

        if (schema_custom_page == 'page') {
          $('#rtrs_schma_page_id_holder').show();
        } else {
          $('#rtrs_schma_page_id_holder').hide();
        } // schema field group


        var rich_snippet_cat = $("#rtrs-rich_snippet_cat").val();
        var rich_snippet_cat_text = $("#rtrs-rich_snippet_cat option:selected").text();

        if (rich_snippet_cat_text.indexOf("[Pro]") >= 0) {
          if (!rtrs.pro) {
            $('.rtrs-pro-alert').show();
          }

          $('.rtrs-schema-field').hide();
        } else {
          $('.rtrs-schema-field').hide();
          $('#rtrs_' + rich_snippet_cat + '_schema_holder').show();
        }

        $('.rtrs-schema-field').hide();

        if (rich_snippet_cat) {
          //auto open first snippet
          if (rich_snippet_cat.length == 1) {
            $('#rtrs_' + rich_snippet_cat[0] + '_schema_holder').find(".rtrs-accordion-body:first").show();
            $('#rtrs_' + rich_snippet_cat[0] + '_schema_holder').find(".rtrs-accordion-label:first .rtrs-accordion-arrow i").removeClass("dashicons-arrow-down-alt2").addClass("dashicons-arrow-up-alt2");
            $('#rtrs_' + rich_snippet_cat[0] + '_schema_holder').find(".rtrs-accordion-label:first").addClass("active");
          }

          for (var i = 0; i < rich_snippet_cat.length; i++) {
            $('#rtrs_' + rich_snippet_cat[i] + '_schema_holder').show();
          }
        }
        /* Review Tab */

        /* Affiliate Shortcode */
        //affiliate tab
        //criteria


        var criteria = $("#rtrs-criteria-multi").is(':checked');

        if (criteria) {
          $("#multi_criteria_holder").show();
        } else {
          $("#multi_criteria_holder").hide();
        } //pros


        var pros = $("#enable_pros").is(':checked');

        if (pros) {
          $("#pros_holder").show();
        } else {
          $("#pros_holder").hide();
        } //cons


        var cons = $("#enable_cons").is(':checked');

        if (cons) {
          $("#cons_holder").show();
        } else {
          $("#cons_holder").hide();
        } //schema tab


        var affiliate_custom_snippet = $("#rtrs-snippet-custom").val();

        if (affiliate_custom_snippet == 'custom') {
          $('.rtrs-snippet-custom').show();
        } else {
          $('.rtrs-snippet-custom').hide();
        }
        /* Setting Tab */
        //pros_cons


        var pros_cons = $("#rtrs-pros_cons").is(':checked');

        if (pros_cons) {
          $("#pros_cons_limit_holder").show();
        } else {
          $("#pros_cons_limit_holder").hide();
        } //email


        var anonymous_review = $("#rtrs-anonymous_review").is(':checked');

        if (anonymous_review) {
          $("#email_holder").show();
          $("#author_holder").show();
        } else {
          $("#rtrs-author").prop('checked', false);
          $("#rtrs-email").prop('checked', false);
          $("#email_holder").hide();
          $("#author_holder").hide();
        } //filter


        var filter = $("#rtrs-filter").is(':checked');

        if (filter) {
          $("#filter_option_holder").show();
        } else {
          $("#filter_option_holder").hide();
        }
        /* Single: Review */

        /* Single: Affiliate */
        //pros single


        var single_pros = $("#rtrs-enable_pros").is(':checked');

        if (single_pros) {
          $("#rtrs_pros_holder").show();
        } else {
          $("#rtrs_pros_holder").hide();
        } //cons single


        var single_cons = $("#rtrs-enable_cons").is(':checked');

        if (single_cons) {
          $("#rtrs_cons_holder").show();
        } else {
          $("#rtrs_cons_holder").hide();
        }
        /* Settins Schema Tab */


        var schema_cat = $("#rtrs_schema_settings-category").val();

        if (schema_cat == 'Restaurant') {
          $('.rtrs_schema_settings-servesCuisine, .rtrs_schema_settings-menu, .rtrs_schema_settings-acceptsReservations').show();
        } else {
          $('.rtrs_schema_settings-servesCuisine, .rtrs_schema_settings-menu, .rtrs_schema_settings-acceptsReservations').hide();
        }

        var schema_archive = $("#rtrs_schema_archive_settings-archive").is(':checked');

        if (schema_archive) {
          $(".rtrs_schema_archive_settings-schema_type ").show();
        } else {
          $(".rtrs_schema_archive_settings-schema_type ").hide();
        }
      }
    },

    /**
     * Ajax functions
     * @since 1.0.0
     * @return {mixed} 
     */
    ajaxEvent: function ajaxEvent() {
      // check already defined post type
      $('#rtrs-post-type, #rtrs-schema-post-type').on('select2:select', function (e) {
        var post_type = e.params.data.id;
        if (post_type == 'page') return;
        var post_id = $("#post_ID").val();
        $.ajax({
          url: rtrs.ajaxurl,
          data: {
            action: 'rtrs_check_post_type',
            post_id: post_id,
            post_type: post_type
          },
          type: 'POST',
          dataType: "json",
          success: function success(resp) {
            if (!resp.success) {
              $('#rtrs-post-type').val("0").select2();
              $('.rtrs-post-type').show();
            }
          }
        });
      }); // import others plugin schema data

      $('.rtrs-import-schema').on('click', function (e) {
        var $this = $(this);
        var data_id = $(this).data('id');
        $.ajax({
          url: rtrs.ajaxurl,
          data: {
            action: 'rtrs_data_import',
            data_id: data_id
          },
          type: 'POST',
          dataType: "json",
          beforeSend: function beforeSend() {
            $this.next().css("visibility", "inherit");
          },
          success: function success(resp) {
            $this.next().css("visibility", "hidden");

            if (resp.success) {
              console.log(resp);
              $this.next().next().addClass('success').html('Data has been imported successfully');
            } else {
              $this.next().next().addClass('failed').html('Plugin data is not available or it is not activated');
            }
          }
        });
      });
    },

    /**
     * Meta Field: Repeater
     * @since 1.0.0
     * @return {mixed} 
     */
    metaRepeater: function metaRepeater() {
      function metaRepeater(selector, selector_slug) {
        // repeater shortable
        var repeater_field_id = "#rtrs-" + selector;

        if ($(repeater_field_id).length) {
          $(repeater_field_id).sortable();
        } // add new repeater


        $(repeater_field_id + " + a").on('click', function (e) {
          //check pro
          var pro_limit = false;
          var pro_msg = null;
          var field_length = $(repeater_field_id + " label").length;

          if (!rtrs.pro) {
            switch (selector) {
              case 'multi-criteria':
                if (field_length >= 3) {
                  pro_limit = true;
                  pro_msg = rtrs.criteria_alt_txt;
                }

                break;

              case 'pros':
                if (field_length >= 3) {
                  pro_limit = true;
                  pro_msg = rtrs.pros_alt_txt;
                }

                break;

              case 'cons':
                if (field_length >= 3) {
                  pro_limit = true;
                  pro_msg = rtrs.cons_alt_txt;
                }

                break;
            }

            if (pro_limit) {
              $('.rtrs-pro-alert').show();

              if (pro_msg) {
                $('.rtrs-pro-alert p span').html(pro_msg + ' ');
              }

              return false;
            }
          }

          e.preventDefault(); //show pro text by conditon                    

          if (!rtrs.pro) {
            var field_length_new = $(repeater_field_id + " label").length;
            field_length_new++;

            if (field_length_new >= 3) {
              $(repeater_field_id + " + a .rtrs-pro").removeClass('rtrs-hidden');
            }
          }

          var field_prefix = '';
          var editReview = $(this).data('type');

          if (editReview == 'edit') {
            field_prefix = 'rt_';
          }

          if ($(this).data('single') && (selector_slug == 'pros' || selector_slug == 'cons')) {
            field_prefix = 'rtrs_'; //prefix for affilite single meta
          }

          var theId = selector_slug + '-' + ($(repeater_field_id + " label").length + 1);
          var new_field = "<label for='" + theId + "'><input type='text' id='" + theId + "' name='" + field_prefix + selector_slug + "[]' value=''><i class='dashicons dashicons-move'></i> <i class='remove dashicons dashicons-dismiss'></i></label>";
          $(repeater_field_id).append(new_field);
        }); // remove repeater 

        $(document).on('click', repeater_field_id + " .remove", function (e) {
          e.preventDefault();
          $(this).parent().remove(); //show hide pro field

          if (!rtrs.pro) {
            var field_length_new = $(repeater_field_id + " label").length;
            field_length_new++;

            if (field_length_new >= 3) {
              $(repeater_field_id + " + a .rtrs-pro").addClass('rtrs-hidden');
            }
          }
        });
      }

      metaRepeater('multi-criteria', 'multi_criteria');
      metaRepeater('pros', 'pros');
      metaRepeater('cons', 'cons');
    },

    /**
     * Meta Field: Image
     * @since 1.0.0
     * @return {mixed} 
     */
    metaImage: function metaImage() {
      //gallery meta field
      $(document).on('click', '.rtrs-upload-box', function (e) {
        e.preventDefault();
        var name = $(this).attr('data-name');
        var field_type = $(this).attr('data-field');
        var self = $(this),
            file_frame,
            json; // If an instance of file_frame already exists, then we can open it rather than creating a new instance

        if (undefined !== file_frame) {
          file_frame.open();
          return;
        } // Here, use the wp.media library to define the settings of the media uploader


        file_frame = wp.media.frames.file_frame = wp.media({
          frame: 'post',
          state: 'insert',
          multiple: field_type == 'image' ? false : true
        }); // Setup an event handler for what to do when an image has been selected

        file_frame.on('insert', function () {
          // Read the JSON data returned from the media uploader
          json = file_frame.state().get('selection').first().toJSON(); // First, make sure that we have the URL of an image to display

          if (0 > $.trim(json.url.length)) {
            return;
          }

          var images = file_frame.state().get('selection').toJSON();
          var img_data = "";
          var multiple = field_type == 'image' ? '' : '[]';
          images.forEach(function (element) {
            img_data += "<div class='rtrs-preview-img'><img src='" + element.url + "' /><input type='hidden' name='" + name + multiple + "' value='" + element.id + "'><button class='rtrs-file-remove' data-id='" + element.id + "'>x</button></div>";
          });

          if (field_type == 'image') {
            self.prev().html(img_data);
          } else {
            self.prev().html(img_data);
          }
        }); // Now display the actual file_frame

        file_frame.open();
      }); //delete image  

      $(document).on('click', '.rtrs-file-remove', function (e) {
        e.preventDefault();

        if (confirm(rtrs.sure_txt)) {
          if ($(this).parent().parent().children('.rtrs-preview-img').length <= 1) {
            $(this).parent().children('img').remove();
            $(this).parent().children('input').val(0);
            $(this).remove();
          } else {
            $(this).parent().remove();
          }
        }
      });
    },

    /**
     * Meta Field: Group
     * @since 1.0.0
     * @return {mixed} 
     */
    metaGroup: function metaGroup() {
      function accordionToggle() {
        $(".rtrs-accordion-label").off().on('click', function (e) {
          e.preventDefault();
          $(this).toggleClass('active');

          if ($(this).hasClass("active")) {
            $(this).find(".rtrs-accordion-arrow i").removeClass("dashicons-arrow-down-alt2").addClass("dashicons-arrow-up-alt2");
          } else {
            $(this).find(".rtrs-accordion-arrow i").removeClass("dashicons-arrow-up-alt2").addClass("dashicons-arrow-down-alt2");
          }

          var panel = $(this).next();

          if (panel.css("display") == "block") {
            panel.slideUp();
          } else {
            panel.slideDown();
          }
        });
      }

      accordionToggle();
      $(document).on("click", ".rtrs-group-wrap + a", function (e) {
        //check pro
        var pro_limit = false;
        var pro_msg = null;
        var data_pro = $(this).data('pro');
        var id = $(this).data('id');
        var name = $(this).data('name');

        if (!rtrs.pro && data_pro == 'yes') {
          if (id == 'rating_criteria') {
            if ($(this).prev().children().length >= 2) {
              $(this).find('.rtrs-pro').removeClass('rtrs-hidden');
            }

            if ($(this).prev().children().length > 2) {
              $('.rtrs-pro-alert').show();

              if (rtrs.criteria_rating) {
                $('.rtrs-pro-alert p span').html(rtrs.criteria_rating + ' ');
              }

              return false;
            }
          } else {
            pro_limit = true;
            pro_msg = rtrs.multiple_txt;

            if (pro_limit) {
              $('.rtrs-pro-alert').show();

              if (pro_msg) {
                $('.rtrs-pro-alert p span').html(pro_msg + ' ');
              }

              return false;
            }
          }
        }

        e.preventDefault(); //fill up input live value to input other wise last fill up not cloing

        $("#" + id + ' input').each(function () {
          $(this).attr('value', $(this).val());
        });
        $("#" + id + ' textarea').each(function () {
          $(this).html($(this).val());
        }); //clone last field

        var clone_copy = $("#" + id + ' > .rtrs-accordion-wrap:last-child').clone();
        $("#" + id).append(clone_copy);
        $("#" + id + ' > .rtrs-accordion-wrap').each(function (key) {
          var this_string = $(this).html(); //increment name attribute

          var regName = name;
          regName = regName.replaceAll("[", "\\[");
          regName = regName.replaceAll("]", "\\]");
          var myRegexp = new RegExp("".concat(regName, "\\[([0-9]+)"), "g");
          var match = myRegexp.exec(this_string);
          var value;

          if (match != null && match.length > 1) {
            value = this_string.replaceAll(name + '[' + match[1], name + '[' + key);
          } //increment id attribute


          var myRegexpId = new RegExp("".concat(id, "\\_([0-9]+)"), "g");
          var matchID = myRegexpId.exec(value);

          if (matchID != null && matchID.length > 1) {
            value = value.replaceAll(id + '_' + matchID[1], id + '_' + key);
            $(this).html(value);
          } //accordion number  


          $(this).find("> .rtrs-accordion-label span.rtrs-accordion-counter").html(key + 1);
        });
        accordionToggle();
      }); //remove  

      $(document).on("click", ".rtrs-accordion-remove", function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        if ($("#" + id).find(".rtrs-accordion-wrap").length < 2) {
          alert(rtrs.at_least_txt);
          return;
        }

        if (confirm(rtrs.sure_txt)) {
          if (!rtrs.pro) {
            if ($(this).closest('.rtrs-group-wrap').attr('id') == 'rating_criteria') {
              if ($("#" + id).find(".rtrs-accordion-wrap").length <= 3) {
                $("#" + id + " + a .rtrs-pro").addClass('rtrs-hidden');
              }
            }
          }

          $(this).closest('.rtrs-accordion-wrap').remove();
          $("#" + id).find(".rtrs-accordion-wrap").each(function (key) {
            //accordion number 
            $(this).find(".rtrs-accordion-label span.rtrs-accordion-counter").html(key + 1);
          });
        }
      });
    },

    /**
     * Setting Field: Group
     * @since 1.0.0
     * @return {mixed} 
     */
    settingGroup: function settingGroup() {
      $(document).on("click", ".schema-pro-field", function (e) {
        //check pro  
        $('.rtrs-pro-alert').show();
        return false;
      });
      $(document).on("change", ".rtrs-auto-schema select", function (e) {
        var selected_schema = $(this).find("option:selected").text();

        if (selected_schema.indexOf("[Pro]") >= 0) {
          if (!rtrs.pro) {
            $('.rtrs-pro-alert').show();
            $(this).find('option:selected').remove();
          }
        }

        return false;
      });
      $(document).on("click", ".rtrs-group-add a", function (e) {
        //check pro 
        var data_pro = $(this).data('pro');
        var id = $(this).data('id');
        var name = $(this).data('name');

        if (!rtrs.pro && data_pro == 'yes') {
          $('.rtrs-pro-alert').show();
          return false;
        }

        e.preventDefault(); //fill up input live value to input other wise last fill up not cloing

        $("#" + id + ' input').each(function () {
          $(this).attr('value', $(this).val());
        });
        var clone_copy = $("#" + id + ' > .rtrs-setting-group-wrap:last-child').clone();
        $("#" + id).append(clone_copy);
        regenerate_name(id, name);
      });

      function regenerate_name(id, name) {
        $("#" + id + ' > .rtrs-setting-group-wrap').each(function (key) {
          var this_string = $(this).html(); //increment name attribute

          var regName = name;
          regName = regName.replaceAll("[", "\\[");
          regName = regName.replaceAll("]", "\\]");
          var myRegexp = new RegExp("".concat(regName, "\\[([0-9]+)"), "g");
          var match = myRegexp.exec(this_string);
          var value;

          if (match != null && match.length > 1) {
            value = this_string.replaceAll(name + '[' + match[1], name + '[' + key);
            $(this).html(value);
          }
        });
      } //remove  


      $(document).on("click", ".rtrs-group-remove", function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $("#" + id + ' input').each(function () {
          $(this).attr('value', $(this).val());
        });

        if (confirm(rtrs.sure_txt)) {
          $(this).parent().remove();
          regenerate_name(id, name);
        }
      });
    },

    /**
     * Setting Field: Image
     * @since 1.0.0
     * @return {mixed} 
     */
    settingImage: function settingImage() {
      $('.rtrs-setting-image-wrap').on('click', '.rtrs-add-image', function (e) {
        e.preventDefault();
        var self = $(this),
            target = self.parents('.rtrs-setting-image-wrap'),
            file_frame,
            image_data,
            json; // If an instance of file_frame already exists, then we can open it rather than creating a new instance

        if (undefined !== file_frame) {
          file_frame.open();
          return;
        } // Here, use the wp.media library to define the settings of the media uploader          


        file_frame = wp.media.frames.file_frame = wp.media({
          frame: 'post',
          state: 'insert',
          multiple: false
        }); // Setup an event handler for what to do when an image has been selected

        file_frame.on('insert', function () {
          // Read the JSON data returned from the media uploader
          json = file_frame.state().get('selection').first().toJSON(); // First, make sure that we have the URL of an image to display

          if (0 > $.trim(json.url.length)) {
            return;
          }

          var imgUrl = typeof json.sizes.medium === "undefined" ? json.url : json.sizes.medium.url;
          target.find('.rtrs-setting-image-id').val(json.id);
          target.find('.image-preview-wrapper').html('<img src="' + imgUrl + '" alt="' + json.title + '" />');

          if (!self.next().hasClass('rtrs-remove-image')) {
            $('<input type="button" class="button button-secondary rtrs-remove-image" value="' + rtrs.remove_img + '">').insertAfter(self);
          }
        }); // Now display the actual file_frame

        file_frame.open();
      }); // Delete the image when "Remove Image" button clicked

      $('.rtrs-setting-image-wrap').on('click', '.rtrs-remove-image', function (e) {
        e.preventDefault();
        var self = $(this),
            target = self.parents('.rtrs-setting-image-wrap');

        if (confirm(rtrs.sure_txt)) {
          target.find('.rtrs-setting-image-id').val('');
          target.find('.image-preview-wrapper img').attr('src', target.find('.image-preview-wrapper').data('placeholder'));
          self.remove();
        }
      });
    },

    /**
     * Licensing
     * @since 1.0.0
     * @return {mixed} 
     */
    Licensing: function Licensing() {
      $(".rtrs-settings .rtrs-license-wrapper").on('click', '.rtrs-licensing-btn', function (e) {
        e.preventDefault();
        console.log('clicked');
        var self = $(this),
            type = self.hasClass('license_activate') ? 'license_activate' : 'license_deactivate';
        $.ajax({
          type: "POST",
          url: rtrs.ajaxurl,
          data: {
            action: 'rtrs_manage_licensing',
            type: type
          },
          beforeSend: function beforeSend() {
            self.addClass('loading');
            self.parents('.description').find(".rt-licence-msg").remove();
            $('<span class="rt-icon-spinner animate-spin"></span>').insertAfter(self);
          },
          success: function success(response) {
            self.next('.rt-icon-spinner').remove();
            self.removeClass('loading');

            if (!response.error) {
              self.text(response.value);
              self.removeClass(type);
              self.addClass(response.type);

              if (response.type == 'license_deactivate') {
                self.removeClass('button-primary');
                self.addClass('danger');
              } else if (response.type == 'license_activate') {
                self.removeClass('danger');
                self.addClass('button-primary');
              }
            }

            if (response.msg) {
              $("<span class='rt-licence-msg'>" + response.msg + "</span>").insertAfter(self);
            }

            self.blur();
          },
          error: function error(jqXHR, exception) {
            self.removeClass('loading');
            self.next('.rt-icon-spinner').remove();
          }
        });
      });
      $('#rtrs_tools_settings-license_key').on('keydown', function (e) {
        $(this).next().remove();
      });
    },

    /* ---------------------------------------------
     function initialize
     --------------------------------------------- */
    initialize: function initialize() {
      rtApp.metaTab();
      rtApp.alertBox();
      rtApp.libSupport();
      rtApp.conditionalField();
      rtApp.autoFillSchema();
      rtApp.ajaxEvent();
      rtApp.metaRepeater();
      rtApp.metaImage();
      rtApp.metaGroup();
      rtApp.settingGroup();
      rtApp.settingImage();
      rtApp.Licensing();
    }
  };
  /* ---------------------------------------------
   Document ready function
   --------------------------------------------- */

  $(function () {
    rtApp.initialize();
  });
  $(window).on('load', function () {});
})(jQuery);

/***/ }),

/***/ "./src/sass/app.scss":
/*!***************************!*\
  !*** ./src/sass/app.scss ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/sass/admin.scss":
/*!*****************************!*\
  !*** ./src/sass/admin.scss ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/assets/js/admin": 0,
/******/ 			"assets/css/app": 0,
/******/ 			"assets/css/admin": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkwp_plugin_rtrs"] = self["webpackChunkwp_plugin_rtrs"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["assets/css/app","assets/css/admin"], () => (__webpack_require__("./src/js/admin.js")))
/******/ 	__webpack_require__.O(undefined, ["assets/css/app","assets/css/admin"], () => (__webpack_require__("./src/sass/app.scss")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["assets/css/app","assets/css/admin"], () => (__webpack_require__("./src/sass/admin.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;