<?php  
/**
 * Review pros cons template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */
use Rtrs\Helpers\Functions;  
$pros_cons = ( isset( $p_meta['pros_cons'] ) && $p_meta['pros_cons'][0] == '1' );  
if ( !$pros_cons ) return;

$rt_pros_cons = get_comment_meta( get_comment_ID(), 'rt_pros_cons', true ); 
if ( $rt_pros_cons ) { 
?>
<div class="rtrs-review-feedback"> 
    <?php if ( isset( $rt_pros_cons['pros'] ) && array_filter( $rt_pros_cons['pros'] ) ) { ?>
    <div class="rtrs-feedback-box rtrs-like-feedback">
        <h3 class="rtrs-feedback-title">
            <span class="item-icon like-icon"><i class="rtrs-thumbs-up"></i></span>
            <span class="item-text"><?php esc_html_e( 'PROS', 'review-schema' ); ?></span>
        </h3>
        <ul class="rtrs-feedback-list">
            <?php foreach( $rt_pros_cons['pros'] as $value ) { ?>
            <li><?php echo esc_html( $value ); ?></li>
            <?php } ?>
        </ul>
    </div>
    <?php }  

    if ( isset( $rt_pros_cons['cons'] ) && array_filter( $rt_pros_cons['cons'] ) ) { ?>
    <div class="rtrs-feedback-box rtrs-unlike-feedback">
        <h3 class="rtrs-feedback-title">
            <span class="item-icon unlike-icon"><i class="rtrs-thumbs-down"></i></span>
            <span class="item-text"><?php esc_html_e( 'CONS', 'review-schema' ); ?></span>
        </h3>
        <ul class="rtrs-feedback-list">
            <?php foreach( $rt_pros_cons['cons'] as $value ) { ?>
            <li><?php echo esc_html( $value ); ?></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
</div>
<?php } ?>