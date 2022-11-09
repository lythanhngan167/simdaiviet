<?php
/**
 * @author    RadiusTheme
 * @version       1.0.0
 */

use Rtcl\Helpers\Functions;

?>
<?php
$style_number = 1;
if ( 'style-2' == $style ) {
	$style_number = 2;
}

$cssstyle = null;
$rand     = rand();

$classes = " rtin-unique-class-$rand ";
if ( $settings['slider_dots'] ) {
	$classes .= ' rtcl-slider-pagination-' . $settings['rtcl_button_dot_style'];
}
if ( $settings['slider_nav'] ) {
	$classes .= ' rtcl-slider-btn-' . $settings['rtcl_button_arrow_style'];
}
$classes .= $settings['rtcl_cat_box_alignment'] ? ' cat-box-' . $settings['rtcl_cat_box_alignment'] . '-alignment' : '';
$classes .= ' rt-el-listing-cat-box-' . $style_number;




if ( $settings['slider_dots'] ) {
	$classes .= ' rtcl-slider-pagination-' . $settings['rtcl_button_dot_style'];
}
if ( $settings['slider_nav'] ) {
	$classes .= ' rtcl-slider-btn-' . $settings['rtcl_button_arrow_style'];
}
// if ( $settings['slider_rtl'] ) {
// $classes .= ' rtcl-slider-rtl';
// }
// slider_rtl
$margin_right = absint( $settings['slider_space_between'] );

// css variable for jumping issue
// Jumping Issue Reduce
if ( ! empty( $settings['rtcl_col_xl'] ) ) {
	$width     = 100 / ( $settings['rtcl_col_xl'] ? $settings['rtcl_col_xl'] : 1 );
	$cssstyle .= "--xl-width: calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $settings['rtcl_col_lg'] ) ) {
	$width     = 100 / ( $settings['rtcl_col_lg'] ? $settings['rtcl_col_lg'] : 1 );
	$cssstyle .= "--lg-width:calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $settings['rtcl_col_md'] ) ) {
	$width     = 100 / ( $settings['rtcl_col_md'] ? $settings['rtcl_col_md'] : 1 );
	$cssstyle .= "--md-width:calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $settings['rtcl_col_sm'] ) ) {
	$width     = 100 / ( $settings['rtcl_col_sm'] ? $settings['rtcl_col_sm'] : 1 );
	$cssstyle .= "--sm-width:calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $settings['rtcl_col_mobile'] ) ) {
	$width     = 100 / ( $settings['rtcl_col_mobile'] ? $settings['rtcl_col_mobile'] : 1 );
	$cssstyle .= "--mb-width:calc( {$width}% - {$margin_right}px );";
}
if ( isset( $settings['slider_space_between'] ) ) {
	$cssstyle .= '--margin-right: ' . $margin_right . 'px;';
	$cssstyle .= '--nagative-margin-right: -' . $margin_right . 'px;';
}

?>
<div class="rtcl rtcl-categories-elementor rtcl-categories-slider rtcl-categories rtcl-categories-grid rtcl-el-slider-wrapper <?php echo esc_html( $classes ); ?>" style="<?php echo esc_attr( $cssstyle ); ?>">
		<?php
			$auto_height    = $settings['rtcl_auto_height'] ? $settings['rtcl_auto_height'] : '0';
			$loop           = $settings['slider_loop'] ? $settings['slider_loop'] : '0';
			$autoplay       = $settings['slider_autoplay'] ? $settings['slider_autoplay'] : '0';
			$stop_on_hover  = $settings['slider_stop_on_hover'] ? $settings['slider_stop_on_hover'] : '0';
			$delay          = $settings['slider_delay'] ? $settings['slider_delay'] : '5000';
			$autoplay_speed = $settings['slider_autoplay_speed'] ? $settings['slider_autoplay_speed'] : '2000';
			$dots           = $settings['slider_dots'] ? $settings['slider_dots'] : '0';
			$nav            = $settings['slider_nav'] ? $settings['slider_nav'] : '0';
			$space_between  = isset( $settings['slider_space_between'] ) ? $settings['slider_space_between'] : '20';

			$autoplay   = boolval( $autoplay ) ? array(
				'delay' => absint( $delay ),
				'pauseOnMouseEnter' => boolval( $stop_on_hover ),
				'disableOnInteraction' => false,
			) : boolval( $autoplay );
			$navigation = boolval( $nav ) ? array(
				'nextEl' => ".rtin-unique-class-$rand .button-right",
				'prevEl' => ".rtin-unique-class-$rand .button-left",
			) : boolval( $nav );
			$pagination = boolval( $dots ) ? array(
				'el'        => ".rtin-unique-class-$rand .rtcl-slider-pagination",
				'clickable' => true,
				'type'      => 'bullets',
			) : boolval( $dots );

			$swiper_data = array(
				'slidesPerView'  => absint( $settings['rtcl_col_xl'] ),
				'slidesPerGroup' => absint( $settings['rtcl_col_xl'] ),
				'spaceBetween'   => absint( $space_between ),
				'loop'           => boolval( $loop ),
				'autoplay'       => $autoplay,
				'speed'          => absint( $autoplay_speed ),
				'navigation'     => $navigation,
				'pagination'     => $pagination,
				'autoHeight'     => boolval( $auto_height ),
				'breakpoints'    => array(
					0    => array(
						'slidesPerView'  => absint( $settings['rtcl_col_mobile'] ),
						'slidesPerGroup' => absint( $settings['rtcl_col_mobile'] ),
					),
					575  => array(
						'slidesPerView'  => absint( $settings['rtcl_col_sm'] ),
						'slidesPerGroup' => absint( $settings['rtcl_col_sm'] ),
					),
					767  => array(
						'slidesPerView'  => absint( $settings['rtcl_col_md'] ),
						'slidesPerGroup' => absint( $settings['rtcl_col_md'] ),
					),
					991  => array(
						'slidesPerView'  => absint( $settings['rtcl_col_lg'] ),
						'slidesPerGroup' => absint( $settings['rtcl_col_lg'] ),
					),
					1199 => array(
						'slidesPerView'  => absint( $settings['rtcl_col_xl'] ),
						'slidesPerGroup' => absint( $settings['rtcl_col_xl'] ),
					),
				),
			);
			$swiper_data = wp_json_encode( $swiper_data );
			?>

		<div class="rtcl-listing-category-slider rtcl-carousel-slider swiper" data-options="<?php echo esc_attr( $swiper_data ); ?>" <?php // echo $rtl ? ' dir="rtl" ' : ''; ?>>
			<div class="rtcl-swiper-lazy-preloader">
				<svg class="spinner" viewBox="0 0 50 50">
					<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
				</svg>
			</div>
			<div class="swiper-wrapper">
