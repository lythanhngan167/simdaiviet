<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Text_Button extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Text With Button', 'classima-core' );
		$this->rt_base = 'rt-text-btn';
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
				'type'    => Controls_Manager::MEDIA,
				'id'      => 'image',
				'label'   => __( 'Image', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-right' => 'background-image: url({{URL}})' ),
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'title',
				'label'   => __( 'Title', 'classima-core' ),
				'default' => 'Lorem Ipsum',
			),
			array(
				'type'    => Controls_Manager::WYSIWYG,
				'id'      => 'content',
				'label'   => __( 'Content', 'classima-core' ),
				'default' => 'Lorem Ipsum has been standard daand scrambled. Rimply dummy text of the printing and typesetting industry',
			),
			array(
				'type' => Controls_Manager::SLIDER,
				'id'      => 'height',
				'mode'    => 'responsive',
				'label'   => __( 'Min Height', 'classima-core' ),
				'size_units' => array( 'px' ),
				'range'  => array(
					'px' => array(
						'min' => 0,
						'max' => 2000,
					),
				),
				'default'  => array(
					'unit' => 'px',
					'size' => 500,
				),
				'selectors' => array(
					'{{WRAPPER}} .rtin-left' => 'min-height: {{SIZE}}{{UNIT}};',
				)
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'btntext',
				'label'   => __( 'Button Text', 'classima-core' ),
				'default' => 'LOREM IPSUM',
			),
			array(
				'type'    => Controls_Manager::URL,
				'id'      => 'btnurl',
				'label'   => __( 'Button URL', 'classima-core' ),
				'placeholder' => 'https://your-link.com',
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
				'id'      => 'text_bgcolor',
				'label'   => __( 'Text Background Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-left' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'content_color',
				'label'   => __( 'Content Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-content' => 'color: {{VALUE}}' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Content Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-content',
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