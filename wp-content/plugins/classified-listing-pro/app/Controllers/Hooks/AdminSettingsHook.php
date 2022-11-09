<?php

namespace RtclPro\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;
use RtclPro\Helpers\Options;
use RtclPro\Helpers\PNHelper;

class AdminSettingsHook {


	public static function init() {
		add_filter( 'rtcl_general_settings_options', array( __CLASS__, 'general_settings_pro_feature' ) );
		add_filter( 'rtcl_moderation_settings_options', array( __CLASS__, 'moderation_settings_pro_feature' ) );
		add_filter( 'rtcl_account_settings_options', array( __CLASS__, 'account_settings_pro_feature' ) );
		add_filter( 'rtcl_style_settings_options', array( __CLASS__, 'style_settings_pro_feature' ) );
		add_filter( 'rtcl_misc_settings_options', array( __CLASS__, 'misc_settings_pro_feature' ) );
		add_filter( 'rtcl_tools_settings_options', array( __CLASS__, 'tools_settings_pro_feature' ) );
		add_filter( 'rtcl_advanced_settings_options', array( __CLASS__, 'advanced_settings_pro_feature' ) );
		add_filter( 'rtcl_get_listing_common_display_options', array( __CLASS__, 'listing_display_options' ) );
		add_filter( 'rtcl_register_settings_tabs', array( __CLASS__, 'add_chat_tab' ) );
		add_filter( 'rtcl_settings_option_fields', array( __CLASS__, 'add_chat_settings_options' ), 10, 2 );
	}

	public static function add_chat_settings_options( $fields, $active_tab ) {
		if ( 'chat' === $active_tab ) {
			$fields = Options::chat_admin_settings();
		}

		return $fields;
	}

	public static function add_chat_tab( $tabs ) {
		$position = array_search( 'misc', array_keys( $tabs ) );
		if ( $position > -1 ) {
			$newOptions = array( 'chat' => esc_html__( 'Chat', 'classified-listing-pro' ) );
			Functions::array_insert( $tabs, $position, $newOptions );
		}

		return $tabs;
	}

	public static function listing_display_options( $options ) {
		$options['popular'] = esc_html__( 'Popular Label', 'classified-listing-pro' );
		$options['top']     = esc_html__( 'Top Label', 'classified-listing-pro' );
		$options['bump_up'] = esc_html__( 'Bump Up Label', 'classified-listing-pro' );

		return $options;
	}

