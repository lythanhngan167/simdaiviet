<?php

namespace RtclPro\Api\V1;

use Rtcl\Resources\Options;
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_HTTP_Response;
use RtclPro\Helpers\Api;
use Rtcl\Helpers\Functions;

class V1_OrderApi {
	public function register_routes() {

		register_rest_route( 'rtcl/v1', 'plans', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'plans_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'type' => [
					'default'     => 'regular',
					'type'        => 'string',
					'required'    => true,
					'description' => 'Plans type\'s (regular, membership)',
					'enum'        => [
						'regular',
						'membership'
					]
				]
			],
		] );
		register_rest_route( 'rtcl/v1', 'payment-gateways', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'payment_gateways_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ]
		] );
		register_rest_route( 'rtcl/v1', 'checkout', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'checkout_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'type'                  => [
					'default'     => 'promotion',
					'type'        => 'string',
					'required'    => true,
					'description' => esc_html__( 'Checkout type\'s (promotion, membership)', 'classified-listing-pro' ),
					'enum'        => [
						'promotion',
						'membership'
					]
				],
				'promotion_type'        => [
					'default'           => 'regular',
					'type'              => 'string',
					'required'          => false,
					'description'       => esc_html__( 'Promotion type\'s (regular, membership)', 'classified-listing-pro' ),
					'enum'              => [
						'regular',
						'membership'
					],
					'validate_callback' => function ( $param_value, $request, $param_key ) {
						if ( 'promotion' === $request->get_param( 'type' ) && ! $param_value ) {
							return new WP_Error( 'invalid_param', sprintf( esc_html__( '%s must be regular or membership', 'classified-listing-pro' ), $param_key ) );
						}

						return true;
					}
				],
				'membership_promotions' => [
					'required'    => false,
					'type'        => 'array',
					'description' => esc_html__( 'Promotions for membership (membership_promotions)', "classified-listing-pro" ),
				],
				'gateway_id'            => [
					'required'    => false,
					'default'     => 'regular',
					'type'        => 'string',
					'description' => esc_html__( 'Payment gateway id', 'classified-listing-pro' )
				],
				'plan_id'               => [
					'required'          => false,
					'type'              => 'integer',
					'description'       => esc_html__( 'Plane id', 'classified-listing-pro' ),
					'sanitize_callback' => 'absint',
				],
				'card_number'           => [
					'type'              => 'string',
					'required'          => false,
					'description'       => esc_html__( 'Credit card number (card_number)', 'classified-listing-pro' ),
					'sanitize_callback' => 'sanitize_text_field'
				],
				'card_exp_month'        => [
					'type'              => 'integer',
					'required'          => false,
					'description'       => esc_html__( 'Credit card expired month (card_exp_month)', 'classified-listing-pro' ),
					'sanitize_callback' => 'absint'
				],
				'card_exp_year'         => [
					'type'              => 'integer',
					'required'          => false,
					'description'       => esc_html__( 'Credit card expired year (card_exp_year)', 'classified-listing-pro' ),
					'sanitize_callback' => 'absint'
				],
				'card_cvc'              => [
					'type'              => 'string',
					'required'          => false,
					'description'       => esc_html__( 'Credit card CVC (card_cvc)', 'classified-listing-pro' ),
					'sanitize_callback' => 'sanitize_text_field'
				],
				'stripe_payment_method' => [
					'type'              => 'string',
					'required'          => false,
					'description'       => esc_html__( 'Stripe payment method id', 'classified-listing-pro' ),
					'sanitize_callback' => 'sanitize_text_field'
				]
			],
		] );
		register_rest_route( 'rtcl/v1', 'payments', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_orders_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'search'   => [
					'description'       => esc_html__( 'Limit results to those matching a string.' ),
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				],
				'per_page' => [
					'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
					'type'              => 'integer',
					'default'           => 20,
					'minimum'           => 1,
					'maximum'           => 100,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
				],
				'page'     => [
					'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
					'minimum'           => 1,
				],
				'order_by' => [
					'description' => esc_html__( 'Order by', 'classified-listing-pro' ),
					'type'        => 'string'
				],
				'order'    => [
					'description' => esc_html__( 'Order', 'classified-listing-pro' ),
					'type'        => 'string'
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'payments/(?P<payment_id>[\d]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_single_order_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'payment_id' => [
					'required'    => true,
					'type'        => 'integer',
					'description' => esc_html__( 'Payment id is required', 'classified-listing-pro' ),
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'orders', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_orders_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'search'   => [
					'description'       => esc_html__( 'Limit results to those matching a string.' ),
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				],
				'per_page' => [
					'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
					'type'              => 'integer',
					'default'           => 20,
					'minimum'           => 1,
					'maximum'           => 100,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
				],
				'page'     => [
					'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
					'minimum'           => 1,
				],
				'order_by' => [
					'description' => esc_html__( 'Order by', 'classified-listing-pro' ),
					'type'        => 'string'
				],
				'order'    => [
					'description' => esc_html__( 'Order', 'classified-listing-pro' ),
					'type'        => 'string'
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'orders/(?P<order_id>[\d]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_single_order_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'order_id' => [
					'required'    => true,
					'type'        => 'integer',
					'description' => esc_html__( 'Order id is required', 'classified-listing-pro' ),
				]
			]
		] );
	}

	public function get_single_order_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		if ( ! get_current_user_id() ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__( "You are not logged in.", 'classified-listing-pro' )
			], 403 );
		}

		if ( ( ! $request->get_param( 'payment_id' ) || ( ! $order = rtcl()->factory->get_order( $request->get_param( 'payment_id' ) ) ) ) && ( ! $request->get_param( 'order_id' ) || ( ! $order = rtcl()->factory->get_order( $request->get_param( 'order_id' ) ) ) ) ) {
			wp_send_json( [
				'status'  => "error",
				'error'   => 'BAD_REQUEST',
				'code'    => '400',
				'message' => esc_html__( 'Order not found.', 'classified-listing-pro' )
			], 400 );
		}

		return rest_ensure_response( Api::get_single_order_data( $order ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_orders_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		if ( ! get_current_user_id() ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__( "You are not logged in.", 'classified-listing-pro' )
			], 403 );
		}
		$per_page = (int) $request->get_param( "per_page" );
		$page     = (int) $request->get_param( "page" );
		$search   = $request->get_param( "search" );
		$order    = $request->get_param( "order" );
		$order_by = $request->get_param( "order_by" );
		$args     = [
			'post_type'      => rtcl()->post_type_payment,
			'post_status'    => array_keys( Options::get_payment_status_list() ),
			'order'          => $order,
			'order_by'       => $order_by,
			'posts_per_page' => $per_page,
			'fields'         => 'ids',
			'paged'          => $page,
			'meta_query'     => [
				[
					'key'     => 'customer_id',
					'value'   => get_current_user_id(),
					'compare' => '=',
				],
			]
		];

		if ( $search ) {
			$args['s'] = $search;
		}

		$response = Api::get_query_order_data( apply_filters( 'rtcl_rest_api_payments_args', $args ) );

		return rest_ensure_response( $response );

	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function checkout_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__( "You are not logged in.", "classified-listing-pro" )
			], 403 );
		}
		do_action( 'rtcl_rest_api_checkout_membership_promotions', $request );


		$type       = $request->get_param( 'type' );
		$plan_id    = $request->get_param( 'plan_id' );
		$gateway_id = $request->get_param( 'gateway_id' );
		$listing_id = $request->get_param( 'listing_id' );

		if ( ! $plan_id || ! $gateway_id || ! $type ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'rest_missing_callback_param',
				'code'          => '400',
				'error_message' => esc_html__( "Required data missing (plan_id, gateway_id, type)", "classified-listing-pro" )
			], 403 );
		}

		$plan    = rtcl()->factory->get_pricing( $plan_id );
		$gateway = Functions::get_payment_gateway( $gateway_id );

		if ( ! $plan || ! $gateway || 'yes' !== $gateway->enabled ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'INVALID_PARAM',
				'code'          => '400',
				'error_message' => esc_html__( "Incorrect Plan or gateway", "classified-listing-pro" )
			], 403 );
		}
		$cardData = [];
		if ( 'authorizenet' === $gateway->id ) {
			$card_number    = $request->get_param( 'card_number' );
			$card_exp_month = $request->get_param( 'card_exp_month' );
			$card_exp_year  = $request->get_param( 'card_exp_year' );
			$card_cvc       = $request->get_param( 'card_cvc' );
			if ( ! $card_number || ! $card_exp_month || ! $card_exp_year || ! $card_cvc ) {
				wp_send_json( [
					'status'        => "error",
					'error'         => 'rest_missing_callback_param',
					'code'          => '400',
					'error_message' => esc_html__( "Missing one of thous param card_number , card_exp_month, card_exp_year, card_cvc", "classified-listing-pro" )
				], 403 );
			}
			$cardData['number']    = $card_number;
			$cardData['exp_month'] = $card_exp_month;
			$cardData['exp_year']  = $card_exp_year;
			$cardData['cvc']       = $card_cvc;
		}
		if ( 'stripe' === $gateway->id && $request->has_param( 'stripe_payment_method' ) ) {
			$paymentMethodId = $request->get_param( 'stripe_payment_method' );
			if ( empty( $paymentMethodId ) || ! preg_match( '/^pm_.*$/', $paymentMethodId ) ) {
				wp_send_json( [
					'status'  => "error",
					'error'   => 'STRIPE_PAYMENT_METHOD_REQUIRED',
					'code'    => 403,
					'message' => esc_html__( 'Unable to verify your request. Please reload the page and try again.', 'classified-listing-pro' )
				], 403 );
			}
			$cardData['stripe_payment_method'] = $paymentMethodId;
		}

		$listing = rtcl()->factory->get_listing( $listing_id );

		if ( 'promotion' === $type && 'regular' === $plan->getType() && ! $listing ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__( "No listing found to make payment.", "classified-listing-pro" )
			], 403 );
		}

		$request = apply_filters( 'rtcl_rest_api_checkout_request_data', $request );

		$errors = new WP_Error();
		do_action( 'rtcl_rest_api_checkout_data', $request, $plan, $gateway, $listing, $errors );

		if ( is_wp_error( $errors ) && $errors->has_errors() ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'VALIDATION_ERROR',
				'code'          => '403',
				'error_message' => $errors->get_error_message()
			], 403 );
		}

		$new_order_args = [
			'post_title'  => esc_html__( 'Order on', 'classified-listing-pro' ) . ' ' . current_time( "l jS F Y h:i:s A" ),
			'post_status' => 'rtcl-created',
			'post_parent' => '0',
			'ping_status' => 'closed',
			'post_author' => 1,
			'post_type'   => rtcl()->post_type_payment,
			'meta_input'  => [
				'customer_id'           => get_current_user_id(),
				'customer_ip_address'   => Functions::get_ip_address(),
				'_order_key'            => apply_filters( 'rtcl_generate_order_key', uniqid( 'rtcl_oder_' ) ),
				'_pricing_id'           => $plan->getId(),
				'amount'                => $plan->getPrice(),
				'_payment_method'       => $gateway->id,
				'_payment_method_title' => $gateway->method_title,
				'_order_currency'       => Functions::get_order_currency(),
			]
		];

		if ( "promotion" === $type && 'regular' === $plan->getType() ) {
			$new_order_args['meta_input']['listing_id'] = $listing ? $listing->get_id() : 0;
		}

		$order_id = wp_insert_post( apply_filters( 'rtcl_rest_api_checkout_process_new_order_args', $new_order_args, $plan, $gateway, $request ) );

		if ( ! $order_id ) {
			wp_send_json( [
				'status'  => "error",
				'error'   => 'VALIDATION_ERROR',
				'code'    => '403',
				'message' => esc_html__( "Error to create new payment.", "classified-listing-pro" )
			], 403 );
		}

		$order = rtcl()->factory->get_order( $order_id );
		$order->set_order_key();
		do_action( 'rtcl_rest_checkout_process_new_payment_created', $order );
		// process payment
		$processed_data = [];
		if ( $order->get_total() > 0 ) {
			$processed_data = $gateway->process_payment( $order, $cardData );
			$processed_data = apply_filters( 'rtcl_rest_checkout_processed_payment_data', $processed_data, $order );
			if ( ! isset( $processed_data['result'] ) || 'success' !== $processed_data['result'] ) {
				do_action( 'rtcl_rest_checkout_process_error', $order, $processed_data );
				wp_delete_post( $order->get_id(), true );
				$response = [
					'status'  => "error",
					'error'   => ! empty( $processed_data['error'] ) ? esc_html( $processed_data['error'] ) : 'PAYMENT_PROCESS_ERROR',
					'code'    => '403',
					'message' => ! empty( $processed_data['message'] ) ? esc_html( $processed_data['message'] ) : esc_html__( "Error to process the payment.", "classified-listing-pro" )
				];
				wp_send_json( $response, 403 );
			}

		} else {
			$gateway = Functions::get_payment_gateway( 'offline' );
			update_post_meta( $order->get_id(), '_payment_method', $gateway->id );
			update_post_meta( $order->get_id(), '_payment_method_title', $gateway->method_title );
			$order->payment_complete( wp_generate_password( 12, true ) );
		}

		do_action( 'rtcl_rest_checkout_process_success', $order, $processed_data, $request );

		$data = Api::get_single_order_data( $order );
		$data = array_merge( $data, $processed_data );

		return rest_ensure_response( apply_filters( 'rtcl_rest_api_checkout_success_data', $data ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function payment_gateways_callback( WP_REST_Request $request ) {
		$gateways = rtcl()->payment_gateways();
		$data     = [];
		if ( ! empty( $gateways ) ) {
			foreach ( $gateways as $gateway ) {
				if ( 'yes' === $gateway->enabled ) {
					if ( "woo-payment" == $gateway->id ) {
						return rest_ensure_response( $gateway->rest_api_data() );
					} else {
						$data[] = apply_filters( 'rtcl_rest_api_gateway_data', $gateway->rest_api_data(), $gateway );
					}
				}
			}
		}

		return rest_ensure_response( $data );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function plans_callback( WP_REST_Request $request ) {
		$plans_args                = apply_filters( 'rtcl_rest_plans_args', [
			'posts_per_page'   => - 1,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'meta_query'       => [
				[
					[
						'key'   => 'pricing_type',
						'value' => 'regular'
					],
					[
						'key'     => 'pricing_type',
						'compare' => 'NOT EXISTS',
					],
					'relation' => 'OR'
				]
			],
			'suppress_filters' => false
		], $request );
		$plans_args['post_type']   = rtcl()->post_type_pricing;
		$plans_args['post_status'] = 'publish';
		$plans_args['fields']      = 'ids';

		$plan_ids = get_posts( $plans_args );
		$data     = [];
		if ( ! empty( $plan_ids ) ) {
			foreach ( $plan_ids as $id ) {
				$data[] = Api::get_plan_data( $id );
			}
		}

		return rest_ensure_response( $data );
	}
}