<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

$cssstyle = null;
$rand     = rand();
$classes  = " rtin-unique-class-$rand ";
if ( $instance['slider_dots'] ) {
	$classes .= ' rtcl-slider-pagination-' . $instance['rtcl_button_dot_style'];
}
if ( $instance['slider_nav'] ) {
	$classes .= ' rtcl-slider-btn-' . $instance['rtcl_button_arrow_style'];
}
// if ( $instance['slider_rtl'] ) {
// $classes .= ' rtcl-slider-rtl';
// }
// slider_rtl
$margin_right = absint( $instance['slider_space_between'] );

// css variable for jumping issue
// Jumping Issue Reduce
if ( ! empty( $instance['rtcl_col_xl'] ) ) {
	$width   = 100 / ( $instance['rtcl_col_xl'] ? $instance['rtcl_col_xl'] : 1 );
	$cssstyle .= "--xl-width: calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $instance['rtcl_col_lg'] ) ) {
	$width   = 100 / ( $instance['rtcl_col_lg'] ? $instance['rtcl_col_lg'] : 1 );
	$cssstyle .= "--lg-width:calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $instance['rtcl_col_md'] ) ) {
	$width   = 100 / ( $instance['rtcl_col_md'] ? $instance['rtcl_col_md'] : 1 );
	$cssstyle .= "--md-width:calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $instance['rtcl_col_sm'] ) ) {
	$width   = 100 / ( $instance['rtcl_col_sm'] ? $instance['rtcl_col_sm'] : 1 );
	$cssstyle .= "--sm-width:calc( {$width}% - {$margin_right}px );";
}
if ( ! empty( $instance['rtcl_col_mobile'] ) ) {
	$width   = 100 / ( $instance['rtcl_col_mobile'] ? $instance['rtcl_col_mobile'] : 1 );
	$cssstyle .= "--mb-width:calc( {$width}% - {$margin_right}px );";
}
if ( isset( $instance['slider_space_between'] ) ) {
	$cssstyle .= '--margin-right: ' . $margin_right . 'px;';
	$cssstyle .= '--nagative-margin-right: -' . $margin_right . 'px;';
}

?>

<div class="rtcl rtcl-listings-sc-wrapper rtcl-elementor-widget rtcl-el-slider-wrapper rtcl-listings-slider <?php echo esc_html( $classes ); ?>" style="<?php echo esc_attr( $cssstyle ); ?>">
	<div class="rtcl-listings-wrapper">

		<?php
		$class  = '';
		$class .= ! empty( $view ) ? 'rtcl-' . $view . '-view ' : 'rtcl-list-view ';
		$class .= ! empty( $style ) ? 'rtcl-' . $style . '-view ' : 'rtcl-style-1-view ';
		?>
		<?php
			$auto_height    = $instance['rtcl_auto_height'] ? $instance['rtcl_auto_height'] : '0';
			$loop           = $instance['slider_loop'] ? $instance['slider_loop'] : '0';
			$autoplay       = $instance['slider_autoplay'] ? $instance['slider_autoplay'] : '0';
			$stop_on_hover  = $instance['slider_stop_on_hover'] ? $instance['slider_stop_on_hover'] : '0';
			$delay          = $instance['slider_delay'] ? $instance['slider_delay'] : '5000';
			$autoplay_speed = $instance['slider_autoplay_speed'] ? $instance['slider_autoplay_speed'] : '200';
			// $per_group      = $instance['slide_per_group'] ? $instance['slide_per_group'] : '1';
			$dots = $instance['slider_dots'] ? $instance['slider_dots'] : '0';
			$nav  = $instance['slider_nav'] ? $instance['slider_nav'] : '0';
			// $rtl           = $instance['slider_rtl'] ? $instance['slider_rtl'] : '0';
			$space_between = isset( $instance['slider_space_between'] ) ? $instance['slider_space_between'] : '20';

			$autoplay   = boolval( $autoplay ) ? array(
				'delay' => absint( $delay ),
				'pauseOnMouseEnter' => boolval( $stop_on_hover ),
				'disableOnInteraction' => false,
			) : boolval( $autoplay );
			$pagination = boolval( $dots ) ? array(
				'el'        => ".rtin-unique-class-$rand .rtcl-slider-pagination",
				'clickable' => true,
				'type'      => 'bullets',
			) : boolval( $dots );
			$navigation = boolval( $nav ) ? array(
				'nextEl' => ".rtin-unique-class-$rand .button-right",
				'prevEl' => ".rtin-unique-class-$rand .button-left",
			) : boolval( $nav );
			$break_0    = array(
				'slidesPerView'  => absint( $instance['rtcl_col_mobile'] ),
				'slidesPerGroup' => absint( $instance['rtcl_col_mobile'] ),
			);
			$break_575  = array(
				'slidesPerView'  => absint( $instance['rtcl_col_sm'] ),
				'slidesPerGroup' => absint( $instance['rtcl_col_sm'] ),
			);
			$break_767  = array(
				'slidesPerView'  => absint( $instance['rtcl_col_md'] ),
				'slidesPerGroup' => absint( $instance['rtcl_col_md'] ),
			);
			$break_991  = array(
				'slidesPerView'  => absint( $instance['rtcl_col_lg'] ),
				'slidesPerGroup' => absint( $instance['rtcl_col_lg'] ),
			);
			$break_1199 = array(
				'slidesPerView'  => absint( $instance['rtcl_col_xl'] ),
				'slidesPerGroup' => absint( $instance['rtcl_col_xl'] ),
			);

			$swiper_data = array(
				// Optional parameters
				'slidesPerView'  => absint( $instance['rtcl_col_xl'] ),
				'slidesPerGroup' => absint( $instance['rtcl_col_xl'] ),
				'spaceBetween'   => absint( $space_between ),
				'loop'           => boolval( $loop ),
				// If we need pagination
				'slideClass'     => 'swiper-slide-customize',
				'autoplay'       => $autoplay,
				// If we need pagination
				'pagination'     => $pagination,
				'speed'          => absint( $autoplay_speed ),
				// allowTouchMove: true,
				// Navigation arrows
				'navigation'     => $navigation,
				'autoHeight'     => boolval( $auto_height ),
				'breakpoints'    => array(
					0    => $break_0,
					575  => $break_575,
					767  => $break_767,
					991  => $break_991,
					1199 => $break_1199,
				),
			);
			$swiper_data = wp_json_encode( $swiper_data );


			?>
		<div class="rtcl-listings rtcl-listings-slider-container swiper <?php echo esc_attr( $class ); ?> rtcl-carousel-slider " data-options="<?php echo esc_attr( $swiper_data ); ?>"  <?php // echo $rtl ? ' dir="rtl" ' : ''; ?> >
		<div class="rtcl-swiper-lazy-preloader">
			<svg class="spinner" viewBox="0 0 50 50">
				<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
			</svg>
		</div>
		<div class="swiper-wrapper">
