<?php
/*
Plugin Name: Classima Core
Plugin URI: https://www.radiustheme.com
Description: Classima Core Plugin for Classima Theme
Version: 1.11
Author: RadiusTheme
Author URI: https://www.radiustheme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'CLASSIMA_CORE' ) ) {
	$plugin_data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
	define( 'CLASSIMA_CORE',               $plugin_data['version'] );
	define( 'CLASSIMA_CORE_THEME_PREFIX',  'classima' );
	define( 'CLASSIMA_CORE_BASE_DIR',      plugin_dir_path( __FILE__ ) );
}

class Classima_Core {

	public $plugin  = 'classima-core';
	public $action  = 'classima_theme_init';
	protected static $instance;

	public function __construct() {
		add_action( 'plugins_loaded',	array( $this, 'demo_importer' ), 17 );
		add_action( 'plugins_loaded',	array( $this, 'load_textdomain' ), 20 );
		add_action( $this->action,		array( $this, 'after_theme_loaded' ) );
		add_action( 'plugins_loaded',	array( $this, 'php_version_check' ));
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	public function php_version_check(){

		if ( version_compare(phpversion(), '7.2', '<') ) {
			add_action( 'admin_notices', [ $this, 'php_version_message' ] );
		} else {
			require_once CLASSIMA_CORE_BASE_DIR . 'optimizer/__init__.php'; // Optimizer
		}
		
	}
	
	public function php_version_message(){

		$class = 'notice notice-warning settings-error';
		/* translators: %s: html tags */
		$message = sprintf( __( 'The %1$sClassima Optimization%2$s requires %1$sphp 7.2+%2$s. Please consider updating php version and know more about it <a href="https://wordpress.org/about/requirements/" target="_blank">here</a>.', 'classima-core' ), '<strong>', '</strong>' );
		
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ));

	}

	public function after_theme_loaded() {
		require_once CLASSIMA_CORE_BASE_DIR . 'lib/sidebar-generator/init.php'; // Sidebar generator
		require_once CLASSIMA_CORE_BASE_DIR . 'lib/wp-svg/init.php'; // SVG support

		if ( defined( 'RT_FRAMEWORK_VERSION' ) ) {
			require_once CLASSIMA_CORE_BASE_DIR . 'inc/post-meta.php'; // Post Meta
			require_once CLASSIMA_CORE_BASE_DIR . 'widgets/init.php'; // Widgets
		}

		if ( did_action( 'elementor/loaded' ) ) {
			require_once CLASSIMA_CORE_BASE_DIR . 'elementor/init.php'; // Elementor
		}
	}

	public function demo_importer() {
		require_once CLASSIMA_CORE_BASE_DIR . 'inc/demo-importer.php';
		require_once CLASSIMA_CORE_BASE_DIR . 'inc/demo-importer-ocdi.php';
	}

	public function load_textdomain() {
		load_plugin_textdomain( $this->plugin , false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

	public static function social_share( $sharer = array() ){
		include CLASSIMA_CORE_BASE_DIR . 'inc/social-share.php';
	}
}

Classima_Core::instance();