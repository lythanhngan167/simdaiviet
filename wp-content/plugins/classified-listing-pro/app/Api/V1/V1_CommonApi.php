<?php

namespace RtclPro\Api\V1;

use RtclPro\Helpers\PNHelper;
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use RtclPro\Helpers\Api;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Utility;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class V1_CommonApi {
	public function register_routes() {
		register_rest_route('rtcl/v1', 'listing-types', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'get_listing_type_callback'],
			'permission_callback' => [Api::class, 'permission_check']
		));
		register_rest_route('rtcl/v1', 'search-fields', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'get_search_fields_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => array(
				'category_id' => array(
					'required'          => false,
					'type'              => 'integer',
					'validate_callback' => function ($value, $request, $param) {
						if (!is_numeric($value)) {
							return new WP_Error('rest_invalid_param', esc_html__('The filter argument must be a integer.', 'classified-listing-pro'), array('status' => 400));
						}
						return true;
					},
					'sanitize_callback' => 'absint',
					'description'       => esc_html__('Category id', 'classified-listing-pro'),
				)
			),
		]);
		register_rest_route('rtcl/v1', 'categories', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'get_categories_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => array(
				'parent_id'    => array(
					'required'          => false,
					'type'              => 'integer',
					'validate_callback' => function ($value, $request, $param) {
						if (!is_numeric($value)) {
							return new WP_Error('rest_invalid_param', esc_html__('The filter argument must be a integer.', 'classified-listing-pro'), array('status' => 400));
						}
						return true;
					},
					'sanitize_callback' => function ($value, $request, $param) {
						return absint($value);
					},
					'description'       => esc_html__('Parent Category id', 'classified-listing-pro'),
				),
				'listing_type' => array(
					'required'    => false,
					'type'        => 'string',
					'description' => esc_html__('Listing type', 'classified-listing-pro'),
				),
			),
		]);
		register_rest_route('rtcl/v1', 'locations', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'get_locations_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => array(
				'parent_id' => array(
					'required'          => false,
					'type'              => 'integer',
					'validate_callback' => function ($value, $request, $param) {
						if (!is_numeric($value)) {
							return new WP_Error('rest_invalid_param', esc_html__('The filter argument must be a integer.', 'classified-listing-pro'), array('status' => 400));
						}
						return true;
					},
					'sanitize_callback' => function ($value, $request, $param) {
						return absint($value);
					},
					'description'       => esc_html__('Parent location id', 'classified-listing-pro'),
				)
			),
		]);
		register_rest_route('rtcl/v1', 'contact', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [$this, 'contact_email_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => array(
				'name'    => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => function ($value, $request, $param) {
						return strip_tags($value);
					},
					'description'       => esc_html__('Contact sender name', 'classified-listing-pro'),
				),
				'phone'   => array(
					'required'    => false,
					'type'        => 'string',
					'description' => esc_html__('Contact phone number.', 'classified-listing-pro'),
				),
				'email'   => array(
					'required'          => true,
					'type'              => 'email',
					'validate_callback' => function ($value, $request, $param) {
						if (!is_email($value)) {
							return new WP_Error('rest_invalid_param', esc_html__('The filter argument must be a email.', 'classified-listing-pro'), array('status' => 400));
						}
						return true;
					},
					'description'       => esc_html__('Contact email required.', 'classified-listing-pro'),
				),
				'message' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => function ($value, $request, $param) {
						return strip_tags($value);
					},
					'description'       => esc_html__('Contact message.', 'classified-listing-pro'),
				)
			),
		]);
		register_rest_route('rtcl/v1', 'config', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'config_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => [],
		]);
		register_rest_route('rtcl/v1', 'config-new-listing', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [$this, 'config_new_listing_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => [],
		]);
	}

	public static function config_callback(WP_REST_Request $request) {
		$currency_id         = Functions::get_currency();
		$payment_currency_id = Functions::get_order_currency();
		$config              = [
			'currency'         => [
				"id"        => $currency_id,
				"symbol"    => Functions::get_currency_symbol($currency_id),
				"position"  => Functions::get_option_item('rtcl_general_settings', 'currency_position', 'left'),
				"separator" => [
					"decimal"  => Functions::get_decimal_separator(),
					"thousand" => Functions::get_thousands_separator()
				]
			],
			'registered_only'  => [
				'listing_contact' => Fns::registered_user_only('listing_seller_information'),
			],
			'payment_currency' => [
				"id"        => $payment_currency_id,
				"symbol"    => Functions::get_currency_symbol($payment_currency_id),
				"position"  => Functions::get_option_item('rtcl_payment_settings', 'currency_position', 'left'),
				"separator" => [
					"decimal"  => Functions::get_decimal_separator(true),
					"thousand" => Functions::get_thousands_separator(true)
				]
			],
			'promotions'       => Options::get_listing_promotions(),
			'datetime_fmt'     => [
				'time' => Utility::dateFormatPHPToMoment(Functions::time_format()),
				'date' => Utility::dateFormatPHPToMoment(Functions::date_format())
			],
			'week_days'        => Api::formatted_array_data(Options::get_week_days()),
			'location_type'    => Functions::location_type(),
			'mark_as_sold'     => Fns::is_enable_mark_as_sold(),
			'radius_search'    => Options::radius_search_options(),
			'image_size'       => Functions::formatBytes(Functions::get_max_upload(), 0),
			'image_type'       => (array)Functions::get_option_item('rtcl_misc_settings', 'image_allowed_type', ['png',
				'jpeg',
				'jpg'
			]),
			'pn_events'           => PNHelper::getAllowedEvents(),
			'iap_disabled'        => Functions::get_option_item('rtcl_app_settings', 'iap_disabled', false, 'checkbox')
		];

		if (Functions::get_option_item('rtcl_moderation_settings', 'has_comment_form', false, 'checkbox')) {
			$config['review'] = [
				'rating'        => Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox'),
				'update_rating' => Functions::get_option_item('rtcl_moderation_settings', 'enable_update_rating', false, 'checkbox')
			];
		}

		if (Functions::has_map() && ($mapType = Functions::get_map_type()) && ('osm' === $mapType || ('google' === $mapType && $mapApiKey = Functions::get_option_item('rtcl_misc_settings', 'map_api_key')))) {
			$mapType      = Functions::get_map_type();
			$center_point = Functions::get_option_item('rtcl_misc_settings', 'map_center');
			$center_point = !empty($center_point) && is_array($center_point) ? wp_parse_args($center_point, ['address' => '', 'lat' => 0, 'lng' => 0]) : ['address' => '', 'lat' => 0, 'lng' => 0];

			$map = [
				'type'   => $mapType,
				'zoom'   => Functions::get_option_item('rtcl_misc_settings', 'map_zoom_level', 16, 'number'),
				'center' => apply_filters('rtcl_map_default_center_latLng', $center_point)
			];

			if ('google' === $mapType) {
				$map['options'] = Options::google_map_script_options();
				$map['api_key'] = $mapApiKey;
			}

			$config['map'] = $map;
		}
		return rest_ensure_response(apply_filters('rtcl_rest_api_config_data', $config));
	}

	public function config_new_listing_callback(WP_REST_Request $request) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if (!$user_id) {
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			);
			wp_send_json($response, 403);
		}
		Functions::clear_notices();// Clear previous notice
		do_action('rtcl_before_add_edit_listing_before_category_condition', 0);
		if (Functions::notice_count('error')) {
			Functions::clear_notices();// Clear all notice
			return rest_ensure_response(['eligible' => false]);
		}
		Functions::clear_notices(); // Clear all notice

		$config = [
			'eligible'      => true,
			'listing_types' => Api::formatted_array_data(Functions::get_listing_types()),
			'price_types'   => Api::get_price_types(),
			'hidden_fields' => Functions::get_option_item('rtcl_moderation_settings', 'hide_form_fields', []),
		];
		return rest_ensure_response($config);
	}

	public function get_search_fields_callback(WP_REST_Request $request) {
		$data                  = [];
		$category_id           = absint($request->get_param('category_id'));
		$data['order_by']      = Api::formatted_array_data(Options::get_listing_orderby_options());
		$data['custom_fields'] = Api::get_custom_fields($category_id);
		$data['listing_types'] = Api::formatted_array_data(Functions::get_listing_types());


		return rest_ensure_response($data);
	}

	public function get_listing_type_callback(WP_REST_Request $request) {
		$types = Api::formatted_array_data(Functions::get_listing_types());
		return rest_ensure_response($types);
	}

	public function get_categories_callback(WP_REST_Request $request) {
		$data      = [];
		$parent_id = $request->get_param('parent_id');
		if ($listing_type = $request->get_param('listing_type')) {
			$data['type'] = $listing_type;
		}

		$categories = Functions::get_sub_terms(rtcl()->category, $parent_id, $data);
		if (!empty($categories)) {
			$categories = array_map(function ($term) {
				$term->icon = [];
				if ($image_id = absint(get_term_meta($term->term_id, '_rtcl_image', true))) {
					if ($image_attributes = wp_get_attachment_image_src($image_id)) {
						list($url) = $image_attributes;
						$term->icon['url'] = $url;
					}
				}
				if ($icon_id = esc_attr(get_term_meta($term->term_id, '_rtcl_icon', true))) {
					$term->icon['class'] = $icon_id;
				}
				return $term;
			}, $categories);
		}

		return rest_ensure_response($categories);
	}

	public function get_locations_callback(WP_REST_Request $request) {
		$data      = [];
		$parent_id = $request->get_param('parent_id');
		return rest_ensure_response(Functions::get_sub_terms(rtcl()->location, $parent_id, $data));
	}

	public function contact_email_callback(WP_REST_Request $request) {
		$name    = $request->get_param('name');
		$email   = $request->get_param('email');
		$phone   = $request->get_param('phone');
		$message = $request->get_param('message');
		if (!rtcl()->mailer()->emails['Contact_Email_To_Admin']->trigger(compact('name', 'email', 'phone', 'message'))) {
			$response = array(
				'status'        => "error",
				'error'         => 'SERVERERROR',
				'code'          => '503',
				'error_message' => "Email not sent."
			);
			wp_send_json($response, 503);
		}
		return rest_ensure_response(compact('name', 'email', 'phone', 'message'));
	}
}
