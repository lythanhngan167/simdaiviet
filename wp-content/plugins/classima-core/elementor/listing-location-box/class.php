<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Location_Box extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Location Box', 'classima-core' );
		$this->rt_base = 'rt-listing-location-box';
		parent::__construct( $data, $args );
	}

	public function rt_fields(){

		$terms  = get_terms( array( 'taxonomy' => 'rtcl_location', 'fields' => 'id=>name', 'hide_empty' => false ) );
		$location_dropdown = array();

		foreach ( $terms as $id => $name ) {
			$location_dropdown[$id] = $name;
		}

		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'location',
				'label'   => __( 'Location', 'classima-core' ),
				'options' => $location_dropdown,
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'display_count',
				'label'       => __( 'Show Listing Counts', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'enable_link',
				'label'       => __( 'Enable Link', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
			),
			array(
				'type' => Controls_Manager::SLIDER,
				'mode' => 'responsive',
				'id'   => 'width',
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'label'   => __( 'Max Width', 'classima-core' ),
				'selectors' => array(
					'{{WRAPPER}} .rt-el-listing-location-box' => 'max-width: {{SIZE}}{{UNIT}};',
				)
			),
			array(
				'type' => Controls_Manager::SLIDER,
				'mode' => 'responsive',
				'id'   => 'height',
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 290,
				),
				'label'   => __( 'Box Height', 'classima-core' ),
				'selectors' => array(
					'{{WRAPPER}} .rt-el-listing-location-box' => 'height: {{SIZE}}{{UNIT}};',
				)
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_background',
				'label'   => __( 'Background', 'classima-core' ),
			),
			array(
				'type'    => \Elementor\Group_Control_Background::get_type(),
				'mode'    => 'group',
				'types' => [ 'classic', 'gradient', 'video' ],
				'id'      => 'bgimg',
				'label'   => __( 'Background', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rt-el-listing-location-box .rtin-img',
			),
			array(
				'mode' => 'section_end',
			),
			
			// Style Tab
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style_color',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Style', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_hover_color',
				'label'   => __( 'Title Hover', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-el-listing-location-box:hover .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'counter_color',
				'label'   => __( 'Counter', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-counter' => 'color: {{VALUE}}' ),
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
				'selector' => '{{WRAPPER}} .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-counter',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	private function rt_term_post_count( $term_id ){

		$args = array(
			'nopaging'            => true,
			'fields'              => 'ids',
			'post_type'           => 'rtcl_listing',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'tax_query' => array(
				array(
					'taxonomy' => 'rtcl_location',
					'field'    => 'term_id',
					'terms'    => $term_id,
				)
			)
		);

		$posts = get_posts( $args );
		return count( $posts );
	}

	protected function render() {
		$data = $this->get_settings();

		$term = get_term( $data['location'], 'rtcl_location' );

		if ( $term && !is_wp_error( $term ) ) {
			$data['title']     = $term->name;
			$data['count']     = $this->rt_term_post_count( $term->term_id );
			$data['permalink'] = Link::get_location_page_link( $term );
		}
		else {
			$data['title'] = __( 'Please Select a Location and Background', 'classima-core' );
			$data['count'] = 0;
			$data['display_count'] = $data['enable_link'] = false;
		}

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}