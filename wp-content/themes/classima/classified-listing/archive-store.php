<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.2.31
 */

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclStore\Helpers\Functions as StoreFunctions;

defined('ABSPATH') || exit;

if (!class_exists( 'RtclPro' )) return;

get_header('store');

?>
<div id="primary" class="content-area classima-store-single rtcl">
    <div class="container">
<?php

if (rtcl()->wp_query()->have_posts()) {

    StoreFunctions::store_loop_start();
    while (rtcl()->wp_query()->have_posts()) : rtcl()->wp_query()->the_post();

        /**
         * Hook: rtcl_listing_loop.
         */
        do_action('rtcl_store_loop');

        RtclFunctions::get_template_part('content', 'store');

    endwhile;

    StoreFunctions::store_loop_end();

    /**
     * Hook: rtcl_after_store_loop.
     *
     * @hooked TemplateHook::pagination() - 10
     */
    do_action('rtcl_after_store_loop');
} else {
    /**
     * Hook: rtcl_no_stores_found.
     *
     * @hooked no_listings_found - 10
     */
    do_action('rtcl_no_stores_found');
}
?>
	</div>
</div>
<?php
get_footer('store');
