<?php
/**
 * RtclStore Uninstall
 *
 * @package RtclStore\Uninstaller
 * @version 1.0.0
 */

require_once __DIR__ . '/vendor/autoload.php';

use RtclStore\Helpers\Install;

defined('WP_UNINSTALL_PLUGIN') || exit;

if (defined('RTCL_STORE_REMOVE_ALL_DATA') && true === RTCL_STORE_REMOVE_ALL_DATA) {


    // Tables.
    Install::drop_tables();

    delete_option('rtcl_membership_settings');
    delete_option('rtcl_store_version');

    // Clear any cached data that has been removed.
    wp_cache_flush();
}