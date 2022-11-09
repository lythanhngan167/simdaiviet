<?php
/**
 * Single store product listing
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.2.31
 *
 * @var Store    $store
 * @var WP_Query $store_ads_query
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use RtclStore\Models\Store;

if (empty($store)) global $store;

if (empty($store)) return;

$args = array(
    'post_type'      => rtcl()->post_type,
    'post_status'    => 'publish',
    'posts_per_page' => Functions::get_option_item('rtcl_general_settings', 'listings_per_page', 20),
    'author'         => $store->owner_id(),
    'paged'          => Pagination::get_page_number(),
);
$store_ads_query = new \WP_Query(apply_filters('rtcl_store_listing_args', $args));
?>
<div class="store-listing-list store-ad-listing-wrapper">
    <h3><?php printf(esc_html__("All ads from %s", "classified-listing-store"), $store->get_the_title()) ?></h3>
    <?php
    if ($store_ads_query->have_posts()) : ?>
        <div class="rtcl-listings rtcl-list-view rtcl-listing-wrapper"
             data-pagination='{"max_num_pages":<?php echo esc_attr($store_ads_query->max_num_pages) ?>, "current_page": 1, "found_posts":<?php echo esc_attr($store_ads_query->found_posts) ?>, "posts_per_page":<?php echo esc_attr($store_ads_query->query_vars['posts_per_page']) ?>}'>
            <!-- the loop -->
            <?php
            while ($store_ads_query->have_posts()) : $store_ads_query->the_post();
                $listing = rtcl()->factory->get_listing(get_the_ID());
                Functions::get_template_part('content', 'listing');
            endwhile; ?>
            <!-- end of the loop -->

            <!-- Use reset postdata to restore original query -->
            <?php wp_reset_postdata(); ?>
        </div>
    <?php else:
        do_action('rtcl_no_listings_found');
    endif; ?>
</div>
