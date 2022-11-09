<?php

namespace Rtrs\Controllers\Marketing;

class Offer {
	public function __construct() {
		add_action(
			'admin_init',
			function () {
				$current = time();
				$start   = strtotime( '06 July 2022' );
				$end     = strtotime( '30 August 2022' );
				if ( $start <= $current && $current <= $end ) {
					if ( get_option( 'rtrs_offet_july_2022' ) != '1' ) {
						if ( ! isset( $GLOBALS['rtrs_offet_july_2022_notice'] ) ) {
							$GLOBALS['rtrs_offet_july_2022_notice'] = 'rtrs_offet_july_2022';
							self::notice();
						}
					}
				}
			}
		, 1 );
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public static function notice() {
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_script( 'jquery' );
			}
		);

		add_action(
			'admin_notices',
			function () {
				$plugin_name   = 'Review Schema - WordPress Review & Structure Data Schema Plugin Pro';
				$download_link = 'https://www.radiustheme.com/downloads/wordpress-review-structure-data-schema-plugin/?utm_source=WordPress&utm_medium=reviewschema&utm_campaign=pro_click'; ?>
				<div class="notice notice-info is-dismissible" data-rtrsdismissable="rtrs_offet_july_2022"
					style="display:grid;grid-template-columns: 100px auto;padding-top: 25px; padding-bottom: 22px;">
					<img alt="<?php echo esc_attr( $plugin_name ); ?>"
						src="<?php echo rtrs()->get_assets_uri( 'imgs/icon-128x128.gif' ); ?>" width="74px"
						height="74px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
					<h3 style="margin:0;"><?php echo sprintf( '%s Lifetime Deal!!', $plugin_name ); ?></h3>

					<p style="margin:0 0 2px;">
						<?php echo esc_html__( "Don't miss out on our biggest sale of the year! Get your.", 'review-schema' ); ?>
						<b>Review Schema Pro plan</b> with <b>UP TO 80% OFF</b>
					</p>

					<p style="margin:0;">
						<a class="button button-primary" href="<?php echo esc_url( $download_link ); ?>" target="_blank">Buy Now</a>
						<a class="button button-dismiss" href="#">Dismiss</a>
					</p>
				</div>
					<?php
			}
		);

		add_action(
			'admin_footer',
			function () {
				?>
				<script type="text/javascript">
					(function ($) {
						$(function () {
							setTimeout(function () {
								$('div[data-rtrsdismissable] .notice-dismiss, div[data-rtrsdismissable] .button-dismiss')
									.on('click', function (e) {
										e.preventDefault();
										$.post(ajaxurl, {
											'action': 'rtrs_dismiss_admin_offer_notice',
											'nonce': <?php echo json_encode( wp_create_nonce( 'rtrs-dismissible-offer-notice' ) ); ?>
										});
										$(e.target).closest('.is-dismissible').remove();
									});
							}, 1000);
						});
					})(jQuery);
				</script>
					<?php
			}
		);

		add_action(
			'wp_ajax_rtrs_dismiss_admin_offer_notice',
			function () {
				check_ajax_referer( 'rtrs-dismissible-offer-notice', 'nonce' );

				update_option( 'rtrs_offet_july_2022', '1' );
				wp_die();
			}
		);
	}
}
