<?php
/**
 * @author  RadiusTheme
 * @since   1.3.4
 * @version 1.3.4
 */

namespace radiustheme\Classima;

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) {
	return;
}
?>
<header id="site-header" class="site-header">
	<?php
	if ( RDTheme::$has_top_bar ) {
		get_template_part( 'template-parts/header/header-top' );
	}
	?>
	<?php get_template_part( 'template-parts/header/header', RDTheme::$header_style ); ?>
</header>
<?php
if (class_exists( 'Rtcl' )) {
	get_template_part('template-parts/header/header', 'offscreen');
}
?>