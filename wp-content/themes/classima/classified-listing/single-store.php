<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions as RtclFunctions;
use radiustheme\Classima\RDTheme;
use radiustheme\Classima\Helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (!class_exists( 'RtclPro' )) return;

RDTheme::$layout = 'right-sidebar';
?>
<?php get_header(); ?>
<div id="primary" class="content-area classima-store-single rtcl">
    <div class="container">
        <?php
        while ( have_posts() ) : the_post();
            RtclFunctions::get_template_part('content', 'single-store');
        endwhile;
        ?>
    </div>
</div>
<?php get_footer(); ?>