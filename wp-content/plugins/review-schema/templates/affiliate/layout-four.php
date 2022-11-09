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
<div class="rtrs-summary-2 rtrs-summary-3 rtrs-summary-4">
    <?php rtrs()->get_partial_path('title', array('p_meta' => $p_meta)); ?>

    <div class="rtrs-rating-summary">
        <div class="rtrs-rating-item">
            <div class="rtrs-product-img">
                <img src="<?php echo esc_url( $p_meta['image'] ); ?>" alt="">
            </div>
        </div>

        <?php if ( $avg_rating = $p_meta['avg_rating'] ) { ?>
        <div class="rtrs-rating-item">
            <div class="rtrs-circle">
                <div class="rtrs-circle-bar">
                    <svg>
                        <circle cx="70" cy="70" r="80" style="stroke-dashoffset: <?php echo esc_attr( 490 - ( 490 * ( $avg_rating * 20 ) ) / 100 ); ?>;"></circle>
                        <circle cx="70" cy="70" r="80" style="stroke-dashoffset: <?php echo esc_attr( 490 - ( 490 * ( $avg_rating * 20 ) ) / 100 ); ?>;"></circle>
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
        
        if ( $criteria = $p_meta['criteria'] ) {
        ?> 
        <div class="rtrs-rating-item">
            <ul class="rtrs-rating-category"> 
                <?php foreach( $criteria as $value ) {
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
        <?php } ?>
    </div>

    <div class="rtrs-feedback-summary"> 
        <?php    
        if ( $pros = $p_meta['pros'] ) { ?>
        <div class="rtrs-feedback-box">
            <h3 class="rtrs-feedback-title">
                <span class="item-icon like-icon"><i class="rtrs-thumbs-up"></i></span>
                <span class="item-text"><?php esc_html_e( 'Pros', 'review-schema' ); ?></span>
            </h3>
            <ul class="rtrs-feedback-list">
                <?php foreach( $pros as $value ) { ?>
                <li><?php echo esc_html( $value ); ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>

        <?php  
        if ( $cons = $p_meta['cons'] ) { ?>
        <div class="rtrs-feedback-box">
            <h3 class="rtrs-feedback-title">
                <span class="item-icon unlike-icon"><i class="rtrs-thumbs-down"></i></span>
                <span class="item-text"><?php esc_html_e( 'Cons', 'review-schema' ); ?></span>
            </h3>
            <ul class="rtrs-feedback-list">
                <?php foreach( $cons as $value ) { ?>
                <li><?php echo esc_html( $value ); ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    </div>

    <?php rtrs()->get_partial_path('buy-btn', array('p_meta' => $p_meta)); ?>
</div>