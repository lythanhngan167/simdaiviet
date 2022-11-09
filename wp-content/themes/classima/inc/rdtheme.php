<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

namespace radiustheme\Classima;

use \Redux;
use \ReduxFrameworkPlugin;

class RDTheme {

	protected static $instance = null;

	// Sitewide static variables
	public static $options;
	public static $ad_options;

	// Template specific variables
	public static $layout;
	public static $sidebar;
	public static $has_tr_header;
	public static $has_top_bar;
	public static $header_style;
	public static $footer_style;
	public static $has_banner;
	public static $has_breadcrumb;
	public static $has_banner_search;
	public static $bgtype;
	public static $bgimg;
	public static $bgcolor;

	// Listing variables
	public static $listing_max_page_num = 1;

	private function __construct() {
		$this->redux_init();
		add_action( 'after_setup_theme', array( $this, 'set_options' ) );
		add_action( 'after_setup_theme', array( $this, 'set_redux_compability_options' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function redux_init() {
		$options1 = Constants::$theme_options;
		$options2 = Constants::$theme_ad_options;
		add_action( 'admin_menu', array( $this, 'remove_redux_menu' ), 12 ); // Remove Redux Menu
		add_filter( "redux/{$options1}/aURL_filter", '__return_empty_string' ); // Remove Redux Ads
		add_filter( "redux/{$options2}/aURL_filter", '__return_empty_string' ); // Remove Redux Ads
		add_action( 'redux/loaded', array( $this, 'remove_redux_demo' ) ); // If Redux is running as a plugin, this will remove the demo notice and links
	}

	public function set_options(){
		include Constants::$theme_inc_dir . 'default-options.php';
		$options    = json_decode( $predefined_options, true );
		$ad_options = json_decode( $predefined_ad_options, true );
		if ( class_exists( 'Redux' ) && isset( $GLOBALS[Constants::$theme_options] ) && isset($GLOBALS[Constants::$theme_ad_options] ) ) {
			$options    = wp_parse_args( $GLOBALS[Constants::$theme_options], $options );
			$ad_options = wp_parse_args( $GLOBALS[Constants::$theme_ad_options], $ad_options );
		}
		self::$options    = $options;
		self::$ad_options = $ad_options;
	}

	// Backward compatibility for newly added options
	public function set_redux_compability_options(){
		$new_options = array(
			'listing_related'      => true,
			'listing_search_items' => array(
				'location'  => '1',
				'category'  => '1',
				'keyword'   => '1',
				'type'      => '0',
			),
			'single_listing_style' => '1',
			'header_btn_txt_mob'   => 'Post Ad',
			'listing_search_style' => 'popup',
			'restrict_admin_area'  => true,
			'header_chat_icon'     => true,
		);

		foreach ( $new_options as $key => $value ) {
			if ( !isset( self::$options[$key] ) ) {
				self::$options[$key] = $value;
			}
		}
	}

	public function remove_redux_menu() {
		remove_submenu_page( 'tools.php','redux-about' );
	}

	public function remove_redux_demo() {
		if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
			add_filter( 'plugin_row_meta', array( $this, 'redux_remove_extra_meta' ), 12, 2 );
			remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
		}
	}

	public function redux_remove_extra_meta( $links, $file ){
	    if ( strpos( $file, 'redux-framework.php' ) !== false ) {
	        $links = array_slice( $links, 0, 3 );
	    }

	    return $links;
	}
}

RDTheme::instance();