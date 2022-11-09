<?php
/**
 *
 * @author 		RadiusTheme
 * @package 	classified-listing/templates
 * @version     1.0.0
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists( 'RtclPro' )) return;

Functions::print_notices();

?>
<div class="rtcl-MyAccount-wrap">
    <div class="rtcl-MyAccount-content">
		<?php do_action( 'rtcl_account_content' ); ?>
    </div>
</div>