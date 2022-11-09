<?php

/**
 * Main Gutenberg PricingTable Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace RtclPro\Controllers\Blocks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\BlockFns;
use Rtcl\Helpers\Link;

class PricingTable
{
	protected $name = 'rtcl/pricing-table';

	protected $attributes = [];


	public function get_attributes($default = false)
	{
		$attributes = array(
			'blockId'      => array(
				'type'    => 'string',
				'default' => '',
			),

			"style" => array(
				"type" => "string",
				"default" => "1"
			),
			'title' => array(
				'type'    => 'string',
				'default' => 'Combo Bundle',
			),
			'currency' => array(
				'type'    => 'string',
				'default' => '$',
			),
			'currency_position' => array(
				'type'    => 'string',
				'default' => 'left',
			),
			'price' => array(
				'type'    => 'string',
				'default' => '0',
			),
			'unit' => array(
				'type'    => 'string',
				'default' => 'mo',
			),
			'badge' => array(
				'type'    => 'string',
				'default' => '',
			),
			'box_icon' => array(
				'type'    => 'string',
				'default' => 'paper-plane',
			),
			'content_alignment' => array(
				'type'    => 'string',
				'default' => 'center',
			),

			'btntext' => array(
				'type'    => 'string',
				'default' => 'Buy now',
			),
			'btntype' => array(
				'type'    => 'string',
				'default' => 'custom',
			),
			'buttonurl' => array(
				'type'    => 'string',
				'default' => '#',
			),
			'page_link' => array(
				'type'    => 'string',
				'default' => '',
			),
			'button_icon' => array(
				'type'    => 'string',
				'default' => '',
			),
			'button_icon_size' => array(
				'type'    => 'number',
				'default' => 15,
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a svg { width:{{button_icon_size}}px; }'
				]]

			),
			'button_icon_gap' => array(
				'type'    => 'number',
				'default' => 8,
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a { gap:{{button_icon_gap}}px; }'
				]]
			),

			'features' => [
				'type' => "array",
				'default' => [
					[
						"icon" => "check",
						"text" => "3 Regular Ads ",
						"color" => "#03bb89"
					],
					[
						"icon" => "check",
						"text" => "No Featured Ads",
						"color" => "#03bb89"
					],
					[
						"icon" => "check",
						"text" => "No Ads will be bumped up",
						"color" => "#03bb89"
					],
					[
						"icon" => "check",
						"text" => "Limited Support ",
						"color" => "#03bb89"
					]
				],
			],
			"header_spacing2" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-2 .pricing-header
				 {padding:{{header_spacing2}};}']]
			),
			"header_bg_color2" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-2 .pricing-header{ background-color:{{header_bg_color2}}; }'
				]]
			),

			"body_spacing2" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-2 .pricing-body
				 {padding:{{body_spacing2}};}']]
			),

			"header_icon_color3" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-3 .box-icon svg{ fill:{{header_icon_color3}}; }'
				]]
			),
			"header_icon_bg_color3" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-3 .box-icon:before,
					{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-3 .box-icon:after { background-color:{{header_icon_bg_color3}}; }'
				]]
			),
			"header_icon_size3" => array(
				"type" => "number",
				"default" => 36,
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-3 .box-icon svg{ width:{{header_icon_size3}}px; }'
				]]
			),

			"title_spacing" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-pricing-title
				 {margin:{{title_spacing}};}']]
			),

			'title_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '22', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-1 .rtcl-gb-pricing-title'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-2 .rtcl-gb-pricing-title'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-3 .rtcl-gb-pricing-title']
				],
			],
			"title_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-1 .rtcl-gb-pricing-title { color:{{title_color}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-2 .rtcl-gb-pricing-title { color:{{title_color}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box.rtcl-gb-pricing-box-view-3 .rtcl-gb-pricing-title { color:{{title_color}}; }']
				]
			),

			'badge_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '14', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-label']
				],
			],
			"badge_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-label { color:{{badge_color}}; }'
				]]
			),
			"badge_bg_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-label { background-color:{{badge_bg_color}}; }'
				]]
			),

			"price_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-price { color:{{price_color}}; }'
				]]
			),
			"price_spacing" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-pricing-price
				 {margin:{{price_spacing}};}']]

			),
			'price_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '48', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '48', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-pricing-price .rtcl-gb-price']
				],
			],
			'currency_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-pricing-price .rtcl-gb-pricing-currency']
				],
			],
			'unit_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-pricing-price .rtcl-gb-pricing-duration']
				],
			],
			"unit_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box .rtcl-gb-pricing-price .rtcl-gb-pricing-duration { color:{{unit_color}}; }'
				]]
			),

			"feature_spacing" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box  .rtcl-gb-pricing-features
				 {margin:{{feature_spacing}};}']]
			),

			'feature_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '32', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box  .rtcl-gb-pricing-features li']
				],
			],
			"feature_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box  .rtcl-gb-pricing-features li { color:{{feature_color}}; }'
				]]
			),
			"feature_icon_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box  .rtcl-gb-pricing-features li svg { fill:{{feature_icon_color}}; }'
				]]
			),
			"feature_icon_size" => array(
				"type" => "number",
				"default" => 15,
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box  .rtcl-gb-pricing-features li svg { width:{{feature_icon_size}}px; }'
				]]
			),
			"feature_icon_gap" => array(
				"type" => "number",
				"default" => 8,
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-pricing-box  .rtcl-gb-pricing-features li { gap:{{feature_icon_gap}}px; }'
				]]
			),

			"button_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {padding:{{button_padding}};}']]
			),
			"button_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {margin:{{button_margin}};}']]
			),
			"button_width" => array(
				"type" => "number",
				"default" => '',
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {min-width:{{button_width}}px;}']]
			),

			'button_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '14', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'Capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a']
				],
			],
			"button_color_style" => array(
				"type" => "string",
				"default" => "normal"
			),
			"button_bg_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {background-color:{{button_bg_color}};}']]
			),
			"button_text_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {color:{{button_text_color}};}']]
			),
			"button_border_type" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {border-style:{{button_border_type}};}']]
			),
			"button_border" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {border-width:{{button_border}};}']]
			),
			"button_border_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a {border-color:{{button_border_color}};}']]
			),
			"button_border_radius" => array(
				"type" => "object",
				'default' => (object)['top' => '2', 'bottom' => '2', 'left' => '2', 'right' => '2', 'unit' => 'px'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a { border-radius:{{button_border_radius}}; }'
					]
				]
			),

			"hv_button_bg_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a:hover {background-color:{{hv_button_bg_color}};}']]
			),
			"hv_button_text_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a:hover {color:{{hv_button_text_color}};}']]
			),
			"hv_button_border_type" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a:hover {border-style:{{hv_button_border_type}};}']]
			),
			"hv_button_border" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a:hover {border-width:{{hv_button_border}};}']]
			),
			"hv_button_border_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box .pricing-footer .rtcl-gb-pricing-button a:hover {border-color:{{hv_button_border_color}};}']]
			),

			"container_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box
				 {padding:{{container_padding}} !important;}']]
			),
			"container_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box
				 {margin:{{container_margin}} !important;}']]
			),
			"containerBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box
				 {background-color:{{containerBGColor}} !important;}']]
			),
			"container_border_type" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box {border-style:{{container_border_type}};}']]
			),
			"container_border" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box {border-width:{{container_border}};}']]
			),
			"container_border_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-pricing-box {border-color:{{container_border_color}};}']]
			),
			"container_border_radius" => array(
				"type" => "object",
				'default' => (object)['top' => '', 'bottom' => '', 'left' => '', 'right' => '', 'unit' => 'px'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-pricing-box { border-radius:{{container_border_radius}}; }'
					]
				]
			),

		);

		if ($default) {
			$temp = [];
			foreach ($attributes as $key => $value) {
				if (isset($value['default'])) {
					$temp[$key] = $value['default'];
				}
			}
			return $temp;
		} else {
			return $attributes;
		}
	}

	public function __construct()
	{
		add_action('init', [$this, 'register_listing_serch_form']);

		add_action('wp_ajax_rtcl_gb_pricing_table_post', [$this, 'rtcl_gb_pricing_table_post']);
		add_action('wp_ajax_nopriv_rtcl_gb_pricing_table_post', [$this, 'rtcl_gb_pricing_table_post']);
	}

	public function rtcl_gb_pricing_table_post()
	{

		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$args           = array(
			'post_type'        => 'rtcl_pricing',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_status'      => 'publish',
			'meta_query' => array(
				array(
					'key'     => 'pricing_type',
					'value'   => 'membership',
					'compare' => '=',
				),
			),
		);
		$posts          = get_posts($args);
		$posts_dropdown = array('0' => __('--Select--', 'classified-listing-pro'));
		if (!is_wp_error($posts)) {
			foreach ($posts as $post) {
				$pricing = rtcl()->factory->get_pricing($post->ID);
				$url     = add_query_arg('option', $pricing->getId(), Link::get_checkout_endpoint_url('membership'));
				$posts_dropdown[$url] = $post->post_title;
				//$posts_dropdown[$post->ID] = $post->post_title;
			}
		}

		if (!empty($posts_dropdown)) :
			wp_send_json($posts_dropdown);
		endif;

		wp_reset_postdata();
		wp_die();
	}


	public function register_listing_serch_form()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/pricing-table',
			[
				'render_callback' => [$this, 'render_callback_search'],
				'attributes' => $this->get_attributes(),
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $settings settings.
	 * @return string
	 */
	public function button($settings)
	{
		$btn  = '';
		$attr = '';
		$button_icon = '';

		if (!empty($settings['button_icon'])) {
			ob_start();
			BlockFns::render_svg_html($settings['button_icon']);
			$button_icon          = ob_get_clean();
		}

		if ($settings['btntype'] == 'page') {
			$url = '#';
			if (!empty($settings['page_link'])) {
				$url     = $settings['page_link'];
			}
			$attr = 'href="' . $url . '"';
		} else {
			if (!empty($settings['buttonurl'])) {
				$attr  = 'href="' . $settings['buttonurl'] . '"';
			}
		}

		if ($settings['btntext']) {
			$btn = '<a ' . $attr . '>' . $settings['btntext'] . $button_icon . '</a>';
		}
		return $btn;
	}
	/**
	 * Return all feature list.
	 *
	 * @param [type] $settings main settings.
	 * @return mixed
	 */
	public function feature_html($settings)
	{
		$feature_html = null;
		$features_list = $settings['features'];
		if (!empty($features_list)) {
			foreach ($features_list as $feature) {
				if (!empty($feature)) {
					ob_start();
					BlockFns::render_svg_html($feature['icon']);
					$icon          = ob_get_clean();
					$feature_html .= '<li>' . $icon . $feature['text'] . '</li>';
				}
			}
		}

		if ($feature_html) {
			$feature_html = '<ul>' . $feature_html . '</ul>';
		}
		return $feature_html;
	}

	public function render_callback_search($attributes)
	{
		$settings = $attributes;
		$style    = isset($settings['style']) ? $settings['style'] : '1';

		ob_start();
		if ('3' === $style) {
			BlockFns::render_svg_html($settings['box_icon']);
		}
		$box_icon = ob_get_clean();

		$pricing_label     = !empty($settings['badge']) ? $settings['badge'] : null;
		$content_alignment = !empty($settings['content_alignment']) ? $settings['content_alignment'] : 'center';
		$currency_position = 'right' === $settings['currency_position'] ? 'currency-right' : 'currency-left';
		$template_style    = 'block/pricing-table/view-' . $style;
		$data              = array(
			'template'              => $template_style,
			'style'                 => $style,
			'settings'              => $settings,
			'feature_html'          => $this->feature_html($settings),
			'btn'                   => $this->button($settings),
			'pricing_label'         => $pricing_label,
			'content_alignment'     => $content_alignment,
			'currency_position'     => $currency_position,
			'default_template_path' => rtclPro()->get_plugin_template_path(),
		);
		$data['box_icon']  = $box_icon;
		$data = apply_filters('rtcl_gb_pricint_table_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
