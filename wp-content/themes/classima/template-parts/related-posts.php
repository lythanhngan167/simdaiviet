<?php
$thumb_size          = 'rdtheme-size2';
$post_id             = get_the_id();
$current_post        = [ $post_id ];
$related_post_number = apply_filters('classima_related_post_number', 3);

$args = [
	'post__not_in'        => $current_post,
	'posts_per_page'      => $related_post_number,
	'no_found_rows'       => true,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
];

$category_ids = [];
$categories   = get_the_category( $post_id );

foreach ( $categories as $category ) {
	$category_ids[] = $category->term_id;
}

$args['category__in'] = $category_ids;

# Get the posts ----------
$related_query = new \wp_query( $args );

$count_post = $related_query->post_count;

if ( !$count_post ) {
	return;
}

?>
<div class="content-block-gap"></div>
<div class="site-content-block classima-related-post">
	<div class="main-title-block">
		<h3 class="main-title">
			<?php esc_html_e( 'Related Post', 'classima' ); ?>
		</h3>
	</div>
	<div class="main-content">
		<div class="row">
			<?php
			while ( $related_query->have_posts() ) {
				$related_query->the_post();
				?>
				<div class="col-md-4">
					<div class="post-each">
						<?php if ( has_post_thumbnail() ) { ?>
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( $thumb_size, [ 'class' => 'img-responsive' ] ); ?>
								</a>
							</div>
						<?php } ?>
						<div class="rtin-content-area">
							<span class="entry-categories"><?php echo the_category( ', ' ); ?></span>
							<h4 class="post-title">
								<a href="<?php the_permalink(); ?>" class="entry-title"><?php the_title(); ?></a>
							</h4>
							<div class="entry-date"><i class="far fa-calendar-alt"></i><?php echo get_the_date(); ?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>