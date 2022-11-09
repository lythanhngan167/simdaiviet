<?php
$rand     = rand();
$unique_class  = " rtcl-gb-unique-class-$rand ";

$slider_arow_dot_style = "rtcl-gb-slider-pagination-style-" . $instance['slider_options']['dotStyle'];
$slider_arow_dot_style .= " rtcl-gb-slider-btn-style-" . $instance['slider_options']['arrowPosition'];

$wrap_class = '';
if (isset($instance['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $instance['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($instance['className'])) {
	$wrap_class .= $instance['className'];
}
$cssstyle = null;
$class  = '';
$class .= 'rtcl-gb-grid-view';
$class .=  " rtcl-gb-slider-pagination-style-" . $instance['slider_options']['dotStyle'];
$class .=  " rtcl-gb-slider-btn-style-" . $instance['slider_options']['arrowPosition'];
$class .= !empty($style) ? ' rtcl-gb-grid-style-' . $style : 'rtcl-gb-grid-style-1';

$margin_right = absint($instance['slider_options']['spaceBetween']);
// css variable for jumping issue
// Jumping Issue Reduce
if (!empty($instance['col_xl'])) {
	$width   = 100 / ($instance['col_xl'] ? $instance['col_xl'] : 1);
	$cssstyle .= "--xl-width: calc( {$width}% - {$margin_right}px );";
}
if (!empty($instance['col_lg'])) {
	$width   = 100 / ($instance['col_lg'] ? $instance['col_lg'] : 1);
	$cssstyle .= "--lg-width:calc( {$width}% - {$margin_right}px );";
}
if (!empty($instance['col_md'])) {
	$width   = 100 / ($instance['col_md'] ? $instance['col_md'] : 1);
	$cssstyle .= "--md-width:calc( {$width}% - {$margin_right}px );";
}
if (!empty($instance['col_sm'])) {
	$width   = 100 / ($instance['col_sm'] ? $instance['col_sm'] : 1);
	$cssstyle .= "--sm-width:calc( {$width}% - {$margin_right}px );";
}
if (!empty($instance['col_mobile'])) {
	$width   = 100 / ($instance['col_mobile'] ? $instance['col_mobile'] : 1);
	$cssstyle .= "--mb-width:calc( {$width}% - {$margin_right}px );";
}
if (isset($instance['slider_options']['spaceBetween'])) {
	$cssstyle .= '--margin-right: ' . $margin_right . 'px;';
	$cssstyle .= '--nagative-margin-right: -' . $margin_right . 'px;';
}
?>

<div class="<?php echo esc_attr($wrap_class); ?>">

	<div class="rtcl rtcl-gb-slider-main-wrap rtcl-gb-block <?php echo esc_attr($unique_class); ?>">

		<?php
		$auto_height    = $instance['slider_options']['autoHeight'] ? $instance['slider_options']['autoHeight'] : '0';
		$loop           = $instance['slider_options']['loop'] ? $instance['slider_options']['loop'] : '0';
		$autoplay       = $instance['slider_options']['autoPlay'] ? $instance['slider_options']['autoPlay'] : '0';
		$stop_on_hover  = $instance['slider_options']['stopOnHover'] ? $instance['slider_options']['stopOnHover'] : '0';
		$delay          = $instance['slider_options']['autoPlayDelay'] ? $instance['slider_options']['autoPlayDelay'] : '5000';
		$autoplay_speed = $instance['slider_options']['autoPlaySlideSpeed'] ? $instance['slider_options']['autoPlaySlideSpeed'] : '200';

		$dots = $instance['slider_options']['dotNavigation'] ? $instance['slider_options']['dotNavigation'] : '0';
		$nav  = $instance['slider_options']['arrowNavigation'] ? $instance['slider_options']['arrowNavigation'] : '0';
		$space_between = isset($instance['slider_options']['spaceBetween']) ? $instance['slider_options']['spaceBetween'] : '20';

		$autoplay   = boolval($autoplay) ? array(
			'delay' => absint($delay),
			'pauseOnMouseEnter' => boolval($stop_on_hover),
			'disableOnInteraction' => false,
		) : boolval($autoplay);
		$pagination = boolval($dots) ? array(
			'el'        => ".rtcl-gb-unique-class-$rand .rtcl-gb-slider-pagination",
			'clickable' => true,
			'type'      => 'bullets',
		) : boolval($dots);
		$navigation = boolval($nav) ? array(
			'nextEl' => ".rtcl-gb-unique-class-$rand .button-right",
			'prevEl' => ".rtcl-gb-unique-class-$rand .button-left",
		) : boolval($nav);
		$break_0    = array(
			'slidesPerView'  => absint($instance['col_mobile']),
			'slidesPerGroup' => absint($instance['col_mobile']),
		);
		$break_575  = array(
			'slidesPerView'  => absint($instance['col_sm']),
			'slidesPerGroup' => absint($instance['col_sm']),
		);
		$break_767  = array(
			'slidesPerView'  => absint($instance['col_md']),
			'slidesPerGroup' => absint($instance['col_md']),
		);
		$break_991  = array(
			'slidesPerView'  => absint($instance['col_lg']),
			'slidesPerGroup' => absint($instance['col_lg']),
		);
		$break_1199 = array(
			'slidesPerView'  => absint($instance['col_xl']),
			'slidesPerGroup' => absint($instance['col_xl']),
		);

		$swiper_data = array(
			// Optional parameters
			'slidesPerView'  => absint($instance['col_xl']),
			'slidesPerGroup' => absint($instance['col_xl']),
			'spaceBetween'   => absint($space_between),
			'loop'           => boolval($loop),
			// If we need pagination
			//'slideClass'     => 'swiper-slide-customize',
			'autoplay'       => $autoplay,
			// If we need pagination
			'pagination'     => $pagination,
			'speed'          => absint($autoplay_speed),
			// allowTouchMove: true,
			// Navigation arrows
			'navigation'     => $navigation,
			'autoHeight'     => boolval($auto_height),
			'breakpoints'    => array(
				0    => $break_0,
				575  => $break_575,
				767  => $break_767,
				991  => $break_991,
				1199 => $break_1199,
			),
		);
		$swiper_data = wp_json_encode($swiper_data);
		?>

		<div class="swiper rtcl-carousel-slider <?php echo esc_attr($class); ?>" data-options="<?php echo esc_attr($swiper_data); ?>" style="<?php echo esc_attr($cssstyle); ?>">
			<?php if (isset($instance['slider_options']['sliderLoader']) && $instance['slider_options']['sliderLoader']) :  ?>
				<div class="rtcl-swiper-lazy-preloader">
					<svg class="spinner" viewBox="0 0 50 50">
						<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
					</svg>
				</div>
			<?php endif; ?>
			<div class="swiper-wrapper">