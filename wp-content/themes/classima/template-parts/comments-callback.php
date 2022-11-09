<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

$date = get_comment_date( '', $comment );
$time = get_comment_time();
$human_time = human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) );
?>
<?php $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';?>
<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $args['has_children'] ? 'parent main-comments' : 'main-comments', $comment ); ?>>

<div id="respond-<?php comment_ID(); ?>" class="each-comment media">

	<?php if ( get_option( 'show_avatars' ) ): ?>
		<div class="pull-left imgholder">
			<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'], "", false, array( 'class'=>'media-object' ) ); ?>
		</div>				
	<?php endif; ?>

	<div class="media-body comments-body">
		<div class="comment-meta clearfix">
			<div class="comment-meta-left">
				<h3 class="comment-author"><?php echo get_comment_author_link( $comment );?></h3>
				<div class="comment-time"><?php printf( esc_html__( ' %1$s ago / %2$s @ %3$s', 'classima'), $human_time, $date, $time );?></div>
			</div>
			<?php
			comment_reply_link( 
				array_merge( $args, array(
					'add_below' => 'respond',
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="reply-area">',
					'after'     => '</div>'
					) ) 
			);
			?>
		</div>
		<div class="comment-text">
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'classima' ); ?></p>
			<?php endif; ?>
			<?php comment_text(); ?>							
		</div>
	</div>
	<div class="clear"></div>
</div>