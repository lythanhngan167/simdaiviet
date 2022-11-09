<?php
/**
 * @author  RadiusTheme
 * @since   1.2
 * @version 1.2
 */

namespace radiustheme\Classima_Core;

use \WPCF7_ContactFormTemplate;

if ( ! defined( 'ABSPATH' ) ) exit;

class Demo_Importer_OCDI {

	public function __construct() {
		add_filter( 'pt-ocdi/import_files',          array( $this, 'demo_config' ) );
		add_filter( 'pt-ocdi/before_content_import', array( $this, 'before_import' ) );
		add_filter( 'pt-ocdi/after_import',          array( $this, 'after_import' ) );
		add_filter( 'pt-ocdi/disable_pt_branding',   '__return_true' );
		add_action( 'init',                          array( $this, 'rewrite_flush_check' ) );
	}

	public function demo_config() {

		$demos_array = array(
			'demo1' => array(
				'title' => __( 'Home 1', 'classima-core' ),
				'page'  => __( 'Home 1', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot1.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/',
			),
			'demo2' => array(
				'title' => __( 'Home 2', 'classima-core' ),
				'page'  => __( 'Home 2', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot2.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-2/',
			),
			'demo3' => array(
				'title' => __( 'Home 3', 'classima-core' ),
				'page'  => __( 'Home 3', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot3.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-3/',
			),
			'demo4' => array(
				'title' => __( 'Home 4', 'classima-core' ),
				'page'  => __( 'Home 4', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot4.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-4/',
			),
			'demo5' => array(
				'title' => __( 'Home 5', 'classima-core' ),
				'page'  => __( 'Home 5', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot5.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-5/',
			),
			'demo6' => array(
				'title' => __( 'Home 6', 'classima-core' ),
				'page'  => __( 'Home 6', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot6.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-6/',
			),
			'demo7' => array(
				'title' => __( 'Home 7', 'classima-core' ),
				'page'  => __( 'Home 7', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot7.png', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-7/',
			),
			'demo8' => array(
				'title' => __( 'Home 8', 'classima-core' ),
				'page'  => __( 'Home 8', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot8.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classima/home-8/',
			),
		);

		$config = array();
		$import_path  = trailingslashit( get_template_directory() ) . 'sample-data/';
		$redux_option = 'classima';

		foreach ( $demos_array as $key => $demo ) {
			$config[] = array(
				'import_file_id'               => $key,
				'import_page_name'             => $demo['page'],
				'import_file_name'             => $demo['title'],
				'local_import_file'            => $import_path . 'contents.xml',
				'local_import_widget_file'     => $import_path . 'widgets.wie',
				'local_import_customizer_file' => $import_path . 'customizer.dat',
				'local_import_redux'           => array(
					array(
						'file_path'   => $import_path . 'options.json',
						'option_name' => $redux_option,
					),
				),
				'import_preview_image_url'   => $demo['screenshot'],
				'preview_url'                => $demo['preview_link'],
			);
		}

		return $config;
	}

	public function before_import( $selected_import ) {
		// Delete My Account and Checkout page
		$myaccount = get_page_by_title( 'My Account' );
		$checkout  = get_page_by_title( 'Checkout' );

		if ( $myaccount ) {
			wp_delete_post( $myaccount->ID, true );
		}

		if ( $checkout ) {
			wp_delete_post( $checkout->ID, true );
		}
	}

	public function after_import( $selected_import ) {
		$this->assign_menu( $selected_import['import_file_id'] );
		$this->assign_frontpage( $selected_import );
		$this->update_contact_form_sender_email();
		$this->update_permalinks();
		$this->update_rtcl_options();
		update_option( 'classima_ocdi_importer_rewrite_flash', true );
	}

	private function assign_menu( $demo ) {
		$primary  = get_term_by( 'name', 'Main Menu', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
			'primary'  => $primary->term_id,
		));
	}

	private function assign_frontpage( $selected_import ) {
		$blog_page  = get_page_by_title( 'Blog' );
		$front_page = get_page_by_title( $selected_import['import_page_name'] );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front',  $front_page->ID );
		update_option( 'page_for_posts', $blog_page->ID );
	}

	private function update_contact_form_sender_email() {
		$form1 = get_page_by_title( 'Contact', OBJECT, 'wpcf7_contact_form' );

		$forms = array( $form1 );
		foreach ( $forms as $form ) {
			if ( !$form ) {
				continue;
			}
			$cf7id = $form->ID;
			$mail  = get_post_meta( $cf7id, '_mail', true );
			if ( class_exists( 'WPCF7_ContactFormTemplate' ) ) {
				$pattern = "/<[^@\s]*@[^@\s]*\.[^@\s]*>/"; // <email@email.com>
				$replacement = '<'. WPCF7_ContactFormTemplate::from_email().'>';
				$mail['sender'] = preg_replace($pattern, $replacement, $mail['sender']);
			}
			update_post_meta( $cf7id, '_mail', $mail );		
		}
	}

	private function update_permalinks() {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	private function update_rtcl_options() {
		$listings     = get_page_by_title( 'All Ads' );
		$listing_form = get_page_by_title( 'Post an Ad' );
		$myaccount    = get_page_by_title( 'My Account' );
		$checkout     = get_page_by_title( 'Checkout' );

		$data = array(
			'rtcl_general_settings'    => array(
				'listings_per_page'            => 9,
				'default_view'                 => 'grid',
				'currency_position'            => 'left',
			),
			'rtcl_moderation_settings' => array(
				'listing_duration'             => 0,
				'new_listing_threshold'        => 155,
				'listing_bump_up_label'        => '',
				'display_options'              => array(
					'category',
					'location',
					'date',
					'price',
					'views',
					'excerpt',
					'new',
					'popular'
				),
				'display_options_detail'       => array(
					'location',
					'date',
					'user',
					'price',
					'views',
					'top',
					'featured',
					'new',
					'popular'
				),
				'has_comment_form'             => 'yes',
				'enable_review_rating'         => 'yes',
				'enable_update_rating'         => 'yes',
			),
			'rtcl_advanced_settings'           => array(
				'permalink'                    => 'listing',

				'listings'                     => $listings->ID,
				'listing_form'                 => $listing_form->ID,
				'myaccount'                    => $myaccount->ID,
				'checkout'                     => $checkout->ID,
			),
			'rtcl_style_settings'   => array(
				'new'               => '#e7291e',
			)
		);

		foreach ( $data as $key => $value ) {
			$defaults = get_option( $key, array() );
			$args = wp_parse_args( $value , $defaults );
			update_option( $key, $args );
		}
	}

	public function rewrite_flush_check() {
		if ( get_option( 'classima_ocdi_importer_rewrite_flash' ) == true  ) {
			flush_rewrite_rules();
			delete_option( 'classima_ocdi_importer_rewrite_flash' );
		}
	}
}

new Demo_Importer_OCDI;