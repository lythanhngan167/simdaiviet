<?php
/**
 * Store single content
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

if ( ! class_exists( 'RtclPro' ) ) {
	return;
}

use radiustheme\Classima\Listing_Functions;
use radiustheme\Classima\RDTheme;
use radiustheme\Classima\Helper;
use Rtcl\Helpers\Functions;
use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclPro\Controllers\Hooks\TemplateHooks as ProTemplateHooks;
use RtclPro\Helpers\Fns;
use RtclStore\Helpers\Functions as StoreFunctions;

global $store;

if ( StoreFunctions::is_store_expired() ) {
	do_action( 'rtcl_single_store_expired_content' );

	return;
}

$store_ads_query = Listing_Functions::store_query();

$banner_class = $store->get_banner_url() ? '' : ' rtin-noimage';

$member_since = esc_html__( 'Member since - ', 'classima' ) . get_the_time( get_option( 'date_format' ) );


RDTheme::$listing_max_page_num = $store_ads_query->max_num_pages;

$post_num   = Listing_Functions::listing_post_num( $store_ads_query );
$count_text = Listing_Functions::listing_count_text( $post_num );

$general_settings = Functions::get_option( 'rtcl_general_settings' );

if ( isset( $_GET['view'] ) && in_array( $_GET['view'], [ 'grid', 'list' ], true ) ) {
	$view = esc_attr( $_GET['view'] );
} else {
	$view = Functions::get_option_item( 'rtcl_general_settings', 'default_view', 'list' );
}
$list_class = ( $view == 'grid' ) ? '' : 'rtcl-list-view';
?>
<div class="rtin-banner-wrap">
    <div class="rtin-banner-img<?php echo esc_attr( $banner_class ); ?>">
		<?php if ( ! $banner_class ): ?>
			<?php $store->the_banner(); ?>
		<?php endif; ?>
    </div>
    <div class="rtin-banner-content">
		<?php if ( $store->get_logo_url() ): ?>
            <div class="rtin-logo"><?php $store->the_logo(); ?></div>
		<?php endif; ?>
        <div class="rtin-store-title-area">
            <h2 class="rtin-store-title">
                <?php
                $store->the_title();
                do_action( 'rtcl_after_store_title', $store );
                ?>
            </h2>
			<?php if ( $store->get_the_slogan() ): ?>
                <div class="rtin-store-slogan"><?php $store->the_slogan(); ?></div>
			<?php endif; ?>
            <ul class="rtin-title-meta">
				<?php if ( $store_address = $store->get_address() ): ?>
                    <li><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo esc_html( $store_address ); ?>
                    </li>
				<?php endif; ?>
                <li><i class="fa fa-user" aria-hidden="true"></i><?php echo esc_html( $member_since ); ?></li>
				<?php if ( $store->get_category() ): ?>
                    <li><i class="fa fa-tags"></i><?php Functions::print_html( $store->get_category() ); ?></li>
				<?php endif; ?>
				<?php if ( $store->is_rating_enable() ): ?>
					<?php if ( comments_open() ): ?>
						<?php if ( class_exists( 'Rtrs' ) ): ?>
                            <li class="store-rating"><i class="fa fa-trophy" aria-hidden="true"></i>
								<?php
								$avg_rating = \Rtrs\Models\Review::getAvgRatings( $store->get_id() );
								if ( $avg_rating ) {
									echo \Rtrs\Helpers\Functions::review_stars( $avg_rating );
									$total_rating = \Rtrs\Models\Review::getTotalRatings( $store->get_id() );
									?>
                                    <span class="reviews-rating-count">(<?php echo absint( $total_rating ); ?>)</span>
								<?php } else { ?>
                                    <span><?php esc_html_e( 'No Ratings', 'classima' ); ?></span>
								<?php } ?>
                            </li>
						<?php endif; ?>
					<?php else: ?>
                        <li class="store-rating"><i class="fa fa-trophy" aria-hidden="true"></i>
							<?php if ( $store->get_review_counts() ): ?>
								<?php echo Fns::get_rating_html( $store->get_average_rating(), $store->get_review_counts() ); ?>
                                <span class="reviews-rating-count">(<?php echo absint( $store->get_review_counts() ); ?>)</span>
							<?php else: ?>
                                <span><?php esc_html_e( 'No Ratings', 'classima' ); ?></span>
							<?php endif; ?>

                        </li>
					<?php endif; ?>
				<?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-9 col-lg-8 col-sm-12 col-12">

        <div class="listing-archive-top">
            <h2 class="rtin-title"><?php echo esc_html( $count_text ); ?></h2>
            <div class="listing-sorting">
				<?php TemplateHooks::catalog_ordering(); ?>
				<?php ProTemplateHooks::view_switcher(); ?>
            </div>
        </div>

        <div class="rtcl rtcl-listings store-ad-listing-wrapper2 rtcl-listings-<?php echo esc_attr( $view ); ?>">
            <div class="<?php echo esc_attr( $list_class ); ?> rtcl-listing-wrapper"
                 data-pagination='{"max_num_pages":<?php echo esc_attr( $store_ads_query->max_num_pages ) ?>, "current_page": 1, "found_posts":<?php echo esc_attr( $store_ads_query->found_posts ) ?>, "posts_per_page":<?php echo esc_attr( $store_ads_query->query_vars['posts_per_page'] ) ?>}'>
				<?php
				if ( $post_num ):
					$temp = Helper::wp_set_temp_query( $store_ads_query );
					Listing_Functions::listing_query( $view, $store_ads_query );
					Helper::get_template_part( 'template-parts/pagination' );
					Helper::wp_reset_temp_query( $temp );
				else:
					Helper::get_custom_listing_template( 'noresults' );
				endif;
				?>
            </div>
        </div>
		<?php
		if ( class_exists( 'Rtrs' ) && ( comments_open() || get_comments_number() ) ) : ?>
            <div class="listing-archive-top rtcl-store-review-heading">
                <h2 class="rtin-title"><?php esc_html_e( 'Store Review', 'classima' ); ?></h2>
            </div>
			<?php comments_template(); ?>
		<?php endif; ?>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-12 col-12">
        <aside class="sidebar-widget-area">
			<?php Helper::get_custom_store_template( 'sidebar-store', true, get_defined_vars() ); ?>
        </aside>
    </div>
</div>