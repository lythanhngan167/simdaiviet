<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Title_Animated extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Section Title(Animated)', 'classima-core' );
		$this->rt_base = 'rt-title-animated';
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
				'type'    => Controls_Manager::REPEATER,
				'id'      => 'items',
				'label'   => __( 'Titles', 'classima-core' ),
				'fields'  => array(
					array(
						'type'  => Controls_Manager::TEXT,
						'name'  => 'title',
						'label' => __( 'Title', 'classima-core' ),
						'default' => 'Lorem Ipsum dolor amet',
					),
				),
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'subtitle',
				'label'   => __( 'Subtitle', 'classima-core' ),
				'default' => 'Lorem Ipsum has been standard daand scrambled. Rimply dummy text of the printing and typesetting industry',
			),
			array(
				'mode' => 'section_end',
			),

			// Alignment
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_align',
				'label'   => __( 'Alignment', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::CHOOSE,
				'id'      => 'align',
				'label'   => __( 'Alignment', 'metro-core' ),
				'options' => $this->rt_alignment_options(),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .rt-el-title-animated' => 'text-align: {{VALUE}};',
				),
			),
			array(
				'mode' => 'section_end',
			),

			// Animation
			array(
				'mode'        => 'section_start',
				'id'          => 'sec_typejs',
				'label'       => __( 'Animation Options', 'classima-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'typejs_cursor',
				'label'       => __( 'Show Cursor', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => '',
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'typejs_speed',
				'label'   => __( 'Speed', 'classima-core' ),
				'default' => 80,
				'description' => __( 'Speed in milliseconds', 'classima-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'typejs_loop',
				'label'       => __( 'Loop', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
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
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function rt_get_titles( $data ) {
		$result = array();
		foreach ( $data['items'] as $item ) {
			$title = trim( $item['title'] );
			if ( $title ) {
				$result[] = $title;
			}
		}

		return $result;
	}

	protected function render() {
		$data = $this->get_settings();

		$options = array(
			'strings'     => $this->rt_get_titles( $data ),
			'typeSpeed'   => $data['typejs_speed'] ? $data['typejs_speed'] : 30,
			'loop'        => $data['typejs_loop'] == 'yes' ? true : false,
			'showCursor'  => $data['typejs_cursor'] == 'yes' ? true : false,
			'contentType' => null,
		);

		$data['options'] = json_encode( $options );

		wp_enqueue_script( 'typed' );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}