<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
?>
<a id="classima-toggle-sidebar" href="#"><?php esc_html_e( 'Tổng kết quả', 'classima' ); ?></a>

<aside class="sidebar-widget-area sidebar-listing-archive">
	<?php do_action( 'classima_before_sidebar' ); ?>
	<?php
	if ( is_active_sidebar( 'sidebar-archive-listing' ) ){
		dynamic_sidebar( 'sidebar-archive-listing' );
	}

	do_action( 'classima_after_sidebar' );
	?>
</aside>