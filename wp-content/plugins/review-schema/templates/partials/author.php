<?php  
/**
 * Review author template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 *   
 */ 
?>
<li class="rtrs-author-link"><?php esc_html_e('by:', 'review-schema'); ?> 
    <?php 
        if ( get_comment_meta( get_comment_ID(), 'rt_anonymous', true ) ) {
            esc_html_e( 'Anonymous', 'review-schema' );
        } else {
            echo get_comment_author_link(); 
        } 
    ?> <i class="rtrs-verified-author"></i>
</li> 