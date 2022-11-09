<?php
$rand     = rand();
$unique_class  = " rtcl-gb-unique-class-$rand ";

$slider_arow_dot_style = "rtcl-gb-slider-pagination-style-" . $settings['slider_options']['dotStyle'];
$slider_arow_dot_style .= " rtcl-gb-slider-btn-style-" . $settings['slider_options']['arrowPosition'];

$wrap_class = '';
if (isset($settings['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $settings['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($settings['className'])) {
	$wrap_class .= $settings['className'];
}
if (isset($settings['align'])) {
	$wrap_class .= ' align' . $settings['align'] . ' ';
}

$cssstyle = null;
$margin_right = absint($settings['slider_options']['spaceBetween']);
// css variable for jumping issue
// Jumping Issue Reduce
if (!empty($settings['col_xl'])) {
	$width   = 100 / ($settings['col_xl'] ? $settings['col_xl'] : 1);
	$cssstyle .= "--xl-width: calc( {$width}% - {$margin_right}px );";
}
if (!empty($settings['col_lg'])) {
	$width   = 100 / ($settings['col_lg'] ? $settings['col_lg'] : 1);
	$cssstyle .= "--lg-width:calc( {$width}% - {$margin_right}px );";
}
if (!empty($settings['col_md'])) {
	$width   = 100 / ($settings['col_md'] ? $settings['col_md'] : 1);
	$cssstyle .= "--md-width:calc( {$width}% - {$margin_right}px );";
}
if (!empty($settings['col_sm'])) {
	$width   = 100 / ($settings['col_sm'] ? $settings['col_sm'] : 1);
	$cssstyle .= "--sm-width:calc( {$width}% - {$margin_right}px );";
}
if (!empty($settings['col_mobile'])) {
	$width   = 100 / ($settings['col_mobile'] ? $settings['col_mobile'] : 1);
	$cssstyle .= "--mb-width:calc( {$width}% - {$margin_right}px );";
}
if (isset($settings['slider_options']['spaceBetween'])) {
	$cssstyle .= '--margin-right: ' . $margin_right . 'px;';
	$cssstyle .= '--nagative-margin-right: -' . $margin_right . 'px;';
}
?>

<div class="<?php echo esc_attr($wrap_class); ?>">
	<div class="rtcl rtcl-gb-block rtcl-gb-slider-main-wrap <?php echo esc_html($unique_class); ?>">

		<?php
		$auto_height    = $settings['slider_options']['autoHeight'] ? $settings['slider_options']['autoHeight'] : '0';
		$loop           = $settings['slider_options']['loop'] ? $settings['slider_options']['loop'] : '0';
		$autoplay       = $settings['slider_options']['autoPlay'] ? $settings['slider_options']['autoPlay'] : '0';
		$stop_on_hover  = $settings['slider_options']['stopOnHover'] ? $settings['slider_options']['stopOnHover'] : '0';
		$delay          = $settings['slider_options']['autoPlayDelay'] ? $settings['slider_options']['autoPlayDelay'] : '5000';
		$autoplay_speed = $settings['slider_options']['autoPlaySlideSpeed'] ? $settings['slider_options']['autoPlaySlideSpeed'] : '200';

		$dots = $settings['slider_options']['dotNavigation'] ? $settings['slider_options']['dotNavigation'] : '0';
		$nav  = $settings['slider_options']['arrowNavigation'] ? $settings['slider_options']['arrowNavigation'] : '0';
		$space_between = isset($settings['slider_options']['spaceBetween']) ? $settings['slider_options']['spaceBetween'] : '20';

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
			'slidesPerView'  => absint($settings['col_mobile']),
			'slidesPerGroup' => absint($settings['col_mobile']),
		);
		$break_575  = array(
			'slidesPerView'  => absint($settings['col_sm']),
			'slidesPerGroup' => absint($settings['col_sm']),
		);
		$break_767  = array(
			'slidesPerView'  => absint($settings['col_md']),
			'slidesPerGroup' => absint($settings['col_md']),
		);
		$break_991  = array(
			'slidesPerView'  => absint($settings['col_lg']),
			'slidesPerGroup' => absint($settings['col_lg']),
		);
		$break_1199 = array(
			'slidesPerView'  => absint($settings['col_xl']),
			'slidesPerGroup' => absint($settings['col_xl']),
		);

		$swiper_data = array(
			// Optional parameters
			'slidesPerView'  => absint($settings['col_xl']),
			'slidesPerGroup' => absint($settings['col_xl']),
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

		<div class="swiper rtcl-gb-cat-wrap rtcl-carousel-slider <?php echo esc_attr($slider_arow_dot_style); ?>" data-options="<?php echo esc_attr($swiper_data); ?>" style="<?php echo esc_attr($cssstyle); ?>">
			<?php if (isset($settings['slider_options']['sliderLoader']) && $settings['slider_options']['sliderLoader']) :  ?>
				<div class="rtcl-swiper-lazy-preloader">
					<svg class="spinner" viewBox="0 0 50 50">
						<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
					</svg>
				</div>
			<?php endif; ?>
			<div class="swiper-wrapper">