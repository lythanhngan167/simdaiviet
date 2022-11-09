<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Listing_Category_Slider extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ) {
		$this->rt_name = __( 'Listing Category Slider', 'classima-core' );
		$this->rt_base = 'rt-listing-cat-slider';
		parent::__construct( $data, $args );
	}

	public function rt_fields() {
		$terms             = get_terms( array(
			'taxonomy'   => 'rtcl_category',
			'fields'     => 'id=>name',
			'parent'     => 0,
			'hide_empty' => false
		) );
		$category_dropdown = array();

		foreach ( $terms as $id => $name ) {
			$category_dropdown[ $id ] = $name;
		}

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_general',
				'label' => __( 'General', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'theme',
				'label'   => __( 'Theme', 'classima-core' ),
				'options' => array(
					'light' => __( 'Light Background', 'classima-core' ),
					'dark'  => __( 'Dark Background', 'classima-core' ),
				),
				'default' => 'light',
			),
			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'cats',
				'label'       => __( 'Categories', 'classima-core' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'description' => __( 'Start typing category names. If empty then all parent categories will be displayed', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'orderby',
				'label'   => __( 'Order By', 'classima-core' ),
				'options' => array(
					'term_id' => __( 'ID', 'classima-core' ),
					'date'    => __( 'Date', 'classima-core' ),
					'name'    => __( 'Title', 'classima-core' ),
					'count'   => __( 'Count', 'classima-core' ),
					'custom'  => __( 'Custom Order', 'classima-core' ),
				),
				'default' => 'name',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'sortby',
				'label'   => __( 'Sort By', 'classima-core' ),
				'options' => array(
					'asc'  => __( 'Ascending', 'classima-core' ),
					'desc' => __( 'Descending', 'classima-core' ),
				),
				'default' => 'asc',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'hide_empty',
				'label'       => __( 'Hide Empty', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Hide Categories that has no listings. Default: On', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'icon_type',
				'label'   => __( 'Icon Type', 'classima-core' ),
				'options' => array(
					'image' => __( 'Image', 'classima-core' ),
					'icon'  => __( 'Icon', 'classima-core' ),
				),
				'default' => 'image',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'count',
				'label'       => __( 'Listing Counts', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Counts. Default: On', 'classima-core' ),
			),
			array(
				'mode' => 'section_end',
			),

			// Slider options
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_slider',
				'label' => __( 'Slider Options', 'classima-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_autoplay',
				'label'       => __( 'Autoplay', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Enable or disable autoplay. Default: On', 'classima-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_stop_on_hover',
				'label'       => __( 'Stop on Hover', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Stop autoplay on mouse hover. Default: On', 'classima-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'slider_interval',
				'label'       => __( 'Autoplay Interval', 'classima-core' ),
				'options'     => array(
					'5000' => __( '5 Seconds', 'classima-core' ),
					'4000' => __( '4 Seconds', 'classima-core' ),
					'3000' => __( '3 Seconds', 'classima-core' ),
					'2000' => __( '2 Seconds', 'classima-core' ),
					'1000' => __( '1 Second', 'classima-core' ),
				),
				'default'     => '5000',
				'description' => __( 'Set any value for example 5 seconds to play it in every 5 seconds. Default: 5 Seconds', 'classima-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'slider_autoplay_speed',
				'label'       => __( 'Autoplay Slide Speed', 'classima-core' ),
				'default'     => 200,
				'description' => __( 'Slide speed in milliseconds. Default: 200', 'classima-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_loop',
				'label'       => __( 'Loop', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Loop to first item. Default: On', 'classima-core' ),
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
				'id'        => 'title_color',
				'label'     => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-item .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'counter_color',
				'label'     => __( 'Counter', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-item .rtin-count' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_type',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Typography', 'classima-core' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-item .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-item .rtin-count',
			),
			array(
				'mode' => 'section_end',
			),
		);

		return $fields;
	}

	public function rt_sort_by_order( $a, $b ) {
		return $a['order'] < $b['order'] ? false : true;
	}

	private function rt_term_post_count( $term_id ) {

		$args = array(
			'nopaging'            => true,
			'fields'              => 'ids',
			'post_type'           => 'rtcl_listing',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'tax_query'           => array(
				array(
					'taxonomy' => 'rtcl_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				)
			)
		);

		$posts = get_posts( $args );

		return count( $posts );
	}

	public function rt_results( $data ) {

		$results = array();

		$args = array(
			'parent'     => 0,
			'include'    => $data['cats'] ? $data['cats'] : array(),
			'hide_empty' => $data['hide_empty'] ? true : false,
			'order'      => 'asc'
		);

		if ( $data['orderby'] == 'custom' ) {
			$args['orderby']  = 'meta_value_num';
			$args['order']    = $data['sortby'] ? $data['sortby'] : 'asc';
			$args['meta_key'] = '_rtcl_order';
		} else {
			$args['orderby'] = $data['orderby'] ? $data['orderby'] : 'name';
			$args['order']   = $data['sortby'] ? $data['sortby'] : 'asc';
		}
		$terms = get_terms( 'rtcl_category', $args );

		foreach ( $terms as $term ) {

			$order = get_term_meta( $term->term_id, '_rtcl_order', true );

			$icon_html = '';

			if ( $data['icon_type'] == 'icon' ) {
				$icon = get_term_meta( $term->term_id, '_rtcl_icon', true );
				if ( $icon ) {
					$icon_html = sprintf( '<span class="rtcl-icon rtcl-icon-%s"></span>', $icon );
				}
			} else {
				$image = get_term_meta( $term->term_id, '_rtcl_image', true );
				if ( $image ) {
					$icon_html = \radiustheme\Lib\WP_SVG::get_attachment_image( $image, 'full', true );
				}
			}

			$count = $this->rt_term_post_count( $term->term_id );


			if ( $data['hide_empty'] && $count < 1 ) {
				continue;
			}

			$results[] = array(
				'name'      => $term->name,
				'order'     => (int) $order,
				'permalink' => Link::get_category_page_link( $term ),
				'count'     => $count,
				'icon_html' => $icon_html,
			);
			if ( 'count' == $args['orderby'] ) {
				if ( 'desc' == $args['order'] ) {
					usort( $results, function ( $a, $b ) {
						return $b['count'] - $a['count'];
					} );
				}
				if ( 'asc' == $args['order'] ) {
					usort( $results, function ( $a, $b ) {
						return $a['count'] - $b['count'];
					} );
				}
			}
		}

		return $results;
	}

	protected function render() {
		$data = $this->get_settings();

		$data['rt_results'] = $this->rt_results( $data );
		$count              = count( $data['rt_results'] );

		$owl_data = [
			"navigation"   => [
				"nextEl" => ".rt-el-listing-cat-slider .swiper-button-next",
				"prevEl" => ".rt-el-listing-cat-slider .swiper-button-prev",
			],
			"loop"         => $data['slider_loop'] == 'yes',
			"speed"        => isset( $data['slider_autoplay_speed'] ) ? $data['slider_autoplay_speed'] : 200,
			"spaceBetween" => 10,
			"breakpoints"  => [
				0    => [
					"slidesPerView" => min( $count, 1 )
				],
				480  => [
					"slidesPerView" => min( $count, 2 )
				],
				600  => [
					"slidesPerView" => min( $count, 3 )
				],
				800  => [
					"slidesPerView" => min( $count, 4 )
				],
				992  => [
					"slidesPerView" => min( $count, 5 )
				],
				1200 => [
					"slidesPerView" => min( $count, 6 )
				]
			]
		];

		if ( $data['slider_autoplay'] ) {
			$owl_data["autoplay"] = [
				"delay"                => $data['slider_interval'],
				"disableOnInteraction" => false,
				"pauseOnMouseEnter"    => $data['slider_stop_on_hover']
			];
		}

		$data['owl_data'] = json_encode( $owl_data );

		$template = 'view';

		$this->rt_template( $template, $data );

		$this->edit_mode_script();

	}
}