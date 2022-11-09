<?php  
/**
 * Review share template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */
use Rtrs\Helpers\Functions; 
$social_share = ( isset( $p_meta['social_share'] ) && $p_meta['social_share'][0] == '1' );   
if ( !$social_share || !function_exists('rtrsp') ) return; 
?>  
<li class="rtrs-author-social">  
    <label><i class="rtrs-share"></i> <?php esc_html_e('Share:', 'review-schema'); ?></label>
    <?php 
        $entity_stars = "";
        if ( $avg = get_comment_meta( get_comment_ID(), 'rating', true ) ) { 
            $entity_stars = Functions::review_entity_stars( $avg );
        }

        $review_text = $entity_stars . " " . get_comment_text();
        $comment_details = get_comment( get_comment_ID() ); 
        $post_url = get_the_permalink( $comment_details->comment_post_ID );  
    ?>
    <a class="rtrs-share-review" data-url="https:\/\/www.facebook.com/sharer/sharer.php?u=<?php echo esc_url($post_url); ?>&quote=<?php echo esc_html($review_text); ?>" href="#"><i class="rtrs-facebook"></i></a>
    <a class="rtrs-share-review" data-url="https:\/\/twitter.com/intent/tweet?text=<?php echo esc_html($review_text); ?>+%20<?php echo esc_url($post_url); ?>" href="#"><i class="rtrs-twitter"></i></a> 
</li>