<?php


namespace RtclPro\Gateways\WooPayment;

use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Log\Logger;
use Rtcl\Models\Pricing;
use RtclPro\Gateways\WooPayment\lib\WC_Product_RTCL_pricing;
use RtclPro\Helpers\Fns;
use WC_Order_Item_Product;

class WooPayment {

	/**
	 * @var array
	 */
	protected $_response = [];

	function __construct() {
		$this->_response['single_purchase'] = true;
		add_filter( 'woocommerce_product_class', [ $this, 'product_class' ], 10, 4 );
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'disable_quantity_box' ], 10, 3 );
		add_filter( 'woocommerce_add_to_cart_handler', [ $this, 'add_to_cart_handler' ], 10, 2 );
		add_action( 'woocommerce_order_status_changed', [ $this, 'rtcl_update_order_status' ], 10, 3 );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'create_order' ], 10, 2 );

		add_action( 'rtcl_add_to_cart', [ $this, 'add_to_woo' ], 100, 4 );
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'order_item_line' ], 10, 4 );

		add_action( 'rtcl_process_checkout_handler', [ __CLASS__, 'checkout_process_cb' ], 10, 3 );

		add_filter( 'rtcl_checkout_form_id', [ __CLASS__, 'rtcl_checkout_form_id' ] );

		add_filter( 'rtcl_checkout_validation_errors', [ __CLASS__, 'remove_gateway_validation' ], 100 );
		add_action( 'admin_notices', [ $this, 'wc_order_notice' ], 99 );

		remove_action( 'rtcl_checkout_form', [ TemplateHooks::class, 'checkout_terms_and_conditions' ], 50 );
		remove_action( 'rtcl_checkout_form', [ TemplateHooks::class, 'add_checkout_payment_method' ] );

		if ( ! Fns::is_woo_order_autocomplete_disable() ) { // TODO : Need to implement this.
//            add_filter('woocommerce_payment_complete_order_status', [__CLASS__, 'autocomplete_wc_orders'], 99, 3);
//            add_action('woocommerce_payment_complete', [__CLASS__, 'autocomplete_wc_orders_action'], 99);
		}

		// Make pricing to wc order line item product
		add_filter( 'woocommerce_order_item_product', [ __CLASS__, 'pricing_to_order_item_product' ], - 1, 2 );

		// Add Dokan order line item info
		add_filter( 'dokan_get_vendor_order_details', [ __CLASS__, 'update_dokan_vendor_order_details' ], 20, 3 );

		// PayU India
		add_filter( 'woocommerce_order_item_get_product_id', [ __CLASS__, 'add_pricing_item_as_product_id' ], 100, 2 );
		//add_filter('woocommerce_product_class', [__CLASS__, 'add_WC_Product_RTCL_pricing_as_product_class'], 100, 2);
		add_filter( 'body_class', [ __CLASS__, 'add_woo_payment_class' ] );
	}

	public static function add_woo_payment_class( $classes ) {
		if ( empty( rtcl()->session ) ) {
			rtcl()->initialize_session();
		}
		if ( is_checkout() && rtcl()->session->get( 'rtcl_app_woo_payment' ) ) {
			$classes[] = 'rtcl-mobile-woo-payment';
		}

		return $classes;
	}

	/**
	 * @param string $product_class
	 * @param int $product_id
	 *
	 * @return string
	 */
	public static function add_WC_Product_RTCL_pricing_as_product_class( $product_class, $product_id ) {
		if ( 'rtcl_pricing' === get_post_type( $product_id ) && WC_Product_RTCL_pricing::class !== $product_class ) {
			$product_class = WC_Product_RTCL_pricing::class;
		}

		return $product_class;
	}

	/**
	 * @param                       $product_id
	 * @param WC_Order_Item_Product $wc_data_obj
	 *
	 * @return mixed
	 */
	public static function add_pricing_item_as_product_id( $product_id, $wc_data_obj ) {
		if ( ! $product_id && ( $rtcl_pricing_id = $wc_data_obj->get_meta( '_rtcl_pricing_id' ) ) && 'rtcl_pricing' === get_post_type( $rtcl_pricing_id ) ) {
			return $rtcl_pricing_id;
		}

		return $product_id;
	}

	public static function update_dokan_vendor_order_details( $order_info, $order_id, $vendor_id ) {
		$order = wc_get_order( $order_id );

		$rtcl_order_id = absint( get_post_meta( $order_id, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $order_id, '_rtcl_payment_id', true ) ) : $rtcl_order_id;
		if ( ! $rtcl_order_id || get_post_type( $rtcl_order_id ) !== rtcl()->post_type_payment ) {
			return $order_info;
		}

		$rtcl_info       = [];
		$rtcl_order_info = [];
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( is_a( $product, WC_Product_RTCL_pricing::class ) ) {
				$rtcl_info['product']  = $item['name'];
				$rtcl_info['quantity'] = $item['quantity'];
				$rtcl_info['total']    = $item['total'];
				array_push( $rtcl_order_info, $rtcl_info );
			}
		}
		$rtcl_order_info = ! empty( $rtcl_order_info ) ? $rtcl_order_info : $order_info;

		return apply_filters( 'rtcl_update_dokan_vendor_order_details', $rtcl_order_info, $order_info, $order_id, $vendor_id );
	}

	public static function pricing_to_order_item_product( $wc_product, $item ) {
		$order_id      = $item->get_order_id();
		$rtcl_order_id = absint( get_post_meta( $order_id, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $order_id, '_rtcl_payment_id', true ) ) : $rtcl_order_id;

		if ( ! $rtcl_order_id || get_post_type( $rtcl_order_id ) !== rtcl()->post_type_payment ) {
			return $wc_product;
		}
		$_rtcl_pricing_id = $item->get_meta( '_rtcl_pricing_id' );
		if ( ! $_rtcl_pricing_id || get_post_type( $_rtcl_pricing_id ) !== rtcl()->post_type_pricing ) {
			return $wc_product;
		}
		$rtcl_pricingProduct = new WC_Product_RTCL_pricing( $_rtcl_pricing_id );

		return apply_filters( 'rtcl_pricing_to_order_item_product', $rtcl_pricingProduct, $wc_product, $item );
	}


	/**
	 *
	 */
	public function wc_order_notice() {
		global $post, $pagenow;
		if ( $pagenow != 'post.php' || empty( $post ) || get_post_type( $post->ID ) != 'shop_order' ) {
			return;
		}
		$rtcl_order_id = absint( get_post_meta( $post->ID, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $post->ID, '_rtcl_payment_id', true ) ) : $rtcl_order_id;
		if ( ! $rtcl_order_id ) {
			return;
		}
		?>
		<style type="text/css">
			.woo-payment-order-notice p {
				font-size: 24px;
			}
		</style>
		<div class="error woo-payment-order-notice">
			<p>
				<?php printf( __( 'This order is related to Classified Listing Payment, For WooCommerce payment please change order status from here it will auto change in Classified Listing Plugin order. <a href="%s">You can view the order from Classified Listing Payment History also.</a>', 'classified-listing-pro' ), get_edit_post_link( $rtcl_payment_id ) ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * @param \WP_Error $errors
	 *
	 * @return \WP_Error
	 */
	static function remove_gateway_validation( $errors ) {
		if ( $errors->has_errors() ) {
			$errors->remove( 'rtcl_checkout_error_empty_payment_gateway' );
		}

		return $errors;
	}


	public function add_to_woo( $pricing_id, $quantity, $item_data, $cart ) {
		if ( rtcl()->post_type_pricing !== get_post_type( $pricing_id ) ) {
			return;
		}
		$pricing = rtcl()->factory->get_pricing( $pricing_id );

		if ( ! $pricing->exists() ) {
			return;
		}
		WC()->cart->empty_cart(); // Always make sure only single pricing is added at cart

		WC()->cart->add_to_cart( $pricing_id, 1, 0, [], $item_data );
	}

	/**
	 * @param Pricing $pricing
	 * @param Int $cart_id
	 * @param array $data
	 */
	static function checkout_process_cb( $pricing, $cart_id, $data ) {
		if ( ! $pricing->exists() ) {
			return;
		}
		wp_redirect( wc_get_checkout_url() );
		exit();

	}


	/**
	 * Get the product class name.
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param int
	 *
	 * @return string
	 */
	public function product_class( $classname, $product_type, $post_type, $product_id ) {
		if ( rtcl()->post_type_pricing == get_post_type( $product_id ) ) {
			$classname = WC_Product_RTCL_pricing::class;
		}

		return $classname;
	}

	/**
	 * Disable select quantity product has post_type 'lp_course'
	 *
	 * @param int $product_quantity
	 * @param string $cart_item_key
	 * @param array $cart_item
	 *
	 * @return mixed
	 */
	public function disable_quantity_box( $product_quantity, $cart_item_key, $cart_item ) {
		return ( get_class( $cart_item['data'] ) === WC_Product_RTCL_pricing::class ) ? sprintf( '<span style="text-align: center; display: block">%s</span>', $cart_item['quantity'] ) : $product_quantity;
	}

	/**
	 * @param $product_type
	 * @param $adding_to_cart
	 *
	 * @return mixed
	 */
	public function add_to_cart_handler( $product_type, $adding_to_cart ) {
		if ( $adding_to_cart instanceof WC_Product_RTCL_pricing ) {
			$pricing                            = rtcl()->factory->get_pricing( $_REQUEST['add-to-cart'] );
			$this->_response['_pricing_id']     = $pricing->getId();
			$this->_response['single_purchase'] = true;
			WC()->cart->empty_cart(); //
			add_action( 'woocommerce_add_to_cart', [ $this, 'added_to_cart' ], 10, 6 );
		}

		return $product_type;
	}


	/**
	 * @param $cart_item_key
	 * @param $product_id
	 * @param $quantity
	 * @param $variation_id
	 * @param $variation
	 * @param $cart_item_data
	 */
	public function added_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		if ( rtcl()->post_type_pricing !== get_post_type( $product_id ) ) {
			return;
		}
		if ( $this->_response['single_purchase'] ) {
			$this->_response['redirect'] = wc_get_checkout_url();
		} else {
		}
		add_filter( 'pre_option_woocommerce_cart_redirect_after_add', [
			$this,
			'cart_redirect_after_add'
		], 1000, 2 );
		add_filter( 'woocommerce_add_to_cart_redirect', [ $this, 'add_to_cart_redirect' ], 1000 );
		ob_start();
		wc_add_to_cart_message( [ $product_id => $quantity ], true );
		wc_print_notices();
		$this->_response['message']       = ob_get_clean();
		$this->_response['added_to_cart'] = 'yes';
		add_action( 'shutdown', [ $this, 'shutdown' ], 100 );// worked in version 2.4.8.1
	}


	/**
	 * @param $a
	 * @param $b
	 *
	 * @return string
	 */
	public function cart_redirect_after_add( $a, $b ) {
		return 'no';
	}


	/**
	 * @param $a
	 *
	 * @return bool
	 */
	public function add_to_cart_redirect( $a ) {
		return false;
	}


	/**
	 *
	 */
	public function shutdown() {
		$output = ob_get_clean();
		if ( $this->_response ) {
			Functions::send_json( $this->_response );
		}
	}


	/**
	 * Update LearnPress order status when WooCommerce updated status
	 *
	 * @param int $order_id
	 * @param string $old_status
	 * @param string $new_status
	 */
	function rtcl_update_order_status( $order_id, $old_status, $new_status ) {
		remove_action( 'woocommerce_order_status_changed', [ $this, 'rtcl_update_order_status' ], 10 );
		$rtcl_order_id = absint( get_post_meta( $order_id, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $order_id, '_rtcl_payment_id', true ) ) : $rtcl_order_id;
		if ( $rtcl_order_id && get_post_type( $rtcl_order_id ) === rtcl()->post_type_payment ) {
			$rtcl_order = rtcl()->factory->get_order( $rtcl_order_id );
			if ( $rtcl_order ) {
				$rtcl_order->update_status( $new_status );
			}
		}
		add_action( 'woocommerce_order_status_changed', [ $this, 'rtcl_update_order_status' ], 10, 3 );
	}


	/**
	 * Add item line meta data contains our course_id from product_id in cart.
	 * Since WC 3.x order item line product_id always is 0 if it is not a REAL product.
	 * Need to track course_id for creating LP order in WC hook after this action.
	 *
	 * @param $item
	 * @param $cart_item_key
	 * @param $values
	 * @param $order
	 */
	public function order_item_line( $item, $cart_item_key, $values, $order ) {
		if ( rtcl()->post_type_pricing === get_post_type( $values['product_id'] ) ) {
			$item->add_meta_data( '_rtcl_pricing_id', $values['product_id'], true );
			if ( isset( $values['listing_id'] ) && rtcl()->post_type === get_post_type( $values['listing_id'] ) ) {
				$item->add_meta_data( '_rtcl_listing_id', $values['listing_id'], true );
			}
		}
	}

	/**
	 * Create RTCL order base on WC order data
	 *
	 * @param $wc_order_id
	 * @param $posted
	 *
	 * @return bool|int|void|\WP_Error
	 */
	public function create_order( $wc_order_id, $posted ) {

		// Get LP order key related with WC order
		$rtcl_order_id = absint( get_post_meta( $wc_order_id, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $wc_order_id, '_rtcl_payment_id', true ) ) : $rtcl_order_id;

		if ( $rtcl_order_id && get_post_type( $rtcl_order_id ) === rtcl()->post_type_payment ) {
			return;
		}

		// Get wc order
		$wc_order = wc_get_order( $wc_order_id );
		if ( ! $wc_order ) {
			return;
		}

		// Get wc order items
		$wc_items = $wc_order->get_items();

		if ( ! $wc_items ) {
			return;
		}
		$data = [];
		// Find LP courses in WC order and preparing to create LP Order
		foreach ( $wc_items as $item ) {

			$pricing_id = ! empty( $item['_rtcl_pricing_id'] ) ? $item['_rtcl_pricing_id'] : 0;
			// ignore item is not a pricing post type
			if ( rtcl()->post_type_pricing != get_post_type( $pricing_id ) ) {
				continue;
			}
			$data['_pricing_id'] = $pricing_id;

			// Check listing id
			$listing_id = ! empty( $item['_rtcl_listing_id'] ) ? $item['_rtcl_listing_id'] : 0;
			if ( rtcl()->post_type === get_post_type( $listing_id ) ) {
				$data['listing_id'] = $listing_id;
			}
			break;// Check only one product
		}

		// If there is no course in wc order
		if ( ! $data || empty( $data ) ) {
			return false;
		}

		# create rtcl_order
		$customer_note  = method_exists( $wc_order, 'get_customer_note' ) ? $wc_order->get_customer_note() : $wc_order->customer_note;
		$pricing        = rtcl()->factory->get_pricing( $data['_pricing_id'] );
		$new_order_args = apply_filters( 'rtcl_create_order_args_at_wc_order', [
			'post_author' => 1,
			'post_parent' => '0',
			'post_type'   => rtcl()->post_type_payment,
			'post_status' => 'rtcl-' . $wc_order->get_status(),
			'ping_status' => 'closed',
			'post_title'  => esc_html__( 'Order on', 'classified-listing-pro' ) . ' ' . current_time( "l jS F Y h:i:s A" ),
			'meta_input'  => [
				'_pricing_id'           => $data['_pricing_id'],
				'_order_currency'       => get_post_meta( $wc_order_id, '_order_currency', true ),
				'_prices_include_tax'   => 'no',
				'customer_id'           => get_post_meta( $wc_order_id, '_customer_user', true ),
				'customer_ip_address'   => Functions::get_ip_address(),
				'_user_agent'           => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
				'_user_id'              => get_post_meta( $wc_order_id, '_customer_user', true ),
				'amount'                => $wc_order->get_total(), // TODO: need to adjust abd  remove
				'_payment_total'        => $wc_order->get_total(), // TODO: need to remove
				'_payment_subtotal'     => $wc_order->get_subtotal(),
				'_order_key'            => get_post_meta( $wc_order_id, '_order_key', true ),
				'_payment_method'       => get_post_meta( $wc_order_id, '_payment_method', true ),
				'_payment_method_title' => get_post_meta( $wc_order_id, '_payment_method_title', true ),
				'_created_via'          => 'wc',
				'_woo_order_id'         => $wc_order_id,
				'_user_note'            => $customer_note,
			]
		] );

		$order_id = wp_insert_post( apply_filters( 'rtcl_checkout_process_new_order_args', $new_order_args, $pricing, null, $data ) );
		$payment  = rtcl()->factory->get_order( $order_id );
		update_post_meta( $wc_order_id, '_rtcl_order_id', $order_id );
		do_action( 'rtcl_checkout_process_success', $payment, [] );

		return $order_id;
	}


	static function rtcl_checkout_form_id() {
		return 'rtcl-woo-checkout-form';
	}


	/**
	 * @param $order_status
	 * @param $wc_order_id
	 *
	 * @param $wc_order \WC_Order
	 *
	 * @return string
	 */
	static function autocomplete_wc_orders( $order_status, $wc_order_id, $wc_order ) {
		$l             = new Logger();
		$rtcl_order_id = absint( get_post_meta( $wc_order_id, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $wc_order_id, '_rtcl_payment_id', true ) ) : $rtcl_order_id;

		if ( ! $rtcl_order_id || get_post_type( $rtcl_order_id ) !== rtcl()->post_type_payment ) {
			return $order_status;
		}
		$l->info( 'Order', [
			'old_status'   => $order_status,
			'order_status' => $wc_order->get_status(),
			'order_meta'   => get_post_meta( $wc_order_id )
		] );

		return 'completed';
	}

	function autocomplete_wc_orders_action( $wc_order_id ) {
		if ( ! $wc_order_id ) {
			return;
		}
		$l        = new Logger();
		$wc_order = wc_get_order( $wc_order_id );
		$l->info( 'AutoPayment' );
		// No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods.
		if ( $wc_order && in_array( $wc_order->get_payment_method(), [ 'bacs', 'cod', 'cheque', '' ] ) ) {
			return;
		}
		$l->info( 'AutoPayment after payment method check' );
		// No Update if Wc order not generated by Classified listing plugin
		$rtcl_order_id = absint( get_post_meta( $wc_order_id, '_rtcl_order_id', true ) );
		// legacy support
		$rtcl_order_id = ! $rtcl_order_id ? absint( get_post_meta( $wc_order_id, '_rtcl_payment_id', true ) ) : $rtcl_order_id;

		if ( ! $rtcl_order_id || get_post_type( $rtcl_order_id ) !== rtcl()->post_type_payment ) {
			return;
		}
		$l->info( 'AutoPayment after Rtcl check' );
		//  Update wc order status to complete
		if ( apply_filters( 'rtcl_autocomplete_wc_orders', true, $wc_order_id, $rtcl_order_id, $wc_order ) ) {
			$l->info( 'AutoPayment Update order status' );
			$wc_order->update_status( 'completed' );
		}
	}

}
