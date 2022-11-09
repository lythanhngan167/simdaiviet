<?php
/**
 * Display single listing reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/listing/single-rtcl_listing-reviews.php.
 *
 * @see
 * @author     RadiusTheme
 * @package    classified-listing/Templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!class_exists( 'RtclPro' )) return;

global $post;
$listing = rtcl()->factory->get_listing($post->ID);
if (!$listing->exists()) return;

/*-------------------------------------
#. Review List
---------------------------------------*/
?>
<?php if ( have_comments() ): ?>
	<?php
	$comments_number = $listing->get_review_count();
	$comments_text   = $comments_number < 2 ? esc_html__( 'Review' , 'classima' ) : esc_html__( 'Reviews' , 'classima' );
	$comments_html   = number_format_i18n( $comments_number ). ' ' . $comments_text;
	$comment_nav_args = array(
		'prev_text'          => esc_html__( 'Older Reviews' , 'classima' ),
		'next_text'          => esc_html__( 'Newer Reviews' , 'classima' ),
		'screen_reader_text' => esc_html__( 'Reviews navigation' , 'classima' ),
	);
	$average_rating = $listing->get_average_rating();
	$rating_count   = $listing->get_rating_count();
	?>

	<div class="content-block-gap"></div>
	<div class="site-content-block">
		<div class="main-title-block review-title-block">
			<h3 class="main-title"><?php echo esc_html( $comments_html );?></h3>
			<div><?php echo Fns::get_rating_html( $average_rating, $rating_count ); ?></div>
		</div>
		<div class="main-content">
			<div class="review-area">

				<ul class="comment-list">
                    <?php wp_list_comments(apply_filters('rtcl_listing_review_list_args', ['callback' => [Fns::class,'comments']])); ?>
				</ul>

				<?php the_comments_navigation( $comment_nav_args ); ?>

				<?php if ( ! comments_open() ) : ?>
					<div class="comments-closed"><?php esc_html_e( 'Reviews are closed.', 'classima' ); ?></div>
				<?php endif;?>

			</div>
		</div>
	</div>
<?php endif;


/*-------------------------------------
#. Review Form
---------------------------------------*/

$commenter = wp_get_current_commenter();

$comment_form = array(
	'title_reply'         => '',
	'title_reply_to'      => '',
	'title_reply_before'  => '',
	'title_reply_after'   => '',
	'comment_notes_after' => '',
	'fields'              => array(
		'author' => '<div class="comment-form-author form-group">' . '<label for="author">' . esc_html__('Your Name', 'classima') . '&nbsp;<span class="required">*</span></label> ' .
		'<input id="author" class="form-control" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" required /></div>',
		'email'  => '<div class="comment-form-email form-group"><label for="email">' . esc_html__('Your Email', 'classima') . '&nbsp;<span class="required">*</span></label> ' .
		'<input id="email" name="email" class="form-control" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" required /></div>',
	),
	'label_submit'        => esc_html__('Submit', 'classima'),
	'class_submit'        => 'submit btn-send',
	'logged_in_as'        => '',
	'comment_field'       => '',
);

if ($account_page_url = Link::get_my_account_page_link()) {
	$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a review.', 'classima'), esc_url($account_page_url)) . '</p>';
}

if (Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox')) {
	$comment_form['comment_field'] = '<div class="comment-form-title  form-group"><label for="title">' . esc_html__('Review Title', 'classima') . '&nbsp;<span class="required">*</span></label><input type="text" class="form-control" name="title" id="title"  aria-required="true" required/></div>';
	$comment_form['comment_field'] .= '<div class="comment-form-rating  form-group"><label for="rating">' . esc_html__('Your Rating', 'classima') . '<span class="required">*</span></label><select name="rating" id="rating" class="form-control" required>
	<option value="">' . esc_html__('Rate&hellip;', 'classima') . '</option>
	<option value="5">' . esc_html__('Perfect', 'classima') . '</option>
	<option value="4">' . esc_html__('Good', 'classima') . '</option>
	<option value="3">' . esc_html__('Average', 'classima') . '</option>
	<option value="2">' . esc_html__('Not that bad', 'classima') . '</option>
	<option value="1">' . esc_html__('Very poor', 'classima') . '</option>
	</select></div>';
}

$comment_form['comment_field'] .= '<div class="comment-form-comment  form-group"><label for="comment">' . esc_html__('Your Review', 'classima') . '&nbsp;<span class="required">*</span></label><textarea id="comment" class="form-control" name="comment" cols="45" rows="8" required></textarea></div>';
?>

<div class="content-block-gap"></div>
<div class="site-content-block">
	<div class="main-title-block"><h3 class="main-title"><?php esc_html_e( 'Leave a Review', 'classima' );?></h3></div>
	<div class="main-content review-area">
		<?php comment_form( $comment_form );?>
	</div>
</div>