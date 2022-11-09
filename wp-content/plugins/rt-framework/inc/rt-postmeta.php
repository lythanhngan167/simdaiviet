<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once RT_FRAMEWORK_BASE_DIR . 'inc/rt-postmeta-fields.php';

if( !class_exists( 'RT_Postmeta' ) ){

	class RT_Postmeta {
		protected static $instance = null;
		public  $version          = RT_FRAMEWORK_VERSION;

		private $fields_obj       = null;
		public  $base_url         = null;
		private $metaboxes        = array();
		private $metabox_fields   = array();

		private $nonce_action     = 'rt_metabox_nonce';
		private $nonce_field      = 'rt_metabox_nonce_secret';

		private function __construct() {
			$this->fields_obj = new RT_Postmeta_Fields();
			$this->base_url   = $this->get_base_url(). '/';
			add_action( 'init', array( $this, 'initialize' ), 12 );
		}

		public static function getInstance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function initialize() {
			if ( !is_admin() ) return;

			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_and_scripts' ) );
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ), 5 );
			add_action( 'save_post', array( $this, 'save_metaboxes' ) );
		}

		public function load_styles_and_scripts(){
			wp_enqueue_style( 'rt-posts-jqui', $this->base_url . 'assets/css/jquery-ui.css', array(), $this->version ); // only datepicker
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-timepicker', $this->base_url . 'assets/css/jquery.timepicker.css', array(), $this->version );
			wp_enqueue_style( 'select2', $this->base_url . 'assets/css/select2.min.css', array(), $this->version );
			wp_enqueue_style( 'rt-posts-style', $this->base_url . 'assets/css/style.css', array(), $this->version );

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-timepicker', $this->base_url . 'assets/js/jquery.timepicker.min.js', array('jquery'), $this->version );
			wp_enqueue_script( 'select2', $this->base_url . 'assets/js/select2.min.js', array('jquery'), $this->version );
			wp_enqueue_media();
			wp_enqueue_script('rt-posts-script', $this->base_url . 'assets/js/script.js', array('jquery', 'jquery-ui-core', 'wp-color-picker', 'jquery-ui-datepicker'), $this->version );
		}

		public function get_base_url(){
			$file = dirname( dirname(__FILE__) );

			// Get correct URL and path to wp-content
			$content_url = untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
			$content_dir = untrailingslashit( WP_CONTENT_DIR );

			// Fix path on Windows
			$file = wp_normalize_path( $file );
			$content_dir = wp_normalize_path( $content_dir );

			$url = str_replace( $content_dir, $content_url, $file );

			return $url;
		}

		public function add_meta_box( $id, $title, $post_types, $callback = '', $context = '', $priority = '', $fields = '' ) {

			$fields = apply_filters( 'rt_postmeta_field_' . $id, $fields );
			$metaboxes = array(
				'title'         => $title,
				'callback'      => $callback,
				'post_type'     => $post_types,
				'context'       => empty( $context ) ? 'normal' : $context,
				'priority'      => $priority,
				'callback_args' => $fields,
			);

			$this->metaboxes[$id] = apply_filters( 'rt_metabox_' . $id, $metaboxes );
			$this->metabox_fields[$id] = $fields['fields'];
		}

		public function register_meta_boxes() {
			foreach ( $this->metaboxes as $metabox_id => $args ) {
				add_meta_box(
					$metabox_id,
					$args['title'],
					empty( $args['callback'] ) ? array( $this, 'display_metaboxes' ) : $args['callback'],
					$args['post_type'],
					$args['context'],
					$args['priority'],
					$args['callback_args']
				);
			}
		}

		public function display_metaboxes( $post, $metabox ) {
			$fields = $metabox['args']['fields'];
			wp_nonce_field( $this->nonce_action, $this->nonce_field );
			$this->fields_obj->display_fields( $fields, $post->ID );
		}

		public function save_metaboxes( $post_id ) {
			if ( empty( $_POST[$this->nonce_field] ) || !check_admin_referer( $this->nonce_action, $this->nonce_field ) ) {
				return $post_id;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

			foreach ( $this->metabox_fields as $fields ) {
				foreach ( $fields as $field => $data ) {
					$this->save_single_meta( $field, $data, $post_id );
				}
			}
		}

		public function save_single_meta( $field, $data, $post_id ){
			if( isset( $_POST[ $field ] ) ){
				$old = get_post_meta( $post_id, $field, true );
				$new = $_POST[ $field ];
				
				if ( $data['type'] == 'group' ) {
					$new = $this->sanitize_group_field( $new, $data['value'] );
				}
				elseif ( $data['type'] == 'repeater' ) {
					$new = $this->sanitize_repeater_field( $new, $data['value'] );
				}
				else{
					$new = $this->sanitize_field( $new, $data['type'] );
				}

				// Update
				if ( $new != $old ) {
					if ( $new == array() ) { // assuming repeater field is empty array
						delete_post_meta( $post_id, $field);
					}
					else {
						update_post_meta( $post_id, $field, $new );
					}
				}
			}
			else{
				if ( $data['type'] == 'checkbox' || $data['type'] == 'multi_checkbox' || $data['type'] == 'multi_select' ) {
					delete_post_meta( $post_id, $field);
				}
			}
		}

		public function sanitize_group_field( $data, $type ){
			foreach ( $type as $key => $value ) {
				$data[$key] = $this->sanitize_field( $data[$key], $value['type'] );
			}
			return $data;
		}

		public function filter_empty( $data ){
			return array_filter( $data );
		}

		public function sanitize_repeater_field( $data, $type ){
			unset( $data['hidden'] ); // unset hidden
			foreach ( $data as $key => $value ) {
				foreach ( $value as $key2 => $value2 ) {
					$fieldtype = $type[$key2]["type"];
					$data[$key][$key2] = $this->sanitize_field( $data[$key][$key2], $fieldtype );
				}
			}
			$data = array_values( $data ); //rearrange
			return $data;
		}

		public function sanitize_field( $data, $type ){
			switch ( $type ) {
				case 'multi_select':
				$data = array_filter( $data, 'sanitize_text_field' );
				break;
				case 'multi_checkbox':
				$data = array_filter( $data, 'sanitize_text_field' );
				break;
				case 'textarea':
				$data = trim( $data );
				$data = wp_kses_post( $data );
				break;
				case 'textarea_html':
				$data = trim( $data );
				break;
				case 'color_picker':
				$data = sanitize_hex_color( $data );
				break;
				default:
				$data = sanitize_text_field( $data );
				break;
			}
			return $data;
		}
	}
}

RT_Postmeta::getInstance();