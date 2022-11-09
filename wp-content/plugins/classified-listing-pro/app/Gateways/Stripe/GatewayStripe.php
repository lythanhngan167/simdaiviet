<?php

namespace RtclPro\Gateways\Stripe;

use Exception;
use RtclPro\Gateways\Stripe\lib\StripeWebhook;
use stdClass;
use Rtcl\Models\Payment;
use RtclPro\Helpers\Api;
use Rtcl\Helpers\Functions;
use RtclPro\Gateways\Stripe\lib\StripeAPI;
use RtclPro\Gateways\Stripe\lib\StripeCustomer;
use RtclPro\Gateways\Stripe\lib\StripeException;
use RtclPro\Gateways\Stripe\lib\StripeHelper;
use RtclPro\Gateways\Stripe\lib\StripeLogger;
use RtclPro\Gateways\Stripe\lib\StripePaymentGateway;

class GatewayStripe extends StripePaymentGateway {

	const ID = 'stripe';
	const SESSION_KEY = 'stripe_rtcl_order_id';
	/**
	 * @var bool
	 */
	private $stripe_receipt_email;
	/**
	 * @var string[]
	 */
	private $stripe_zerocurrency;

	/**
	 * @var string
	 */
	private $statement_descriptor;

	/**
	 * Should we store the users credit cards?
	 *
	 * @var bool
	 */
	public $saved_cards;

	/**
	 * Should we capture Credit cards
	 *
	 * @var bool
	 */
	public $capture;

	/**
	 * @var bool
	 */
	private $testmode;

	/**
	 * @var bool
	 */
	public $inline_cc_form;

	public $retry_interval;
	/**
	 * @var null|object|stdClass
	 */
	private $order_pay_intent;

	public function __construct() {
		$this->retry_interval = 1;
		$this->id             = self::ID;
		$this->option         = $this->option . $this->id;
		$this->icon           = plugins_url( 'images/stripe.png', __FILE__ );
		$this->has_fields     = true;
		$this->method_title   = esc_html__( 'Stripe', 'classified-listing-pro' );
		$this->init_form_fields();
		$this->init_settings();

		$this->title                = $this->get_option( 'title' );
		$this->description          = $this->get_option( 'description' );
		$this->stripe_testpublickey = $this->get_option( 'stripe_testpublickey' );
		$this->stripe_testsecretkey = $this->get_option( 'stripe_testsecretkey' );
		$this->stripe_livepublickey = $this->get_option( 'stripe_livepublickey' );
		$this->stripe_livesecretkey = $this->get_option( 'stripe_livesecretkey' );
		// TODO: need to remove this
		$this->stripe_sandbox       = 'yes' === $this->get_option( 'stripe_sandbox' );
		$this->testmode             = 'yes' === $this->get_option( 'testmode' ) || $this->stripe_sandbox;
		$this->inline_cc_form       = 'yes' === $this->get_option( 'inline_cc_form' );
		$this->capture              = $this->get_option( 'capture' );
		$this->statement_descriptor = $this->get_option( 'statement_descriptor' );
		$this->saved_cards          = 'yes' === $this->get_option( 'saved_cards' );

		$this->stripe_receipt_email = 'yes' === $this->get_option( 'stripe_receipt_email' );

		$this->stripe_zerocurrency = [
			"BIF",
			"CLP",
			"DJF",
			"GNF",
			"JPY",
			"KMF",
			"KRW",
			"MGA",
			"PYG",
			"RWF",
			"VND",
			"VUV",
			"XAF",
			"XOF",
			"XPF"
		];

		add_action( 'admin_notices', [ $this, 'do_ssl_check' ] );
		add_action( 'rtcl_payment_receipt_top_' . $this->id, [ $this, 'render_payment_intent_inputs' ], 10, 2 );
		add_action( 'rtcl_api_rtcl_gateway_' . $this->id . '_confirm_payment_intent', [
			$this,
			'api_confirm_payment_intent'
		] );
		add_action( 'rtcl_api_rtcl_gateway_' . $this->id . '_webhook', [ $this, 'webhook' ] );
	}

	public function webhook() {
		$webHook = new StripeWebhook();
		$webHook->check_for_webhook();
	}

