<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Post extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Post', 'classima-core' );
		$this->rt_base = 'rt-post';
		parent::__construct( $data, $args );
	}

	public function rt_fields(){
		$categories = get_categories();
		$category_dropdown = array( '0' => __( 'All Categories', 'classima-core' ) );

		foreach ( $categories as $category ) {
			$category_dropdown[$category->term_id] = $category->name;
		}

		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classima-core' ),
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
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'cat',
				'label'   => __( 'Categories', 'classima-core' ),
				'options' => $category_dropdown,
				'default' => '0',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'orderby',
				'label'   => __( 'Order By', 'classima-core' ),
				'options' => array(
					'date'  => __( 'Date (Recents comes first)', 'classima-core' ),
					'title' => __( 'Title', 'classima-core' ),
				),
				'default' => 'date',
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'count',
				'label'   => __( 'Content Limit', 'classima-core' ),
				'default' => 25,
				'description' => __( 'Maximum number of words to display', 'classima-core' ),
				'condition'   => array( 'style' => array( '1' ) ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'author',
				'label'       => __( 'Author Display', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Show or hide author name', 'classima-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'btn',
				'label'       => __( 'Button Display', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Show or hide view-all button', 'classima-core' ),
                'condition'   => array( 'style' => array( '1', '2' ) ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'btntext',
				'label'   => __( 'Button Text', 'classima-core' ),
				'default' => __( 'VIEW ALL', 'classima-core' ),
				'condition'   => array( 'btn' => array( 'yes' ) ),
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
				'id'      => 'bgcolor',
				'label'   => __( 'Background', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-each' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .post-title a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'meta_color',
				'label'   => __( 'Meta', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .post-meta li, {{WRAPPER}} .post-meta li a, {{WRAPPER}} .post-date' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'author_color',
				'label'   => __( 'Author', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .post-meta .author-name a' => 'color: {{VALUE}}' ),
				'condition' => array( 'style' => array( '1' ) ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'content_color',
				'label'   => __( 'Content', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .post-content' => 'color: {{VALUE}}' ),
				'condition' => array( 'style' => array( '1' ) ),
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
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classima-core' ),
				'selector' => '{{WRAPPER}} .post-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'meta_typo',
				'label'    => __( 'Meta', 'classima-core' ),
				'selector' => '{{WRAPPER}} .post-meta li, {{WRAPPER}} .post-meta li a, {{WRAPPER}} .post-date',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'content_typo',
				'label'    => __( 'Content', 'classima-core' ),
				'selector' => '{{WRAPPER}} .post-content',
				'condition' => array( 'style' => array( '1' ) ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'author_typo',
				'label'    => __( 'Author', 'classima-core' ),
				'selector' => '{{WRAPPER}} .post-meta .author-name a',
				'condition' => array( 'style' => array( '1' ) ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'btn_typo',
				'label'    => __( 'Button', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-view a',
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
		} elseif ( $data['style'] == '3' ) {
            $template = 'view-3';
        }
		else {
			$template = 'view-1';
		}

		return $this->rt_template( $template, $data );
	}
}