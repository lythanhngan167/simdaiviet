<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

// Layout class
if ( RDTheme::$layout == 'full-width' ) {
	$layout_class = 'col-sm-12 col-12';
}
else{
	$layout_class = 'col-lg-9 col-md-8 col-sm-12 col-12';
}
?>
<?php get_header(); ?>
<div id="primary" class="content-area site-search">
	<div class="container">
		<div class="row">
			<?php
			if ( RDTheme::$layout == 'left-sidebar' ) {
				get_sidebar();
			}
			?>
			<div class="<?php echo esc_attr( $layout_class );?>">
				<div class="main-content">
					<?php if ( have_posts() ) :?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'template-parts/content', 'search' ); ?>
						<?php endwhile; ?>
					<?php else:?>
						<?php get_template_part( 'template-parts/content', 'none' );?>
					<?php endif;?>
				</div>
				<?php get_template_part( 'template-parts/pagination' );?>
			</div>
			<?php
			if ( RDTheme::$layout == 'right-sidebar' ) {
				get_sidebar();
			}
			?>
		</div>
	</div>
</div>
<?php get_footer(); ?>