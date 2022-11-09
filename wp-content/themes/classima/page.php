<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
?>
<?php get_header(); ?>
<div id="primary" class="content-area">
	<div class="container">
		<?php do_action( 'classima_header_top' ); ?>
		<div class="row">
			<?php
			if ( RDTheme::$layout == 'left-sidebar' ) {
				get_sidebar();
			}
			?>
			<div class="<?php Helper::the_layout_class();?>">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					get_template_part( 'template-parts/content', 'page' );
					if ( comments_open() || get_comments_number() ){
						comments_template();
					}
					?>
				<?php endwhile; ?>
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