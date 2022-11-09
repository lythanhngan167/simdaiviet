<?php  
/**
 * Affiliate title template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */ 
?>  
<div class="rtrs-title-area rtrs-clearfix">
    <div class="rtrs-title">
        <?php if ( $title = $p_meta['title'] ) { ?>
            <h3><?php echo esc_html( $title ); ?></h3>
        <?php } ?>
    </div>

    <div class="rtrs-price-area">
        <?php 
        $offer_price = $p_meta['offer_price'];
        $regular_price = $p_meta['regular_price'];
        if ( !$offer_price ) {
            $offer_price = $regular_price;
        }
        if ( $offer_price ) { ?>
            <span class="rtrs-offer-price"><?php echo esc_html( $offer_price ); ?></span>
        <?php } 
        
        if ( $p_meta['offer_price'] && $regular_price ) { ?>
            <span class="rtrs-regular-price"><?php echo esc_html( $regular_price ); ?></span>
        <?php } ?>
    </div>
</div>