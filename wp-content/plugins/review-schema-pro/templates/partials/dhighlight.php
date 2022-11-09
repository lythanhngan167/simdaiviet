<?php  
/**
 * Review highlight template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 *   
 */ 
$highlight_review = ( isset( $p_meta['highlight_review'] ) && $p_meta['highlight_review'][0] == '1' );  
if ( !$highlight_review ) return;

if ( current_user_can('administrator') ) {
        $hightlight = ( get_comment_meta( get_comment_ID(), 'rt_highlight', true ) == 1 ) ? "remove" : "highlight"; 
        $hightlight_text = ( get_comment_meta( get_comment_ID(), 'rt_highlight', true ) == 1 ) ? esc_html__( 'Remove Highlight?', 'review-schema' ) : esc_html__( 'Highlight?', 'review-schema' ); 
    ?>
    <button class="rtrs-review-highlight" data-highlight="<?php echo esc_attr( $hightlight ); ?>" data-comment-id="<?php echo esc_attr( get_comment_ID() ); ?>"><?php echo esc_html( $hightlight_text ); ?></button> 
<?php } ?>  