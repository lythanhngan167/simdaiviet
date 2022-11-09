<?php
/**
 *Manage Listing by user
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use radiustheme\Classima\Helper;
use radiustheme\Classima\RDTheme;

RDTheme::$listing_max_page_num = $rtcl_query->max_num_pages;

$layout = 1;
$display = array(
	'fields' => false,
	'excerpt' => false,
);
?>
<div class="rtcl rtcl-listings manage-listing">
    <?php do_action('rtcl_my_account_before_my_listing', $rtcl_query); ?>
    <div class="rtcl-list-view">
        <?php
        if ( $rtcl_query->have_posts() ) :
            while ( $rtcl_query->have_posts() ) : $rtcl_query->the_post();
                Helper::get_template_part( 'classified-listing/custom/list', compact( 'layout', 'display' ) );
            endwhile;wp_reset_postdata();
        else :
            Helper::get_custom_listing_template( 'noresults' );
        endif;
        ?>
    </div>
    <?php do_action('rtcl_my_account_after_my_listing', $rtcl_query); ?>
</div>