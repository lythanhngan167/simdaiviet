<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.3.4
 */

namespace radiustheme\Classima;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
	
</head>

<body <?php body_class(); ?>>
	<?php do_action( 'wp_body_open' );?>
		
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'classima' ); ?></a>
		<?php get_template_part( 'template-parts/content', 'menu' ); ?>
			 
		<div id="content" class="site-content">
			
		<div class="banner-main">
	<div class="container">
        <a href="#">
        <img src="http://192.168.1.7/khosim/wp-content/uploads/2022/10/banenrphongthuy-1.png" alt="" style="
    padding-top: 10px;
">
		</a>
	</div>
</div>


	
<div class="container">
	<div class="banner-content">
		<h1 class="entry-title"><?php Helper::the_title();?></h1>
		<?php if ( RDTheme::$has_breadcrumb ): ?>
		<div class="main-breadcrumb"><?php Helper::the_breadcrumb(); ?></div>
		<?php endif; ?>
	</div>
</div>


			<!-- <?php get_template_part('template-parts/content', 'banner'); ?> -->
			
			
			
			<!-- <div class="banner-main">
	<div class="container">
        
        <img src="http://192.168.1.5/khosim/wp-content/uploads/2022/10/banenrphongthuy.png" alt="" style="
    padding-top: 10px;
">
	</div>
</div> -->