<?php  
/**
 * Review summary layout three template
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
    <div class="rtrs-rating-summary">
        <div class="rtrs-rating-item grid-span-2">
            <ul class="rtrs-rating-category">
                <?php foreach( Review::getCriteriaAvgRatings( get_the_ID() ) as $value ) {
                    if ( !$value['avg'] ) continue;
                    ?>     
                    <li>
                        <label><?php echo esc_html( $value['title'] ); ?></label>
                        <div class="rating-icon">
                            <?php echo Functions::review_stars( $value['avg'] ); ?>
                        </div>
                        <div class="rating-number">
                            <span class="total-number"><?php echo esc_html( $value['avg'] ); ?> /</span>
                            <span class="outof-number">5</span>
                        </div>
                    </li>
                <?php } ?> 
            </ul>
        </div> 
        
        <?php if ( $avg_rating = Review::getAvgRatings( get_the_ID() ) ) { ?>
        <div class="rtrs-rating-item">
            <div class="rtrs-rating-overall">
                <div class="rating-percent"><?php echo esc_html( $avg_rating ); ?></div>
                <div class="rating-text"><?php esc_html_e( 'OVERALL', 'review-schema' ); ?></div>
                <div class="rating-icon">
                    <?php echo Functions::review_stars( $avg_rating ); ?>
                </div>
                <p>
                    <?php 
                        printf(
                            esc_html( _n( 'Based on %s rating', 'Based on %s ratings', $total_rating, 'review-schema' ) ), 
                            esc_html( $total_rating ) 
                        ); 
                    ?>
                </p>
            </div>
        </div>
        <?php } ?> 
    </div>
</div>