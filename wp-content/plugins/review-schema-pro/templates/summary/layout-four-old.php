<?php  
/**
 * Review summary layout four template
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
<div class="rtrs-summary-2 rtrs-summary-3 rtrs-summary-4">
    <div class="rtrs-rating-summary">
        <?php if ( $avg_rating = Review::getAvgRatings( get_the_ID() ) ) { ?>
        <div class="rtrs-rating-item">
            <div class="rtrs-circle">
                <div class="rtrs-circle-bar">
                    <svg>
                        <circle cx="70" cy="70" r="80" style="stroke-dashoffset: calc(490 - (490 * <?php echo esc_attr( $avg_rating*20 ); ?>)/100);"></circle>
                        <circle cx="70" cy="70" r="80" style="stroke-dashoffset: calc(490 - (490 * <?php echo esc_attr( $avg_rating*20 ); ?>)/100);"></circle>
                    </svg>
                </div>
                <div class="rtrs-circle-content">
                    <div class="rating-text"><?php esc_html_e( 'OVERALL', 'review-schema' ); ?></div>
                    <div class="rating-percent"><?php echo esc_html( $avg_rating*20 ); ?>%</div>
                    <div class="rating-icon">
                        <?php echo Functions::review_stars( $avg_rating ); ?>
                    </div>
                </div> 
            </div>
        </div>
        <?php } 
        
        $criteria = ( isset( $p_meta['criteria'] ) && $p_meta['criteria'][0] == 'multi' ); 
        if ( $criteria ) {
        ?> 
        <div class="rtrs-rating-item">
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
        <?php } ?>
    </div>
</div>