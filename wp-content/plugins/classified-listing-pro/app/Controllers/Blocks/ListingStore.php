<?php

/**
 * Main Gutenberg ListingStore Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace RtclPro\Controllers\Blocks;

use Rtcl\Helpers\Functions;

class ListingStore
{
	protected $name = 'rtcl/listing-store';

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
				"default" => "grid"
			),

			"stores" => array(
				"type" => "array",
			),
			"store_cats" => array(
				"type" => "array",
			),
			"store_type" => array(
				"type" => "string",
				"default" => "all",
			),
			"store_limit" => array(
				"type" => "number",
				"default" => 4,
			),
			"orderby" => array(
				"type" => "string",
				"default" => "title",
			),
			"sortby" => array(
				"type" => "string",
				"default" => "ASC",
			),
			// "hide_empty" => array(
			// 	"type" => "boolean",
			// 	"default" => false,
			// ),
			"show_logo" => array(
				"type" => "boolean",
				"default" => true,
			),
			"show_title" => array(
				"type" => "boolean",
				"default" => true,
			),
			"show_count" => array(
				"type" => "boolean",
				"default" => true,
			),
			"count_after_text" => array(
				"type" => "string",
			),
			"show_desc" => array(
				"type" => "boolean",
				"default" => true,
			),
			"desc_limit" => array(
				"type" => "number",
				"default" => 20,
			),

			"col_xl" => array(
				"type" => "string",
				"default" => "3",
			),
			"col_lg" => array(
				"type" => "string",
				"default" => "3",
			),
			"col_md" => array(
				"type" => "string",
				"default" => "4",
			),
			"col_sm" => array(
				"type" => "string",
				"default" => "6",
			),
			"col_mobile" => array(
				"type" => "string",
				"default" => "6",
			),
			"image_size" => array(
				"type" => "string",
				"default" => "rtcl-store-logo",
			),
			"custom_image_width" => array(
				"type" => "number",
				"default" => 400,
			),
			"custom_image_height" => array(
				"type" => "number",
				"default" => 280,
			),

			"col_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item,
				{{RTCL}} .rtcl-gb-listing-store .rtcl-item {padding:{{col_padding}} !important;}']]
			),

			"gutter_padding" => array(
				"type" => "number",
				"default" => 15,
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store.style-grid .rtcl-col-wrap {padding:{{gutter_padding}}px;}'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store.style-list {gap:{{gutter_padding}}px;}']
				]
			),

			'colBGColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item { background-color:{{colBGColor}}; }']]
			],
			'colBorderColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item { border-color:{{colBorderColor}}; }']]
			],
			'colBorderWith'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item {border-width:{{colBorderWith}}; }']]
			],
			'colBorderStyle'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item { border-style:{{colBorderStyle}}; }']]
			],

			"colBorderRadius" => array(
				"type" => "object",
				'default' => (object)['top' => '', 'bottom' => '', 'left' => '', 'right' => '', 'unit' => 'px'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item { border-radius:{{colBorderRadius}}; }'
					]
				]
			),

			"titleColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-title{color:{{titleColor}} !important;}']]
			),
			"titleHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-title:hover {color:{{titleHoverColor}} !important;}'],
				],

			),
			'titleTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '20', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-title'
					],
				],
			],
			"counterColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-count{color:{{counterColor}}; }']]
			),
			'counterTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-count'],],
			],
			"contentColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-description{color:{{contentColor}} !important; }']]
			),
			'contentTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-store .rtcl-item .rtcl-description'
					],
				],
			],
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

		add_action('wp_ajax_rtcl_gb_store_list_controll', [$this, 'rtcl_gb_store_list_controll']);
		add_action('wp_ajax_nopriv_rtcl_gb_store_list_controll', [$this, 'rtcl_gb_store_list_controll']);

		add_action('wp_ajax_rtcl_gb_store_category_controll', [$this, 'rtcl_gb_store_category_controll']);
		add_action('wp_ajax_nopriv_rtcl_gb_store_category_controll', [$this, 'rtcl_gb_store_category_controll']);

		add_action('wp_ajax_nopriv_rtcl_gb_store_list_block', [$this, 'rtcl_gb_store_list_block']);
		add_action('wp_ajax_rtcl_gb_store_list_block', [$this, 'rtcl_gb_store_list_block']);
	}

	public static function rtcl_gb_store_list_query($data)
	{
		$results = [];
		$data['store_cats'] = !empty($data['store_cats']) ? wp_list_pluck($data['store_cats'], 'value') : [];
		$data['store_limit'] = isset($data['store_limit']) ? $data['store_limit'] : 4;
		$orderby = !empty($data['orderby']) ? $data['orderby'] : 'title';
		$order = !empty($data['sortby']) ? $data['sortby'] : 'ASC';

		$args = array(
			'post_type'        => 'store',
			'suppress_filters' => false,
			'post_status'      => 'publish',
		);

		$args['orderby'] = $orderby;
		$args['order'] = $order;

		// Taxonomy
		if (!empty($data['store_cats'])) {
			$args['tax_query'][] = [
				'taxonomy' => 'store_category',
				'field' => 'term_id',
				'terms' => $data['store_cats'],
			];
		}

		if ('all' == $data['store_type'] && $data['store_limit']) {
			$args['posts_per_page'] = $data['store_limit'];
		}

		if ('selected' == $data['store_type'] && !empty($data['stores'])) {
			$data['stores'] = wp_list_pluck($data['stores'], 'value');
			$args['include'] = !empty($data['stores']) ? $data['stores'] : array();
		} elseif ('selected' == $data['store_type'] && empty($data['stores'])) {
			return array();
		}

		//image size
		$image_size = isset($data['image_size']) ? $data['image_size'] : 'rtcl-thumbnail';
		if ('custom' == $image_size) {
			if (isset($data['custom_image_width']) && isset($data['custom_image_height'])) {
				$image_size = array(
					$data['custom_image_width'],
					$data['custom_image_height'],
				);
			}
		}

		$posts = get_posts($args);

		if (!is_wp_error($posts)) {
			foreach ($posts as $post) {
				$store = new \RtclStore\Models\Store($post->ID);

				$results[] = array(
					'logo'      => $store->get_the_logo($image_size),
					'title'     => $store->get_the_title(),
					'permalink' => $store->get_the_permalink(),
					'description' => $store->get_the_description(),
					'count'     => $store->get_ad_count()
				);

				if ('count' == $args['orderby']) {
					if ('DESC' == $args['order']) {
						usort($results, function ($a, $b) {
							return $b['count'] - $a['count'];
						});
					}
					if ('ASC' == $args['order']) {
						usort($results, function ($a, $b) {
							return $a['count'] - $b['count'];
						});
					}
				}
			}
		}

		return $results;
	}

	public function rtcl_gb_store_list_block()
	{
		if (!wp_verify_nonce($_POST['rtcl_nonce'], 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}
		$data = $_POST['attributes'];
		$results = self::rtcl_gb_store_list_query($data);

		if (!empty($results)) {
			wp_send_json_success($results);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function rtcl_gb_store_category_controll()
	{

		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$args = [
			'taxonomy'     => 'store_category',
			'fields'       => 'id=>name',
			'height_empty' => true,
		];

		$store_cat_dropdown = [];
		$terms = get_terms($args);
		if (!is_wp_error($terms)) {
			foreach ($terms as $id => $name) {
				$store_cat_dropdown[$id] = $name;
			}
		}

		if (!empty($store_cat_dropdown)) :
			wp_send_json($store_cat_dropdown);
		endif;

		wp_reset_postdata();
		wp_die();
	}


	public function rtcl_gb_store_list_controll()
	{

		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$store_dropdown = [];
		$args = array(
			'post_type'        => 'store',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_status'      => 'publish',
		);
		$posts = get_posts($args);

		if (!is_wp_error($posts)) {
			foreach ($posts as $post) {
				$store_dropdown[$post->ID] = $post->post_title;
			}
		}

		if (!empty($store_dropdown)) :
			wp_send_json($store_dropdown);
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
			'rtcl/listing-store',
			[
				'render_callback' => [$this, 'render_callback_search'],
				'attributes' => $this->get_attributes(),
			]
		);
	}


	public function render_callback_search($attributes)
	{
		$settings = $attributes;
		$stores = self::rtcl_gb_store_list_query($settings);
		$style    = isset($settings['style']) ? $settings['style'] : 'grid';


		$template_style    = 'block/listing-store/style-' . $style;
		$data              = array(
			'template'              => $template_style,
			'style'                 => $style,
			'settings'              => $settings,
			'stores' 				=> $stores,
			'default_template_path' => rtclPro()->get_plugin_template_path(),
		);


		$data = apply_filters('rtcl_gb_listing_store_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
