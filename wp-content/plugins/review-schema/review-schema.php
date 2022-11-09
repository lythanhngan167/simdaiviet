<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Review Schema
 * Plugin URI:        https://wordpress.org/plugins/review-schema/
 * Description:       The most comprehensive multi-criteria Review & Rating with JSON-LD based Structure Data Schema solution for WordPress website. Support Review Rating and auto generated schema markup for page, post, WooCommerce & custom post type.
 * Version:           2.0.2
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       review-schema
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define PLUGIN_FILE.
if ( ! defined( 'RTRS_PLUGIN_FILE' ) ) {
	define( 'RTRS_PLUGIN_FILE', __FILE__ );
}

// Define VERSION.
if ( ! defined( 'RTRS_VERSION' ) ) {
	define( 'RTRS_VERSION', '2.0.2' );
}

if ( ! class_exists( 'Rtrs' ) ) {
	require_once 'app/Rtrs.php';
}

// TODO: GLobal settings is not working for schema ( If Shortcode no create )
// TODO: Post page Rich snippet type is not set by default from global settings https://prnt.sc/ONLvtXoM4C2n