<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\Classima;

use Rtcl\Models\Listing;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;
use RtclStore\Models\Store;
use RtclStore\Helpers\Functions as StoreFunctions;

$id           = get_the_id();
$listing      = new Listing( $id );
$alternate_contact_form = Functions::get_option_item( 'rtcl_moderation_settings', 'alternate_contact_form_shortcode');
$class = '';
if (RDTheme::$options['single_listing_style'] == '4') {
    $class = 'rtin-details4-sidebar';
}
?>
<div class="col-xl-3 col-lg-4 col-sm-12 col-12">
	<aside class="sidebar-widget-area">
		<div class="classima-listing-single-sidebar <?php echo esc_attr($class); ?>">
			<?php do_action( 'classima_before_sidebar' ); ?>

			<?php if ( RDTheme::$options['single_listing_style'] == '1' || RDTheme::$options['single_listing_style'] == '3' ): ?>
                <?php if ( $listing->can_show_price() ): ?>
					<div class="rtin-price">
                        <?php
                        if (method_exists( $listing, 'get_price_html')) {
                            Functions::print_html($listing->get_price_html());
                        }
                        ?>
                    </div>
				<?php endif; ?>
			<?php endif; ?>
			
			<?php
			Helper::get_custom_listing_template( 'seller-info' );

			if ( is_active_sidebar( 'sidebar-single-listing' ) ) {
				dynamic_sidebar( 'sidebar-single-listing' );
			}

			// MAP
			if ( '4' == RDTheme::$options['single_listing_style'] ) {
				do_action( 'rtcl_single_listing_content_end', $listing );
				do_action( 'classima_single_listing_after_location' );
            }
			
			do_action( 'classima_after_sidebar' );
			?>

			<div class="modal fade" id="classima-mail-to-seller" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body" data-hide="0">
							<?php
							if ( $alternate_contact_form ) {
								echo sprintf('<div id="rtcl-contact-form">%s</div>', do_shortcode( $alternate_contact_form ) );
							}
							else {
								$listing->email_to_seller_form();
							}
							?>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</aside>
</div>