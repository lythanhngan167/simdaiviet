<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;

$keyword = isset( $_GET['q'] ) ? esc_attr($_GET['q']) : '';
$class   = "rtin-{$data['theme']} rtin-style-{$data['style']}";
?>
<div class="rt-el-listing-search rtcl <?php echo esc_attr( $class ); ?>">
	<?php
        if ($data['style'] == '3') {
            require_once 'search-banner.php';
        } else {
	        Helper::get_custom_listing_template( 'listing-search' );
        }
    ?>
</div>