(function($){
	"use strict";

	// Custom functions
	function add_close_btn(){
		$('#widgets-right .sidebar-rdtheme-custom .sidebar-name h2').children('.spinner').each(function() {
			if ( ! $(this).prev('.sidebar-rdtheme-custom-closebtn').length) {
				$(this).before('<a class="sidebar-rdtheme-custom-closebtn" style="vertical-align: text-top;" href="#">x</a>');
			}
		});	
	}

	// Initialize
	$(document).ready(function() {
		$('#rdtheme-new-sidebar').parent().prependTo($('#widgets-right .sidebars-column-1'));
		add_close_btn();
	});

	// Add Form submission
	$(document).on('submit','#rdtheme-new-sidebar form',function(event){
		event.preventDefault();

		$(this).find('input[type="submit"]').attr('disabled', 'disabled');
		$(this).closest('#rdtheme-new-sidebar').find('.spinner').addClass('is-active');

		$.ajax({
			context: this,
			url: $(this).attr('action'),
			type: $(this).attr('method'),
			dataType: 'json',
			data: $(this).serializeArray(),
			complete: function(response) {
				$(this).closest('#rdtheme-new-sidebar').find('.spinner').removeClass('is-active');
				$(this).find('input[type="submit"]').removeAttr('disabled');

				if ( ! response || ! response.responseJSON || ! response.responseJSON.success) {
					if (response && response.responseJSON && response.responseJSON.data) {
						alert(response.responseJSON.data);
					}
					else {
						alert(RDThemeSidebarObj.failed);
					}
				}
				else {
					var html = $('#wpbody-content > .wrap').clone();
					html.find('#widgets-right .sidebars-column-2').append(response.responseJSON.data);
					$(document.body).unbind('click.widgets-toggle');
					$('#wpbody-content > .wrap').replaceWith(html.clone());
					setTimeout(function() {
						wpWidgets.init();
						add_close_btn();
					}, 200);
				}
			},
		});
	});

	// Remove button action
	$(document).on('click','#widgets-right .sidebar-rdtheme-custom .sidebar-name h2 .sidebar-rdtheme-custom-closebtn',function(event){
		event.preventDefault();
		event.stopPropagation();

		if (confirm(RDThemeSidebarObj.confirm)) {
			$(this).addClass('hidden').next('.spinner').addClass('is-active');

			$.ajax({
				context: this,
				url: RDThemeSidebarObj.ajaxurl,
				dataType: 'json',
				data: {
					id: $(this).closest('.widgets-sortables').attr('id'),
					_wpnonce: RDThemeSidebarObj.nonce,
				},
				complete: function(response) {
					if ( ! response || ! response.responseJSON || ! response.responseJSON.success) {
						if (response && response.responseJSON && response.responseJSON.data) {
							alert(response.responseJSON.data);
						}
						else {
							alert(RDThemeSidebarObj.failed);
						}

						$(this).removeClass('hidden').next('.spinner').removeClass('is-active');
					}
					else {
						$(this).closest('.sidebar-rdtheme-custom').remove();
					}
				},
			});
		}
	});
})(jQuery);