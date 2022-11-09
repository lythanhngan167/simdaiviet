<?php
// If uninstall not called from WordPress, then exit

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';

$settings = get_option('rtcl_tools_settings');
if (!empty($settings['delete_all_data']) && 'yes' === $settings['delete_all_data']) {

    global $wpdb;

    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rtcl_conversations");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rtcl_conversation_messages");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rtcl_push_notifications");
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type IN ( 'rtcl_order_note' );");
    $wpdb->query("DELETE meta FROM {$wpdb->commentmeta} meta LEFT JOIN {$wpdb->comments} comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL;");

    delete_option('rtcl_version_pro');
    delete_option('rtcl_app_settings');

    // Clear any cached data that has been removed
    wp_cache_flush();
}