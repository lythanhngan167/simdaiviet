<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Search extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Search', 'classima-core' );
		$this->rt_base = 'rt-listing-search';
		parent::__construct( $data, $args );
	}

	public function rt_fields(){
		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'theme',
				'label'   => __( 'Theme', 'classima-core' ),
				'options' => array(
					'light' => __( 'Light Background', 'classima-core' ),
					'dark'  => __( 'Dark Background', 'classima-core' ),
				),
				'default' => 'light',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'style',
				'label'   => __( 'Style', 'classima-core' ),
				'options' => array(
					'1' => __( 'Style 1', 'classima-core' ),
					'2' => __( 'Style 2', 'classima-core' ),
					'3' => __( 'Style 3', 'classima-core' ),
				),
				'default' => '1',
				'condition'   => array( 'theme' => array( 'dark' ) ),
			),
			array(
				'mode' => 'section_end',
			),

			// Style Tab
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style_color',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Color', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'text_color',
				'label'   => __( 'Text', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .classima-listing-search-form .rtcl-search-input-button, {{WRAPPER}} 
					.classima-listing-search-form .rtcl-search-input-button::before, {{WRAPPER}} .classima-listing-search-form .rtin-keyword input' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'btn_bgcolor',
				'label'   => __( 'Button Background', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .classima-listing-search-form .rtin-search-btn' => 'background: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'btn_bgcolor_hover',
				'label'   => __( 'Button Hover Background', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .classima-listing-search-form .rtin-search-btn:hover' => 'background: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style_type',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Typography', 'classima-core' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'text_typo',
				'label'    => __( 'Text', 'classima-core' ),
				'selector' => '{{WRAPPER}} .classima-listing-search-form .rtcl-search-input-button, {{WRAPPER}} 
					.classima-listing-search-form .rtcl-search-input-button::before, {{WRAPPER}} .classima-listing-search-form .rtin-keyword input',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'btn_typo',
				'label'    => __( 'Button', 'classima-core' ),
				'selector' => '{{WRAPPER}} .classima-listing-search-form .rtin-search-btn',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}