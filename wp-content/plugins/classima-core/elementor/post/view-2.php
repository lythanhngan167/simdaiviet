<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use \WP_Query;
use radiustheme\Classima\Helper;

$thumb_size = 'rdtheme-size2';

if ( !empty( $cat ) ) {
	$blog_permalink = get_category_link( $cat );
}
else {
	$blog_page = get_option( 'page_for_posts' );
	$blog_permalink = $blog_page ? get_permalink( $blog_page ) : home_url( '/' );
}

$args = array(
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
	'cat'                 => (int) $data['cat'],
	'orderby'             => $data['orderby'],
);

switch ( $data['orderby'] ) {
	case 'title':
	case 'menu_order':
	$args['order'] = 'ASC';
	break;
}

$query = new WP_Query( $args );
?>
<div class="rt-el-post-2">
	<?php if ( $query->have_posts() ) :?>
		<div class="row">
			<?php while ( $query->have_posts() ) : $query->the_post();?>
				<div class="col-md-4 col-12">
					<div class="rtin-each">
						<?php if ( has_post_thumbnail() ): ?>
							<div class="post-thumbnail">
								<a href="<?php the_permalink();?>"><?php the_post_thumbnail( $thumb_size );?></a>
							</div>
						<?php endif; ?>

						<div class="rtin-content-area">
							<ul class="post-meta">
								<li><?php the_time( get_option( 'date_format' ) );?></li>
								<?php if ( $data['author'] ): ?>
									<li> / <?php the_author_posts_link();?></li>
								<?php endif; ?>
							</ul>
							<h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
						</div>

					</div>
				</div>
			<?php endwhile;?>
		</div>
		
		<?php if ( $data['btn'] ): ?>
			<div class="rtin-view"><a class="rdtheme-button-3" href="<?php echo esc_url( $blog_permalink ); ?>"><?php echo esc_html( $data['btntext'] ); ?></a></div>
		<?php endif; ?>
		
	<?php endif;?>
	<?php wp_reset_postdata();?>
</div>