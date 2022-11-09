<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

$post_class = Helper::has_sidebar() ? 'col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12' : 'col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12';
?>
<?php get_header(); ?>
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
					<?php if ( have_posts() ) :?>
						<?php
						if ( ( is_home() || is_archive() ) && RDTheme::$options['blog_style'] == 'style2' ) {
							echo '<div class="post-isotope row">';
							while ( have_posts() ) : the_post();
								echo '<div class="' . $post_class. '">';
								get_template_part( 'template-parts/content-alt' );
								echo '</div>';
							endwhile;
							echo '</div>';
						}
						else {
							while ( have_posts() ) : the_post();
								get_template_part( 'template-parts/content' );
							endwhile;
						}
						?>
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