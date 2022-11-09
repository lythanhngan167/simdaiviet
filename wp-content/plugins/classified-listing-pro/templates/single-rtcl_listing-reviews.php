<?php
/**
 * Display single listing reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/single-rtcl_listing-reviews.php.
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

if ( ! comments_open() ) {
	return;
}

global $post;
$listing = rtcl()->factory->get_listing( $post->ID );
if ( ! $listing->exists() ) {
	return;
}

?>
<div id="reviews_" class="rtcl-Reviews rtcl">
    <div id="comments">
        <div class="rtcl-reviews-meta">
            <h4 class="rtcl-single-listing-section-title">
				<?php esc_html_e( "Reviews", "classified-listing-pro" ); ?>
            </h4>
			<?php if ( have_comments() ) :
				$average = $listing->get_average_rating();
				$rating_count = $listing->get_rating_count();
				?>
                <!-- Single Listing Review / Meta -->
                <div class="listing-meta">
                    <!-- Listing / Rating -->
                    <div class="listing-meta-rating"><?php echo esc_html( $average ); ?></div>
                    <div class="reviews-rating">
						<?php echo Fns::get_rating_html( $average, $rating_count ); ?>
                        <span class="reviews-rating-count">(<?php echo absint( $rating_count ); ?>)</span>
                    </div>
                </div>
			<?php endif; ?>
            <div class="rtcl-reviews-meta-action">
                <a class="rtcl-animate" href="#respond"><?php esc_html_e( "Leave Review", "classified-listing-pro" ) ?><i
                            class="rtcl-icon-level-down"></i></a>
            </div>
        </div>
		<?php if ( have_comments() ) : ?>
            <ol class="comment-list">
				<?php wp_list_comments( apply_filters( 'rtcl_listing_review_list_args', [
					'callback' => [
						Fns::class,
						'comments'
					]
				] ) ); ?>
            </ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="rtcl-pagination">';
				paginate_comments_links( apply_filters( 'rtcl_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

            <p class="rtcl-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'classified-listing-pro' ); ?></p>

		<?php endif; ?>
    </div>

    <div id="review-form-wrapper">
        <div id="review-form">
			<?php
			$commenter = wp_get_current_commenter();

			$comment_form = [
				'title_reply'         => have_comments() ? esc_html__( 'Leave Review', 'classified-listing-pro' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'classified-listing-pro' ), get_the_title() ),
				'title_reply_to'      => __( 'Leave a Reply to %s', 'classified-listing-pro' ),
				'title_reply_before'  => '<h4 id="reply-title" class="comment-reply-title">',
				'title_reply_after'   => '</h4>',
				'comment_notes_after' => '',
				'fields'              => [
					'author' => '<div class="comment-form-author form-group">' . '<label for="author">' . esc_html__( 'Name', 'classified-listing-pro' ) . '&nbsp;<span class="required">*</span></label> ' .
					            '<input id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" required /></div>',
					'email'  => '<div class="comment-form-email form-group"><label for="email">' . esc_html__( 'Email', 'classified-listing-pro' ) . '&nbsp;<span class="required">*</span></label> ' .
					            '<input id="email" name="email" class="form-control" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" required /></div>',
                ],
				'label_submit'        => esc_html__( 'Submit', 'classified-listing-pro' ),
				'class_submit'        => 'btn btn-primary',
				'logged_in_as'        => '',
				'comment_field'       => '',
            ];

			if ( $account_page_url = Link::get_my_account_page_link() ) {
				$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'classified-listing-pro' ), esc_url( $account_page_url ) ) . '</p>';
			}

            $comment_form['comment_field'] = '<div class="comment-form-title  form-group"><label for="title">' . esc_html__( 'Review title', 'classified-listing-pro' ) . '&nbsp;<span class="required">*</span></label><input type="text" class="form-control" name="title" id="title"  aria-required="true" required/></div>';
            if ( Functions::get_option_item( 'rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox' ) ) {
				$comment_form['comment_field'] .= '<div class="comment-form-rating  form-group"><label for="rating">' . esc_html__( 'Your rating', 'classified-listing-pro' ) . '<span class="required">*</span></label><select name="rating" id="rating" class="form-control" aria-required="true" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'classified-listing-pro' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'classified-listing-pro' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'classified-listing-pro' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'classified-listing-pro' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'classified-listing-pro' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'classified-listing-pro' ) . '</option>
						</select></div>';
			}

			$comment_form['comment_field'] .= '<div class="comment-form-comment  form-group"><label for="comment">' . esc_html__( 'Your review', 'classified-listing-pro' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true" required></textarea></div>';

			comment_form( apply_filters( 'rtcl_listing_review_comment_form_args', $comment_form ) );
			?>
        </div>
    </div>
</div>
