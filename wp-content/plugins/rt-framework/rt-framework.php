<?php
/*
Plugin Name: RT Framework
Plugin URI: http://radiustheme.com
Description: Theme Framework by RadiusTheme
Version: 2.8
Author: RadiusTheme
Author URI: http://radiustheme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined( 'RT_FRAMEWORK_VERSION' ) ) exit;

define( 'RT_FRAMEWORK_VERSION',  ( WP_DEBUG ) ? time() : '2.8' );
define( 'RT_FRAMEWORK_BASE_DIR', trailingslashit( dirname( __FILE__ ) ) );

// Text Domain
add_action( 'plugins_loaded', 'rt_fw_load_textdomain' );
function rt_fw_load_textdomain() {
	load_plugin_textdomain( 'rt-framework' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

// Load Framework
add_action( 'setup_theme', 'rt_fw_load_files' );
function rt_fw_load_files(){
	require_once RT_FRAMEWORK_BASE_DIR . 'inc/rt-posts.php';
	require_once RT_FRAMEWORK_BASE_DIR . 'inc/rt-postmeta.php';
	require_once RT_FRAMEWORK_BASE_DIR . 'inc/rt-taxmeta.php';
	require_once RT_FRAMEWORK_BASE_DIR . 'inc/rt-widget-fields.php';
}