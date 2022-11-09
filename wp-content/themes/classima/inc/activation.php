<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

class Activation {

	protected static $instance = null;

	public function __construct() {
		add_action( 'after_switch_theme',   array( $this, 'init' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function init() {
		if ( !get_option( 'classima_activated_before' ) ) {
			update_option( 'classima_activated_before', 'yes' );
			$this->set_elementor_default_options();
			$this->set_classified_listing_default_options();
		}
	}

	public function set_elementor_default_options() {
		$data = array(
			'elementor_disable_typography_schemes' => 'yes',
			'elementor_disable_color_schemes'      => 'yes',
			'elementor_css_print_method'           => 'internal',
			'elementor_cpt_support'                => array( 'page' ),
			'elementor_container_width'            => '1200',

			'_elementor_general_settings'          => array(
				'default_generic_fonts' => 'Sans-serif',
				'global_image_lightbox' => 'yes',
				'container_width'       => '1200',
			),
			'_elementor_global_css' 	=> array(
				'time'   => '1534145031',
				'fonts'  => array(),
				'status' => 'inline',
				'0'      => false,
				'css'    => '.elementor-section.elementor-section-boxed > .elementor-container{max-width:1240px;}',
			),
		);

		foreach ( $data as $key => $value ) {
			update_option( $key, $value );
		}
	}

	public function set_classified_listing_default_options() {
		$data = array(
			'rtcl_misc_settings'       => array(
				'image_size_gallery'           => array( 'width' => 870, 'height' => 493 ), // changed
				'image_size_gallery_thumbnail' => array( 'width' => 170, 'height' => 116, 'crop' => 'yes' ), // changed
				'image_size_thumbnail'         => array( 'width' => 400, 'height' => 280, 'crop' => 'yes' ), // changed
				'image_allowed_type'           => array( 'png', 'jpg', 'jpeg' ),
				'image_allowed_memory'         => 3, // changed
				'image_edit_cap'               => 'yes',
				'social_services'              => array( 'facebook', 'twitter', 'gplus' ),
				'social_pages'                 => array( 'listing' ),

				'store_banner_size'            => array( 'width' => 1230, 'height' => 313, 'crop' => 'yes' ), // changed
				'store_logo_size'              => array( 'width' => 180, 'height' => 140, 'crop' => 'yes' ), // changed
			),
			'rtcl_membership_settings'         => array(
				'enable'                       => 'yes', // changed
				'enable_store'                 => 'yes', // changed
				'number_of_free_ads'           => 3,
				'renewal_days_for_free_ads'    => 30
			),
		);

		foreach ( $data as $key => $value ) {
			update_option( $key, $value );
		}
	}

}

Activation::instance();