<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Info_Box extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Info Box', 'classima-core' );
		$this->rt_base = 'rt-info-box';
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
                'type'    => Controls_Manager::TEXT,
                'id'      => 'block_no',
                'label'   => __( 'Block Number', 'classima-core' ),
                'default' => '01',
                'condition' => array(
                    'style' => array('2')
                ),
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
				'default' => 'fas fa-pencil-alt',
				'condition'   => array( 'icontype' => array( 'icon' ) ),
			),
			array(
				'type'    => Controls_Manager::MEDIA,
				'id'      => 'image',
				'label'   => __( 'Image', 'classima-core' ),
				'condition'   => array( 'icontype' => array( 'image' ) ),
				'description' => __( 'Recommended image size is 160x160 px.<br/>You can upload SVG format as well, to get SVG images click here: <a target="_blank" href="https://www.flaticon.com/">flaticon.com</a>', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'title',
				'label'   => __( 'Title', 'classima-core' ),
				'default' => 'Lorem Ipsum',
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'content',
				'label'   => __( 'Content', 'classima-core' ),
				'default' => '',
			),
			array(
				'type'  => Controls_Manager::URL,
				'id'    => 'url',
				'label' => __( 'Link (Optional)', 'classima-core' ),
				'placeholder' => 'https://your-link.com',
			),
			array(
				'mode' => 'section_end',
			),

			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Style', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'bgcolor',
				'label'   => __( 'Background Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-el-info-box' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'icon_color',
				'label'   => __( 'Icon Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-icon i' => 'color: {{VALUE}}', '{{WRAPPER}} .rtin-icon svg' => 'fill: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title, {{WRAPPER}} .rtin-title a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_hover_color',
				'label'   => __( 'Title Hover Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title a:hover' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'content_color',
				'label'   => __( 'Content Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-text' => 'color: {{VALUE}}' ),
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
				'id'       => 'content_typo',
				'label'    => __( 'Content Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-text',
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