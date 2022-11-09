<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

$thumb_size      = Helper::has_sidebar() ? 'rdtheme-size3' : 'rdtheme-size1';
$has_entry_meta = ( RDTheme::$options['post_cats'] && has_category() ) || RDTheme::$options['post_author_name'] || RDTheme::$options['post_comment_num'] || RDTheme::$options['post_date'] ? true : false;

$comments_number = get_comments_number();
$comments_text   = sprintf( '(%s)' , number_format_i18n( $comments_number ));

$footer_class    = RDTheme::$options['post_tags'] && has_tag() && RDTheme::$options['post_social'] && class_exists( 'Classima_Core' ) ? 'col-md-6 col-sm-12 col-12' : 'col-md-12 col-sm-12 col-12';

$has_post_footer = ( RDTheme::$options['post_tags'] && has_tag() ) || ( RDTheme::$options['post_social'] && class_exists( 'Classima_Core' ) ) ? true : false;
?>
<div class="site-content-block">
	<div class="main-content">

		<div id="post-<?php the_ID(); ?>" <?php post_class( 'post-each post-each-single' ); ?>>

			<?php do_action( 'classima_before_content' );?>

			<?php if ( has_post_thumbnail() ): ?>
				<div class="post-thumbnail">
					<a href="<?php the_permalink();?>"><?php the_post_thumbnail( $thumb_size );?></a>
				</div>
			<?php endif; ?>

			<div class="post-content-area">

				<?php if ( $has_entry_meta ): ?>

					<ul class="post-meta">
						<?php if ( RDTheme::$options['post_date'] ): ?>
							<li><i class="fa fa-calendar" aria-hidden="true"></i><span class="updated published"><?php the_time( get_option( 'date_format' ) );?></span></li>
						<?php endif; ?>

						<?php if ( RDTheme::$options['post_author_name'] ): ?>
							<li><i class="fa fa-user" aria-hidden="true"></i><span class="vcard author"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="fn"><?php the_author(); ?></a></span></li>
						<?php endif; ?>

						<?php if ( RDTheme::$options['post_comment_num'] ): ?>
							<li><i class="fa fa-comments" aria-hidden="true"></i><?php echo esc_html( $comments_text );?></li>
						<?php endif; ?>

						<?php if ( RDTheme::$options['post_cats'] && has_category() ): ?>
							<li><i class="fa fa-tags" aria-hidden="true"></i><?php the_category( ', ' );?></li>
						<?php endif; ?>
					</ul>

				<?php endif; ?>

				<div class="post-content entry-content clearfix"><?php the_content();?></div>
				<?php wp_link_pages();?>

				<?php if ( $has_post_footer ): ?>
					<div class="post-footer">
						<div class="row">
							<?php if ( RDTheme::$options['post_tags'] && has_tag() ): ?>
								<div class="<?php echo esc_attr( $footer_class );?>">
									<div class="post-tags"><?php echo get_the_term_list( $post->ID, 'post_tag' ); ?></div>
								</div>
							<?php endif; ?>
							<?php if ( RDTheme::$options['post_social'] && class_exists( 'Classima_Core' ) ): ?>
								<div class="<?php echo esc_attr( $footer_class );?>">
									<?php \Classima_Core::social_share( RDTheme::$options['post_share'] ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php do_action( 'classima_after_content' );?>

			</div>
		</div>

	</div>
</div>

<?php if ( RDTheme::$options['post_about_author'] ): ?>
	<div class="content-block-gap"></div>
	<div class="site-content-block">
		<div class="main-title-block"><h3 class="main-title"><?php esc_html_e( 'About Author', 'classima' );?></h3></div>
		<div class="main-content">
			<div class="post-author-block">
				<div class="rtin-left">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 120 ); ?>
				</div>
				<div class="rtin-right">
					<h4 class="author-name"><?php the_author_posts_link();?></h4>
					<div class="author-bio"><?php echo esc_html( get_the_author_meta( 'description' ) );?></div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php
if ( RDTheme::$options['post_related'] ) {
	get_template_part( 'template-parts/related-posts' );
}
?>
