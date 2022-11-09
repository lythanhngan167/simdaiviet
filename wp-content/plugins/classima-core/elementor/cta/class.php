<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class CTA extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Call to Action', 'classima-core' );
		$this->rt_base = 'rt-cta';
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
                'id'      => 'style',
                'label'   => __( 'Select Style', 'classima-core' ),
                'options' => array(
                    '1'  => __( 'Style 1', 'classima-core' ),
                    '2'  => __( 'Style 2', 'classima-core' ),
                ),
                'default' => '1',
            ),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'title1',
				'label'   => __( 'Title', 'classima-core' ),
				'default' => 'Lorem Ipsum Title',
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'subtitle',
				'label'   => __( 'Subtitle', 'classima-core' ),
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris varisit.',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'buttontext',
				'label'   => __( 'Button Text', 'classima-core' ),
				'default' => 'Lorem Ipsum',
			),
			array(
				'type'    => Controls_Manager::URL,
				'id'      => 'buttonurl',
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
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'padding',
				'size_units' => [ 'px' ],
				'label'      => __( 'Padding', 'classima-core' ),
				'selectors'  => array(
				                    '{{WRAPPER}} .rt-el-cta-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				                    '{{WRAPPER}} .rt-el-cta-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
                                ),
			),
			array(
				'type' => Controls_Manager::SLIDER,
				'id'   => 'width',
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 2000,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 850,
				),
				'label'   => __( 'Content Max Width', 'classima-core' ),
				'selectors' => array(
					'{{WRAPPER}} .rt-el-cta-1 .rtin-left' => 'max-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .rt-el-cta-2 .rtin-content' => 'max-width: {{SIZE}}{{UNIT}};',
				)
			),
			array(
				'type'    => \Elementor\Group_Control_Background::get_type(),
				'mode'    => 'group',
				'types' => [ 'classic', 'gradient', 'video' ],
				'id'      => 'background',
				'label'   => __( 'Background', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rt-el-cta-1',
                'condition' => array(
                    'style' => array('1')
                ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'subtitle_color',
				'label'   => __( 'Subtitle Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-subtitle' => 'color: {{VALUE}}' ),
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
				'id'       => 'subtitle_typo',
				'label'    => __( 'Subtitle Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-subtitle',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'btn_typo',
				'label'    => __( 'Button Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-right a',
			),
			array(
				'mode' => 'section_end',
			),

			// Style Tab Button
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_btn_colors',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Button Colors', 'classima-core' ),
			),
			array(
				'mode'    => 'tabs_start',
				'id'      => 'btn_style',
			),
			array(
				'mode'    => 'tab_start',
				'id'      => 'btn_style_normal',
				'label'   => 'Normal',
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'btn_bgcolor',
				'label'   => __( 'Button Background Color', 'classima-core' ),
				'selectors' => array(
				                    '{{WRAPPER}} .rtin-right a' => 'background-color: {{VALUE}}',
				                    '{{WRAPPER}} .rtin-btn a' => 'background-color: {{VALUE}}',
                                ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'btn_color',
				'label'   => __( 'Button Text Color', 'classima-core' ),
				'selectors' => array(
				                    '{{WRAPPER}} .rtin-right a' => 'color: {{VALUE}}',
				                    '{{WRAPPER}} .rtin-btn a' => 'color: {{VALUE}}',
                                ),
			),
			array(
				'mode'    => 'tab_end',
			),
			array(
				'mode'    => 'tab_start',
				'id'      => 'btn_style_hover',
				'label'   => 'Hover',
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'btn_hover_bgcolor',
				'label'   => __( 'Button background Color', 'classima-core' ),
				'selectors' => array(
				                    '{{WRAPPER}} .rtin-right a:hover' => 'background-color: {{VALUE}}',
				                    '{{WRAPPER}} .rtin-btn a:hover' => 'background-color: {{VALUE}}',
                                ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'btn_hover_color',
				'label'   => __( 'Button Text Color', 'classima-core' ),
				'selectors' => array(
				                    '{{WRAPPER}} .rtin-right a:hover' => 'color: {{VALUE}}',
				                    '{{WRAPPER}} .rtin-btn a:hover' => 'color: {{VALUE}}',
                                ),
			),
			array(
				'mode'    => 'tab_end',
			),
			array(
				'mode'    => 'tabs_end',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();

		if ( $data['style'] == 2 ) {
            $template = 'view-2';
        } else {
            $template = 'view-1';
        }

		return $this->rt_template( $template, $data );
	}
}