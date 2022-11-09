<?php
/**
 * Main Elementor ELStoreController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since    1.0.0
 */

namespace RtclStore\Controllers;

use RtclStore\Controllers\Elementor\Widgets\ListingStore;

/**
 * Main Elementor ELStoreController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
class ELStoreController {

	/**
	 * Initialize all hooks function
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'rtcl_el_widget_for_classified_listing', array( __CLASS__, 'el_widget_for_classified_listing' ), 10 );
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $class_list main data.
	 *
	 * @return array
	 */
	public static function el_widget_for_classified_listing( $class_list ) {
		$class_list[] = ListingStore::class;
		return $class_list;
	}

}
