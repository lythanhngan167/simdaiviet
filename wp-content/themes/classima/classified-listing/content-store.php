<?php
/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.2.31
 *
 */

if (!class_exists( 'RtclPro' )) return;

use RtclStore\Helpers\Functions as StoreFunctions;

global $store;

?>
<div <?php StoreFunctions::store_class('rt-el-listing-store-grid', $store) ?>>
    <?php
    /**
     * Hook: rtcl_store_loop_item_start.
     *
     * @hooked open_store_link - 10
     */
    do_action('rtcl_before_store_loop_item');

    /**
     * Hook: rtcl_store_loop_item_start.
     *
     * @hooked store_thumbnail - 10
     */
    do_action('rtcl_store_loop_item_thumbnail');


    /**
     * Hook: rtcl_store_loop_item.
     *
     * @hooked loop_item_content_start - 5
     * @hooked loop_item_store_title - 10
     * @hooked store_meta - 20
     * @hooked store_excerpt - 30
     * @hooked loop_item_content_end - 100
     */
    do_action('rtcl_store_loop_item');

    /**
     * Hook: rtcl_after_store_loop_item.
     *
     * @hooked close_store_link - 5
     */
    do_action('rtcl_after_store_loop_item');
    ?>
</div>