	public static function advanced_settings_pro_feature( $options ) {
		$newOptions = array(
			'myaccount_chat_endpoint' => array(
				'title'   => esc_html__( 'Chat', 'classified-listing-pro' ),
				'type'    => 'text',
				'default' => 'chat',
			),
			'myaccount_verify'        => array(
				'title'   => esc_html__( 'Account verify', 'classified-listing-pro' ),
				'type'    => 'text',
				'default' => 'verify',
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_advanced__settings_pro_feature', $newOptions );

		return self::append_options( 'myaccount_favourites_endpoint', $options, $newOptions );
	}

	public static function tools_settings_pro_feature( $options ) {
		// if (Fns::check_license()) {
		// $settings = Functions::get_option('rtcl_tools_settings');
		// $status = !empty($settings['license_status']) && $settings['license_status'] === 'valid';
		// $license_status = !empty($settings['license_key']) ? sprintf("<span class='license-status'>%s</span>",
		// $status ? "<span data-action='rtcl_manage_licensing' class='button-secondary rt-licensing-btn danger license_deactivate'>" . esc_html__("Deactivate License", "classified-listing-pro") . "</span>"
		// : "<span data-action='rtcl_manage_licensing' class='button-secondary rt-licensing-btn button-primary license_activate'>" . esc_html__("Activate License", "classified-listing-pro") . "</span>"
		// ) : ' ';

		// $license = array(
		// 'licensing_section' => array(
		// 'title' => esc_html__('Licensing', 'classified-listing-pro'),
		// 'type'  => 'title',
		// ),
		// 'license_key'       => array(
		// 'title'         => esc_html__('Main plugin license key', 'classified-listing-pro'),
		// 'type'          => 'text',
		// 'wrapper_class' => 'rtcl-license-wrapper',
		// 'description'   => $license_status
		// )
		// );

		// $options = array_merge($license, $options);
		// }

		$apiKey            = get_option( 'rtcl_rest_api_key', null );
		$rest_api_key_html = sprintf(
			'<a href="%s">Create Rest API key</a>',
			add_query_arg(
				array(
					'_wpnonce'                   => wp_create_nonce( 'rtcl_generate_rest_api_key' ),
					'rtcl_generate_rest_api_key' => 1,
				),
				admin_url( 'edit.php?post_type=' . rtcl()->post_type . '&page=rtcl-settings&tab=tools' )
			)
		);
		if ( $apiKey ) {
			$rest_api_key_html = sprintf( '<span class="rtcl-rest-api-key">%s</span>', $apiKey );
			if ( ! wp_is_uuid( $apiKey ) ) {
				$rest_api_key_html .= '<span class="rtcl-rest-api-key-invalid" style="color: red">%s Key is not validate.</span> ';
			}
		}
		$newOptions = array(
			'app_section'    => array(
				'title' => esc_html__( 'App Management', 'classified-listing-pro' ),
				'type'  => 'title',
			),
			'allow_rest_api' => array(
				'title'       => esc_html__( 'Allow REST API', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'Allow to handle data to app.', 'classified-listing-pro' ),
			),
			'rest_api_key'   => array(
				'title'       => esc_html__( 'REST API key', 'classified-listing-pro' ),
				'type'        => 'html',
				'html'        => $rest_api_key_html,
				'description' => '<span style="color: red">' . esc_html__( 'This is one time generated key. Do not recreate this key. if you regenerate then you need to change the key from your application where you are currently using.', 'classified-listing-pro' ) . '</span>',
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_tools_settings_pro_feature', $newOptions );

		return self::append_options( 'delete_all_data', $options, $newOptions );
	}

	public static function misc_settings_pro_feature( $options ) {
		$newOptions = array(
			'required_gallery_image' => array(
				'title' => esc_html__( 'Required gallery image', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Make gallery image mandatory.', 'classified-listing-pro' ),
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_misc_settings_pro_feature_1', $newOptions );
		$options    = self::append_options( 'placeholder_image', $options, $newOptions );

		$newOptions = array(
			'disable_gallery_zoom'       => array(
				'title' => esc_html__( 'Disable gallery zoom', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Disable', 'classified-listing-pro' ),
			),
			'disable_gallery_photoswipe' => array(
				'title'       => esc_html__( 'Disable gallery lightbox', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Disable', 'classified-listing-pro' ),
				'description' => esc_html__( 'Disable gallery lightbox (PopUp lightbox Gallery)', 'classified-listing-pro' ),
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_misc_settings_pro_feature_2', $newOptions );
		return self::append_options( 'disable_gallery_slider', $options, $newOptions );
	}

	public static function style_settings_pro_feature( $options ) {
		$newOptions = array(
			'top'          => array(
				'title' => esc_html__( 'Top label background color', 'classified-listing-pro' ),
				'type'  => 'color',
			),
			'top_text'     => array(
				'title' => esc_html__( 'Top label text color', 'classified-listing-pro' ),
				'type'  => 'color',
			),
			'popular'      => array(
				'title' => esc_html__( 'Popular label background color', 'classified-listing-pro' ),
				'type'  => 'color',
			),
			'popular_text' => array(
				'title' => esc_html__( 'Popular label text color', 'classified-listing-pro' ),
				'type'  => 'color',
			),
			'bump_up'      => array(
				'title' => esc_html__( 'BumpUp label background color', 'classified-listing-pro' ),
				'type'  => 'color',
			),
			'bump_up_text' => array(
				'title' => esc_html__( 'BumpUp text color', 'classified-listing-pro' ),
				'type'  => 'color',
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_style_settings_pro_feature', $newOptions );

		return self::append_options( 'feature_text', $options, $newOptions );
	}

	public static function account_settings_pro_feature( $options ) {
		$newOptions = array(
			'allowed_core_permission_roles' => array(
				'title'       => esc_html__( 'Admin Menu Access role', 'classified-listing-pro' ),
				'type'        => 'multi_checkbox',
				'options'     => Functions::get_user_roles( '', array( 'administrator', 'rtcl_manager' ) ),
				'description' => wp_kses(
					__( 'Allowed all Classified Listing Admin Menu access to a user role as like Administrator. [<span style="color: red;">NOT RECOMMENDED</span>]', 'classified-listing-pro' ),
					array(
						'span' => array(
							'style' => array( 'color' ),
						),
					)
				),
			),
			'enable_post_for_unregister'    => array(
				'title'       => esc_html__( 'Allow post for unregister user', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'Allow visitor to create a post and account will create automatically', 'classified-listing-pro' ),
			),
			'user_verification'             => array(
				'title'       => esc_html__( 'User Verification', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'User Registration will be pending and a verification email will send to the user email.', 'classified-listing-pro' ),
			),
			'verify_max_resend_allowed'     => array(
				'title'             => esc_html__( 'Max Re-send attempts', 'classified-listing-pro' ),
				'type'              => 'number',
				'default'           => 5,
				'css'               => 'width:50px',
				'wrapper_class'     => Functions::get_option_item( 'rtcl_account_settings', 'user_verification', null, 'checkbox' ) ? '' : 'hidden',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '1',
					'max'  => '15',
				),
				'description'       => esc_html__( 'Max number of re-send requests a user can make, more than that, his account will be locked.', 'classified-listing-pro' ),
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_account_settings_pro_feature', $newOptions );

		return self::append_options( 'user_role', $options, $newOptions );
	}

	public static function moderation_settings_pro_feature( $options ) {
		$newOptions = array(
			'popular_listing_threshold'  => array(
				'title'       => esc_html__( 'Popular listing threshold (in views count)', 'classified-listing-pro' ),
				'type'        => 'number',
				'default'     => 1000,
				'description' => esc_html__(
					'Enter the minimum number of views required for a listing to be tagged as "Popular".',
					'classified-listing-pro'
				),
			),
			'popular_listing_label'      => array(
				'title'       => esc_html__( 'Label text for popular listings', 'classified-listing-pro' ),
				'type'        => 'text',
				'default'     => esc_html__( 'Popular', 'classified-listing-pro' ),
				'description' => esc_html__( 'Enter the text you want to use inside the "Popular" tag.', 'classified-listing-pro' ),
			),
			'listing_top_label'          => array(
				'title'       => esc_html__( 'Label text for Top listings', 'classified-listing-pro' ),
				'type'        => 'text',
				'default'     => esc_html__( 'Top', 'classified-listing-pro' ),
				'description' => esc_html__( 'Enter the text you want to use inside the "Top" tag.', 'classified-listing-pro' ),
			),
			'listing_bump_up_label'      => array(
				'title'       => esc_html__( 'Label text for Bump Up listings', 'classified-listing-pro' ),
				'type'        => 'text',
				'default'     => esc_html__( 'Bump Up', 'classified-listing-pro' ),
				'description' => esc_html__( 'Enter the text you want to use inside the "Bump Up" tag.', 'classified-listing-pro' ),
			),
			'listing_enable_top_listing' => array(
				'title'   => esc_html__( 'Enable top listing at listing page', 'classified-listing-pro' ),
				'type'    => 'checkbox',
				'default' => 'yes',
				'label'   => esc_html__( 'Enable top listing', 'classified-listing-pro' ),
			),
			'listing_top_per_page'       => array(
				'title'       => esc_html__( 'Top listing number to display', 'classified-listing-pro' ),
				'type'        => 'number',
				'default'     => 2,
				'description' => esc_html__( 'Enter number of top listing to display at listing page', 'classified-listing-pro' ),
			),
			'registered_only'            => array(
				'title'   => esc_html__( 'Registered user only', 'classified-listing-pro' ),
				'type'    => 'multi_checkbox',
				'options' => Options::get_registered_only_options(),
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_moderation_settings_pro_feature_1', $newOptions );
		$options    = self::append_options( 'listing_featured_label', $options, $newOptions );

		$newOptions = array(
			'enable_review_rating' => array(
				'title' => esc_html__( 'Enable review rating', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Allow visitors to make review rating.', 'classified-listing-pro' ),
			),
			'enable_update_rating' => array(
				'title' => esc_html__( 'Enable update rating', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'If same user try to add duplicate post than allow it to update previous one.', 'classified-listing-pro' ),
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_moderation_settings_pro_feature_2', $newOptions );

		return self::append_options( 'has_map', $options, $newOptions );
	}

	public static function general_settings_pro_feature( $options ) {
		$newOptions = array(
			'enable_quick_view'   => array(
				'title' => esc_html__( 'Enable quick view', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Enable Quick view.', 'classified-listing-pro' ),
			),
			'enable_compare'      => array(
				'title' => esc_html__( 'Enable compare', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Enable compare', 'classified-listing-pro' ),
			),
			'compare_limit'       => array(
				'title'       => esc_html__( 'Compare limit', 'classified-listing-pro' ),
				'type'        => 'number',
				'default'     => 3,
				'css'         => 'width:50px',
				'description' => esc_html__( 'Maximum number of listings to compare', 'classified-listing-pro' ),
			),
			'enable_mark_as_sold' => array(
				'title' => esc_html__( 'Enable Mark as Sold', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Enable Mark as Sold', 'classified-listing-pro' ),
			),
			'default_view'        => array(
				'title'   => esc_html__( 'Default Listing view', 'classified-listing-pro' ),
				'type'    => 'select',
				'default' => 'list',
				'options' => array(
					'list' => esc_html__( 'List view', 'classified-listing-pro' ),
					'grid' => esc_html__( 'Grid view', 'classified-listing-pro' ),
				),
			),
		);
		$newOptions = apply_filters( 'rtcl_pro_general_settings_pro_feature', $newOptions );
		return self::append_options( 'related_posts_per_page', $options, $newOptions );
	}

	/**
	 * @param       $target_item
	 * @param       $options
	 * @param array       $newOptions
	 *
	 * @return array
	 */
	private static function append_options( $target_item, $options, array $newOptions ) {
		$position = array_search( $target_item, array_keys( $options ) );
		if ( $position > -1 ) {
			Functions::array_insert( $options, $position, $newOptions );
		} else {
			array_unshift(
				$newOptions,
				array(
					$target_item . '_pro_section' => array(
						'title'       => esc_html__( 'Pro settings', 'classified-listing-pro' ),
						'type'        => 'title',
						'description' => '',
					),
				)
			);

			$options = array_merge( $options, $newOptions );
		}

		return $options;
	}
}
