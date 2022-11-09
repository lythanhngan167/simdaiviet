<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions as RtclFunctions;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
get_header('store'); ?>
<?php
/**
 * rtcl_before_main_content hook.
 *
 * @hooked rtcl_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked rtcl_breadcrumb - 20
 */
do_action('rtcl_before_main_content');
?>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>

    <?php RtclFunctions::get_template_part('content', 'single-store'); ?>

<?php endwhile; // end of the loop. ?>

<?php
/**
 * rtcl_after_main_content hook.
 *
 * @hooked rtcl_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('rtcl_after_main_content');
?>

<?php
/**
 * rtcl_store_sidebar hook.
 *
 * @hooked get_store_sidebar - 10
 */
do_action('rtcl_store_sidebar');
?>

<?php
get_footer('store');
