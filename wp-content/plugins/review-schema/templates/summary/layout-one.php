<?php  
/**
 * Review summary summary layout one template
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
<div class="rtrs-summary"> 
    <?php if ( $avg_rating = Review::getAvgRatings( get_the_ID() ) ) { ?>
    <div class="rtrs-summary-box">
        <div class="rtrs-rating-box">
            <div class="rtrs-rating-number">
                <span class="rtrs-rating"><?php echo esc_html( $avg_rating ); ?></span>
                <span class="rtrs-rating-out">/5</span>
            </div>
            <div class="rtrs-rating-icon">
                <?php echo Functions::review_stars( $avg_rating ); ?>
                <div class="rtrs-rating-text"> 
                    <?php 
                        printf(
                            esc_html( _n( 'Based on %s rating', 'Based on %s ratings', $total_rating, 'review-schema' ) ), 
                            esc_html( $total_rating ) 
                        ); 
                    ?> 
                </div>
            </div>
        </div>
    </div>
    <?php } 
    
    if ( isset( $p_meta['recommendation'] ) && $p_meta['recommendation'][0] == '1' ) {
    ?>
    <div class="rtrs-summary-box">
        <div class="rtrs-rating-box">
            <div class="rtrs-recomnded-icon">
                <i class="rtrs-thumbs-up"></i>
            </div>
            <div class="rtrs-recomnded-content">
                <span class="rtrs-recomnded-number"> 
                    <?php 
                        $total_recommended = Review::getTotalRecommendation( get_the_ID() );
                        printf(
                            wp_kses( _n( '<span>%s</span>User', '<span>%s</span>Users', $total_recommended, 'review-schema' ), array( 'span' => array() ) ), 
                            esc_html( $total_recommended ) 
                        ); 
                    ?> 
                </span>
                <p class="rtrs-recomnded-text"><?php echo esc_html_e( 'Recommended this item', 'review-schema' ); ?></p>
            </div>
        </div>
    </div> 
    <?php 
    } //end recommendation

    if ( isset( $p_meta['criteria'] ) && $p_meta['criteria'][0] == 'multi' ) {
    ?> 
    <div class="rtrs-summary-box">
        <div class="rtrs-progress-wrap">
            <?php foreach( Review::getCriteriaAvgRatings( get_the_ID() ) as $value ) {
                if ( !$value['avg'] ) continue;
                ?>  
                <div class="rtrs-progress">
                    <label><?php echo esc_html($value['title']); ?></label>
                    <progress class="rtrs-progress-bar service-preogress" value="<?php echo esc_html($value['avg']*20); ?>" max="100"></progress>
                    <span class="progress-percent"><?php echo esc_html($value['avg']*20); ?>%</span>
                </div> 
            <?php } ?>  
        </div>
    </div> 
    <?php } ?>
</div>