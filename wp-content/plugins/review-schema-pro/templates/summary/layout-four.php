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
<div class="rtrs-summary-3 rtrs-summary-4-by-user">
    <div class="rtrs-rating-summary">
        <div class="rtrs-rating-item grid-span-2"> 
            <ul class="rtrs-rating-category">
                <?php 
                $avg_by_star = Review::getAvgRatingByStar( get_the_ID() );

                for( $i = 5; $i >= 1; $i-- ) {  ?>     
                    <li> 
                        <div class="rating-icon">
                            <?php echo Functions::review_stars( $i ); ?> 
                        </div>
                        
                        <div class="rtrs-progress-wrap">
                            <div class="rtrs-progress"> 
                                <?php 
                                    if ( $avg_by_star[$i] ) {
                                        $percent = ( 100 / $total_rating ) * $avg_by_star[$i];
                                    } else {
                                        $percent = 0;
                                    } 
                                ?>
                                <progress class="rtrs-progress-bar starting-preogress" value="<?php echo esc_attr( $percent ); ?>" max="100"></progress> 
                            </div> 
                        </div>

                        <div class="rating-number-user">
                            <span class="total-number"><?php echo esc_html( $avg_by_star[$i] ); ?></span> 
                        </div>
                    </li>
                <?php } ?> 
            </ul>
        </div> 
        
        <?php if ( $avg_rating = Review::getAvgRatings( get_the_ID() ) ) { ?>
        <div class="rtrs-rating-item">
            <div class="rtrs-rating-overall">
                <div class="rating-percent"><?php echo esc_html( $avg_rating ); ?><span class="rtrs-out-of">/5</span></div>
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