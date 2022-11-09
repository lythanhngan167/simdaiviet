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

use Rtcl\Helpers\Functions;


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$title = get_comment_meta( $comment->comment_ID, 'title', true );
$time  = get_comment_date(). ' @' . get_comment_time();
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    <div class="each-comment media clearfix">

        <div class="pull-left imgholder">
            <?php echo get_avatar( $comment, 100, "", false, array( 'class'=>'media-object' ) ); ?>
        </div> 

        <div class="media-body comments-body">
            <div class="comment-meta clearfix">
                <div class="comment-meta-left">
                    <h3 class="comment-title"><?php echo esc_html( $title );?></h3>
                    <div class="comment-info">
                        <span class="c-author"><?php comment_author(); ?></span>
                        <span class="c-seperator">/</span>
                        <span class="c-time"><?php echo esc_html( $time );?></span>
                    </div>
                </div>
                <div class="rating-display-area">
                    <?php Functions::get_template( 'listing/review-rating', null, '', rtclPro()->get_plugin_template_path() ); ?>
                </div>
            </div>
            <div class="comment-text">
                <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your review is awaiting moderation.', 'classima' ); ?></p>
                <?php endif; ?>
                <?php comment_text(); ?>                            
            </div>
        </div>

    </div>