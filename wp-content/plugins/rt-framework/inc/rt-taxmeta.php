<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once RT_FRAMEWORK_BASE_DIR . 'inc/rt-taxmeta-fields.php';

if( !class_exists( 'RT_TaxMeta' ) ){

	class RT_TaxMeta extends RT_Postmeta {
		protected static $term_instance = null;

		private $tax_meta        = array();

		private function __construct() {
			$this->fields_obj = new RT_TaxMeta_Fields();
			$this->base_url   = $this->get_base_url(). '/';
			add_action( 'init', array( $this, 'initialize' ), 12 );
		}

		public static function getInstance() {
			if ( null == self::$term_instance ) {
				self::$term_instance = new self;
			}
			return self::$term_instance;
		}

		public function initialize() {
			if ( !is_admin() ) return;

			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_and_scripts' ) );

			$tax_done = array();

			foreach ( $this->tax_meta as $id => $meta ) {
				if ( !in_array( $meta['taxonomy'], $tax_done ) ) {
					add_action( "{$meta['taxonomy']}_add_form_fields", array( $this, 'add_tax_fields' ), $meta['priority'] );
					add_action( "{$meta['taxonomy']}_edit_form_fields", array( $this, 'edit_tax_fields' ), $meta['priority'], 2 );
					$tax_done[] = $meta['taxonomy'];
				}
			}

			add_action( 'created_term', array( $this, 'save_fields' ), 10, 3 );
			add_action( 'edit_term', array( $this, 'save_fields' ), 10, 3 );
		}

		public function add_tax_meta( $id, $taxonomy, $priority = 10, $fields = '' ) {
			$tax_metas = array(
				'taxonomy' => $taxonomy,
				'priority' => $priority,
				'fields'   => $fields,
			);
			$this->tax_meta[$id] = apply_filters( 'rt_tax_meta_' . $id, $tax_metas );
		}

		public function add_tax_fields( $taxonomy ) {
			foreach ( $this->tax_meta as $meta ) {
				if ( $taxonomy == $meta['taxonomy'] ) {
					$this->fields_obj->display_fields( $meta['fields'], 0, 'add' );
				}
			}
		}

		public function edit_tax_fields( $term, $taxonomy ) {
			foreach ( $this->tax_meta as $meta ) {
				if ( $taxonomy == $meta['taxonomy'] ) {
					$this->fields_obj->display_fields( $meta['fields'], $term->term_id );
				}
			}
		}

		public function save_fields( $term_id, $tt_id, $taxonomy ) {
			foreach ( $this->tax_meta as $meta ) {
				if ( $taxonomy == $meta['taxonomy'] ) {
					foreach ( $meta['fields'] as $field => $data ) {
						$this->save_single_meta( $field, $data, $term_id );
					}
				}
			}
		}

		public function save_single_meta( $field, $data, $term_id ){
			if( isset( $_POST[ $field ] ) ){
				$old = get_term_meta( $term_id, $field, true );
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
						delete_term_meta( $term_id, $field);
					}
					else {
						update_term_meta( $term_id, $field, $new );
					}
				}
			}
			else{
				if ( $data['type'] == 'checkbox' || $data['type'] == 'multi_checkbox' || $data['type'] == 'multi_select' ) {
					delete_term_meta( $term_id, $field);
				}
			}
		}
	}
}

RT_TaxMeta::getInstance();