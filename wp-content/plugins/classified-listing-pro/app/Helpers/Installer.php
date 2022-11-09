<?php

namespace RtclPro\Helpers;

class Installer {
	public static function activate() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'rtcl_pro_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'rtcl_pro_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		if ( ! get_option( 'rtcl_pro_version' ) ) {
			self::create_options();
		}
		self::create_tables();
		self::update_rtcl_version();

		delete_transient( 'rtcl_pro_installing' );

		do_action( 'rtcl_flush_rewrite_rules' );
		do_action( 'rtcl_pro_installed' );

	}

	private static function create_options() {
		// General settings update
		$isDirty   = false;
		$gSettings = get_option( 'rtcl_general_settings' );
		if ( ! isset( $gSettings['compare_limit'] ) ) {
			$gSettings['compare_limit'] = 3;
			$isDirty                    = true;
		}
		if ( ! isset( $gSettings['default_view'] ) ) {
			$gSettings['default_view'] = 'list';
			$isDirty                   = true;
		}
		if ( ! isset( $gSettings['location_type'] ) ) {
			$gSettings['location_type'] = 'local';
			$isDirty                    = true;
		}
		if ( $isDirty ) {
			update_option( 'rtcl_general_settings', $gSettings );
		}

		// Moderation settings update
		$isDirty   = false;
		$mSettings = get_option( 'rtcl_moderation_settings' );
		if ( ! isset( $mSettings['listing_top_per_page'] ) ) {
			$mSettings['listing_top_per_page'] = 2;
			$isDirty                           = true;
		}
		if ( ! isset( $mSettings['popular_listing_threshold'] ) ) {
			$mSettings['popular_listing_threshold'] = 1000;
			$isDirty                                = true;
		}
		if ( $isDirty ) {
			update_option( 'rtcl_moderation_settings', $mSettings );
		}

		// Account settings update
		$isDirty   = false;
		$aSettings = get_option( 'rtcl_account_settings' );
		if ( ! isset( $aSettings['verify_max_resend_allowed'] ) ) {
			$aSettings['verify_max_resend_allowed'] = 5;
			$isDirty                                = true;
		}
		if ( ! isset( $aSettings['popular_listing_threshold'] ) ) {
			$aSettings['popular_listing_threshold'] = 1000;
			$isDirty                                = true;
		}
		if ( $isDirty ) {
			update_option( 'rtcl_account_settings', $aSettings );
		}

		// advanced settings update
		$advDirty    = false;
		$advSettings = get_option( 'rtcl_advanced_settings' );
		if ( ! isset( $advSettings['myaccount_chat_endpoint'] ) ) {
			$advSettings['myaccount_chat_endpoint'] = 'chat';
			$advDirty                               = true;
		}
		if ( ! isset( $advSettings['myaccount_verify'] ) ) {
			$advSettings['myaccount_verify'] = 'verify';
			$advDirty                        = true;
		}
		if ( $advDirty ) {
			update_option( 'rtcl_advanced_settings', $advSettings );
		}
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$tables = self::get_chat_table_schema() + self::get_pushNotification_table_schema();
		dbDelta( $tables );
	}

	/**
	 * @return array
	 */
	static function get_pushNotification_table_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$push_notifications_table_name = $wpdb->prefix . "rtcl_push_notifications";
		$table_schema                  = [];
		$max_index_length              = 191;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $push_notifications_table_name ) ) !== $push_notifications_table_name ) {
			$table_schema[] = "CREATE TABLE $push_notifications_table_name (
                      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                      push_token varchar(255) NOT NULL,
                      user_id int(10) UNSIGNED DEFAULT NULL,
                      events longtext DEFAULT NULL,
                      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (id),
                      UNIQUE KEY push_token (push_token($max_index_length))
                      ) $collate;";
		}

		return $table_schema;
	}

	/**
	 * @return array
	 */
	static function get_chat_table_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$conversation_table_name         = $wpdb->prefix . "rtcl_conversations";
		$conversation_message_table_name = $wpdb->prefix . "rtcl_conversation_messages";
		$table_schema                    = [];

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $conversation_table_name ) ) !== $conversation_table_name ) {
			$table_schema[] = "CREATE TABLE $conversation_table_name (
                          con_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          listing_id BIGINT UNSIGNED NOT NULL,
                          sender_id int(10) UNSIGNED NOT NULL,
                          recipient_id int(10) UNSIGNED NOT NULL,
                          sender_delete tinyint(1) NOT NULL DEFAULT '0',
                          recipient_delete tinyint(1) NOT NULL DEFAULT '0',
                          last_message_id int(10) UNSIGNED DEFAULT NULL,
                          sender_review tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
                          recipient_review tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
                          invert_review tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
                          created_at timestamp NOT NULL,
                          PRIMARY KEY (con_id)
                        ) $collate;";
		}
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $conversation_message_table_name ) ) !== $conversation_message_table_name ) {
			$table_schema[] = "CREATE TABLE $conversation_message_table_name (
                      message_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                      con_id bigint(20) unsigned NOT NULL,
                      source_id int(10) unsigned NOT NULL,
                      message longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                      is_read tinyint(1) NOT NULL DEFAULT '0',
                      created_at timestamp NOT NULL,
                      PRIMARY KEY (message_id),
                      KEY con_id (con_id)
                    ) $collate;";
		}

		return $table_schema;
	}


	private static function update_rtcl_version() {
		delete_option( 'rtcl_pro_version' );
		add_option( 'rtcl_pro_version', RTCL_PRO_VERSION );
	}

	public static function deactivate() {

	}
}