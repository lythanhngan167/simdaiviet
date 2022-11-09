<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class RtButton extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Button', 'classima-core' );
		$this->rt_base = 'rt-btn';
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
				'type'    => Controls_Manager::ICON,
				'id'      => 'icon',
				'label'   => __( 'Icon', 'classima-core' ),
			),
			array(
				'type' => Controls_Manager::SLIDER,
				'id'      => 'width',
				'mode'    => 'responsive',
				'label'   => __( 'Min Width', 'classima-core' ),
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
					'{{WRAPPER}} .rt-btn--style2' => 'min-width: {{SIZE}}{{UNIT}};',
				)
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
					'{{WRAPPER}} .rt-btn--style2' => 'min-height: {{SIZE}}{{UNIT}};',
				)
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
				'type'    => Controls_Manager::CHOOSE,
				'id'      => 'align',
				'label'   => __( 'Alignment', 'classima-core' ),
				'options' => $this->rt_alignment_options(),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .rt-btn-animated-icon' => 'text-align: {{VALUE}};',
				),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'text_bgcolor',
				'label'   => __( 'Background Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-btn--style2' => 'background-color: {{VALUE}}; justify-content: center' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Text Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-btn--style2' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'icon_color',
				'label'   => __( 'Icon Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-btn--style2 i' => 'color: {{VALUE}}' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Text Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rt-btn--style2',
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