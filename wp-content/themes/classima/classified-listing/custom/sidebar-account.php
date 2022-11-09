<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
?>
<div class="col-lg-3 col-md-4 ol-sm-12 col-12">
	<aside class="sidebar-widget-area">
        <div id="myaccount-collapse-menu">
            <span>
                <span></span>
            </span>
        </div>
		<div class="classified-account-menu widget">
			<h3 class="widgettitle"><?php esc_html_e( 'My Account', 'classima' );?></h3>
			<?php do_action( 'rtcl_account_navigation' ); ?>
		</div>
		<?php
		if ( is_active_sidebar( 'sidebar-myaccount' ) ){
			dynamic_sidebar( 'sidebar-myaccount' );
		}
		?>
	</aside>
</div>