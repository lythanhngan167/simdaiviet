<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Classified Listing Store
 * Plugin URI:        https://radiustheme.com/demo/wordpress/classifiedpro
 * Description:       This is the Add-on plugin for classified listing pro. By using this Add-on you can create store and able to create membership.
 * Version:           1.4.21
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       classified-listing-store
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RTCL_STORE_VERSION', '1.4.21' );
define( 'RTCL_STORE_PLUGIN_FILE', __FILE__ );

require_once 'app/RtclStore.php';
