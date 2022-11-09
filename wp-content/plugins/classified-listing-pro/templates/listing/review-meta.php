<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/listing/review-meta.php.
 *
 * @author RadiousTheme
 * @package classified-listing/Templates
 * @version 1.0.0
 */

use Rtcl\Helpers\Functions;

defined('ABSPATH') || exit;

global $comment;

if ('0' === $comment->comment_approved) { ?>

    <p class="meta">
        <em class="rtcl-review-awaiting-approval">
            <?php esc_html_e('Your review is awaiting approval', 'classified-listing-pro'); ?>
        </em>
    </p>

<?php } else { ?>

    <div class="media-author">
        <span class="rtcl-review-author"><?php comment_author(); ?> </span>
        <time class="rtcl-review-published-date"
              datetime="<?php echo esc_attr(get_comment_date('c')); ?>"><?php echo esc_html(Functions::datetime('time-elapsed', get_comment_date(Functions::date_format()))); ?></time>
    </div>

    <?php
}
