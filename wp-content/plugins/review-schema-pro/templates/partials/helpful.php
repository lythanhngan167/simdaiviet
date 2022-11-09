<?php  
/**
 * Review helpful template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */
use Rtrs\Helpers\Functions;  

$like = ( isset( $p_meta['like'] ) && $p_meta['like'][0] == '1' );  
$dislike = ( isset( $p_meta['dislike'] ) && $p_meta['dislike'][0] == '1' );  

if ( $like ) { 
    $get_helpful_like = get_comment_meta( get_comment_ID(), 'rt_helpful_like', true );  
    $get_helpful_like_check = ( $get_helpful_like == "" ) ? array() : $get_helpful_like;
    $helpful_like_count = ( $get_helpful_like == "" ) ? 0 : count( $get_helpful_like ); 
    $helpful_like = "";
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $helpful_like = in_array($current_user->ID, $get_helpful_like_check) ? "remove" : "helpful";  
    } 
?>

<button class="rtrs-tooltip rtrs-review-helpful" data-type="like" data-helpful="<?php echo esc_attr( $helpful_like ); ?>" data-comment-id="<?php echo esc_attr( get_comment_ID() ); ?>"><i class="rtrs-thumbs-up"></i><span class="helpful-count"><?php echo esc_html( $helpful_like_count ); ?></span>
    <?php if ( !is_user_logged_in() ) { ?>
    <span class="rtrs-tooltiptext"><?php esc_html_e( 'Please login to like', 'review-schema' ); ?></span>
    <?php } else { ?>
        <span class="rtrs-tooltiptext"><?php esc_html_e( 'Helpful?', 'review-schema' ); ?></span>
    <?php } ?>
</button> 
<?php 
} 

if ( $dislike ) { 
    $get_helpful_dislike = get_comment_meta( get_comment_ID(), 'rt_helpful_dislike', true );   
    $get_helpful_dislike_check = ( $get_helpful_dislike == "" ) ? array() : $get_helpful_dislike;
    $helpful_dislike_count = ( $get_helpful_dislike == "" ) ? 0 : count( $get_helpful_dislike ); 
    $helpful_dislike = in_array(1, $get_helpful_dislike_check) ? "remove" : "helpful";  

    $helpful_dislike = "";
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $helpful_dislike = in_array($current_user->ID, $get_helpful_dislike_check) ? "remove" : "helpful";  
    } 
?>
<button class="rtrs-tooltip rtrs-review-helpful" data-type="dislike" data-helpful="<?php echo esc_attr( $helpful_dislike ); ?>" data-comment-id="<?php echo esc_attr( get_comment_ID() ); ?>"><i class="rtrs-thumbs-down"></i><span class="helpful-count"><?php echo esc_html( $helpful_dislike_count ); ?></span>
    <?php if ( !is_user_logged_in() ) { ?>
        <span class="rtrs-tooltiptext"><?php esc_html_e( 'Please login to dislike', 'review-schema' ); ?></span>
    <?php } else { ?>
        <span class="rtrs-tooltiptext"><?php esc_html_e( 'Not helpful?', 'review-schema' ); ?></span>
    <?php } ?>
</button> 
<?php
}