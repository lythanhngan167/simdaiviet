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
<div class="rt-el-post-3">
	<?php if ( $query->have_posts() ) :?>
		<div class="row">
			<?php while ( $query->have_posts() ) : $query->the_post();?>
				<?php
				$content = Helper::get_current_post_content();
				$content = wp_trim_words( $content, $data['count'] );
				?>
				<div class="col-md-4 col-12">
					<div class="rtin-each">
						<?php if ( has_post_thumbnail() ): ?>
							<div class="post-thumbnail">
								<a href="<?php the_permalink();?>"><?php the_post_thumbnail( $thumb_size );?></a>
							</div>
						<?php endif; ?>

						<div class="rtin-content-area">
							<div class="post-date"><span class="updated published"><?php the_time( get_option( 'date_format' ) );?></span></div>
							<h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
							<?php if ( $data['author'] ): ?>
								<div class="post-meta">
									<div class="post-author">
                                        <h3 class="author-name"><i class="fa fa-user" aria-hidden="true"></i> <?php esc_html_e('By ', 'classima-core'); ?><?php the_author_posts_link(); ?></h3>
									</div>
								</div>
							<?php endif; ?>
						</div>

					</div>
				</div>
			<?php endwhile;?>
		</div>
	<?php endif;?>
	<?php wp_reset_postdata();?>
</div>