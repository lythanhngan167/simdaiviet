<?php

namespace RtclPro\Helpers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\Payment;
use Rtcl\Models\Pricing;
use Rtcl\Resources\Options;
use RtclPro\Api\Authentication\AuthenticationApiKey;
use RtclPro\Api\Authentication\AuthenticationJWTAuth;
use WP_Comment;
use WP_Error;
use WP_Query;
use WP_User;

class Api {
	/**
	 * @var array
	 */
	private static $headers = [];

	static function is_valid_auth_request() {
		self::$headers = self::get_all_headers();

		return AuthenticationJWTAuth::is_valid_request( self::$headers );
	}

	/**
	 * @param $request
	 *
	 * @return false|WP_User
	 */
	static function is_valid_http_auth_request( $request ) {

		$jwt_token_raw = ! empty( $request['token'] ) ? trim( $request['token'] ) : '';

		if ( ! $jwt_token_raw ) {
			wp_die( 'Authorization token missing', 'MISSING_AUTHORIZATION_TOKEN', 401 );
		}

		$jwt_token = explode( ".", $jwt_token_raw );
		$jwt       = new AuthenticationJWTAuth();

		if ( ! $jwt->jwt_token_segment_validation( $jwt_token ) ) {
			wp_die( 'Incorrect JWT token Format.', 'SEGMENT_FAULT', 401 );
		}

		if ( ! $jwt->is_jwt_signature_validation( $jwt_token ) ) {
			wp_die( 'JWT Signature is invalid.', 'INVALID_SIGNATURE', 401 );
		}

		$user_id = get_current_user_id();
		if ( ! $user_id || ! $user = get_user_by( 'ID', $user_id ) ) {
			wp_die( 'User not found.', 'USER_NOT_FOUND', 403 );
		}
		// Make the user logged in with browser
		wp_set_auth_cookie( $user_id );
		do_action( 'wp_login', $user->user_login, $user );

		if ( empty( rtcl()->session ) ) {
			rtcl()->initialize_session();
		}
		rtcl()->session->set( 'rtcl_app_woo_payment', true );

		return $user;
	}

	static function check_is_auth_user_request() {
		$headers = self::get_all_headers();

		return AuthenticationJWTAuth::check_is_valid_request( $headers );
	}

	static function get_price_types() {
		$price_types = Options::get_price_types();
		$types       = [];
		if ( ! empty( $price_types ) ) {
			foreach ( $price_types as $key => $value ) {
				$types[] = [
					'id'   => $key,
					'name' => $value
				];
			}
		}

		return $types;
	}

