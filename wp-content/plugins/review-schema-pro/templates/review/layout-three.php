<?php  
/**
 * Review layout three template
 * @author      RadiusTheme
 * @package     review-schema/templates/review
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */  

use Rtrs\Helpers\Functions; 
$highlight_bg = get_comment_meta( get_comment_ID(), 'rt_highlight', true ) == 1  ? "rtrs-top-review" : ""; 

$purchased_badge = ( isset( $p_meta['purchased_badge'] ) && $p_meta['purchased_badge'][0] == '1' );  
$purchased_user = '';
if ( $purchased_badge ) { 
    $purchased_user = Functions::purchased_user( $comment_details ) ? 'verified-user': ''; 
} 
?>    
<div class="rtrs-each-review <?php echo esc_attr( $highlight_bg . ' ' . $purchased_user ); ?>"> 

    <?php if ( get_option( 'show_avatars' ) ) { ?>
    <div class="rtrs-review-imgholder">
        <?php 
            $avatar = '';
            if ( get_comment_meta( get_comment_ID(), 'rt_anonymous', true ) ) {
                $avatar = RTRS_URL . '/assets/imgs/avatar.jpg';
            } else {
                $avatar = get_avatar_url( $comment->comment_author_email, ['size' => '70'] );
            } 
        ?>
        <img src="<?php echo esc_url( $avatar ); ?>" alt="">
    </div>
    <?php } ?>

    <div class="rtrs-review-body">
         
        <ul class="rtrs-review-meta">  
            <?php if ( $avg = get_comment_meta( get_comment_ID(), 'rating', true ) ) {  ?>
                <li class="rtrs-review-rating"><?php echo Functions::review_stars( $avg ); ?></li>
            <?php } ?> 
            
            <?php rtrs()->get_partial_path('author', array('p_meta' => $p_meta)); ?> 

            <li class="rtrs-review-date"><i class="rtrs-calendar"></i>  
            <?php echo Functions::comment_review_time( $comment ); ?>
            <?php  
            if (  $review_edit == 'yes' && $comment->user_id == get_current_user_id() ) { ?>
                <span class="rtrs-review-edit-btn" data-comment-post-id="<?php echo esc_attr( $comment->comment_post_ID ); ?>" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>">
                    <?php esc_html_e( '(Edit)', 'review-schema' ); ?>
                </span> 
            <?php } ?>

            </li> 
        </ul>

        <?php if ( $title = get_comment_meta( get_comment_ID(), 'rt_title', true ) ) {  ?>
            <h4 class="rtrs-review-title"><?php echo esc_html( $title ); ?></h4>
        <?php } ?>
        
        <?php comment_text(); ?>

        <?php if ( $comment->comment_approved == '0' ) : ?>
            <p><em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'review-schema' ); ?></em></p>
        <?php endif; ?>

        <?php rtrs()->get_partial_path('attachment', array('p_meta' => $p_meta)); ?>

        <?php rtrs()->get_partial_path('pros-cons', array('p_meta' => $p_meta)); ?> 

        <div class="rtrs-action-area">
            <?php rtrs()->get_partial_path('highlight', array('p_meta' => $p_meta)); ?>  
        </div>

        <ul class="rtrs-review-meta rtrs-meta-right">
            <li class="rtrs-action-area"><?php rtrs()->get_partial_path('helpful', array('p_meta' => $p_meta)); ?> </li>
            <?php rtrs()->get_partial_path('share', array('p_meta' => $p_meta)); ?> 
        </ul>

        <?php rtrs()->get_partial_path('reply', array('args' => $args, 'add_below' => $add_below, 'depth' => $depth )); ?> 

    </div>
</div>