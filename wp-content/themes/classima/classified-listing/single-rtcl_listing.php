<?php
/**
 *
 * @author        RadiusTheme
 * @package    classified-listing/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'RtclPro' ) ) {
	return;
}

use Rtcl\Models\Listing;
use Rtcl\Helpers\Functions;
use radiustheme\Classima\RDTheme;
use radiustheme\Classima\Helper;
use Rtcl\Controllers\BusinessHoursController;

$listing = new Listing( $post->ID );
?>
<?php get_header(); ?>
    <div id="primary" class="content-area classima-listing-single rtcl">
        <div class="container">
			<?php do_action( 'classima_header_top' ); ?>
            <div class="row">
				<?php
				if ( RDTheme::$layout == 'left-sidebar' ) {
					Helper::get_custom_listing_template( 'sidebar-single' );
				}
				?>
                <div class="col-xl-9 col-lg-8 col-sm-12 col-12">
					<?php
					if ( RDTheme::$options['single_listing_style'] == '2' ) {
						Helper::get_custom_listing_template( 'content-single-2' );
					} else if ( RDTheme::$options['single_listing_style'] == '3' ) {
						Helper::get_custom_listing_template( 'content-single-3' );
					} else if ( RDTheme::$options['single_listing_style'] == '4' ) {
						Helper::get_custom_listing_template( 'content-single-4' );
					} else {
						Helper::get_custom_listing_template( 'content-single' );
					}

					$mobileSidebarClass = ( RDTheme::$options['single_listing_style'] == '4' ) ? 'rtin-details4-sidebar' : '';

					?>

                    <div class="classima-listing-single-mob classima-listing-single-sidebar sidebar-widget-area <?php echo esc_attr($mobileSidebarClass); ?>">
                        <div class="content-block-gap"></div>
						<?php Helper::get_custom_listing_template( 'seller-info' ); ?>
                    </div>

                    <!-- Business Hours  -->
					<?php if ( Functions::is_enable_business_hours() && ! empty( BusinessHoursController::get_business_hours( $listing->get_id() ) ) ): ?>
                        <div class="content-block-gap"></div>
                        <div class="site-content-block classima-single-business-hour">
                            <div class="main-title-block"><h3
                                        class="main-title"><?php esc_html_e( 'Business Hours', 'classima' ); ?></h3>
                            </div>
							
                            <div class="main-content">
								
								<?php do_action( 'rtcl_single_listing_business_hours' ); ?>
                            </div>
                        </div>
					<?php endif; ?>

					<?php do_action( 'classima_single_listing_after_product' ); ?>
                    <!-- MAP  -->
					<?php
					    if ( '4' !== RDTheme::$options['single_listing_style'] ) {
	                        do_action( 'rtcl_single_listing_content_end', $listing );
	                        do_action( 'classima_single_listing_after_location' );
                        }
                    ?>

                    <!-- Social Profile  -->
					<?php do_action( 'rtcl_single_listing_social_profiles' ); ?>

					<?php
					if ( RDTheme::$options['listing_related'] ) {
						$listing->the_related_listings();
					}
					?>

					<?php do_action( 'classima_single_listing_after_related' ); ?>
					<?php
					while ( have_posts() ) : the_post();
						if ( comments_open() || get_comments_number() ) {
							comments_template();
						}
					endwhile;
					?>
                </div>
				<?php
				if ( RDTheme::$layout != 'left-sidebar' ) {
					Helper::get_custom_listing_template( 'sidebar-single' );
				}
				?>
            </div>
        </div>
    </div>
<?php get_footer(); ?>