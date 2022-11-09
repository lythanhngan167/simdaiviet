'use strict';
(function ($) {
	jQuery(document).ready(function($){

		// ColorPicker, Datepicker, Timepicker
		$(".rt-metabox-picker").each(function() {
			// Exclude first hidden repeater field
			if ( !$(this).parents('.rt-postmeta-repeater.repeater-init').length ) {
				executePickers($(this));
			}
		});

		// Multi Select
		$(".rt-multi-select").select2();
		$(document).on('widget-updated', function(event) {
			$(".rt-multi-select").select2();
		});

		// initialize conditionals
		$( ".rt-postmeta-container .rt-postmeta-dependent" ).each(function() {
			var name  = $( this ).data('required');
			var value = $( this ).data('required-value');

			var $input = $( "input[name=" + name +"]" );
			var inputType = $input.attr('type');

			var fieldValue = null;

			// radio
			if ( inputType == 'radio' ) {
				fieldValue = $( "input[name=" + name +"]:checked" ).val();
			}

			//action
			if ( value != fieldValue ) {
				$( this ).hide();
			}
		});

		// radio field onchange conditional
		$( ".rt-postmeta-container input[type=radio]" ).on('change', function() {
			var name = $( this ).attr('name');
			var value = $( this ).val();

			// hide
			$( '.rt-postmeta-container tr[data-required="'+name+'"]' )
			.filter(function () {
				return $(this).data("required-value") != value;
			}).hide();

			// show
			$( '.rt-postmeta-container tr[data-required="'+name+'"]' )
			.filter(function () {
				return $(this).data("required-value") == value;
			}).show();

		});

		/*Repeater*/

		// Generate close button
		var repeaterCloseHtml = '<a class="rt-postmeta-repeater-close"></a>'
		$(".rt-postmeta-repeater tr:last-child td").append(repeaterCloseHtml);

		// Close button action
		$(".rt-postmeta-repeater-wrap").on('click', '.rt-postmeta-repeater-close', function(event) {
			$(this).closest('.rt-postmeta-repeater').fadeOut("fast", function(){ $(this).remove(); })
		});

		// Add more button action
		$( ".rt-postmeta-container .rt-postmeta-repeater-addmore" ).on('click', 'button', function(event) {

			// Num Data
			var $wrapper = $(this).closest('.rt-postmeta-repeater-wrap');
			var oldNum = $wrapper.data('num');
			var newNum = oldNum + 1;
			$wrapper.data('num', newNum);

			// Generate contents
			var $repeaterContent = $wrapper.find(".rt-postmeta-repeater.repeater-init");

			var inputField = $wrapper.data('fieldname');;

			inputField = inputField.split('[')[0];
			var replaceString = inputField + '\\[hidden\\]';
			var replaceWith   = inputField + '[' + oldNum +']';
			var replaceString = new RegExp (replaceString , "g");

			var repeaterHtml = $repeaterContent[0].innerHTML.replace(replaceString, replaceWith);

			var newElement =  document.createElement('table');
			newElement.className = 'rt-postmeta-repeater';
			newElement.innerHTML = repeaterHtml;

			// Execute contents
			$(this).closest('.rt-postmeta-repeater-addmore').before(newElement);

			// Execute Pickers
			$(newElement).find(".rt-metabox-picker").each(function() {
				executePickers($(this));
			});

			return false;
		});

		// Enable Sortable
		$(".rt-postmeta-repeater-wrap").sortable({
			items: '.rt-postmeta-repeater',
			cursor: "move"
		});

		// Image upload field
		$("body").on('click', '.rt_upload_image', function(event) {
			var btnClicked = $(this);
			var custom_uploader = wp.media({
				multiple: false
			}).on("select", function () {
				var attachment = custom_uploader.state().get("selection").first().toJSON();
				btnClicked.closest(".rt_metabox_image").find(".custom_upload_image").val(attachment.id).trigger('change');
				btnClicked.closest(".rt_metabox_image").find(".custom_preview_image").attr("src", attachment.url).show();
				btnClicked.closest(".rt_metabox_image").find(".rt_remove_image_wrap").show();

			}).open();
		});
		$("body").on('click', '.rt_remove_image', function(event) {
			event.preventDefault();
			remove_uploaded_image($(this).closest(".rt_metabox_image"));
			return false;
		});

		// Gallery upload field
		var rtMetaGalleryFrame = wp.media({multiple: true});
		var rtMetaGalleryBtn;

		$("body").on('click', '.rt_upload_gallery', function(event) {
			rtMetaGalleryBtn = $(this);
			rtMetaGalleryFrame.open();
		});
		$("body").on('click', '.rt_remove_gallery', function(event) {
			event.preventDefault();
			$(this).closest(".rt_metabox_gallery").find(".custom_upload_image").val("");
			$(this).closest(".rt_metabox_gallery").find(".custom_preview_images").html('');
			$(this).closest(".rt_metabox_gallery").find(".rt_remove_gallery").hide();
			return false;
		});

		rtMetaGalleryFrame.on("select", function () {
			var selection  = rtMetaGalleryFrame.state().get('selection');
			var ids  = [];

			rtMetaGalleryBtn.closest(".rt_metabox_gallery").find(".custom_preview_images").html('');

			selection.map( function( attachment ) {
				attachment = attachment.toJSON();
				ids.push(attachment.id);
				rtMetaGalleryBtn.closest(".rt_metabox_gallery").find(".custom_preview_images").append("<img src=" +attachment.url+">");
			});

			rtMetaGalleryBtn.closest(".rt_metabox_gallery").find(".custom_upload_image").val(ids);
			rtMetaGalleryBtn.closest(".rt_metabox_gallery").find(".rt_remove_gallery").show();
		});

		rtMetaGalleryFrame.on('open',function(event) {
			var selection = rtMetaGalleryFrame.state().get('selection');
			var ids = rtMetaGalleryBtn.closest(".rt_metabox_gallery").find(".custom_upload_image").val().split(',');

			ids.forEach(function(id) {
				var attachment = wp.media.attachment(id);
				attachment.fetch();
				selection.add( attachment ? [ attachment ] : [] );
			});
		});

		// File upload field
		$("body").on('click', '.rt_upload_file', function(event) {
			var btnClicked = $(this);
			var custom_uploader = wp.media({
				multiple: false
			}).on("select", function () {
				var attachment = custom_uploader.state().get("selection").first().toJSON();
				console.log(attachment);
				btnClicked.closest(".rt_metabox_file").find(".custom_upload_file").val(attachment.id);
				btnClicked.closest(".rt_metabox_file").find(".custom_preview_file").attr("href", attachment.url).html(attachment.title).show();
				btnClicked.closest(".rt_metabox_file").find(".rt_remove_file_wrap").show();
			}).open();
			
		});
		$("body").on('click', '.rt_remove_file', function(event) {
			event.preventDefault();
			$(this).closest(".rt_metabox_file").find(".custom_upload_file").val("");
			$(this).closest(".rt_metabox_file").find(".custom_preview_file").attr("href", "#").html("").hide();
			$(this).closest(".rt_metabox_file").find(".rt_remove_file_wrap").hide();
			return false;
		});
	});

	function executePickers($item) {
		if ($item.hasClass('rt-metabox-colorpicker')) {
			$item.wpColorPicker();
		}
		else if ($item.hasClass('rt-metabox-datepicker')) {
			var options = $.extend(
			    {},
			    $.datepicker.regional["en-US"],
			    { dateFormat: $item.data('format')}
			);
			$item.datepicker(options);
		}
		else if ($item.hasClass('rt-metabox-timepicker')) {
			$item.timepicker();
		}
		else if ($item.hasClass('rt-metabox-timepicker-24')) {
			$item.timepicker({'timeFormat': 'H:i'});
		}
	}

	function remove_uploaded_image($item){
		$item.find(".custom_upload_image").val("").trigger('change');
		$item.find(".custom_preview_image").attr("src", "").hide();
		$item.find(".rt_remove_image_wrap").hide();
	}

	/* Taxonomy Fields adding items ajax fix */
	$( document ).ajaxComplete( function( event, request, options ) {
		if ( request && 4 === request.readyState && 200 === request.status
			&& options.data ) {

			if(typeof options.data.indexOf == 'undefined') {
				return;
			}

			if ( options.data.indexOf( 'action=add-tag' ) < 0 ) {
				return;
			}

			if(typeof wpAjax == 'undefined') {
				return;
			}

			var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
			if ( ! res || res.errors ) {
				return;
			}

			// Image upload field
			$("#addtag .rt_metabox_image").each(function() {
				remove_uploaded_image($(this));
			});

			// Number
			$('#addtag input[type="number"]').val('');

			// Select
			$('#addtag select.rtfm-meta-field').each(function() {
				var value = $(this).find('[selected="selected"]').val();
				if ( value ) {
					$(this).val(value);
				}
			});
			
			// @todo @kowsar -- need to check following fields Radio, Color Picker, Time Picker, icon_select, checkbox, multi_select, multi_checkbox, file, date_picker, time_picker_24

			return;
		}
	} );


}(jQuery));