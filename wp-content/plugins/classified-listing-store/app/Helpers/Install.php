<?php

namespace RtclStore\Helpers;

class Install {
	/**
	 * Check if classified parent plugin is activated or not
	 *
	 * @return bool
	 */
	public static function _is_parent_activated() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active( 'classified-listing-pro/classified-listing-pro.php' ) ) {
			return true;
		}

		return false;
	}

	static function _install() {
		if ( ! self::_is_parent_activated() ) {
			deactivate_plugins( RTCL_STORE_PLUGIN_FILE );
			wp_die( __( '<strong>Classified Listing Store</strong> plugin requires <strong>Classified Listing Pro</strong> to be installed and activated.', 'classified-listing-store' ), __( 'Error', 'classified-listing-store' ), array( 'back_link' => true ) );
		}
	}

	public static function activate() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'rtcl_store_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'rtcl_store_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		if ( ! get_option( 'rtcl_store_version' ) ) {
			self::create_options();
		}
		self::create_tables();
		self::update_rtcl_version();

		delete_transient( 'rtcl_store_installing' );
		do_action( 'rtcl_flush_rewrite_rules' );
		do_action( 'rtcl_store_installed' );
	}

	public static function deactivate() {

	}

	private static function create_options() {
		$advSettings = get_option( 'rtcl_advanced_settings', array() );
		if ( ! isset( $advSettings['myaccount_store_endpoint'] ) ) {
			$advSettings['myaccount_store_endpoint'] = 'store';
		}
		if ( ! isset( $advSettings['permalink_store'] ) ) {
			$advSettings['permalink_store'] = 'store';
		}
		if ( ! isset( $advSettings['store_category_base'] ) ) {
			$advSettings['store_category_base'] = 'store-category';
		}
		if ( ! empty( $advSettings['store'] ) ) {
			$id                   = wp_insert_post(
				array(
					'post_title'     => __( 'Store', 'classified-listing-store' ),
					'post_content'   => '',
					'post_status'    => 'publish',
					'post_author'    => 1,
					'post_type'      => 'page',
					'comment_status' => 'closed',
				)
			);
			$advSettings['store'] = $id;
		}
		update_option( 'rtcl_advanced_settings', $advSettings );
		$miscSettings = get_option( 'rtcl_misc_settings', array() );
		if ( ! isset( $miscSettings['store_banner_size'] ) ) {
			$miscSettings['store_banner_size'] = array(
				'width'  => 992,
				'height' => 300,
				'crop'   => 'yes',
			);
		}
		if ( ! isset( $miscSettings['store_logo_size'] ) ) {
			$miscSettings['store_logo_size'] = array(
				'width'  => 200,
				'height' => 150,
				'crop'   => 'yes',
			);
		}
		update_option( 'rtcl_misc_settings', $miscSettings );

		if ( false == get_option( 'rtcl_membership_settings' ) ) {
			$defaults = array(
				'number_of_free_ads'        => 3,
				'renewal_days_for_free_ads' => 30,
			);
			add_option( 'rtcl_membership_settings', apply_filters( 'rtcl_membership_settings' . '_defaults', $defaults ) );
		}
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_schema() );
	}

	private static function update_rtcl_version() {
		delete_option( 'rtcl_store_version' );
		add_option( 'rtcl_store_version', RTCL_STORE_VERSION );
	}

	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$log_table             = $wpdb->prefix . 'rtcl_posting_log';
		$membership_table      = $wpdb->prefix . 'rtcl_membership';
		$membership_meta_table = $wpdb->prefix . 'rtcl_membership_meta';
		$table_schema          = array();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $log_table ) ) !== $log_table ) {
			$table_schema[] = "CREATE TABLE `$log_table` (
					  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					  post_id int(11) NOT NULL,
					  user_id int(11) NOT NULL,
					  cat_id int(11) NOT NULL,
					  created_at DATETIME NOT NULL,
					  PRIMARY KEY  (id),
					  UNIQUE KEY post_id (post_id),
					  KEY user_id (user_id)
					) $collate;";
		}
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $membership_table ) ) !== $membership_table ) {
			$table_schema[] = "CREATE TABLE `$membership_table` (
                                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                user_id int(11) NOT NULL,
                                ads int(11) NOT NULL DEFAULT '0',
                                posted_ads int(11) NOT NULL DEFAULT '0',
                                expiry_date DATETIME NOT NULL DEFAULT '0000-01-02 00:00:00',
                                member_since DATETIME NOT NULL DEFAULT '0000-01-02 00:00:00',
                                active boolean NOT NULL DEFAULT TRUE,
                                UNIQUE KEY user_id (user_id)
                            ) $collate;";
		}
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $membership_meta_table ) ) !== $membership_meta_table ) {
			$table_schema[] = "CREATE TABLE `$membership_meta_table` (
                                meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                membership_id bigint(20) unsigned NOT NULL,
                                meta_key varchar(191) NOT NULL,
                                meta_value longtext,
                                KEY membership_id (membership_id),
                                KEY meta_key (meta_key)
                            ) $collate;";
		}

		return $table_schema;

	}

	/**
	 * Drop Rtcl store tables.
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = self::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
		}
	}

	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}rtcl_posting_log",
			"{$wpdb->prefix}rtcl_membership",
			"{$wpdb->prefix}rtcl_membership_meta",
		);

		return apply_filters( 'rtcl_store_install_get_tables', $tables );

	}

}
