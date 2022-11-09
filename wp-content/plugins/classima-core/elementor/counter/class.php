<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Counter extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Counter', 'classima-core' );
		$this->rt_base = 'rt-counter';
		parent::__construct( $data, $args );
	}

	private function rt_load_scripts(){
		wp_enqueue_script( 'waypoints' );
		wp_enqueue_script( 'counterup' );
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
				'id'      => 'icontype',
				'label'   => __( 'Icon Type', 'classima-core' ),
				'options' => array(
					'icon'  => __( 'Icon', 'classima-core' ),
					'image' => __( 'Custom Image', 'classima-core' ),
				),
				'default' => 'icon',
			),
			array(
				'type'    => Controls_Manager::ICON,
				'id'      => 'icon',
				'label'   => __( 'Icon', 'classima-core' ),
				'default' => 'fa fa-handshake-o',
				'condition'   => array( 'icontype' => array( 'icon' ) ),
			),
			array(
				'type'    => Controls_Manager::MEDIA,
				'id'      => 'image',
				'label'   => __( 'Image', 'classima-core' ),
				'condition'   => array( 'icontype' => array( 'image' ) ),
				'description' => __( 'Recommended image size is 94x94 px.<br/>You can upload SVG format as well, to get SVG images click here: <a target="_blank" href="https://www.flaticon.com/">flaticon.com</a>', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'number',
				'label'   => __( 'Counter Number', 'classima-core' ),
				'default' => 5000,
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'suffix',
				'label'   => __( 'Counter Suffix', 'classima-core' ),
				'description' => __( 'Put any text or symbol after Counter Number eg. +', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'title',
				'label'   => __( 'Title', 'classima-core' ),
				'default' => __( 'Satisfied Customers', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'speed',
				'label'   => __( 'Animation Speed', 'classima-core' ),
				'default' => 1000,
				'description' => __( 'The total duration of the count animation in milisecond eg. 1000', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'steps',
				'label'   => __( 'Animation Steps', 'classima-core' ),
				'default' => 10,
				'description' => __( 'Counter steps eg. 10', 'classima-core' ),
			),
			array(
				'mode' => 'section_end',
			),

			// Style Tab
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Style', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'icon_color',
				'label'   => __( 'Icon Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-item .rtin-left svg, {{WRAPPER}} .rtin-item .rtin-left .fa' => 'color: {{VALUE}}; fill: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'counter_color',
				'label'   => __( 'Counter Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-counter' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-counter',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-title',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();
		$this->rt_load_scripts();

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}