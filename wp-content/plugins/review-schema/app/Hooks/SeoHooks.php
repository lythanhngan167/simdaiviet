<?php
namespace Rtrs\Hooks;

class SeoHooks {

	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
	}

	static function isYoastActive() {
		if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}
		return false;
	}

	static function isWcActive() {
		return class_exists( 'woocommerce' );
	}

	public static function isEddActive() {
		return class_exists( 'Easy_Digital_Downloads' );
	}

	public static function plugins_loaded() {
		$settings = get_option( 'rtrs_schema_tpp_settings' );
		if ( self::isYoastActive() ) {
			if ( isset( $settings['yoast_search_schema'] ) && $settings['yoast_search_schema']  == 'yes' ) {
				add_filter( 'disable_wpseo_json_ld_search', '__return_true' );
			}
			if ( isset( $settings['yoast_schema'] ) && $settings['yoast_schema'] == 'yes' ) {
				add_filter( 'wpseo_json_ld_output', array( __CLASS__, 'disable_yoast_schema_data' ), 10 );
				add_filter( 'wpseo_schema_graph_pieces', '__return_empty_array' );
			}
		}

		if ( self::isWcActive() ) { 
			if ( isset( $settings['wc_schema'] ) && $settings['wc_schema'] == 'yes' ) {
				add_filter( 'woocommerce_structured_data_type_for_page', array(
					__CLASS__,
					'remove_product_structured_data'
				), 10, 2 );
				add_action( 'init', array( __CLASS__, 'remove_output_structured_data' ) );
			}
		}
		if ( self::isEddActive() ) {
			if ( isset( $settings['edd_schema'] ) && $settings['edd_schema']  == 'yes' ) {
				add_filter( 'edd_add_schema_microdata', '__return_false' );
			}
		}
	}

	public static function disable_yoast_schema_data( $data ) {
		$data = array();
		return $data;
	}

	/**
	 * Remove all product structured data.
	 */
	static function remove_product_structured_data( $types ) {
		if ( ( $index = array_search( 'product', $types ) ) !== false ) {
			unset( $types[ $index ] );
		}
		return $types;
	}

	/* Remove the default WooCommerce 3 JSON/LD structured data */
	static function remove_output_structured_data() {
		remove_action( 'wp_footer', array(
			WC()->structured_data,
			'output_structured_data'
		), 10 ); // This removes structured data from all frontend pages
		remove_action( 'woocommerce_email_order_details', array(
			WC()->structured_data,
			'output_email_structured_data'
		), 30 ); // This removes structured data from all Emails sent by WooCommerce
	}

}