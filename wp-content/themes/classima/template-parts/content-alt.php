<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

$thumb_size = 'rdtheme-size2';
$has_entry_meta = ( RDTheme::$options['blog_cats'] && has_category() ) || RDTheme::$options['blog_author_name'] || RDTheme::$options['blog_comment_num'] || RDTheme::$options['blog_date'] ? true : false;

$comments_number = get_comments_number();
$comments_text   = sprintf( '(%s)' , number_format_i18n( $comments_number ));
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-each post-each-alt' ); ?>>
	<?php if ( has_post_thumbnail() ): ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink();?>"><?php the_post_thumbnail( $thumb_size );?></a>
		</div>
	<?php endif; ?>

	<div class="rtin-content-area">
		<h2 class="post-title"><a href="<?php the_permalink();?>" class="entry-title" rel="bookmark"><?php the_title();?></a></h2>

		<?php if ( $has_entry_meta ): ?>

			<ul class="post-meta">
				<?php if ( RDTheme::$options['blog_date'] ): ?>
					<li><i class="fa fa-calendar" aria-hidden="true"></i><span class="updated published"><?php the_time( get_option( 'date_format' ) );?></span></li>
				<?php endif; ?>

				<?php if ( RDTheme::$options['blog_author_name'] ): ?>
					<li><i class="fa fa-user" aria-hidden="true"></i><span class="vcard author"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="fn"><?php the_author(); ?></a></span></li>
				<?php endif; ?>

				<?php if ( RDTheme::$options['blog_comment_num'] ): ?>
					<li><i class="fa fa-comments" aria-hidden="true"></i><?php echo esc_html( $comments_text );?></li>
				<?php endif; ?>

				<?php if ( RDTheme::$options['blog_cats'] && has_category() ): ?>
					<li><i class="fa fa-tags" aria-hidden="true"></i><?php the_category( ', ' );?></li>
				<?php endif; ?>
			</ul>

		<?php endif; ?>

		<div class="post-content entry-summary"><?php the_excerpt();?></div>

        <?php if ( RDTheme::$options['blog_button'] ): ?>
            <div class="rtin-button post-btn">
                <a href="<?php the_permalink(); ?>"><?php esc_html_e('Read More', 'classima' ); ?></a>
            </div>
        <?php endif; ?>

	</div>

</article>