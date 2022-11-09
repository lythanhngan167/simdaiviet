<?php

namespace Rtrs\Controllers\Admin;

use Rtrs\Models\SettingsAPI;

class AdminSettings extends SettingsAPI {
	protected $tabs = [];

	protected $active_tab;

	protected $current_section;

	public function __construct() {
		add_action( 'init', [ $this, 'edd_comments' ], 999 );
		add_action( 'admin_init', [ $this, 'setTabs' ] );
		add_action( 'admin_init', [ $this, 'save' ] );
		add_action( 'admin_menu', [ $this, 'add_rtrs_menu' ], 10 );
		add_action( 'admin_menu', [ $this, 'add_settings_menu' ], 30 );
		add_action( 'rtrs_admin_settings_groups', [ $this, 'setup_settings' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( RTRS_PLUGIN_FILE ), [ $this, 'marketing_links' ] );
	}

	/**
	 * Support EDD for review system.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function edd_comments() {
		add_post_type_support( 'download', 'comments' );
	}

	public function add_rtrs_menu() {
		add_menu_page(
			esc_html__( 'Review Schema', 'review-schema' ),
			esc_html__( 'Review Schema', 'review-schema' ),
			'manage_options',
			'review-schema',
			false,
			RTRS_URL . '/assets/imgs/icon-20x20.png',
			20
		);
	}

	public function add_settings_menu() {
		add_submenu_page(
			'review-schema',
			esc_html__( 'Settings', 'review-schema' ),
			esc_html__( 'Settings', 'review-schema' ),
			'manage_options',
			'rtrs-settings',
			[ $this, 'display_settings_form' ],
			20
		);
		add_submenu_page(
			'review-schema',
			esc_html__( 'Get Help', 'review-schema' ),
			esc_html__( 'Get Help', 'review-schema' ),
			'manage_options',
			'rtrs-reviews-get-help',
			[ $this, 'get_help_page' ],
			30
		);
	}

	/**
	 * get Help
	 */
	public function get_help_page() {
		require_once RTRS_PATH . 'views/pages/get-help.php';
	}

	public function display_settings_form() {
		require_once RTRS_PATH . 'views/settings/admin-settings-display.php';
	}

	public function setup_settings() {
		$this->set_fields();
		$this->admin_options();
	}

	public function set_fields() {
		$field = [];
		if ( $this->active_tab && $this->current_section && array_key_exists( $this->active_tab, $this->tabs ) && array_key_exists( $this->current_section, $this->subtabs ) ) {
			$file_name = RTRS_PATH . "views/settings/{$this->active_tab}-{$this->current_section}-settings.php";
		} else {
			$file_name = RTRS_PATH . "views/settings/{$this->active_tab}-settings.php";
		}
		if ( file_exists( $file_name ) ) {
			$field = include $file_name;
		}

		$this->form_fields = apply_filters( 'rtrs_settings_option_fields', $field, $this->active_tab, $this->current_section );
	}

	protected function add_subsections() {
		if ( ! $this->active_tab ) {
			return;
		}
		if ( method_exists( $this, $this->active_tab . '_add_subsections' ) ) {
			$this->{$this->active_tab . '_add_subsections'}();
		} else {
			$sub_sections = apply_filters( 'rtrs_' . $this->active_tab . '_sub_sections', [] );
			if ( is_array( $sub_sections ) && ! empty( $sub_sections ) ) {
				$this->subtabs = $sub_sections;
			}
		}
	}

	protected function schema_add_subsections() {
		$sub_sections  = [
			''                   => esc_html__( 'Organization Info', 'review-schema' ),
			'social_profiles'    => esc_html__( 'Social Profiles', 'review-schema' ),
			'corporate_contacts' => esc_html__( 'Corporate Contacts', 'review-schema' ),
			'sitelink'           => esc_html__( 'Search Result', 'review-schema' ),
			'publisher'          => esc_html__( 'Publisher Info', 'review-schema' ),
			// 'migration' => esc_html__("Migration", 'review-schema'),
			'tpp'                => esc_html__( 'Third Party Plugin', 'review-schema' ),
		];
		$this->subtabs = apply_filters( 'rtrs_schema_sub_sections', $sub_sections );
	}

	public function save() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD']
			|| ! isset( $_REQUEST['page'] )
			|| ( isset( $_REQUEST['post_type'] ) && rtrs()->getPostType() !== $_REQUEST['post_type'] )
			|| ( isset( $_REQUEST['page'] ) && 'rtrs-settings' !== $_REQUEST['page'] )
			|| ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'rtrs-reviews' )
		) {
			return;
		}

		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'rtrs-settings' ) ) {
			die( esc_html__( 'Action failed. Please refresh the page and retry.', 'review-schema' ) );
		}
		$this->set_fields();
		$this->process_admin_options();

		self::add_message( esc_html__( 'Your settings have been saved.', 'review-schema' ) );
		update_option( 'rtrs_queue_flush_rewrite_rules', 'yes' );

		do_action( 'rtrs_admin_settings_saved', $this->option, $this );
	}

	public function setTabs() {
		$this->tabs = [
			'review'      => esc_html__( 'Review', 'review-schema' ),
			'schema'      => esc_html__( 'Schema', 'review-schema' ),
			'woocommerce' => esc_html__( 'WooCommerce', 'review-schema' ),
			'media'       => esc_html__( 'Media', 'review-schema' ),
			'misc'       => esc_html__( 'Misc', 'review-schema' ),
			// 'support'     => esc_html__('Support', 'review-schema'),
		];

		// Hook to register custom tabs
		$this->tabs = apply_filters( 'rtrs_register_settings_tabs', $this->tabs );

		// Find the active tab
		$this->option = $this->active_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->tabs ) ? trim( $_GET['tab'] ) : 'review';
		$this->add_subsections();

		if ( ! empty( $this->subtabs ) ) {
			$this->current_section = ! empty( $_GET['section'] ) && array_key_exists( $_GET['section'], $this->subtabs ) ? trim( $_GET['section'] ) : '';
			$this->option          = $this->current_section ? $this->option . '_' . $this->current_section : $this->active_tab;
			$this->option         .= '_settings';
		} else {
			$this->option = $this->option . '_settings';
		}
	}

	/**
	 * Marketing links.
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function marketing_links( $links ) {
		$links[] = '<a target="_blank" href="' . esc_url( 'https://www.radiustheme.com/demo/plugins/review-schema' ) . '">Demo</a>';
		$links[] = '<a target="_blank" href="' . esc_url( 'https://www.radiustheme.com/docs/review-schema/review-schema' ) . '">Documentation</a>';
		if ( ! function_exists( 'rtrsp' ) ) {
			$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;" href="' . esc_url( 'https://www.radiustheme.com/downloads/wordpress-review-structure-data-schema-plugin/?utm_source=WordPress&utm_medium=reviewschema&utm_campaign=pro_click' ) . '">Get Pro</a>';
		}

		return $links;
	}
}
