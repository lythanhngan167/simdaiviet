<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;

$layout = $data['style'];
$display = array(
	'cat'   => $data['cat_display'] ? true : false,
    'views'   => $data['views_display'] ? true : false,
    'fields'   => $data['field_display']==='yes' ? true : false,
	'type'      => $data['type_display']==='yes' ? true : false,
	'label' => false,
);

$query = $data['query'];
?>
<div class="rt-el-listing-slider owl-wrap rtin-<?php echo esc_attr( $data['style'] );?>" style="<?php echo esc_attr( $data['css_style'] ); ?>">
	<div class="owl-custom-nav-area">
		<h3 class="owl-custom-nav-title"><?php echo esc_html( $data['sec_title'] );?></h3>
		<div class="owl-custom-nav rtin-custom-nav-<?php echo esc_attr( $data['rand'] );?>">
			<div class="owl-prev"><i class="fa fa-angle-left"></i></div><div class="owl-next"><i class="fa fa-angle-right"></i></div>
		</div>
	</div>
	<?php if ( $query->have_posts() ) :?>
        <div class="rtcl-carousel-slider" data-options="<?php echo esc_attr( $data['owl_data'] ); ?>">
            <div class="swiper-wrapper">
                <?php while ( $query->have_posts() ) : $query->the_post();?>
                    <?php Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) );?>
                <?php endwhile;?>
            </div>
        </div>
	<?php endif;?>
	<?php wp_reset_postdata(); ?>
</div>