<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/listing/review.php.
 *
 * @see
 * @author  RadiusTheme
 * @package classified-listing/Templates
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="comment-<?php comment_ID(); ?>" class="comment-container">
        <div class="media">
            <div class="media-info">
                <?php
                /**
                 * The rtcl_review_before hook
                 *
                 * @hooked rtcl_review_display_gravatar - 10
                 */
                do_action('rtcl_review_before', $comment);
                ?>
                <div class="rtcl-review-meta">
                    <?php
                    /**
                     * The rtcl_review_meta hook.
                     *
                     * @hooked rtcl_review_display_meta - 10
                     */
                    do_action('rtcl_review_meta', $comment);

                    /**
                     * The rtcl_review_before_comment_meta hook.
                     *
                     * @hooked rtcl_review_display_rating - 10
                     */
                    do_action('rtcl_review_after_meta', $comment);
                    ?>
                </div>
            </div>
            <div class="comment-body media-body">
                <?php
                do_action('rtcl_review_before_comment_text', $comment);

                /**
                 * The rtcl_review_comment_text hook
                 *
                 * @hooked rtcl_review_display_comment_title - 10
                 * @hooked rtcl_review_display_comment_text - 20
                 */
                do_action('rtcl_review_comment_text', $comment);

                do_action('rtcl_review_after_comment_text', $comment); ?>
            </div>
        </div>
    </div>
