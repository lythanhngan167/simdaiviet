<?php  
/**
 * Review layout one template
 * @author      RadiusTheme
 * @package     review-schema/templates/review
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */  

use Rtrs\Models\Review; 
use Rtrs\Helpers\Functions; 
?>   
<div class="rtrs-summary-3">
    <?php rtrs()->get_partial_path('title', array('p_meta' => $p_meta)); ?> 

    <div class="rtrs-rating-summary">
        <div class="rtrs-rating-item grid-span-2">
            <ul class="rtrs-rating-category">
                <?php foreach( $p_meta['criteria'] as $value ) {
                    if ( !$value['avg'] ) continue;
                    $avg_value = ( $value['type'] == 'percent' ) ? $value['avg']/5 : $value['avg'];
                    ?>     
                    <li>
                        <label><?php echo esc_html( $value['title'] ); ?></label>
                        <div class="rating-icon">
                            <?php echo Functions::review_stars( $avg_value ); ?>
                        </div>
                        <div class="rating-number">
                            <span class="total-number"><?php echo esc_html( $avg_value ); ?> /</span>
                            <span class="outof-number">5</span>
                        </div>
                    </li>
                <?php } ?> 
            </ul>
        </div> 
        
        <?php if ( $avg_rating = $p_meta['avg_rating'] ) { ?>
        <div class="rtrs-rating-item">
            <div class="rtrs-rating-overall">
                <div class="rating-percent"><?php echo esc_html( $avg_rating ); ?></div>
                <div class="rating-text"><?php esc_html_e( 'OVERALL', 'review-schema' ); ?></div>
                <div class="rating-icon">
                    <?php echo Functions::review_stars( $avg_rating ); ?>
                </div>
                <p>
                    <?php
                        $total_rating = $p_meta['total_rating']; 
                        printf(
                            esc_html( _n( 'Based on %s rating', 'Based on %s ratings', $total_rating, 'review-schema' ) ), 
                            esc_html( $total_rating ) 
                        ); 
                    ?> 
                </p>
            </div>
        </div>
        <?php } ?> 
        
        <?php 
        if ( $summary = $p_meta['summary'] ) { ?>
        <div class="rtrs-rating-item grid-span-1">
            <div class="rtrs-feedback-text">
                <h3 class="rtrs-feedback-ttile"><?php esc_html_e( 'Summery', 'review-schema' ); ?></h3>
                <p><?php echo wp_kses_post( $summary ); ?></p>
            </div>
        </div> 
        <?php } ?>

    </div>

    <?php rtrs()->get_partial_path('buy-btn', array('p_meta' => $p_meta)); ?> 
</div>