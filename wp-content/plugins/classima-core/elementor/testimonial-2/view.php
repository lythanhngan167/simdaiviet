<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-testimonial-2 rt-el-testimonial-nav">
	<div class="rtcl-carousel-slider" data-options="<?php echo esc_attr( $data['owl_data'] );?>">
        <div class="swiper-wrapper">
            <?php foreach ( $data['items'] as $item ): ?>
                <div class="swiper-slide">
                    <div class="rtin-item">
                        <p class="rtin-content"><?php echo esc_html( $item['content'] );?></p>
                        <?php if ( $item['image'] ): ?>
                            <div class="rtin-thumb"><?php echo wp_get_attachment_image( $item['image']['id'], 'thumbnail' );?></div>
                        <?php endif; ?>
                        <h3 class="rtin-name"><?php echo esc_html( $item['name'] );?></h3>
                        <?php if ( $item['designation'] ): ?>
                            <div class="rtin-designation"><?php echo esc_html( $item['designation'] );?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
	</div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>