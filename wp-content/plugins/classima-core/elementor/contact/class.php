<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Contact extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Contact', 'classima-core' );
		$this->rt_base = 'rt-contact';
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
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'address',
				'label'   => __( 'Address', 'classima-core' ),
				'default' => 'Lorem Ipsum text of the printing and typesetting industry',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'phone',
				'label'   => __( 'Phone', 'classima-core' ),
				'default' => '+0000000000',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'fax',
				'label'   => __( 'Fax', 'classima-core' ),
				'default' => '+0000000000',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'email',
				'label'   => __( 'Email', 'classima-core' ),
				'default' => 'info@example.com',
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
				'id'      => 'icon_color',
				'label'   => __( 'Icon', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} ul li i' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'text_color',
				'label'   => __( 'Text/Link', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} ul li, {{WRAPPER}} ul li a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'text_hover_color',
				'label'   => __( 'Link Hover', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} ul li a:hover' => 'color: {{VALUE}}' ),
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
				'selector' => '{{WRAPPER}} ul li',
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