<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.7
 */

namespace radiustheme\Classima;

use RtclPro\Controllers\Hooks\TemplateHooks;
use Rtcl\Models\Listing;
use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

$id = get_the_id();
$listing = new Listing( $id );

$hidden_fields = Functions::get_option_item( 'rtcl_moderation_settings', 'hide_form_fields', array() );
if ( ! in_array( 'features', $hidden_fields ) ) {
	$spec_info    = get_post_meta( $id, "classima_spec_info", true );
	$spec_items   = isset( $spec_info['specs'] ) ? $spec_info['specs'] : '';
	if ( $spec_items ) {
		$spec_items = explode( PHP_EOL, $spec_items );
	}	
}
else {
	$spec_items = false;
}

$slider_class = Functions::get_listing_images( $id ) ? '' : ' no-gallery-image';
$slider_class .= method_exists('RtclPro\Helpers\Fns', 'is_mark_as_sold') && Fns::is_mark_as_sold($listing->get_id()) ? ' is-sold' : '';

$time_format = apply_filters( 'classima_single_listing_time_format', 'F j, Y g:i a' );
$date        = date_i18n( $time_format,  get_the_time( 'U' ) );
$has_meta    = $listing->can_show_date() || $listing->can_show_views() || ( $listing->has_location() && $listing->can_show_location() ) ? true : false;
?>
<div class="site-content-block classima-single-details classima-single-details-3">
	<div class="main-content">
		<?php do_action( 'classima_single_listing_before_contents' );?>

        <h2 class="entry-title"><?php Helper::the_title();?></h2>

        <div class="single-listing-meta-wrap">
            <?php if ( $has_meta ): ?>
                <ul class="single-listing-meta">
                    <?php if ( $listing->can_show_date() ): ?>
                        <li><i class="far fa-clock" aria-hidden="true"></i><?php echo esc_html( $date );?></li>
                    <?php endif; ?>

                    <?php if ( $listing->has_location() && $listing->can_show_location() ): ?>
                        <li><i class="fa fa-map-marker" aria-hidden="true"></i><?php $listing->the_locations( true, false, false );?></li>
                    <?php endif; ?>

                    <?php if ( $listing->can_show_views() ): ?>
                        <li><i class="fa fa-eye" aria-hidden="true"></i><?php echo sprintf( esc_html__( '%s views', 'classima' ), number_format_i18n( $listing->get_view_counts() ) );?></li>
                    <?php endif; ?>

	                <?php if ( Listing_Functions::can_show_ad_type() ): ?>
		                <?php
		                $type = Listing_Functions::get_listing_type( $listing );
		                if (isset($type['label'])) {
			                ?>
                            <li class="rtin-type"><i class="fa fa-tags" aria-hidden="true"></i><?php echo esc_html($type['label']); ?></li>
		                <?php } ?>
	                <?php endif; ?>

                </ul>
            <?php endif; ?>
            <?php $listing->the_badges(); ?>
        </div>

		<div class="rtin-slider-box<?php echo esc_attr( $slider_class );?>">
			<?php $listing->the_gallery(); ?>
            <?php TemplateHooks::sold_out_banner(); ?>
		</div>

		<?php if ( $listing->can_show_price() ): ?>
			<div class="rtin-price">
                <?php
                if (method_exists( $listing, 'get_price_html')) {
                    Functions::print_html($listing->get_price_html());
                }
                ?>
            </div>
		<?php endif; ?>

        <div class="classima-custom-fields-wrap">
            <?php $listing->the_custom_fields(); ?>
        </div>

		<div class="rtin-content-area">
			<div class="row">
				<div class="col-12 col-md-12">
                    <h3 class="rtin-specs-title"><?php esc_html_e('Description', 'classima' ); ?></h3>
					<div class="rtin-content"><?php $listing->the_content(); ?></div>
				</div>
			</div>			
		</div>

		<?php if ( $spec_items ): ?>
			<div class="rtin-specs">
				<h3 class="rtin-specs-title"><?php esc_html_e( 'Features:', 'classima' );?></h3>
				<ul class="rtin-spec-items clearfix rtin-list-col-2">
					<?php foreach ( $spec_items as $spec_item ): ?>
						<li><?php echo wp_kses_post( $spec_item )?></li>
					<?php endforeach ?>
				</ul>
			</div>
		<?php endif; ?>

        <?php $listing->the_actions(); ?>

		<?php do_action( 'classima_single_listing_after_contents' );?>
	</div>
</div>