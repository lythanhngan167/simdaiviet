<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
?>
<div class="site-content-block">
	<div class="main-content">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php do_action( 'classima_before_content' );?>

			<?php if ( has_post_thumbnail() ): ?>
				<div class="main-thumbnail"><?php the_post_thumbnail( 'rdtheme-size1' );?></div>
			<?php endif; ?>
			<?php the_content();?>
			
			<?php wp_link_pages();?>

			<?php do_action( 'classima_after_content' );?>
		</div>
	</div>
</div>