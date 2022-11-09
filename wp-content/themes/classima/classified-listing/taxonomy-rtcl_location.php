<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.5.4
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Page;
use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclPro\Controllers\Hooks\TemplateHooks as ProTemplateHooks;
use radiustheme\Classima\Listing_Functions;
use radiustheme\Classima\RDTheme;
use radiustheme\Classima\Helper;
use RtclPro\Helpers\Fns;

defined('ABSPATH') || exit;

if (!class_exists( 'RtclPro' )) return;

$layout_class = 'col-xl-9 col-lg-8 col-sm-12 col-12';

$rtcl_query = rtcl()->wp_query();
$rtcl_top_query = Fns::top_listings_query();

$post_num   = Listing_Functions::listing_post_num( $rtcl_query );
$count_text = Listing_Functions::listing_count_text( $post_num );

$general_settings = Functions::get_option( 'rtcl_general_settings' );

if ( isset( $_GET['view'] ) && in_array( $_GET['view'], [ 'grid', 'list' ], true ) ) {
    $view = esc_attr( $_GET['view'] );
}
else {
    $view = Functions::get_option_item( 'rtcl_general_settings', 'default_view', 'list' );
}
$list_class = ( $view == 'grid' ) ? '' : 'rtcl-list-view';
$map = false;
?>
<?php get_header(); ?>
<div id="primary" class="content-area classima-listing-archive rtcl">
    <div class="container">
        <?php do_action( 'classima_header_top' );?>
        <div class="row">

            <?php if ( RDTheme::$layout == 'left-sidebar' ): ?>
                <div class="col-xl-3 col-lg-4 col-sm-12 col-12"><?php Helper::get_custom_listing_template( 'sidebar-archive' );?></div>
            <?php endif; ?>

            <div class="<?php echo esc_attr( $layout_class );?>">

                <?php do_action('rtcl_archive_description'); ?>

                <div class="listing-archive-top">
                    <h2 class="rtin-title"><?php echo esc_html( $count_text );?></h2>
                    <div class="listing-sorting">
                        <?php TemplateHooks::catalog_ordering(); ?>
                        <?php ProTemplateHooks::view_switcher(); ?>
                    </div>
                </div>

                <?php do_action( 'classima_listing_before_items' );?>

                <div class="rtcl rtcl-listings rtcl-listings-<?php echo esc_attr( $view ); ?>">
                    <div class="<?php echo esc_attr( $list_class ); ?>">
                        <?php if ( $post_num ): ?>
                            <?php Listing_Functions::listing_query( $view, $rtcl_query, $rtcl_top_query, $map );?>
                        <?php else: ?>
                            <?php Helper::get_custom_listing_template( 'noresults' ); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php do_action( 'classima_listing_after_items' );?>

                <?php Helper::get_template_part( 'template-parts/pagination' ); ?>

            </div>

            <?php if ( RDTheme::$layout == 'right-sidebar' ): ?>
                <div class="col-xl-3 col-lg-4 col-sm-12 col-12"><?php Helper::get_custom_listing_template( 'sidebar-archive' );?></div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php get_footer(); ?>