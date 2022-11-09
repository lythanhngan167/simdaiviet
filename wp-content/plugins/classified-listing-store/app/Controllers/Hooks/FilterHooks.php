<?php

namespace RtclStore\Controllers\Hooks;

class FilterHooks {

	public static function init() {
		add_filter( 'rtcl_addons', array( __CLASS__, 'remove_classified_listing_store' ) );
		// GB Block Hooks
		add_filter( 'rtcl_gb_localize_script', array( __CLASS__, 'gb_block_listing_store' ), 10 );
		add_filter( 'rtcl_licenses', array( __CLASS__, 'license' ), 10 );
	}
	public static function license( $licenses ) {
			$licenses[] = array(
				'plugin_file' => RTCL_STORE_PLUGIN_FILE,
				'api_data'    => array(
					'key_name'    => 'license_store_key',
					'status_name' => 'license_store_status',
					'action_name' => 'rtcl_store_manage_licensing',
					'product_id'  => 86410,
					'version'     => RTCL_STORE_VERSION,
				),
				'settings'    => array(
					'title' => esc_html__( 'Store plugin license key', 'classified-listing-store' ),
				),
			);
			return $licenses;
	}

	public static function remove_classified_listing_store( $addons ) {
		unset( $addons['classified_listing_store'] );

		return $addons;
	}

	public static function gb_block_listing_store( $data ) {
		$data['listing_store_block'] = true;
		return $data;
	}
}
