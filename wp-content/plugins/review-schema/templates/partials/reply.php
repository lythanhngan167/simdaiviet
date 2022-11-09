<?php  
/**
 * Review reply template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */ 
if ( !current_user_can('administrator') ) return; 
//admin reply not allowed for classifed listing
if ( get_post_type( get_the_ID() ) == 'rtcl_listing' ) return;

?>   
<div class="rtrs-reply-btn"> 
    <?php 
        echo preg_replace( '/comment-reply-link/', 'comment-reply-link rtrs-item-btn', 
            get_comment_reply_link(array_merge( $args, array(
                'add_below' => $add_below, 
                'depth' => $depth, 
                // 'reply_to_text' => '',
                'max_depth' => $args['max_depth']))), 1 ); 
    ?>
</div> 