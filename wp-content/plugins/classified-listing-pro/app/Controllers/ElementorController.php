<?php
/**
 * Main Elementor ElementorMainController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since    1.0.0
 */

namespace RtclPro\Controllers;

use RtclPro\Controllers\Elementor\Hooks\ELFilterHooksPro;
use RtclPro\Controllers\Elementor\Widgets\ListingCategorySlider;
use RtclPro\Controllers\Elementor\Widgets\ListingSlider;
use RtclPro\Controllers\Elementor\Widgets\PricingTable;

/**
 * Main Elementor ElementorMainController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
class ElementorController {

	/**
	 * Initialize all hooks function
	 *
	 * @return void
	 */
	public static function init() {
		
		add_filter( 'rtcl_el_widget_for_classified_listing', array( __CLASS__, 'el_widget_for_classified_listing' ), 10 );
		/*
		add_action(
			'elementor/widgets/widgets_registered',
			function() {
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					TemplateHooks::init();
					add_action( 'init', array( TemplateLoader::class, 'init' ) );
					add_action( 'rtcl_single_listing_social_profiles', array( SocialProfilesController::class, 'display_social_profiles' ) );
				}
			}
		);
		*/
		ELFilterHooksPro::init();
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $class_list main data.
	 *
	 * @return array
	 */
	public static function el_widget_for_classified_listing( $class_list ) {
		$el_classes = array(
			ListingCategorySlider::class,
			ListingSlider::class,
			PricingTable::class,
		);
		$class_list = array_merge(
			$class_list,
			$el_classes
		);
		return $class_list;
	}

}
