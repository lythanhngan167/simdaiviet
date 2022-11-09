<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Testimonial_1 extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Testimonial 1', 'classima-core' );
		$this->rt_base = 'rt-testimonial-1';
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
                'type'    => Controls_Manager::SELECT,
                'id'      => 'style',
                'label'   => __( 'Select Style', 'classima-core' ),
                'options' => array(
                    '1' => __('Style 1', 'classima-core'),
                    '2' => __('Style 2', 'classima-core'),
                ),
                'default' => '1',
            ),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'name',
				'label'   => __( 'Name', 'classima-core' ),
				'default' => 'John Doe',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'designation',
				'label'   => __( 'Designation', 'classima-core' ),
				'default' => 'Designer',
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'content',
				'label'   => __( 'Content', 'classima-core' ),
				'default' => 'Lorem Ipsum has been standard daand scrambled. Rimply dummy text of the printing and typesetting industry',
			),
            array(
                'type'    => Controls_Manager::SLIDER,
                'id'      => 'rating',
                'label'   => __( 'Rating', 'classima-core' ),
                'range' => array(
                    '%' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'default' => array(
                    'unit' => '%',
                    'size' => 50,
                ),
                'condition' => array(
                    'style' => array('2')
                ),
            ),
			array(
				'type'    => Controls_Manager::MEDIA,
				'id'      => 'image',
				'label'   => __( 'Thumbnail', 'classima-core' ),
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
				'id'      => 'name_color',
				'label'   => __( 'Name Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-name' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'designation_color',
				'label'   => __( 'Designation Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-designation' => 'color: {{VALUE}}' ),
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
				'id'       => 'name_typo',
				'label'    => __( 'Name Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-name',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'designation_typo',
				'label'    => __( 'Designation Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-designation',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'content_typo',
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

		if ( $data['style'] == 2) {
            $template = 'view-2';
        } else {
            $template = 'view';
        }

		return $this->rt_template( $template, $data );
	}
}