<?php
/**
 * @package ClassifiedListing/Templates
 * @version 2.2.1.1
 */

use radiustheme\Classima\Helper;
use radiustheme\Classima\Listing_Functions;
use radiustheme\Classima\RDTheme;
use Rtcl\Helpers\Functions;

defined('ABSPATH') || exit;

get_header();
?>
<div id="primary" class="content-area site-index">
	<div class="container">
		<?php do_action( 'classima_header_top' );?>
		<div class="row">
			<?php
			if ( RDTheme::$layout == 'left-sidebar' ) {
				get_sidebar();
			}
			?>
			<div class="<?php Helper::the_layout_class();?>">
				<div class="main-content">
					<?php Functions::get_template( 'listing/author-content'); ?>
				</div>
			</div>
			<?php
			if ( RDTheme::$layout == 'right-sidebar' ) {
				get_sidebar();
			}
			?>
		</div>
	</div>
</div>
<?php
get_footer();
