<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Store_List extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Store List', 'classima-core' );
		$this->rt_base = 'rt-listing-store-list';
		parent::__construct( $data, $args );
	}

	public function rt_fields(){

		$terms  = get_terms( array( 'taxonomy' => 'store_category', 'parent'   => 0, 'hide_empty' => false, 'fields' => 'id=>name' ) );
		$category_dropdown = array( '0' => __( 'All Categories', 'classima-core' ) );

		foreach ( $terms as $id => $name ) {
			$category_dropdown[$id] = $name;
		}

		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classima-core' ),
			),
			array(
				'type'       => Controls_Manager::NUMBER,
				'id'         => 'number',
				'label'      => __( 'Number of Items', 'classima-core' ),
				'default'    => '4',
				'description' => __( 'Write -1 to show all', 'classima-core' ),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'cat',
				'label'     => __( 'Categories', 'classima-core' ),
				'options'   => $category_dropdown,
				'default'   => '0',
				'multiple'  => true,
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
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'order',
				'label'   => __( 'Sort By', 'classima-core' ),
				'options' => array(
					'asc'  => __( 'Ascending', 'classima-core' ),
					'desc' => __( 'Descending', 'classima-core' ),
				),
				'default' => 'desc',
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
				'selectors' => array( '{{WRAPPER}} .rtin-title a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_h_color',
				'label'   => __( 'Title Hover', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-title a:hover' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'time_color',
				'label'   => __( 'Time', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-time' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'counter_color',
				'label'   => __( 'Counter', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-count' => 'color: {{VALUE}}' ),
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
				'id'       => 'time_typo',
				'label'    => __( 'Time', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-time',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-count',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	private function rt_store_query( $data ) {
		$result = array();

		$args = array(
			'post_type'           => 'store',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $data['number'],
		);

		// Taxonomy
		if ( !empty( $data['cat'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'store_category',
					'field' => 'term_id',
					'terms' => $data['cat'],
				)
			);
		}

		$args['orderby']    = $data['orderby'];
		$args['order']      = $data['order'];

		$items = get_posts( $args );

		foreach ( $items as $item ) {

			$store = new \RtclStore\Models\Store( $item->ID );

			$result[] = array(
				'logo'      => $store->get_the_logo(),
				'title'     => $store->get_the_title(),
				'permalink' => $store->get_the_permalink(),
				'count'     => $store->get_ad_count(),
				'time'      => get_the_time( 'Y', $item->ID ),
				'time2'     => get_the_time( get_option( 'date_format' ), $item->ID ),
				'slogan'    => get_post_meta( $item->ID, 'slogan', true ),
				'address'   => get_post_meta( $item->ID, 'address', true ),
			);
		}

		return $result;
	}

	protected function render() {
		$data = $this->get_settings();

		$data['stores'] = $this->rt_store_query( $data );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}