	/**
	 *
	 * @return WP_Error|bool
	 */
	static function permission_check() {
		$headers = self::get_all_headers();
		do_action( 'rtcl_rest_set_local', $headers );
		if ( ! Functions::get_option_item( 'rtcl_tools_settings', 'allow_rest_api', false, 'checkbox' ) ) {
			return new WP_Error( 'DENIED_REST_API', esc_html__( 'Denied api call', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}
		AuthenticationApiKey::is_valid_request( $headers );

		return true;
	}

	/**
	 * @param $request
	 *
	 * @return WP_Error
	 */
	static function http_request_permission_check( $request ) {
		if ( ! Functions::get_option_item( 'rtcl_tools_settings', 'allow_rest_api', false, 'checkbox' ) ) {
			wp_die( esc_html__( 'Denied api call', 'classified-listing-pro' ), 'DENIED_REST_API', 403 );
		}
		$api_key = get_option( 'rtcl_rest_api_key', null );
		if ( empty( $request['api_key'] ) || $api_key !== $request['api_key'] ) {
			wp_die( 'Sorry, you are using invalid API Key', 'INVALID_API_KEY', [ 'response' => 401 ] );
		}
	}

	static function get_all_headers() {
		if ( ! empty( self::$headers ) ) {
			return self::$headers;
		}
		$headers = [];
		foreach ( $_SERVER as $name => $value ) {
			if ( substr( $name, 0, 5 ) == 'HTTP_' ) {
				$headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
			}
		}

		self::$headers = array_change_key_case( $headers, CASE_UPPER );

		return self::$headers;
	}

	static function create_jwt_token( $client_secret, $user ) {

		$iat = time();
		$exp = time() + ( 24 * 60 * 60 );

		// Create the token header
		$header = json_encode( [
			'alg' => 'HS256',
			'typ' => 'JWT'
		] );

		// Create the token payload
		$payload = json_encode( [
			'sub'  => '123456789',
			'name' => $user->user_login,
			'iat'  => $iat,
			'exp'  => $exp
		] );

		// Encode Header
		$base64UrlHeader = self::base64UrlEncode( $header );

		// Encode Payload
		$base64UrlPayload = self::base64UrlEncode( $payload );

		// Create Signature Hash
		$signature = hash_hmac( 'sha256', $base64UrlHeader . "." . $base64UrlPayload, $client_secret, true );

		// Encode Signature to Base64Url String
		$base64UrlSignature = self::base64UrlEncode( $signature );

		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

		return [
			'token_type' => 'Bearer',
			'iat'        => $iat,
			'expires_in' => $exp,
			'jwt_token'  => $jwt,
		];

	}

	static function base64UrlEncode( $text ) {
		return rtrim( strtr( base64_encode( $text ), '+/', '-_' ), '=' );
	}

	/**
	 * @param Listing $listing
	 * @param bool    $as_top
	 *
	 * @return array|null
	 */
	static function get_listing_data( $listing, $as_top = false ) {
		if ( ! $listing || ! is_a( $listing, Listing::class ) || $listing->get_post_type() !== rtcl()->post_type ) {
			return null;
		}
		$listing_id  = $listing->get_id();
		$extra_class = apply_filters( 'rtcl_listing_extra_class', [], $listing );
		$data        = [
			'listing_id'     => $listing_id,
			'author_id'      => $listing->get_owner_id(),
			'title'          => $listing->get_the_title(),
			'pricing_type'   => $listing->get_pricing_type(),
			'price'          => Functions::price( $listing->get_price(), true ),
			'max_price'      => Functions::price( $listing->get_max_price(), true ),
			'price_type'     => $listing->get_price_type(),
			'price_units'    => Api::get_listing_price_units( $listing ),
			'price_unit'     => $listing->get_price_unit(),
			'categories'     => $listing->get_categories(),
			'ad_type'        => $listing->get_ad_type(),
			'status'         => $listing->get_status(),
			'images'         => $listing->get_images(),
			'created_at'     => $listing->get_listing()->post_date,
			'created_at_gmt' => $listing->get_listing()->post_date_gmt,
			'view_count'     => $listing->get_view_counts(),
			'promotions'     => $listing->get_promotions(),
			'badges'         => array_merge(
				$listing->get_label_class(),
				is_array( $extra_class ) ? $extra_class : [],
				$as_top ? [ 'as-top' ] : []
			),
			'contact'        => [
				'locations'       => $listing->get_locations(),
				'latitude'        => get_post_meta( $listing_id, 'latitude', true ),
				'longitude'       => get_post_meta( $listing_id, 'longitude', true ),
				'hide_map'        => ! empty( get_post_meta( $listing_id, 'hide_map', true ) ),
				'zipcode'         => get_post_meta( $listing_id, 'zipcode', true ),
				'address'         => get_post_meta( $listing_id, 'address', true ),
				'phone'           => get_post_meta( $listing_id, 'phone', true ),
				'whatsapp_number' => get_post_meta( $listing_id, '_rtcl_whatsapp_number', true ),
				'email'           => get_post_meta( $listing_id, 'email', true ),
				'website'         => get_post_meta( $listing_id, 'website', true ),
				'geo_address'     => get_post_meta( $listing_id, '_rtcl_geo_address', true )
			]
		];

		return apply_filters( 'rtcl_rest_api_listing_data', $data, $listing );
	}

	/**
	 * @param integer|Payment $order_id
	 *
	 * @return mixed|void
	 */
	static function get_order_data( $order_id ) {

		if ( ! $order = rtcl()->factory->get_order( $order_id ) ) {
			return;
		}

		$order_data = [
			"id"             => $order->get_id(),
			"price"          => Functions::get_payment_formatted_price( $order->get_total() ),
			"method"         => esc_html__( $order->get_payment_method_title() ),
			"status"         => Functions::get_status_i18n( $order->get_status() ),
			'transaction_id' => $order->get_transaction_id(),
			'order_key'      => $order->get_order_key(),
			'paid_date'      => $order->get_date_paid() ? Functions::datetime( 'mysql', $order->get_date_paid() ) : '',
			'created_date'   => $order->get_created_date(),
			'gateway'        => $order->gateway ? $order->gateway->rest_api_data() : ''
		];

		return apply_filters( 'rtcl_rest_api_order_data', $order_data, $order );
	}

	/**
	 * @param Listing $listing
	 *
	 * @param         $cat_id
	 *
	 * @return array
	 */
	public static function get_listing_price_units( $listing = null, $cat_id = null ) {
		if ( ! $cat_id && ! $listing ) {
			return [];
		}
		$raw_price_units = Options::get_price_unit_list();
		$price_units     = [];
		if ( is_a( $listing, Listing::class ) ) {
			$price_units = $listing->get_price_units();
		} else if ( $cat_id ) {
			$price_units = Functions::get_category_price_units( $cat_id );
		}
		$units = [];
		if ( ! empty( $price_units ) && ! empty( $raw_price_units ) ) {
			foreach ( $raw_price_units as $unit_key => $unit ) {
				if ( in_array( $unit_key, $price_units ) ) {
					$units[] = [
						'id'    => $unit_key,
						'name'  => isset( $unit['title'] ) ? $unit['title'] : '',
						'short' => isset( $unit['short'] ) ? $unit['short'] : ''
					];
				}
			}
		}

		return $units;
	}

	/**
	 * @param Payment $order
	 *
	 * @return array $data
	 */
	public static function get_single_order_data( $order ) {
		if ( ! $order instanceof Payment ) {
			return null;
		}
		$data         = self::get_order_data( $order );
		$data['plan'] = self::get_plan_data( $order->pricing );

		return apply_filters( 'rtcl_rest_api_single_order_data', $data, $order );
	}

	/**
	 * @param integer|Pricing $plan_id
	 *
	 * @return mixed|void
	 */
	static function get_plan_data( $plan_id ) {
		$plan = null;
		if ( is_numeric( $plan_id ) ) {
			$plan = rtcl()->factory->get_pricing( $plan_id );
		} else if ( is_a( $plan_id, Pricing::class ) ) {
			$plan = $plan_id;
		}
		if ( ! $plan ) {
			return;
		}

		$plan_data = [
			'id'          => $plan->getId(),
			'price'       => $plan->getPrice(),
			'description' => $plan->getDescription(),
			'title'       => $plan->getTitle(),
			'visible'     => $plan->getVisible(),
			'type'        => $plan->getType(),
			'promotion'   => [
				'regular' => $plan->getRegularPromotions()
			]
		];

		return apply_filters( 'rtcl_rest_api_plan_data', $plan_data, $plan );
	}


	/**
	 * @param Listing $listing
	 *
	 * @return array $data
	 */
	public static function get_single_listing_data( Listing $listing ) {
		if ( ! is_a( $listing, Listing::class ) || $listing->get_post_type() !== rtcl()->post_type ) {
			return null;
		}
		$data                = self::get_listing_data( $listing );
		$data['url']         = $listing->get_the_permalink();
		$data['description'] = strip_tags( strip_shortcodes( $listing->get_listing()->post_content ) );
		$current_user_id     = 0;
		if ( Api::check_is_auth_user_request() ) {
			$current_user_id      = get_current_user_id();
			$favourites           = get_user_meta( $current_user_id, 'rtcl_favourites', true );
			$favourites           = ! empty( $favourites ) && is_array( $favourites ) ? $favourites : [];
			$data['is_favourite'] = ! empty( $favourites ) && in_array( $listing->get_id(), $favourites );
		}

		if ( ! Functions::is_video_urls_disabled() && ! apply_filters( 'rtcl_disable_gallery_video', Functions::is_video_gallery_disabled() ) ) {
			$video_urls         = get_post_meta( $listing->get_id(), '_rtcl_video_urls', true );
			$data['video_urls'] = ! empty( $video_urls ) && is_array( $video_urls ) ? $video_urls : [];
		}

		if ( Functions::is_enable_social_profiles() ) {
			$social_profiles         = get_post_meta( $listing->get_id(), '_rtcl_social_profiles', true );
			$data['social_profiles'] = ! empty( $social_profiles ) && is_array( $social_profiles ) ? $social_profiles : [];
		}

		if ( Functions::is_enable_business_hours() ) {
			$bhs         = get_post_meta( $listing->get_id(), '_rtcl_bhs', true );
			$bhs         = ! empty( $bhs ) && is_array( $bhs ) ? $bhs : [];
			$special_bhs = get_post_meta( $listing->get_id(), '_rtcl_special_bhs', true );
			$special_bhs = is_array( $special_bhs ) && ! empty( $special_bhs ) ? $special_bhs : [];
			$data['bh']  = [
				'bhs'         => $bhs,
				'special_bhs' => $special_bhs
			];
		}

		$data['author']        = self::get_user_data( get_user_by( 'ID', $listing->get_owner_id() ) );
		$category_ids          = $listing->get_category_ids();
		$category_id           = ( is_array( $category_ids ) && ! empty( $category_ids ) ) ? end( $category_ids ) : 0;
		$data['custom_fields'] = self::get_custom_fields( $category_id, $listing->get_id() );
		$data['related']       = self::get_related_listings( $listing );
		$reviewData            = [
			'rating' => [
				'average' => $listing->get_average_rating(),
				'count'   => $listing->get_rating_count()
			]
		];
		if ( $current_user_id ) {
			$args    = [
				'post_type' => rtcl()->post_type,
				'post_id'   => $listing->get_id(),
				'user_id'   => $current_user_id,
				'number'    => 1,
				'parent'    => 0,
			];
			$reviews = get_comments( $args );
			if ( ! empty( $reviews ) ) {
				$reviewData['item'] = Api::get_single_review_data( $reviews[0] );
			}
		}
		$data['review'] = apply_filters( 'rtcl_rest_single_listing_review_rating', $reviewData, $listing );

		return apply_filters( 'rtcl_rest_api_single_listing_data', $data, $listing, $current_user_id );
	}

	static function get_single_review_data( $review ) {
		$data = [];
		if ( $review && ( is_a( $review, WP_Comment::class ) || is_object( $review ) ) ) {
			$data = [
				'id'                 => (int) $review->comment_ID,
				'listing'            => (int) $review->comment_post_ID,
				'parent'             => (int) $review->comment_parent,
				'author'             => (int) $review->user_id,
				'author_name'        => $review->comment_author,
				'author_email'       => $review->comment_author_email,
				'author_url'         => $review->comment_author_url,
				'author_ip'          => $review->comment_author_IP,
				'author_user_agent'  => $review->comment_agent,
				'date'               => mysql_to_rfc3339( $review->comment_date ),
				'date_gmt'           => mysql_to_rfc3339( $review->comment_date_gmt ),
				'rating'             => intval( get_comment_meta( $review->comment_ID, 'rating', true ) ),
				'title'              => get_comment_meta( $review->comment_ID, 'title', true ),
				'content'            => [
					'rendered' => apply_filters( 'comment_text', $review->comment_content, $review ),
					'raw'      => $review->comment_content,
				],
				'status'             => self::prepare_status_response_for_review( $review->comment_approved ),
				'author_avatar_urls' => rest_get_avatar_urls( $review )
			];
		}

		return apply_filters( 'rtcl_rest_single_review_data', $data, $review );
	}


	/**
	 * Checks comment_approved to set comment status for single comment output.
	 *
	 * @param string|int $comment_approved comment status.
	 *
	 * @return string Comment status.
	 *
	 */
	protected static function prepare_status_response_for_review( $comment_approved ) {

		switch ( $comment_approved ) {
			case 'hold':
			case '0':
				$status = 'hold';
				break;

			case 'approve':
			case '1':
				$status = 'approved';
				break;

			case 'spam':
			case 'trash':
			default:
				$status = $comment_approved;
				break;
		}

		return $status;
	}

	/**
	 * @param Listing $listing
	 *
	 * @return array $data
	 */
	static function get_related_listings( Listing $listing ) {
		if ( ! $listing || ! is_a( $listing, Listing::class ) || $listing->get_post_type() !== rtcl()->post_type ) {
			return null;
		}
		$related_listings      = [];
		$categories            = $listing->get_categories();
		$category              = ! empty( $categories ) ? end( $categories )->term_id : 0;
		$related_post_per_page = apply_filters( 'rtcl_listing_related_posts_per_page', Functions::get_option_item( 'rtcl_general_settings', 'related_posts_per_page', 4, 'number' ) );
		if ( ! $related_post_per_page ) {
			return $related_listings;
		}
		$query_args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $related_post_per_page,
			'post__not_in'   => [ $listing->get_id() ],
			'fields'         => 'ids'
		];
		if ( $category ) {
			$general_settings = Functions::get_option( 'rtcl_general_settings' );

			$query_args['tax_query'] = [
				[
					'taxonomy'         => rtcl()->category,
					'field'            => 'term_id',
					'terms'            => $category,
					'include_children' => isset( $general_settings['include_results_from'] ) && in_array( 'child_categories', $general_settings['include_results_from'] )
				]
			];
		}
		$relatedQ = new \WP_Query( apply_filters( 'rtcl_related_listing_query_arg', $query_args ) );
		if ( ! empty( $relatedQ->posts ) ) {
			foreach ( $relatedQ->posts as $id ) {
				if ( $_listing = rtcl()->factory->get_listing( $id ) ) {
					$related_listings[] = Api::get_listing_data( $_listing );
				}
			}
		}

		return $related_listings;
	}

	/**
	 * @param int $category_id
	 * @param int $post_id
	 *
	 * @return array
	 */
	static function get_custom_fields( $category_id = 0, $post_id = 0 ) {
		$fields    = [];
		$field_ids = Functions::get_custom_field_ids( $category_id );
		if ( ! empty( $field_ids ) ) {
			foreach ( $field_ids as $field_id ) {
				if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
					$field_value = $field->getValue( $post_id );
					$field_data  = [
						'id'          => $field->getFieldId(),
						'meta_key'    => $field->getMetaKey(),
						'label'       => $field->getLabel(),
						'slug'        => $field->getSlug(),
						'description' => $field->getDescription(),
						'searchable'  => $field->isSearchable(),
						'listable'    => $field->getListable(),
						'type'        => $field->getType(),
						'icon'        => trim( $field->getIconClass() ),
						'required'    => (bool) $field->getRequired(),
						'placeholder' => $field->getPlaceholder(),
						'value'       => $field_value,
					];
					if ( ( $conditions = $field->getConditions() ) && is_array( $conditions ) ) {
						$field_data['dependency'] = $conditions;
					}

					if ( in_array( $field->getType(), [ 'radio', 'select', 'checkbox' ] ) ) {
						$options = $field->getOptions();
						if ( isset( $options['choices'] ) && is_array( $options['choices'] ) && ! empty( $options['choices'] ) ) {
							$choices = [];
							foreach ( $options['choices'] as $key => $value ) {
								$choices[] = [
									'id'   => $key,
									'name' => $value
								];
							}
							$options['choices'] = $choices;
						}
						$field_data['options'] = $options;
					} else if ( $field->getType() === 'number' ) {
						$field_data['min']       = $field->getMin();
						$field_data['max']       = $field->getMin();
						$field_data['step_size'] = $field->getStepSize();
					} else if ( $field->getType() === 'date' ) {
						$field_data['date'] = [
							'type'     => $field->getDateType() && in_array( $field->getDateType(), [
								'date',
								'date_range',
								'date_time',
								'date_time_range'
							] ) ? $field->getDateType() : 'date',
							'format'   => $field->getDateFullFormat(),
							'jsFormat' => $field->getDateFullFormat( 'js' )
						];
					} else if ( $field->getType() === 'url' ) {
						$field_data['target'] = $field->getTarget();
					}

					$fields[] = $field_data;
				}
			}
		}

		return $fields;
	}


