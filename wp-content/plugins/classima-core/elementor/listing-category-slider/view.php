<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

if ( !$data['rt_results'] ) {
	return;
}
?>
<div class="rt-el-listing-cat-slider slider-nav-enabled rtin-<?php echo esc_attr( $data['theme'] );?>">
	<div class="rtcl-carousel-slider" data-options="<?php echo esc_attr( $data['owl_data'] );?>">
        <div class="swiper-wrapper">
            <?php foreach ( $data['rt_results'] as $result ): ?>
                <div class="swiper-slide">
                    <a class="rtin-item" href="<?php echo esc_attr( $result['permalink'] );?>">
		                <?php if ( $result['icon_html'] ): ?>
                            <div class="rtin-icon"><?php echo $result['icon_html'];?></div>
		                <?php endif; ?>
                        <div class="rtin-title"><?php echo esc_html( $result['name'] );?></div>
		                <?php if ( $data['count'] ): ?>
                            <div class="rtin-count">(<?php echo esc_html( $result['count'] );?>)</div>
		                <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
	</div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>