	/**
	 *
	 */
	public function api_confirm_payment_intent() {
		$post      = file_get_contents( 'php://input' );
		$post_data = json_decode( $post, true );
		if ( 0 !== json_last_error() ) {
			$post_data = null;
		}
		if ( ! empty( $_POST['rest_api'] ) || $post_data && ! empty( $post_data['rest_api'] ) ) {
			$permission = Api::permission_check();
			if ( true !== $permission ) {
				$response = [
					'status' => 'error',
					'error'  => [
						'type'    => 'authentication_error',
						'message' => is_wp_error( $permission ) ? $permission->get_error_message() : esc_html__( 'Rest Api permission error', 'classified-listing-pro' ),
					],
				];
				wp_send_json( $response );
			}
			Api::is_valid_auth_request();
			$order_id = isset( $_POST['order_id'] ) ? absint( Functions::clean( wp_unslash( $_POST['order_id'] ) ) ) : 0;
			$order_id = ! $order_id && isset( $post_data['order_id'] ) ? absint( Functions::clean( wp_unslash( $post_data['order_id'] ) ) ) : $order_id;
		} else {
			if ( empty( rtcl()->session ) ) {
				rtcl()->initialize_session();
			}
			$order_id         = absint( rtcl()->session->get( self::SESSION_KEY ) );
			$request_order_id = isset( $_POST['order_id'] ) ? Functions::clean( wp_unslash( $_POST['order_id'] ) ) : ( isset( $_GET['order_id'] ) ? Functions::clean( wp_unslash( $_GET['order_id'] ) ) : 0 );
			$order_id         = $order_id ?: absint( $request_order_id );
		}
		$order = rtcl()->factory->get_order( $order_id );
		if ( ! get_current_user_id() || ! $order ) {
			$response = [
				'status' => 'error',
				'error'  => [
					'type'    => 'authentication_error',
					'message' => __( 'You are not logged in or Order is missing.', 'classified-listing-pro' ),
				],
			];
			wp_send_json( $response, 403 );
		}

		try {
			$intent = $this->get_intent_from_order( $order );
			if ( empty( $intent->error ) && isset( $intent->object ) && 'payment_intent' === $intent->object ) {
				// Confirm the intent after locking the order to make sure webhooks will not interfere.
				$this->lock_order_payment( $order, $intent );
				$prepared_pm_obj = $this->prepare_order_pm_obj( $order );
				$confirm_intent  = $this->confirm_intent( $intent, $order, $prepared_pm_obj );
				if ( ! empty( $confirm_intent->error ) ) {
					$this->maybe_remove_non_existent_customer( $confirm_intent->error, $order );
					$this->unlock_order_payment( $order );
					$this->throw_localized_message( $confirm_intent, $order );
				} else {
					// Use the last charge within the intent to proceed.
					$response = end( $confirm_intent->charges->data );
					// Process valid response.
					$this->process_response( $response, $order );

					// Unlock the order.
					$this->unlock_order_payment( $order );


					// Return thank you page redirect.
					$response = [
						'result'     => 'success',
						'redirect'   => $this->get_return_url( $order ),
						'order_data' => APi::get_order_data( $order )
					];
				}
			} else {
				$response = [
					'status' => 'error',
					'error'  => [
						'type'    => 'confirm_payment_intent_error',
						'message' => 'Intent creating error',
					],
				];
			}

		} catch ( StripeException $e ) {
			$response = [
				'status' => 'error',
				'error'  => [
					'type'    => 'confirm_payment_intent_error',
					'message' => $e->getMessage(),
				],
			];
		} catch ( Exception $e ) {
			$response = [
				'status' => 'error',
				'error'  => [
					'type'    => 'confirm_payment_intent_error',
					'message' => $e->getMessage(),
				],
			];
		}
		wp_send_json( $response );
	}

	/**
	 * @param int     $payment_id
	 * @param Payment $order
	 *
	 * @return void
	 * @throws Exception
	 */
	public function render_payment_intent_inputs( $payment_id, $order ) {
		if ( ! $order instanceof Payment || $order->get_payment_method() !== $this->id || $order->get_status() !== 'rtcl-failed' || ! $order->get_meta( '_stripe_requires_action' ) ) {
			return;
		}
		try {
			$this->prepare_payment_intent_for_order_page( $order );
		} catch ( StripeException $e ) {
		}
		if ( ! empty( $this->order_pay_intent ) && $this->order_pay_intent->status === 'requires_action' ) {
			$this->load_scripts();
			echo '<input type="hidden" id="stripe-intent-id" value="' . esc_attr( $this->order_pay_intent->client_secret ) . '" />';
			echo '<input type="hidden" id="stripe-intent-return" value="' . esc_attr( $this->get_return_url( $order ) ) . '" />';
		}
	}

