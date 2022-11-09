<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pricing_Box extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ) {
		$this->rt_name = __( 'Pricing Box', 'classima-core' );
		$this->rt_base = 'rt-pricing-box';
		parent::__construct( $data, $args );
	}

	public function rt_fields() {
		$args           = array(
			'post_type'           => 'rtcl_pricing',
			'posts_per_page'      => - 1,
			'suppress_filters'    => false,
			'ignore_sticky_posts' => 1,
			'orderby'             => 'title',
			'order'               => 'ASC',
			'post_status'         => 'publish',
			'meta_query'          => [
				[
					'key'   => 'pricing_type',
					'value' => 'membership',
				]
			],
		);
		$posts          = get_posts( $args );
		$posts_dropdown = array( '0' => __( '--Select--', 'classima-core' ) );
		foreach ( $posts as $post ) {
			$posts_dropdown[ $post->ID ] = $post->post_title;
		}

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_general',
				'label' => __( 'General', 'classima-core' ),
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
			),
			array(
				'type'        => Controls_Manager::TEXT,
				'id'          => 'currency',
				'label'       => __( 'Currency Symbol', 'classima-core' ),
				'default'     => '$',
				'description' => __( 'Currency sign eg. $', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'price',
				'label'   => __( 'Price', 'classima-core' ),
				'default' => '0',
			),
			array(
				'type'    => Controls_Manager::SWITCHER,
				'id'      => 'per_sign',
				'label'   => __( 'Show per sign', 'classima-core' ),
				'default' => 'yes',
			),
			array(
				'type'        => Controls_Manager::TEXT,
				'id'          => 'unit',
				'label'       => __( 'Unit Name', 'classima-core' ),
				'default'     => 'mo',
				'description' => __( "eg. month or year. Keep empty if you don't want to show unit", 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'title',
				'label'   => __( 'Title', 'classima-core' ),
				'default' => 'LOREM IPSUM',
			),
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'id'          => 'features',
				'label'       => __( 'Features', 'classima-core' ),
				'default'     => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor',
				'description' => __( 'One line per feature eg.<br/>10 Ads per month<br/>Featured on first week', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'btntext',
				'label'   => __( 'Button Text', 'classima-core' ),
				'default' => 'Lorem Ipsum',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'btntype',
				'label'   => __( 'Button Link Type', 'classima-core' ),
				'options' => array(
					'page'   => __( 'Pricing Page Link', 'classima-core' ),
					'custom' => __( 'Custom Link', 'classima-core' ),
				),
				'default' => 'custom',
			),
			array(
				'type'        => Controls_Manager::URL,
				'id'          => 'buttonurl',
				'label'       => __( 'Button URL', 'classima-core' ),
				'placeholder' => 'https://your-link.com',
				'condition'   => array( 'btntype' => array( 'custom' ) ),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'page',
				'label'     => __( 'Select Pricing', 'classima-core' ),
				'options'   => $posts_dropdown,
				'default'   => '0',
				'condition' => array( 'btntype' => array( 'page' ) ),
			),
			array(
				'mode' => 'section_end',
			),

			// Style Tab
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_color',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Color', 'classima-core' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'bgcolor',
				'label'     => __( 'Background', 'classima-core' ),
				'default'   => '#f6f6f6',
				'selectors' => array( '{{WRAPPER}} .rt-el-pricing-box, {{WRAPPER}} .rt-el-pricing-box-2' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'price_color',
				'label'     => __( 'Price', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-price .rtin-currency, {{WRAPPER}} .rtin-price .rtin-number, {{WRAPPER}} .rtin-price .rtin-duration' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'features_color',
				'label'     => __( 'Features', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-features' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'btn_color',
				'label'     => __( 'Button', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-button a' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'btn_hover_color',
				'label'     => __( 'Button Hover', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-button a:hover' => 'background-color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_typo',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Typography', 'classima-core' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'price_typo',
				'label'    => __( 'Price', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-price .rtin-currency, {{WRAPPER}} .rtin-price .rtin-number, {{WRAPPER}} .rtin-price .rtin-duration',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'features_typo',
				'label'    => __( 'Features', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-features, {{WRAPPER}} .rtin-features li',
			),
			array(
				'type'     => Controls_Manager::COLOR,
				'id'       => 'btn_typo',
				'label'    => __( 'Button', 'classima-core' ),
				'selector' => '{{WRAPPER}} rtin-button a',
			),
			array(
				'mode' => 'section_end',
			),
		);

		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();

		if ( $data['style'] == '2' ) {
			$template = 'view-2';
		} else if ( $data['style'] == '3' ) {
			$template = 'view-3';
		} else {
			$template = 'view-1';
		}

		return $this->rt_template( $template, $data );
	}
}