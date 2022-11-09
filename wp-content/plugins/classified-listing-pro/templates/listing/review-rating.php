<?php
/**
 * The template to display the reviewers star rating in reviews
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/listing/review-rating.php.
 *
 * @author  RadiousTheme
 * @package classified-listing/Templates
 * @version 1.0.0
 */

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $comment;
$rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));

if ($rating && Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox')) {
    echo Fns::get_rating_html($rating);
}
