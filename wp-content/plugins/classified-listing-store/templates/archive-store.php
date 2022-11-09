<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.2.31
 */

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclStore\Helpers\Functions as StoreFunctions;

defined('ABSPATH') || exit;

get_header('store');

/**
 * Hook: rtcl_before_main_content.
 *
 * @hooked rtcl_output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action('rtcl_before_main_content');

?>
    <header class="rtcl-stores-header">
        <?php if (apply_filters('rtcl_store_show_archive_page_title', true)) : ?>
            <h1 class="rtcl-stores-header-title page-title"><?php StoreFunctions::page_title(); ?></h1>
        <?php endif; ?>

        <?php do_action('rtcl_archive_description'); ?>
    </header>
<?php

if (rtcl()->wp_query()->have_posts()) {

    /**
     * Hook: rtcl_before_listing_loop.
     *
     * @hooked TemplateHooks::output_all_notices() - 10
     * @hooked TemplateHooks::listings_actions - 20
     *
     */
    do_action('rtcl_before_store_loop');


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

/**
 * Hook: rtcl_after_main_content.
 *
 * @hooked rtcl_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('rtcl_after_main_content');

/**
 * rtcl_store_sidebar hook.
 *
 * @hooked get_store_sidebar - 10
 */
do_action('rtcl_store_sidebar');

get_footer('store');