	static function formatted_array_data( $raw_data ) {
		$data = [];
		if ( ! empty( $raw_data ) && is_array( $raw_data ) ) {
			foreach ( $raw_data as $key => $name ) {
				$data[] = [
					'id'   => $key,
					'name' => $name
				];
			}
		}

		return $data;
	}

	static function get_listing_types() {
		return self::formatted_array_data( Functions::get_listing_types() );
	}

	static function get_order_by() {
		return self::formatted_array_data( Options::get_listing_orderby_options() );
	}

	/**
	 * @param WP_User | string | int $user
	 *
	 * @return array
	 */
	static function get_user_data( $user ) {
		if ( ! $user || ( ! is_a( $user, \WP_User::class ) && ! ( $user = get_user_by( is_email( $user ) ? 'email' : ( is_numeric( $user ) ? 'ID' : 'login' ), $user ) ) ) ) {
			return [];
		}
		$locations_arr = get_user_meta( $user->ID, '_rtcl_location', true );
		$locations_arr = ! empty( $locations_arr ) && is_array( $locations_arr ) ? array_filter( array_map( 'absint', $locations_arr ) ) : [];
		$user_data     = [
			'first_name'      => $user->first_name,
			'last_name'       => $user->last_name,
			'description'     => $user->description,
			'id'              => $user->ID,
			'isAdmin'         => in_array( 'administrator', (array) $user->roles ),
			'email'           => $user->user_email,
			'username'        => $user->user_login,
			'phone'           => get_user_meta( $user->ID, '_rtcl_phone', true ),
			'whatsapp_number' => get_user_meta( $user->ID, '_rtcl_whatsapp_number', true ),
			'website'         => get_user_meta( $user->ID, '_rtcl_website', true ),
			'locations'       => ! empty( $locations_arr ) ? get_terms( [
				'taxonomy'   => rtcl()->location,
				'include'    => $locations_arr,
				'hide_empty' => false,
				'orderby'    => 'include'
			] ) : [],
			'zipcode'         => get_user_meta( $user->ID, '_rtcl_zipcode', true ),
			'address'         => get_user_meta( $user->ID, '_rtcl_address', true ),
			'latitude'        => get_user_meta( $user->ID, '_rtcl_latitude', true ),
			'longitude'       => get_user_meta( $user->ID, '_rtcl_longitude', true )
		];
		if ( $pp_id = get_user_meta( $user->ID, '_rtcl_pp_id', true ) ) {
			$img                       = wp_get_attachment_image_src( $pp_id, [ 80, 80 ] );
			$user_data['pp_thumb_url'] = $img[0];
		} else {
			$user_data['pp_thumb_url'] = Functions::get_avatar_img_url( $user );
		}

		return apply_filters( 'rtcl_rest_api_user_data', $user_data, $user );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_query_order_data( $args = [] ) {
		$data    = [];
		$results = new WP_Query( $args );

		$pagination = null;
		if ( ! empty( $results->posts ) ) {
			foreach ( $results->posts as $id ) {
				$order  = rtcl()->factory->get_order( $id );
				$data[] = Api::get_order_data( $order );
			}

			$pagination = [
				'total'        => $results->found_posts,
				'count'        => $results->post_count,
				'per_page'     => $results->query['posts_per_page'],
				'current_page' => $results->query['paged'],
				'total_pages'  => $results->max_num_pages
			];
		}

		return [
			'data'       => $data,
			'pagination' => $pagination
		];
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_query_listing_data( $args = [] ) {
		$data    = [];
		$results = new WP_Query( $args );

		if ( ! empty( $args['rtcl_top_listings'] ) && $args['paged'] <= 1 ) {
			$topArgs                   = $args;
			$topArgs['posts_per_page'] = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_top_per_page', 2 );
			$topArgs['orderby']        = 'rand';
			$topArgs['meta_query'][]   = [
				'key'     => '_top',
				'value'   => 1,
				'compare' => '='
			];

			if ( count( $topArgs['meta_query'] ) > 1 ) {
				$topArgs['meta_query']['relation'] = "AND";
			}
			$topQuery = new WP_Query( apply_filters( 'rtcl_top_listings_query_args', $topArgs ) );
			if ( ! empty( $topQuery->posts ) ) {
				foreach ( $topQuery->posts as $post_id ) {
					$listing = rtcl()->factory->get_listing( $post_id );
					$data[]  = Api::get_listing_data( $listing, true );
				}
			}
		}

		$pagination = null;
		if ( ! empty( $results->posts ) ) {
			foreach ( $results->posts as $post_id ) {
				$listing = rtcl()->factory->get_listing( $post_id );
				$data[]  = Api::get_listing_data( $listing );
			}

			$pagination = [
				'total'        => $results->found_posts,
				'count'        => $results->post_count,
				'per_page'     => $results->query['posts_per_page'],
				'current_page' => $results->query['paged'],
				'total_pages'  => $results->max_num_pages
			];
		}

		return [
			'data'       => $data,
			'pagination' => $pagination
		];
	}


	public static function get_ordering_args( $orderby_value = null ) {
		// Get ordering from query string unless defined.
		$orderby = null;
		$order   = null;
		if ( $orderby_value ) {
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
		}
		if ( ! $orderby && ! $order ) {
			if ( is_search() ) {
				$orderby_value = 'relevance';
			} else {
				$order_by      = Functions::get_option_item( 'rtcl_general_settings', 'orderby', 'date' );
				$order         = Functions::get_option_item( 'rtcl_general_settings', 'order', 'desc' );
				$orderby_value = apply_filters( 'rtcl_default_catalog_orderby', $order_by . "-" . $order, $order_by, $order );
			}

			// Get order + orderby args from string.
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
		}

		// Convert to correct format.
		$orderby = strtolower( is_array( $orderby ) ? (string) current( $orderby ) : (string) $orderby );
		$order   = strtoupper( is_array( $order ) ? (string) current( $order ) : (string) $order );
		$args    = [
			'orderby'  => $orderby,
			'order'    => ( 'DESC' === $order ) ? 'DESC' : 'ASC',
			'meta_key' => '', // @codingStandardsIgnoreLine
		];

		switch ( $orderby ) {
			case 'id':
				$args['orderby'] = 'ID';
				break;
			case 'menu_order':
				$args['orderby'] = 'menu_order title';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'date' :
				$args['orderby'] = 'date';
				$args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'price' :
				$args['meta_key'] = 'price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'views' :
				$args['meta_key'] = '_views';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'rand' :
				$args['orderby'] = 'rand';
				break;
		}

		return apply_filters( 'rtcl_rest_api_listings_ordering_args', $args, $orderby, $order );
	}
}