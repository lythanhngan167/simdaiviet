<?php  
/**
 * Affiliate Buy btn template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */ 
?>  
<?php if ( $btn_txt = $p_meta['btn_txt'] ) { ?>
    <a href="<?php echo esc_url( $p_meta['btn_url'] ); ?>" class="rtrs-buy-btn"><?php echo esc_html( $btn_txt ); ?></a>
<?php } ?>