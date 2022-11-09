<?php
/*
Plugin Name: RT Demo Importer
Plugin URI: http://radiustheme.com
Description: Uninstall this plugin after you've finished importing demo contents
Version: 4.3.1
Author: RadiusTheme
Author URI: http://radiustheme.com
*/

if ( is_admin() && !defined( 'FW' ) ) {
	require_once dirname(__FILE__) . '/unyson/framework/bootstrap.php';

	add_filter( 'fw_framework_directory_uri', 'rtdi_fw_framework_directory_uri' );
	add_action( 'admin_menu',                 'rtdi_remove_unyson_menus', 12 );
	add_action( 'network_admin_menu',         'rtdi_remove_unyson_menus', 12 );
	add_action( 'after_setup_theme',          'rtdi_remove_unyson_footer_version', 12 );
	add_action( 'admin_enqueue_scripts',      'rtdi_fw_admin_styles', 20 );
	//add_filter( 'fw:ext:backups:add-restore-task:image-sizes-restore', '__return_false' ); // Enable it to skip image restore step
}

function rtdi_fw_framework_directory_uri() {
	return plugin_dir_url( __FILE__ ) . 'unyson/framework';
}

function rtdi_remove_unyson_menus() {
	remove_menu_page( 'fw-extensions' );
	remove_submenu_page( 'tools.php', 'fw-backups' );
}

function rtdi_remove_unyson_footer_version() {
	$fw_obj = fw();
	remove_filter( 'update_footer', array( $fw_obj->backend, '_filter_footer_version'), 11 );
}

function rtdi_fw_admin_styles(){
	$css = "#fw-ext-backups-demo-list .fw-ext-backups-demo-item.active .theme-actions {display: block !important;}";
	wp_add_inline_style( 'fw-ext-backups-demo', $css );
}

add_action( 'admin_enqueue_scripts', 'rt_demo_import_enqueue_script' );
function rt_demo_import_enqueue_script() {
    wp_enqueue_style('rt-demo', plugin_dir_url(__FILE__) . 'assets/css/demo-page.css');
    wp_enqueue_script('rt-demo', plugin_dir_url(__FILE__) . 'assets/js/demo.js', '', 1.0, true);
}