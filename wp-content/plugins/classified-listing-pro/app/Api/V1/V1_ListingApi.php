<?php

namespace RtclPro\Api\V1;

use WP_Term;
use WP_REST_Server;
use WP_REST_Request;
use RtclPro\Helpers\Api;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;
use Rtcl\Controllers\Hooks\Filters;

class V1_ListingApi {
	public function register_routes() {
		register_rest_route( 'rtcl/v1', 'listings', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_listings_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'search'               => [
						'description'       => esc_html__( 'Limit results to those matching a string.' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'locations'            => [
						'type'        => 'array',
						'items'       => [
							'type' => 'integer'
						],
						'description' => esc_html__( 'Category ids as array of integer or only single integer.', 'classified-listing-pro' ),
					],
					'categories'           => [
						'type'        => 'array',
						'items'       => [
							'type' => 'integer'
						],
						'description' => esc_html__( 'Location ids as array of integer or only single integer.', 'classified-listing-pro' ),
					],
					'per_page'             => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 20,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'                 => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					],
					'custom_fields'        => [
						'description' => esc_html__( 'custom_fields is an array or object or JSON object', 'classified-listing-pro' ),
					],
					'price_range'          => [
						'description' => esc_html__( 'price range.', 'classified-listing-pro' ),
						'type'        => 'array'
					],
					'listing_type'         => [
						'description' => esc_html__( 'Listing type.', 'classified-listing-pro' ),
						'type'        => 'string'
					],
					'order_by'             => [
						'description' => esc_html__( 'Order by.', 'classified-listing-pro' ),
						'type'        => 'string'
					],
					'promotion_in'         => [
						'description' => esc_html__( 'Promotion include filter', 'classified-listing-pro' ),
						'type'        => 'array',
						'items'       => [
							'type' => 'string'
						],
					],
					'promotion_not_in'     => [
						'description' => esc_html__( 'Promotion exclude filter', 'classified-listing-pro' ),
						'type'        => 'array',
						'items'       => [
							'type' => 'string'
						],
					],
					'disable_top_listings' => [
						'description' => esc_html__( 'Promotion include filter', 'classified-listing-pro' ),
						'type'        => 'boolean',
						'default'     => false
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', '/listings/(?P<listing_id>[\d]+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_single_listing_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing id is required', 'classified-listing-pro' ),
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', '/listing/form', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_from_data_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_type' => [
						'required' => false,
						'type'     => 'string',
					],
					'category_id'  => [
						'required' => false,
						'type'     => 'integer'
					],
					'listing_id'   => [
						'required' => false,
						'type'     => 'integer'
					],
				],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_listing_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id'     => [
						'type' => 'integer'
					],
					'locations'      => [
						'type'        => 'array',
						'items'       => [
							'type' => 'integer'
						],
						'description' => esc_html__( 'Locations ids as array of integers or only single integer.', 'classified-listing-pro' ),
					],
					'category_id'    => [
						'type'        => 'integer',
						'description' => esc_html__( 'Category id integer.', 'classified-listing-pro' ),
					],
					'listing_type'   => [
						'type'        => 'string',
						'description' => esc_html__( 'listing_type e.g. sell', 'classified-listing-pro' ),
					],
					'gallery_delete' => [
						'type'        => 'array',
						'items'       => [
							'type' => 'integer'
						],
						'description' => esc_html__( 'Image ids which will be deleted', 'classified-listing-pro' ),
					],
					'gallery_sort'   => [
						'type'        => 'array',
						'description' => esc_html__( 'Image ids / name for new image ids, which will be deleted', 'classified-listing-pro' ),
					],

				]
			]
		] );
		register_rest_route( 'rtcl/v1', '/listing/report', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'report_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing id is required', 'classified-listing-pro' ),
					],
					'message'    => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'message is the report details', 'classified-listing-pro' ),
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', '/listing/email-seller', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'email_to_seller_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing id is required', 'classified-listing-pro' ),
					],
					'name'       => [
						'required'    => false,
						'type'        => 'string',
						'description' => esc_html__( 'name field is the sender name.', 'classified-listing-pro' ),
					],
					'email'      => [
						'required'    => false,
						'type'        => 'string',
						'description' => esc_html__( 'email field is the sender email.', 'classified-listing-pro' ),
					],
					'message'    => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'message is the message details', 'classified-listing-pro' ),
					]
				]
			]
		] );
	}

	public function get_listings_callback( WP_REST_Request $request ) {
		$general_settings = Functions::get_option( 'rtcl_general_settings' );
		$per_page         = (int) $request->get_param( "per_page" );
		$page             = (int) $request->get_param( "page" );
		$locations        = $request->get_param( "locations" );
		$categories       = $request->get_param( "categories" );
		$search           = $request->get_param( "search" );
		$price_range      = $request->get_param( "price_range" );
		$listing_type     = $request->get_param( "listing_type" );
		$order_by         = $request->get_param( "order_by" );
		$custom_fields    = $request->get_param( "custom_fields" );
		$promotion_in     = $request->get_param( "promotion_in" );
		$promotion_not_in = $request->get_param( "promotion_not_in" );

		// Prepare variables
		$args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'fields'         => 'ids'
		];
		if ( $search ) {
			$args['s'] = $search;
		}
		$ordering        = Api::get_ordering_args( $order_by );
		$args['orderby'] = $ordering['orderby'];
		$args['order']   = $ordering['order'];
		if ( isset( $ordering['meta_key'] ) ) {
			$args['meta_key'] = $ordering['meta_key'];
		}
		$tax_queries  = [];
		$meta_queries = [];
		if ( ! empty( $categories ) ) {
			$tax_queries[] = [
				'taxonomy'         => rtcl()->category,
				'field'            => 'term_id',
				'terms'            => $categories,
				'include_children' => isset( $general_settings['include_results_from'] ) && in_array( 'child_categories',
						$general_settings['include_results_from'] ),
			];

		}
		if ( ! empty( $locations ) && "local" === Functions::location_type() ) {
			$tax_queries[] = [
				'taxonomy'         => rtcl()->location,
				'field'            => 'term_id',
				'terms'            => $locations,
				'include_children' => isset( $general_settings['include_results_from'] ) && in_array( 'child_locations',
						$general_settings['include_results_from'] ),
			];
		}
		$count_tax_queries = count( $tax_queries );
		if ( $count_tax_queries ) {
			$args['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( [ 'relation' => 'AND' ], $tax_queries ) : $tax_queries;
		}

		// set price range filter
		if ( is_array( $price_range ) && count( $price_range ) === 2 ) {
			$min = ! empty( $price_range[0] ) ? absint( $price_range[0] ) : null;
			$max = ! empty( $price_range[1] ) ? absint( $price_range[1] ) : null;
			if ( $min !== null && $max !== null ) {
				$meta_queries[] = [
					'key'     => 'price',
					'value'   => [ $min, $max ],
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				];
			} else {
				if ( $min === null ) {
					$meta_queries[] = [
						'key'     => 'price',
						'value'   => $max,
						'type'    => 'NUMERIC',
						'compare' => '<='
					];
				} else {
					$meta_queries[] = [
						'key'     => 'price',
						'value'   => $min,
						'type'    => 'NUMERIC',
						'compare' => '>='
					];
				}
			}

		}


		// Promotions filter
		if ( ! empty( $promotion_in ) && is_array( $promotion_in ) ) {
			$promotions = array_keys( Options::get_listing_promotions() );
			foreach ( $promotion_in as $promotion ) {
				if ( is_string( $promotion ) && in_array( $promotion, $promotions ) ) {
					$meta_queries[] = [
						'key'     => $promotion,
						'compare' => '=',
						'value'   => 1
					];
				}
			}
		}

		if ( ! empty( $promotion_not_in ) && is_array( $promotion_not_in ) ) {
			$promotions = array_keys( Options::get_listing_promotions() );
			foreach ( $promotion_not_in as $promotion ) {
				if ( is_string( $promotion ) && in_array( $promotion, $promotions ) ) {
					$meta_queries[] = [
						'relation' => 'OR',
						[
							'key'     => $promotion,
							'compare' => '!=',
							'value'   => 1
						],
						[
							'key'     => $promotion,
							'compare' => 'NOT EXISTS',
						]
					];
				}
			}
		}

		// Listing type filter
		if ( $listing_type && in_array( $listing_type, array_keys( Functions::get_listing_types() ) ) && ! Functions::is_ad_type_disabled() ) {
			$meta_queries[] = [
				'key'     => 'ad_type',
				'value'   => $listing_type,
				'compare' => '='
			];
		}

		// Listing custom fields
		if ( ! empty( $custom_fields ) ) {
			if ( is_string( $custom_fields ) ) {
				$custom_fields = json_decode( $custom_fields );
				$custom_fields = is_object( $custom_fields ) ? (array) $custom_fields : $custom_fields;
			} elseif ( is_object( $custom_fields ) ) {
				$custom_fields = (array) $custom_fields;
			}
			if ( is_array( $custom_fields ) && ! empty( $custom_fields ) ) {
				foreach ( $custom_fields as $key => $values ) {
					if ( ! empty( $values ) ) {
						$field_id = absint( str_replace( "_field_", '', $key ) );
						$field    = rtcl()->factory->get_custom_field( $field_id );
						if ( $field ) {
							if ( is_array( $values ) ) {
								if ( $field->getType() === 'number' && count( $values ) === 2 ) {
									$min = ! empty( $values[0] ) ? absint( $values[0] ) : null;
									$max = ! empty( $values[1] ) ? absint( $values[1] ) : null;
									if ( $n = count( $values ) ) {
										if ( $min !== null && $max !== null ) {
											$meta_queries[] = [
												'key'     => $key,
												'value'   => [ $min, $max ],
												'type'    => 'NUMERIC',
												'compare' => 'BETWEEN'
											];
										} else {
											if ( $min === null ) {
												$meta_queries[] = [
													'key'     => $key,
													'value'   => $min,
													'type'    => 'NUMERIC',
													'compare' => '<='
												];
											} else {
												$meta_queries[] = [
													'key'     => $key,
													'value'   => $max,
													'type'    => 'NUMERIC',
													'compare' => '>='
												];
											}
										}
									}
								} else if ( in_array( $field->getType(), [ 'checkbox', 'select', 'radio' ] ) ) {
									if ( count( $values ) > 1 ) {

										$sub_meta_queries = [];

										foreach ( $values as $value ) {
											$sub_meta_queries[] = [
												'key'     => $key,
												'value'   => sanitize_text_field( $value ),
												'compare' => 'LIKE'
											];
										}

										$meta_queries[] = array_merge( [ 'relation' => 'AND' ], $sub_meta_queries );

									} else {
										$meta_queries[] = [
											'key'     => $key,
											'value'   => sanitize_text_field( $values[0] ),
											'compare' => 'LIKE'
										];
									}
								}
							} else {
								if ( $field->getType() === 'date' ) {
									$date_type   = $field->getDateType();
									$search_type = $field->getDateSearchableType();
									$type        = $date_type == 'date_time' || $date_type == 'date_time_range' ? 'DATETIME' : 'DATE';
									if ( $date_type == 'date' || $date_type == 'date_time' ) {
										$meta_key = $field->getMetaKey();

										if ( $search_type == 'single' ) {
											$meta_queries[] = [
												'key'     => $meta_key,
												'value'   => $field->sanitize_date_field( $values, [ 'range' => false ] ),
												'compare' => '=',
												'type'    => $type
											];
										} else {
											$dates          = $field->sanitize_date_field( $values, [ 'range' => true ] );
											$start_date     = $dates['start'];
											$end_date       = $dates['end'];
											$meta_queries[] = [
												'key'     => $meta_key,
												'value'   => [ $start_date, $end_date ],
												'compare' => 'BETWEEN',
												'type'    => $type
											];
										}

									} else if ( $date_type == 'date_range' || $date_type == 'date_range_time' ) {
										$start_meta_key = $field->getDateRangeMetaKey( 'start' );
										$end_meta_key   = $field->getDateRangeMetaKey( 'end' );

										if ( $search_type == 'single' ) {
											$start_date = $end_date = $field->sanitize_date_field( $values, [ 'range' => false ] );
										} else {
											$dates      = $field->sanitize_date_field( $values, [ 'range' => true ] );
											$start_date = $dates['start'];
											$end_date   = $dates['end'];
										}
										if ( $start_date ) {
											$meta_queries[] = [
												'key'     => $start_meta_key,
												'value'   => $start_date,
												'compare' => $search_type == 'single' ? '<=' : '>=',
												'type'    => $type
											];
										}
										if ( $end_date ) {
											$meta_queries[] = [
												'key'     => $end_meta_key,
												'value'   => $end_date,
												'compare' => $search_type == 'single' ? '>=' : '<=',
												'type'    => $type
											];
										}
									}

								} else {
									$operator       = ( in_array( $field->getType(), [
										'text',
										'textarea',
										'url'
									] ) ) ? 'LIKE' : '=';
									$meta_queries[] = [
										'key'     => $key,
										'value'   => sanitize_text_field( $values ),
										'compare' => $operator
									];
								}
							}
						}
					}
				}
			}
		}


		$count_meta_queries = count( $meta_queries );
		if ( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( [ 'relation' => 'AND' ], $meta_queries ) : $meta_queries;
		}


		// Radius search
		$radius_search = $request->get_param( "radius_search" );
		if ( Functions::is_enable_map() && ! empty( $radius_search ) ) {
			if ( is_string( $radius_search ) ) {
				$radius_search = json_decode( $radius_search );
				$radius_search = is_object( $radius_search ) ? (array) $radius_search : $radius_search;
			} elseif ( is_object( $radius_search ) ) {
				$radius_search = (array) $radius_search;
			}
			if ( ! empty( $radius_search ) && is_array( $radius_search ) ) {
				$distance  = ! empty( $radius_search['distance'] ) ? absint( $radius_search['distance'] ) : 0;
				$latitude  = ! empty( $radius_search['latitude'] ) ? $radius_search['latitude'] : 0;
				$longitude = ! empty( $radius_search['longitude'] ) ? $radius_search['longitude'] : 0;
				if ( $distance && $latitude && $longitude ) {
					$rs_data                = Options::radius_search_options();
					$geo_query              = [
						'lat_field' => 'latitude',
						'lng_field' => 'longitude',
						'latitude'  => $latitude,
						'longitude' => $longitude,
						'distance'  => $distance,
						'units'     => $rs_data["units"]
					];
					$args['rtcl_geo_query'] = array_filter( apply_filters( 'rtcl_rest_listing_query_geo_query', $geo_query, $this ) );
				}
			}
		}
		// Add top listings
		if ( Fns::is_enable_top_listings() && ! $request->get_param( 'disable_top_listings' ) ) {
			$args['rtcl_top_listings'] = true;
		}
		$response = Api::get_query_listing_data( apply_filters( 'rtcl_rest_response_listings_args', $args ) );

		return rest_ensure_response( $response );

	}


	public function report_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			];
			wp_send_json( $response, 403 );
		}
		if ( ! $request->get_param( 'listing_id' ) || ( ! $listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) || $listing->get_post_type() !== rtcl()->post_type ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'Listing not found.'
			];
			wp_send_json( $response, 400 );
		}
		if ( $listing->get_owner_id() === $user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'You are not permitted to report.'
			];
			wp_send_json( $response, 400 );
		}
		$message     = esc_textarea( $request->get_param( "message" ) );
		$sender_data = [
			'message' => $message
		];
		$is_send     = rtcl()->mailer()->emails['Report_Abuse_Email_To_Admin']->trigger( $listing->get_id(), $sender_data );
		if ( ! $is_send ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADGATEWAY',
				'code'          => '502',
				'error_message' => 'Error while sending mail for some server issue.'
			];
			wp_send_json( $response, 502 );
		}
		$notification = absint( get_post_meta( $listing->get_id(), '_abuse_report_by_visitor', true ) ) + 1;
		update_post_meta( $listing->get_id(), '_abuse_report_by_visitor', $notification );

		return rest_ensure_response( $notification );
	}

	public function email_to_seller_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			];
			wp_send_json( $response, 403 );
		}
		if ( ! $request->get_param( 'listing_id' ) || ( ! $listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) || ! $listing->exists() ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'Listing not found.'
			];
			wp_send_json( $response, 400 );
		}

		if ( $listing->get_owner_id() === $user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'You are not permitted to email.'
			];
			wp_send_json( $response, 400 );
		}
		$message     = stripslashes( esc_textarea( $request->get_param( "message" ) ) );
		$user        = get_userdata( $user_id );
		$name        = $request->get_param( "name" );
		$email       = $request->get_param( "email" );
		$sender_data = [
			'name'    => $name ? $name : $user->display_name,
			'email'   => $email ? $email : $user->user_email,
			'message' => $message
		];
		if ( ! Functions::get_option_item( 'rtcl_email_settings', 'notify_users', 'disable_contact_email', 'multi_checkbox' ) ) {
			rtcl()->mailer()->emails['Listing_Contact_Email_To_Owner']->trigger( $listing->get_id(), $sender_data );
		}
		if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'listing_contact', 'multi_checkbox' ) ) {
			rtcl()->mailer()->emails['Listing_Contact_Email_To_Admin']->trigger( $listing->get_id(), $sender_data );
		}
		$notification = absint( get_post_meta( $listing->get_id(), '_notification_by_visitor', true ) ) + 1;
		update_post_meta( $listing->get_id(), '_notification_by_visitor', $notification );

		return rest_ensure_response( $notification );
	}

	public function get_single_listing_callback( WP_REST_Request $request ) {
		if ( ! $request->get_param( 'listing_id' ) || ( ! $listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) || $listing->get_post_type() !== rtcl()->post_type ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Listing not found.', "classified-listing-pro" )
			];
			wp_send_json( $response, 400 );
		}

		$listing_data = Api::get_single_listing_data( $listing );
		Functions::update_listing_views_count( $listing->get_id() );

		return rest_ensure_response( $listing_data );
	}


	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function permission_check( WP_REST_Request $request ) {
		return true;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_from_data_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			];
			wp_send_json( $response, 403 );
		}
		$category_id = absint( $request->get_param( 'category_id' ) );
		$listing_id  = absint( $request->get_param( 'listing_id' ) );
		$listing     = rtcl()->factory->get_listing( $listing_id );
		if ( $listing ) {
			$category_ids = $listing->get_category_ids();
			$category_id  = ( is_array( $category_ids ) && ! empty( $category_ids ) ) ? end( $category_ids ) : 0;
		}
		$moderation_settings = Functions::get_option( 'rtcl_moderation_settings' );
		$currency            = Functions::get_currency();
		$form_data           = [
			'config'        => [
				'bhs'             => Functions::is_enable_business_hours(),
				'pricing_types'   => $this->get_pricing_types(),
				'price_types'     => $this->get_price_types(),
				'currency'        => Functions::get_currency(),
				'currency_symbol' => Functions::get_currency_symbol( $currency ),
				'price_units'     => Api::get_listing_price_units( $listing, $category_id ),
				'hidden_fields'   => ! empty( $moderation_settings['hide_form_fields'] ) ? $moderation_settings['hide_form_fields'] : [],
				'gallery'         => [
					'max_image_limit' => (int) Functions::get_option_item( 'rtcl_moderation_settings', 'maximum_images_per_listing', 5 ),
					'max_image_size'  => Functions::get_max_upload(),
					'extensions'      => apply_filters( 'rtcl_gallery_image_allowed_extensions', Functions::get_option_item( 'rtcl_misc_settings', 'image_allowed_type', [
						'jpg',
						'jpeg',
						'png'
					] ) ),
					'image_required'  => Functions::is_gallery_image_required()
				],
				'limit'           => [
					'title'       => Functions::get_title_character_limit() ?: null,
					'description' => Functions::get_description_character_limit() ?: null,
				],
				'video_urls'      => ! Functions::is_video_urls_disabled()
			],
			'listing'       => $listing ? Api::get_single_listing_data( $listing ) : null,
			'custom_fields' => Api::get_custom_fields( $category_id, $listing_id )
		];

		if ( Functions::is_enable_social_profiles() ) {
			$form_data['config']['social_profiles'] = $this->get_array_format( Options::get_social_profiles_list() );
		}

		$form_data = apply_filters( 'rtcl_rest_listing_form_data', $form_data, $listing, $request );

		return rest_ensure_response( $form_data );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function create_listing_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			];
			wp_send_json( $response, 403 );
		}
		$listing_id = absint( $request->get_param( 'listing_id' ) );
		$listing    = rtcl()->factory->get_listing( $listing_id );
		if ( $listing ) {
			if ( $listing->get_author_id() !== $user_id ) {
				$response = [
					'status'        => "error",
					'error'         => 'FORBIDDEN',
					'code'          => '403',
					'error_message' => 'You are not allow to edit this ad.'
				];
				wp_send_json( $response, 403 );
			}
		} else {
			$listing_id = 0;
			$post       = null;
		}
		$raw_cat_id  = absint( $request->get_param( 'category_id' ) );
		$category    = get_term_by( 'id', $raw_cat_id, rtcl()->category );
		$category_id = 0;
		if ( is_a( $category, WP_Term::class ) ) {
			$category_id = $category->term_id;
		}
		if ( ! $listing_id && ! $category_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => 'Category not selected.'
			];
			wp_send_json( $response, 403 );
		}
		// Check if user has not any post remaining
		Functions::clear_notices();// Clear previous notice
		do_action( 'rtcl_before_add_edit_listing_before_category_condition', $listing_id );
		$category_id = 0;
		if ( ! Functions::notice_count( 'error' ) ) {
			$raw_cat_id   = absint( $request->get_param( 'category_id' ) );
			$listing_type = $request->get_param( 'listing_type' ) && in_array( $request->get_param( 'listing_type' ), array_keys( Functions::get_listing_types() ) ) ? esc_attr( $request->get_param( 'listing_type' ) ) : '';
			if ( ! $listing_id && $raw_cat_id ) {
				$category = get_term_by( 'id', $raw_cat_id, rtcl()->category );
				if ( is_a( $category, WP_Term::class ) ) {
					$category_id = $category->term_id;
					$parent_id   = Functions::get_term_top_most_parent_id( $category_id, rtcl()->category );
					if ( Functions::term_has_children( $category_id ) ) {
						Functions::add_notice( __( "Please select ad type and category", 'classified-listing-pro' ), 'error' );
					}
					if ( ! Functions::is_ad_type_disabled() && ! $listing_type ) {
						Functions::add_notice( __( "Please select an ad type", 'classified-listing-pro' ), 'error' );
					}
					$cats_on_type = wp_list_pluck( Functions::get_one_level_categories( 0, $listing_type ), 'term_id' );
					if ( ! in_array( $parent_id, $cats_on_type ) ) {
						Functions::add_notice( __( "Please select correct type and category", 'classified-listing-pro' ), 'error' );
					}
					do_action( 'rtcl_before_add_edit_listing_into_category_condition', $listing_id, $category_id );
				} else {
					Functions::add_notice( __( "Category is not valid", 'classified-listing-pro' ), 'error' );
				}
			}
			if ( ! $listing_id && ! $category_id ) {
				Functions::add_notice( __( "Category not selected", 'classified-listing-pro' ), 'error' );
			}
		}

		if ( Functions::notice_count( 'error' ) ) {
			$error    = Functions::get_notices( 'error' );
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => is_array( $error ) ? $error[0] : $error,
			];
			wp_send_json( $response, 403 );
		}
		Functions::clear_notices(); // Clear all notice

		if ( Functions::is_enable_terms_conditions() && ! $request->get_param( 'agree' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "Please agree with the terms and conditions."
			];
			wp_send_json( $response, 403 );
		}

		$cats      = [ $category_id ];
		$locations = [];
		$metas     = [];
		if ( Functions::is_enable_terms_conditions() && $request->get_param( 'agree' ) ) {
			$metas['rtcl_agree'] = 1;
		}

		if ( $request->has_param( 'pricing_type' ) ) {
			$listing_pricing_type           = sanitize_text_field( $request->get_param( 'pricing_type' ) );
			$metas['_rtcl_listing_pricing'] = in_array( $listing_pricing_type, array_keys( Options::get_listing_pricing_types() ) ) ? $listing_pricing_type : 'price';
			if ( $request->has_param( 'max_price' ) && 'range' === $listing_pricing_type ) {
				$metas['_rtcl_max_price'] = Functions::format_decimal( $request->get_param( 'max_price' ) );
			}
		}
		if ( $request->get_param( 'price_type' ) ) {
			$metas['price_type'] = Functions::sanitize( $request->get_param( 'price_type' ) );
		}
		if ( $request->get_param( 'price' ) ) {
			$metas['price'] = Functions::format_decimal( $request->get_param( 'price' ) );
		}

		if ( "geo" === Functions::location_type() ) {
			$metas['_rtcl_geo_address'] = Functions::sanitize( $request->get_param( 'geo_address' ) );
		} else {
			if ( ! empty( $request->get_param( 'locations' ) ) && is_array( $request->get_param( 'locations' ) ) ) {
				$locations = $request->get_param( 'locations' );
			}
			$metas['zipcode'] = Functions::sanitize( $request->get_param( 'zipcode' ) );
			$metas['address'] = Functions::sanitize( $request->get_param( 'address' ), 'textarea' );
		}

		if ( $request->get_param( 'phone' ) ) {
			$metas['phone'] = Functions::sanitize( $request->get_param( 'phone' ) );
		}
		if ( $request->get_param( 'whatsapp_number' ) ) {
			$metas['_rtcl_whatsapp_number'] = Functions::sanitize( $request->get_param( 'whatsapp_number' ) );
		}
		if ( $request->get_param( 'email' ) ) {
			$metas['email'] = Functions::sanitize( $request->get_param( 'email' ), 'email' );
		}
		if ( $request->get_param( 'website' ) ) {
			$metas['website'] = Functions::sanitize( $request->get_param( 'website' ), 'url' );
		}
		if ( $request->get_param( 'latitude' ) ) {
			$metas['latitude'] = Functions::sanitize( $request->get_param( 'latitude' ) );
		}
		if ( $request->get_param( 'longitude' ) ) {
			$metas['longitude'] = Functions::sanitize( $request->get_param( 'longitude' ) );
		}
		if ( $request->get_param( 'price_unit' ) ) {
			$metas['_rtcl_price_unit'] = Functions::sanitize( $request->get_param( 'price_unit' ) );
		}

		if ( ! Functions::is_video_urls_disabled() && $request->has_param( 'video_urls' ) ) {
			$metas['_rtcl_video_urls'] = Functions::sanitize( $request->get_param( 'video_urls' ), 'video_urls' );
		}

		if ( Functions::is_enable_social_profiles() && $request->has_param( 'social_profiles' ) ) {
			$raw_profiles = $request->get_param( 'social_profiles' );
			$social_list  = Options::get_social_profiles_list();
			$profiles     = [];
			foreach ( $social_list as $item => $value ) {
				if ( ! empty( $raw_profiles[ $item ] ) ) {
					$profiles[ $item ] = esc_url_raw( $raw_profiles[ $item ] );
				}
			}
			if ( ! empty( $profiles ) ) {
				$metas['_rtcl_social_profiles'] = $profiles;
			} else if ( $listing ) {
				delete_post_meta( $listing->get_id(), '_rtcl_social_profiles' );
			}
		}


		if ( Functions::is_enable_business_hours() && ( $request->has_param( 'active_bhs' ) || $request->has_param( 'active_special_bhs' ) ) ) {
			$raw_bhs         = $request->get_param( 'bhs' );
			$raw_special_bhs = $request->get_param( 'special_bhs' );

			if ( $raw_bhs && ! is_array( $raw_bhs ) && ( $json_bsh = json_decode( $raw_bhs, true ) ) && json_last_error() === JSON_ERROR_NONE ) {
				$raw_bhs = $json_bsh;
			}
			if ( $raw_special_bhs && ! is_array( $raw_special_bhs ) && ( $json_special_bsh = json_decode( $raw_special_bhs, true ) ) && json_last_error() === JSON_ERROR_NONE ) {
				$raw_special_bhs = $json_special_bsh;
			}
			if ( $listing ) {
				delete_post_meta( $listing->get_id(), '_rtcl_bhs' );
				delete_post_meta( $listing->get_id(), '_rtcl_special_bhs' );
			}

			if ( $request->get_param( 'active_bhs' ) && ! empty( $raw_bhs ) && is_array( $raw_bhs ) ) {
				$new_bhs = Functions::sanitize( $raw_bhs, 'business_hours' );
				if ( ! empty( $new_bhs ) ) {
					$metas['_rtcl_bhs'] = $new_bhs;
				}

				if ( $request->get_param( 'active_special_bhs' ) && ! empty( $raw_special_bhs ) && is_array( $raw_special_bhs ) ) {
					$new_shs = Functions::sanitize( $raw_special_bhs, 'special_business_hours' );
					if ( ! empty( $new_shs ) ) {
						$metas['_rtcl_special_bhs'] = $new_shs;
					}
				}
			}
		}

		$metas['hide_map']  = $request->get_param( 'hide_map' ) === 1 ? 1 : null;
		$title              = Functions::sanitize( $request->get_param( 'title' ), 'title' );
		$post_arg           = [
			'post_title'   => $title,
			'post_content' => Functions::sanitize( $request->get_param( 'description' ), 'content' ),
			'meta_input'   => $metas
		];
		$new_metas          = [];
		$new_listing_status = Functions::get_option_item( 'rtcl_moderation_settings', 'new_listing_status', 'pending' );
		$type               = 'new';
		if ( $listing ) {
			if ( $listing->get_status() === "rtcl-temp" ) {
				$post_arg['post_name']   = $title;
				$post_arg['post_status'] = $new_listing_status;
			} else {
				$type              = 'update';
				$status_after_edit = Functions::get_option_item( 'rtcl_moderation_settings', 'edited_listing_status' );
				if ( "publish" === $listing->get_status() && $status_after_edit && $listing->get_status() !== $status_after_edit ) {
					$post_arg['post_status'] = $status_after_edit;
				}
			}
			$post_arg['ID'] = $listing_id;
			$success        = wp_update_post( apply_filters( 'rtcl_new_listing_update_data', $post_arg, $request->get_params() ) );
		} else {
			$post_arg['post_status'] = $new_listing_status;
			$post_arg['post_author'] = $user_id;
			$post_arg['post_type']   = rtcl()->post_type;
			$listing_id              = $success = wp_insert_post( apply_filters( 'rtcl_new_listing_insert_data', $post_arg, $request->get_params() ) );
		}

		if ( ! empty( $cats ) && is_array( $cats ) && $type === 'new' && $listing_id ) {
			wp_set_object_terms( $listing_id, $cats, rtcl()->category );
			$new_metas['ad_type'] = $listing_type;
		}

		if ( ! empty( $locations ) && is_array( $locations ) ) {
			wp_set_object_terms( $listing_id, $locations, rtcl()->location );
		}

		// Custom Meta field
		$custom_fields = $request->get_param( 'custom_fields' );
		if ( $listing_id && ! empty( $custom_fields ) && is_array( $custom_fields ) ) {
			foreach ( $custom_fields as $key => $value ) {
				$field_id = (int) str_replace( '_field_', '', $key );
				if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
					if ( 'checkbox' === $field->getType() && ! empty( $value ) && ! is_array( $value ) && ( $json_value = json_decode( $value, true ) ) && json_last_error() === JSON_ERROR_NONE ) {
						$value = $json_value;
					}
					$field->saveSanitizedValue( $listing_id, $value );
				}
			}
		}


		/* meta data */
		if ( ! empty( $new_metas ) && $listing_id ) {
			foreach ( $new_metas as $key => $value ) {
				update_post_meta( $listing_id, $key, $value );
			}
		}

		if ( ! $success && ! $listing_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '500',
				'error_message' => esc_html__( 'Internal Server Error', 'classified-listing-pro' )
			];
			wp_send_json( $response, 403 );
		}


		$files           = $request->get_file_params();
		$uploaded_images = [];
		if ( ! empty( $files['gallery']['name'] ) ) {
			$gallery = $files['gallery'];
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			foreach ( $gallery['name'] as $key => $value ) {
				if ( $gallery['name'][ $key ] ) {
					$image = [
						'name'     => $gallery['name'][ $key ],
						'type'     => $gallery['type'][ $key ],
						'tmp_name' => $gallery['tmp_name'][ $key ],
						'error'    => $gallery['error'][ $key ],
						'size'     => $gallery['size'][ $key ]
					];

					Filters::beforeUpload();
					$status = wp_handle_upload( $image, [ 'test_form' => false ] );
					Filters::afterUpload();
					if ( $status && ! isset( $status['error'] ) ) {
						$filename      = $status['file'];
						$filetype      = wp_check_filetype( basename( $filename ) );
						$wp_upload_dir = wp_upload_dir();
						$attachment    = [
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						];
						$attach_id     = wp_insert_attachment( $attachment, $filename, $listing_id );
						if ( ! is_wp_error( $attach_id ) ) {
							$uploaded_images[ $image['name'] ] = $attach_id;
							wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
						}
					}
				}
			}
		}

		$listing = rtcl()->factory->get_listing( $listing_id );

		// send emails
		if ( $type === 'new' ) {
			$ads = absint( get_user_meta( $user_id, '_rtcl_ads', true ) );
			update_user_meta( $user_id, '_rtcl_ads', $ads + 1 );
			if ( 'publish' === $new_listing_status ) {
				try {
					Functions::add_default_expiry_date( $listing_id );
				} catch ( \Exception $e ) {
				}
			}
		}

		// if update
		if ( $type === 'update' ) {
			//Delete images
			$gallery_delete_ids = $request->get_param( 'gallery_delete' );
			if ( ! empty( $gallery_delete_ids ) ) {
				$children_ids = get_children( [
					'post_parent'    => $listing_id,
					'post_type'      => 'attachment',
					'posts_per_page' => - 1,
					'post_status'    => 'inherit',
					'fields'         => 'ids'
				] );
				if ( ! empty( $children_ids ) ) {
					foreach ( $gallery_delete_ids as $g_id ) {
						if ( in_array( $g_id, $children_ids ) ) {
							wp_delete_attachment( $g_id, true );
						}
					}
				}
			}

			// Sorting images
			$gallery_sort = $request->get_param( 'gallery_sort' );
			if ( ! empty( $gallery_sort ) ) {
				if ( ! empty( $uploaded_images ) ) {
					$gallery_sort = array_map( function ( $item ) use ( $uploaded_images ) {
						return isset( $uploaded_images[ $item ] ) ? $uploaded_images[ $item ] : $item;
					}, $gallery_sort );
					$gallery_sort = array_filter( $gallery_sort );
				}
				if ( ! empty( $gallery_sort ) ) {
					update_post_meta( $listing_id, '_rtcl_attachments_order', $gallery_sort );
				}
			}
		}

		do_action( 'rtcl_listing_form_after_save_or_update', $listing, $type, $category_id, $new_listing_status, [
			'data'   => $request->get_params(),
			'params' => $request->get_params(),
			'files'  => $request->get_file_params(),
			'type'   => 'api'
		] ); // TODO: Need to removed data
		do_action( 'rtcl_rest_listing_form_after_save_or_update', $listing, $request, [
			'new_listing_status' => $new_listing_status,
			'category_id'        => $category_id,
			'type'               => $type
		] );

		return rest_ensure_response( Api::get_single_listing_data( $listing ) );
	}


	/**
	 * @param $options
	 *
	 * @return array
	 */
	private function get_array_format( $options ) {
		$data = [];
		if ( ! empty( $options ) ) {
			foreach ( $options as $key => $value ) {
				$data[] = [
					'id'   => $key,
					'name' => $value
				];
			}
		}

		return $data;
	}

	private function get_pricing_types() {
		$pricing_types = Options::get_listing_pricing_types();
		$types         = [];
		if ( ! empty( $pricing_types ) ) {
			foreach ( $pricing_types as $key => $value ) {
				$types[] = [
					'id'   => $key,
					'name' => $value
				];
			}
		}

		return $types;
	}

	private function get_price_types() {
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
}