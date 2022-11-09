<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-each post-each-search' ); ?>>
	<h2 class="post-title"><a href="<?php the_permalink();?>" class="entry-title" rel="bookmark"><?php the_title();?></a></h2>
	<div class="post-content entry-summary"><?php the_excerpt();?></div>
</article>