	/**
	 * Prepares the Payment Intent for it to be completed in the "Pay for Order" page.
	 *
	 * @param Payment|null $order Order object, or null to get the order from the "order-pay" URL parameter
	 *
	 * @throws StripeException
	 * @throws Exception
	 */
	public function prepare_payment_intent_for_order_page( $order = null ) {
		$intent = $this->get_intent_from_order( $order );

		if ( ! $intent ) {
			throw new StripeException(
				'Payment Intent not found',
				sprintf(
				/* translators: %s is the order Id */
					__( 'Payment Intent not found for order #%s', 'classified-listing-pro' ),
					$order->get_id()
				)
			);
		}

		if ( 'requires_payment_method' === $intent->status && isset( $intent->last_payment_error )
			 && 'authentication_required' === $intent->last_payment_error->code ) {
			$level3_data = $this->get_level3_data_from_order( $order );
			$intent      = StripeAPI::request_with_level3_data(
				[
					'payment_method' => $intent->last_payment_error->source->id,
				],
				'payment_intents/' . $intent->id . '/confirm',
				$level3_data,
				$order
			);

			if ( isset( $intent->error ) ) {
				throw new StripeException( print_r( $intent, true ), $intent->error->message );
			}
		}

		$this->order_pay_intent = $intent;
	}

	public function admin_options() {
		?>
		<h3><?php esc_html_e( 'Stripe Credit cards payment gateway addon for Classified listing', 'classified-listing-pro' ); ?></h3>
		<p><?php esc_html_e( 'Stripe is a company that provides a way for individuals and businesses to accept payments over the Internet.', 'classified-listing-pro' ); ?></p>
		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
			<script type="text/javascript">

				jQuery('#rtcl_stripe_statement_descriptor').on('keypress', function () {
					if (jQuery('#rtcl_stripe_statement_descriptor').val().length > 22) {
						alert('Statement Descriptor Accepts only 22 Characters.When you close this popup field will be emptied please make sure not to enter more than 22 Characters.');
						jQuery('#rtcl_stripe_statement_descriptor').val('');
					}
				})
				jQuery('#rtcl_stripe_testmode').on('change', function () {
					var sandbox = jQuery('#rtcl_stripe_stripe_testsecretkey, #rtcl_stripe_stripe_testpublickey').closest('tr'),
						production = jQuery('#rtcl_stripe_stripe_livesecretkey, #rtcl_stripe_stripe_livepublickey').closest('tr');

					if (jQuery(this).is(':checked')) {
						sandbox.show();
						production.hide();
					} else {
						sandbox.hide();
						production.show();
					}
				}).change();
			</script>
		</table>
		<?php
	}

