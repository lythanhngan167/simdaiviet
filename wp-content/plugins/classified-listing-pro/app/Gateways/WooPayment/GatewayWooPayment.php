<?php


namespace RtclPro\Gateways\WooPayment;


use Exception;
use Rtcl\Models\PaymentGateway;
use RtclPro\Helpers\Api;

class GatewayWooPayment extends PaymentGateway {

	public $id = 'woo-payment';

	function __construct() {

		$this->option             = $this->option . $this->id;
		$this->order_button_text  = esc_html__( 'WooCommerce Payout', 'classified-listing-pro' );
		$this->method_title       = esc_html__( 'WooCommerce Payment', 'classified-listing-pro' );
		$this->method_description = esc_html__( 'Make a payment with WooCommerce payment methods.', 'classified-listing-pro' );
		// Load the settings.
		$this->init_form_fields();

		$this->init_settings();

		// Define user set variables.
		$this->enable      = $this->get_option( 'enable' );
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		add_action( 'rtcl_api_rtcl_gateway_woo', [ $this, 'goto_web_with_check' ] );
	}

	public function goto_web_with_check() {
		$request = wp_unslash( $_GET );
		Api::http_request_permission_check( $request );
		Api::is_valid_http_auth_request( $request );
		$pricing_id = ! empty( $request['pricing_id'] ) ? absint( $request['pricing_id'] ) : 0;
		$listing_id = ! empty( $request['listing_id'] ) ? absint( $request['listing_id'] ) : 0;
		$pricing    = rtcl()->factory->get_pricing( $pricing_id );
		if ( ! $pricing_id || ! $pricing || ! $pricing->exists() ) {
			wp_die( 'Invalid pricing id given', 'INVALID_PRICING', 401 );
		}
		$checkout_data = apply_filters( 'rtcl_checkout_process_data', wp_parse_args( $_REQUEST, [
			'type'           => '',
			'listing_id'     => $listing_id,
			'pricing_id'     => $pricing_id,
			'payment_method' => ''
		] ) );
		$gateway       = '';

		// Use WP_Error to handle checkout errors.
		$errors = new \WP_Error();
		do_action( 'rtcl_checkout_data', $checkout_data, $pricing, $gateway, $_REQUEST, $errors );
		$errors = apply_filters( 'rtcl_checkout_validation_errors', $errors, $checkout_data, $pricing, $gateway, $_REQUEST );
		if ( is_wp_error( $errors ) && $errors->has_errors() ) {
			wp_die( $errors->get_error_message(), $errors->get_error_code(), 401 );
		}
		// TODO: need to apply for 0 price
		try {
			$cart = rtcl()->cart;
			$cart->empty_cart();

			if ( $cart_id = $cart->add_to_cart( $pricing->getId(), 1, $checkout_data ) ) {
				do_action( "rtcl_process_checkout_handler", $pricing, $cart_id, $checkout_data );
			}

		} catch ( Exception $e ) {
			wp_die( $e->getMessage(), $e->getCode(), 401 );
		}
	}

	public function init_form_fields() {
		$available_payment_html = '';
		$payment_gateways       = WC()->payment_gateways()->payment_gateways();
		ob_start();
		if ( $payment_gateways ) {
			foreach ( $payment_gateways as $payment_gateway ) {
				$title = sprintf(
					esc_html__( 'This payment is %s, please click the link beside to enable/disable.', 'classified-listing-pro' ),
					$payment_gateway->enabled == 'yes' ? 'enabled' : 'disabled'
				);
				?>
				<li>
					<label>
                        <span title="<?php echo $title; ?>"
							  class="dashicons <?php echo $payment_gateway->enabled == 'yes' ? 'dashicons-yes' : 'dashicons-dismiss'; ?>"></span>
						<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_' . $payment_gateway->id ); ?>"
						   target="_blank"> <?php echo( $payment_gateway->method_title ); ?> </a>
					</label>
				</li>
				<?php
			}
		}
		$available_payment_html .= ob_get_clean();
		$this->form_fields      = [
			'enabled'            => [
				'title'       => esc_html__( 'Enable', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable WooCommerce Payment', 'classified-listing-pro' ),
				'description' => __( '<span style="color: red">If <strong>WooCommerce Payment</strong> is enabled you can not use other payments provided by ClassifiedListing.</span>', 'classified-listing-pro' )
			],
			'available_payments' => [
				'title'       => esc_html__( 'WooCommerce Payments', 'classified-listing-pro' ),
				'type'        => 'html',
				'html'        => $available_payment_html ? sprintf( '<ul class="rtcl-woo-payments">%s</ul>', $available_payment_html ) : '',
				'description' => __( 'List of all available payment gateways installed and activated for WooCommerce. Click on a payment method to go to <strong>WooCommerce Payment</strong> settings.', 'classified-listing-pro' ),
			],
//            'order_autocomplete_disable' => [
//                'label'       => __('Disable', 'classified-listing-pro'),
//                'title'       => __('Order To Autocomplete', 'classified-listing-pro'),
//                'type'        => 'checkbox',
//                'description' => __('Autocomplete WooCommerce Orders', 'classified-listing-pro')
//            ],
		];
	}

	/**
	 * @param string $id
	 *
	 * @return string
	 */
	private function getApiRequestUrl( $id ) {
		if ( empty( $id ) ) {
			return '';
		}

		return add_query_arg( 'rtcl-api', $id, trailingslashit( get_home_url() ) );
	}

	/**
	 * @return array
	 */
	public function rest_api_data() {
		$rest_api_data           = parent::rest_api_data();
		$rest_api_data['routes'] = [
			'web'     => $this->getApiRequestUrl( 'rtcl_gateway_woo' ),
			'allowed' => [
				'checkout' => wc_get_checkout_url()
			]
		];

		return $rest_api_data;
	}

}