	public function init_form_fields() {

		$this->form_fields = [
			'enabled'              => [
				'title' => esc_html__( 'Enable/Disable', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Enable Stripe', 'classified-listing-pro' ),
			],
			'title'                => [
				'title'       => esc_html__( 'Title', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This controls the title which the user sees during checkout.',
					'classified-listing-pro' ),
				'default'     => esc_html__( 'Credit Card', 'classified-listing-pro' ),
			],
			'description'          => [
				'title'       => esc_html__( 'Description', 'classified-listing-pro' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'This controls the description which the user sees during checkout.',
					'classified-listing-pro' ),
				'default'     => esc_html__( 'All cards are stored by &copy;Stripe servers we do not store any card details',
					'classified-listing-pro' ),
			],
			'api_credentials'      => [
				'title' => __( 'Stripe Account Keys', 'classified-listing-pro' ),
				'type'  => 'title',
			],
			'testmode'             => [
				'title'       => __( 'Test mode', 'classified-listing-pro' ),
				'label'       => __( 'Enable Test Mode', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in test mode using test API keys.', 'classified-listing-pro' ),
				'default'     => 'yes',
			],
			'stripe_testpublickey' => [
				'title'       => esc_html__( 'Test Publishable Key', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the Publishable Key found in API Keys in Account Dashboard.',
					'classified-listing-pro' ),
				'default'     => '',
				'placeholder' => 'Stripe Test Publishable Key',
				'dependency'  => [
					'rules' => [
						'#rtcl_payment_stripe-testmode' => [
							'type'  => 'equal',
							'value' => 'yes'
						]
					]
				]
			],
			'stripe_testsecretkey' => [
				'title'       => esc_html__( 'Test Secret Key', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the Secret Key found in API Keys in Account Dashboard.',
					'classified-listing-pro' ),
				'default'     => '',
				'placeholder' => 'Stripe Test Secret Key',
				'dependency'  => [
					'rules' => [
						'#rtcl_payment_stripe-testmode' => [
							'type'  => 'equal',
							'value' => 'yes'
						]
					]
				]
			],
			'stripe_livepublickey' => [
				'title'       => esc_html__( 'Live Publishable Key', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the Publishable Key found in API Keys in Account Dashboard.',
					'classified-listing-pro' ),
				'default'     => '',
				'placeholder' => 'Stripe Live Publishable Key',
				'dependency'  => [
					'rules' => [
						'#rtcl_payment_stripe-testmode' => [
							'type'  => 'notequal',
							'value' => 'yes'
						]
					]
				]
			],
			'stripe_livesecretkey' => [
				'title'       => esc_html__( 'Live Secret Key', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the Secret Key found in API Keys in Account Dashboard.',
					'classified-listing-pro' ),
				'default'     => '',
				'placeholder' => 'Stripe Live Secret Key',
				'dependency'  => [
					'rules' => [
						'#rtcl_payment_stripe-testmode' => [
							'type'  => 'notequal',
							'value' => 'yes'
						]
					]
				]
			],
			'inline_cc_form'       => [
				'title'       => __( 'Inline Credit Card Form', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Choose the style you want to show for your credit card form. When unchecked, the credit card form will display separate credit card number field, expiry date field and cvc field.', 'classified-listing-pro' ),
				'default'     => 'yes',
			],
			'statement_descriptor' => [
				'title'       => __( 'Statement Descriptor', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => __( 'Statement descriptors are limited to 22 characters, cannot use the special characters >, <, ", \, \', *, /, (, ), {, }, and must not consist solely of numbers. This will appear on your customer\'s statement in capital letters.', 'classified-listing-pro' ),
				'default'     => '',
			],
			'capture'              => [
				'title'       => __( 'Capture', 'classified-listing-pro' ),
				'label'       => __( 'Capture charge immediately', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'classified-listing-pro' ),
				'default'     => 'yes',
			],
			'stripe_receipt_email' => [
				'title'       => esc_html__( 'Enable stripe receipt email', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable receipt email from Stripe (Active If Checked)', 'classified-listing-pro' ),
				'description' => esc_html__( 'If checked will send stripe receipt email to billing email in live mode only',
					'classified-listing-pro' ),
				'default'     => 'no',
			],
			'saved_cards'          => [
				'title'       => __( 'Saved Cards', 'classified-listing-pro' ),
				'label'       => __( 'Enable Payment via Saved Cards', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Stripe servers, not on your store.', 'classified-listing-pro' ),
				'default'     => 'yes',
			],
			'force_3d_secure'      => [
				'title'       => __( 'Force 3D Secure', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'default'     => 'no',
				'description' => sprintf( __( 'Stripe internally determines when 3D secure should be presented based on their SCA engine. If <strong>Force 3D Secure</strong> is enabled, 3D Secure will be forced for ALL credit card transactions. In test mode 3D secure only shows for %1$s3DS Test Cards%2$s regardless of this setting.', 'classified-listing-pro' ), '<a target="_blank" href="https://stripe.com/docs/testing#regulatory-cards">', '</a>' ),
			],
			'webhook_url'          => [
				'type'        => 'html',
				'title'       => __( 'Webhook url', 'classified-listing-pro' ),
				'html'        => '<strong>' . $this->getApiRequestUrl( 'rtcl_gateway_' . $this->id . '_webhook' ) . '</strong>',
				'description' => sprintf( __( '<strong>Important:</strong> the webhook url is called by Stripe when events occur in your account, like a source becomes chargeable. You must add this webhook to your Stripe Dashboard if you are using any of the local gateways. %1$sWebhook guide%2$s', 'classified-listing-pro' ), '<a target="_blank" href="https://docs.paymentplugins.com/wc-stripe/config/#/webhooks?id=configure-webhooks">', '</a>' ),
			],
			'webhook_secret_live'  => [
				'type'        => 'text',
				'title'       => __( 'Live Webhook Secret', 'classified-listing-pro' ),
				'description' => sprintf( __( 'The webhook secret is used to authenticate webhooks sent from Stripe. It ensures no 3rd party can send you events, pretending to be Stripe. %1$sWebhook guide%2$s', 'classified-listing-pro' ), '<a target="_blank" href="https://docs.paymentplugins.com/wc-stripe/config/#/webhooks?id=configure-webhooks">', '</a>' ),
				'dependency'  => [
					'rules' => [
						'#rtcl_payment_stripe-testmode' => [
							'type'  => 'notequal',
							'value' => 'yes'
						]
					]
				]
			],
			'webhook_secret_test'  => [
				'type'        => 'text',
				'title'       => __( 'Test Webhook Secret', 'classified-listing-pro' ),
				'description' => sprintf( __( 'The webhook secret is used to authenticate webhooks sent from Stripe. It ensures no 3rd party can send you events, pretending to be Stripe. %1$sWebhook guide%2$s', 'classified-listing-pro' ), '<a target="_blank" href="https://docs.paymentplugins.com/wc-stripe/config/#/webhooks?id=configure-webhooks">', '</a>' ),
				'dependency'  => [
					'rules' => [
						'#rtcl_payment_stripe-testmode' => [
							'type'  => 'equal',
							'value' => 'yes'
						]
					]
				]
			],
			'logging'              => [
				'title'       => __( 'Logging', 'classified-listing-pro' ),
				'label'       => __( 'Log debug messages', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Save debug messages to the Classified Listing System Status log.', 'classified-listing-pro' ),
				'default'     => 'no',
			],
		];

	}

	public function get_description() {
		return apply_filters( 'rtcl_gateway_description', wpautop( wptexturize( trim( $this->description ) ) ), $this->id );
	}

	/*Is Available*/
	public function is_available() {
		if ( ! in_array( Functions::get_order_currency(), apply_filters( 'rtcl_stripe_supported_currencies',
			[
				'AED',
				'ALL',
				'ANG',
				'ARS',
				'AUD',
				'AWG',
				'BBD',
				'BDT',
				'BIF',
				'BMD',
				'BND',
				'BOB',
				'BRL',
				'BSD',
				'BWP',
				'BZD',
				'CAD',
				'CHF',
				'CLP',
				'CNY',
				'COP',
				'CRC',
				'CVE',
				'CZK',
				'DJF',
				'DKK',
				'DOP',
				'DZD',
				'EGP',
				'ETB',
				'EUR',
				'FJD',
				'FKP',
				'GBP',
				'GIP',
				'GMD',
				'GNF',
				'GTQ',
				'GYD',
				'HKD',
				'HNL',
				'HRK',
				'HTG',
				'HUF',
				'IDR',
				'ILS',
				'INR',
				'ISK',
				'JMD',
				'JPY',
				'KES',
				'KHR',
				'KMF',
				'KRW',
				'KYD',
				'KZT',
				'LAK',
				'LBP',
				'LKR',
				'LRD',
				'MAD',
				'MDL',
				'MNT',
				'MOP',
				'MRO',
				'MUR',
				'MVR',
				'MWK',
				'MXN',
				'MYR',
				'NAD',
				'NGN',
				'NIO',
				'NOK',
				'NPR',
				'NZD',
				'PAB',
				'PKR',
				'PLN',
				'PYG',
				'QAR',
				'RUB',
				'SAR',
				'SBD',
				'SCR',
				'SEK',
				'SGD',
				'SHP',
				'SLL',
				'SOS',
				'STD',
				'SVC',
				'SZL',
				'THB',
				'TOP',
				'TTD',
				'TWD',
				'TZS',
				'UAH',
				'UGX',
				'USD',
				'UYU',
				'UZS',
				'VND',
				'VUV',
				'WST',
				'XAF',
				'XOF',
				'XPF',
				'YER',
				'ZAR',
				'AFN',
				'AMD',
				'AOA',
				'AZN',
				'BAM',
				'BGN',
				'CDF',
				'GEL',
				'KGS',
				'LSL',
				'MGA',
				'MKD',
				'MZN',
				'RON',
				'RSD',
				'RWF',
				'SRD',
				'TJS',
				'TRY',
				'XCD',
				'ZMW'
			] ) ) ) {
			return false;
		}


		if ( $this->testmode && ( empty( $this->stripe_testpublickey ) || empty( $this->stripe_testsecretkey ) ) ) {
			return false;
		}

		if ( ! $this->testmode && ( empty( $this->stripe_livepublickey ) || empty( $this->stripe_livesecretkey ) ) ) {
			return false;
		}

		return true;
	}

	public function do_ssl_check() {
		$payment_options = Functions::get_option( 'rtcl_payment_settings' );
		$use_https       = ! empty( $payment_options['use_https'] ) ? $payment_options['use_https'] : 'no';
		if ( ! $this->testmode && "no" == $use_https && "yes" == $this->enabled ) {
			echo '<div class="error"><p>' . wp_kses(
					sprintf(
						__( "<strong>%s</strong> is enabled and Classified listing is not forcing the SSL on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>", 'classified-listing-pro' ),
						$this->method_title,
						admin_url( 'admin.php?page=rtcl-settings&tab=payment' )
					),
					[ 'strong' => [] ]
				) . '</p></div>';
		}
	}

	public function load_scripts() {

		wp_register_style( 'stripe_rtcl', plugins_url( 'assets/css/stripe-rtcl.css', __FILE__ ), [], RTCL_PRO_VERSION );
		wp_enqueue_style( 'stripe_rtcl' );
		wp_register_script( 'stripe', 'https://js.stripe.com/v3/', '', '3.0', true );
		wp_enqueue_script( 'stripe_rtcl', plugins_url( 'assets/js/stripe-rtcl.js', __FILE__ ), [
			'stripe',
			'jquery',
			'rtcl-common',
			'rtcl-validator'
		], RTCL_PRO_VERSION, true );
		$billing_name  = '';
		$billing_email = '';
		if ( is_user_logged_in() ) {
			$user          = wp_get_current_user();
			$billing_name  = trim( $user->user_firstname . ' ' . $user->user_lastname );
			$billing_email = $user->user_email;
		}
		$billing_name  = empty( $billing_name ) ? "Rtcl Listing" : $billing_name;
		$stripe_params = [
			'key'              => $this->testmode ? $this->stripe_testpublickey : $this->stripe_livepublickey,
			'billing'          => [
				'name'  => $billing_name,
				'email' => $billing_email
			],
			'routes'           => [
				'confirm_payment_intent' => $this->getApiRequestUrl( 'rtcl_gateway_' . $this->id . '_confirm_payment_intent' ),
			],
			'local'            => StripeHelper::convert_rtcl_locale_to_stripe_locale( get_locale() ),
			'elements_options' => apply_filters( 'rtcl_stripe_elements_options', [] ),
			'inline_cc_form'   => $this->inline_cc_form,
			'elements_styling' => apply_filters( 'rtcl_stripe_elements_styling', false ),
			'elements_classes' => apply_filters( 'rtcl_stripe_elements_classes', false ),
			'i18'              => StripeHelper::get_localized_messages()
		];


		wp_localize_script( 'stripe_rtcl', 'rtcl_stripe_params', apply_filters( 'rtcl_stripe_params', $stripe_params ) );

	}

	/*Get Icon*/
	public function get_icon() {
		$icon = '<img src="' . esc_url( plugins_url( 'images/stripe.png', __FILE__ ) ) . '" alt="Stripe Gateway" />';

		return apply_filters( 'rtcl_stripe_icon', $icon, $this->id );
	}


	/*Start of credit card form */
	public function payment_fields() {
		$html = apply_filters( 'rtcl_stripe_description', wpautop( wp_kses_post( wptexturize( trim( $this->description ) ) ) ) );
		$html .= $this->form();

		return $html;
	}

	public function form() {
		$this->load_scripts();
		ob_start();
		?>
		<fieldset id="rtcl-<?php echo esc_attr( $this->id ); ?>-cc-form"
				  class='rtcl-credit-card-form rtcl-payment-form'>
			<?php do_action( 'rtcl_credit_card_form_start', $this->id ); ?>
			<?php if ( $this->inline_cc_form ) { ?>
				<label for="card-element">
					<?php esc_html_e( 'Credit or debit card', 'classified-listing-pro' ); ?>
				</label>

				<div id="stripe-card-element" class="rtcl-stripe-elements-field">
					<!-- a Stripe Element will be inserted here. -->
				</div>
			<?php } else { ?>
				<div class="form-group">
					<label for="stripe-card-number"><?php esc_html_e( 'Card Number', 'classified-listing-pro' ) ?>
						<span class="required">*</span></label>
					<div class="stripe-card-group">
						<div id="stripe-card-element" class="rtcl-stripe-elements-field">
							<!-- a Stripe Element will be inserted here. -->
						</div>

						<i class="stripe-credit-card-brand stripe-card-brand" alt="Credit Card"></i>
					</div>
				</div>
				<div class="form-row">
					<div class="col form-group">
						<label for="stripe-exp-element"><?php esc_html_e( 'Expiry Date', 'classified-listing-pro' ) ?>
							<span class="required">*</span></label>
						<div id="stripe-exp-element" class="rtcl-stripe-elements-field">
							<!-- a Stripe Element will be inserted here. -->
						</div>
					</div>
					<div class="col form-group">
						<label
							for="stripe-cvc-element"><?php esc_html_e( 'Card Code (CVC)', 'classified-listing-pro' ) ?>
							<span class="required">*</span></label>
						<div id="stripe-cvc-element" class="rtcl-stripe-elements-field">
							<!-- a Stripe Element will be inserted here. -->
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="stripe-source-errors" role="alert"></div>
			<?php do_action( 'rtcl_credit_card_form_end', $this->id ); ?>
		</fieldset>
		<?php
		return ob_get_clean();
	}

	/**
	 * Process the payment
	 *
	 * @param Payment $order
	 * @param array   $data
	 *
	 * @return array
	 * @throws Exception If payment will not be accepted.
	 * @since 4.1.0 Add 4th parameter to track previous error.
	 */
	public function process_payment( $order, $data = [] ) {

		if ( ! $order instanceof Payment ) {
			return [
				'result'   => 'error',
				'message'  => esc_html__( 'Payment not found!', 'classified-listing-pro' ),
				'redirect' => '',
			];
		}
		if ( empty( rtcl()->session ) ) {
			rtcl()->initialize_session();
		}
		$paymentMethodId = ! empty( $data['stripe_payment_method'] ) ? Functions::clean( wp_unslash( $data['stripe_payment_method'] ) ) : ( isset( $_POST['stripe_payment_method'] ) ? Functions::clean( wp_unslash( $_POST['stripe_payment_method'] ) ) : '' );

		rtcl()->session->set( self::SESSION_KEY, $order->get_id() );
		$is_rest = defined( 'REST_REQUEST' ) && REST_REQUEST;

		try {
			$force_save_source = false;
			if ( $is_rest && ! $paymentMethodId ) {
				$prepared_pm_obj = $this->prepare_payment_method( wp_get_current_user()->ID );
			} else {
				// 1. Verify.
				if ( ! preg_match( '/^pm_.*$/', $paymentMethodId ) ) {
					throw new Exception( __( 'Unable to verify your request. Please reload the page and try again.', 'classified-listing-pro' ) );
				}
				$paymentMethod_obj = self::get_pm_object( $paymentMethodId );
				// 2. Load the customer ID (and create a customer eventually).
				$customer           = new StripeCustomer( wp_get_current_user()->ID );
				$pm_object          = $customer->attach_payment_method( $paymentMethod_obj->id );
				$stripe_customer_id = ! empty( $pm_object->customer ) ? $pm_object->customer : null;
				$prepared_pm_obj    = $this->prepare_payment_method( get_current_user_id(), $force_save_source, $stripe_customer_id, [ 'stripe_payment_method' => $paymentMethodId ] );

				$this->maybe_disallow_prepaid_card( $prepared_pm_obj );
				$this->check_pm( $prepared_pm_obj );
				$this->save_pm_to_order( $order, $prepared_pm_obj );
			}


			if ( 0 >= $order->get_total() ) {
				return $this->complete_free_order( $order, $prepared_pm_obj, $force_save_source );
			}

			// This will throw exception if not valid.
			$this->validate_minimum_order_amount( $order );

			StripeLogger::log( "Info: Begin processing payment for order {$order->get_id()} for the amount of {$order->get_total()}" );

			$intent = $this->create_intent( $order, $prepared_pm_obj );

			// Confirm the intent after locking the order to make sure webhooks will not interfere.
			if ( empty( $intent->error ) ) {
				$this->lock_order_payment( $order, $intent );
				$intent = $this->confirm_intent( $intent, $order, $prepared_pm_obj );
			}

			if ( ! empty( $intent->error ) ) {
				$this->maybe_remove_non_existent_customer( $intent->error, $order );
				$this->unlock_order_payment( $order );
				$this->throw_localized_message( $intent, $order );
			}
			if ( ! empty( $intent ) ) {
				// Use the last charge within the intent to proceed.
				$response = end( $intent->charges->data );
				// If the intent requires a 3DS flow, redirect to it.
				if ( 'requires_action' === $intent->status || 'requires_source_action' === $intent->status || 'requires_payment_method' === $intent->status ) {
					$order->update_status( 'failed' );
					update_post_meta( $order->get_id(), '_stripe_requires_action', $intent->status );
					$this->unlock_order_payment( $order );

					return [
						'requiresAction'               => true,
						'pi_status'                    => $intent->status,
						'result'                       => 'success',
						'messages'                     => __( "Your order is created and please confirm your order", 'classified-listing-pro' ),
						'redirect'                     => $this->get_return_url( $order ),
						'payment_intent_client_secret' => $intent->client_secret
					];
				}
			}
			// Process valid response.
			$this->process_response( $response, $order );

			// Unlock the order.
			$this->unlock_order_payment( $order );

			// Return thank you page redirect.
			return [
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			];

		} catch ( StripeException $e ) {
			Functions::add_notice( $e->getLocalizedMessage(), 'error' );
			StripeLogger::log( 'Error: ' . $e->getMessage() );

			do_action( 'rtcl_gateway_stripe_process_payment_error', $e, $order );

			/* translators: error message */
			$order->update_status( 'failed' );

			return [
				'result'   => 'fail',
				'redirect' => '',
			];
		}
	}

	/**
	 * Checks if a source object represents a prepaid credit card and
	 * throws an exception if it is one, but that is not allowed.
	 *
	 * @param object $prepared_source The object with source details.
	 *
	 * @throws StripeException An exception if the card is prepaid, but prepaid cards are not allowed.
	 */
	public function maybe_disallow_prepaid_card( $prepared_source ) {
		// Check if we don't allow prepaid credit cards.
		if ( apply_filters( 'rtcl_stripe_allow_prepaid_card', true ) || ! $this->is_prepaid_card( $prepared_source->source_object ) ) {
			return;
		}

		$localized_message = __( 'Sorry, we\'re not accepting prepaid cards at this time. Your credit card has not been charged. Please try with alternative payment method.', 'classified-listing-pro' );
		throw new StripeException( print_r( $prepared_source->source_object, true ), $localized_message );
	}

	/**
	 * Checks whether a payment method id exists.
	 *
	 * @param object $prepared_pm The source that should be verified.
	 *
	 * @throws StripeException     An exception if the paymentMethod ID is missing.
	 */
	public function check_pm( $prepared_pm ) {
		if ( empty( $prepared_pm->pm_id ) ) {
			$localized_message = __( 'Payment processing failed. Please retry.', 'classified-listing-pro' );
			throw new StripeException( print_r( $prepared_pm, true ), $localized_message );
		}
	}

	/**
	 * Completes an order without a positive value.
	 *
	 * @param Payment $order           The order to complete.
	 * @param object  $prepared_source Payment source and customer data.
	 * @param bool    $force_save_pm   Whether the payment source must be saved, like when dealing with a Subscription setup.
	 *
	 * @return array                      Redirection data for `process_payment`.
	 * @throws StripeException
	 */
	public function complete_free_order( $order, $prepared_source, $force_save_pm ) {
//        if ($force_save_pm) {
//            $intent_secret = $this->setup_intent($order, $prepared_source);
//
//            if (!empty($intent_secret)) {
//                // `get_return_url()` must be called immediately before returning a value.
//                return [
//                    'result'              => 'success',
//                    'redirect'            => $this->get_return_url($order),
//                    'setup_intent_secret' => $intent_secret,
//                ];
//            }
//        }

		$order->payment_complete();

		// Return thank you page redirect.
		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	/**
	 * Saves payment method
	 *
	 * @param object $pm_object
	 *
	 * @throws StripeException
	 */
	public function save_payment_method( $pm_object ) {
		$user_id  = get_current_user_id();
		$customer = new StripeCustomer( $user_id );

		if ( ( $user_id && 'reusable' === $pm_object->usage ) ) {
			$response = $customer->attach_payment_method( $pm_object->id );

			if ( ! empty( $response->error ) ) {
				throw new StripeException( print_r( $response, true ), $this->get_localized_error_message_from_response( $response ) );
			}
			if ( is_wp_error( $response ) ) {
				throw new StripeException( $response->get_error_message(), $response->get_error_message() );
			}
		}
	}


	/**
	 * Gets a localized message for an error from a response, adds it as a note to the order, and throws it.
	 *
	 * @param stdClass $response The response from the Stripe API.
	 * @param Payment  $order    The order to add a note to.
	 *
	 * @throws StripeException An exception with the right message.
	 */
	public function throw_localized_message( $response, $order ) {
		$localized_message = $this->get_localized_error_message_from_response( $response );

		$order->add_note( $localized_message );

		throw new StripeException( print_r( $response, true ), $localized_message );
	}


	/**
	 * Customer param wrong? The user may have been deleted on stripe's end. Remove customer_id. Can be retried without.
	 *
	 * @param object  $error The error that was returned from Stripe's API.
	 * @param Payment $order The order this payment is being processed.
	 *
	 * @return bool           A flag that indicates that the customer does not exist and should be removed.
	 */
	public function maybe_remove_non_existent_customer( $error, $order ) {
		if ( ! $this->is_no_such_customer_error( $error ) ) {
			return false;
		}

		delete_user_option( $order->get_customer_id(), '_stripe_customer_id' );
		delete_post_meta( $order->get_id(), '_stripe_customer_id' );

		return true;
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
	 * Generates a localized message for an error from a response.
	 *
	 * @param stdClass $response The response from the Stripe API.
	 *
	 * @return string The localized error message.
	 *
	 */
	public function get_localized_error_message_from_response( $response ) {
		$localized_messages = StripeHelper::get_localized_messages();

		if ( 'card_error' === $response->error->type ) {
			$localized_message = isset( $localized_messages[ $response->error->code ] ) ? $localized_messages[ $response->error->code ] : $response->error->message;
		} else {
			$localized_message = isset( $localized_messages[ $response->error->type ] ) ? $localized_messages[ $response->error->type ] : $response->error->message;
		}

		return $localized_message;
	}


	/**
	 * @return array
	 */
	public function rest_api_data() {
		$rest_api_data                                     = parent::rest_api_data();
		$rest_api_data['key']                              = $this->testmode ? $this->stripe_testpublickey : $this->stripe_livepublickey;
		$rest_api_data['routes']['confirm_payment_intent'] = $this->getApiRequestUrl( 'rtcl_gateway_' . $this->id . '_confirm_payment_intent' );

		return $rest_api_data;
	}
}
