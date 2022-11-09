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

class Listing_Category_List extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ) {
		$this->rt_name = __( 'Listing Category List', 'classima-core' );
		$this->rt_base = 'rt-listing-cat-list';
		parent::__construct( $data, $args );
	}

	public function rt_fields() {
		$terms             = get_terms( [
			'taxonomy'   => 'rtcl_category',
			'fields'     => 'id=>name',
			'parent'     => 0,
			'hide_empty' => false
		] );
		$category_dropdown = [];

		foreach ( $terms as $id => $name ) {
			$category_dropdown[ $id ] = $name;
		}

		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'sec_general',
				'label' => __( 'General', 'classima-core' ),
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'style',
				'label'   => __( 'Style', 'classima-core' ),
				'options' => [
					'1' => __( 'Style 1', 'classima-core' ),
					'2' => __( 'Style 2', 'classima-core' ),
				],
				'default' => '1',
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'all_cat_btn',
				'label'       => __( 'All Categories Button', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Show all categories button. Default: Off', 'classima-core' ),
				'condition'   => [ 'style' => [ '1' ] ],
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'top_cat',
				'label'       => __( 'Top Categories', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Show top categories. Default: On', 'classima-core' ),
				'condition'   => [ 'style' => [ '1' ] ],
			],
			[
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'top_cats_list',
				'label'       => __( 'Top Categories', 'classima-core' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'description' => __( 'Start typing category names. If empty then all parent categories will be displayed', 'classima-core' ),
				'condition'   => [ 'top_cat' => [ 'yes' ] ],
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'orderby',
				'label'   => __( 'Order By', 'classima-core' ),
				'options' => [
					'term_id' => __( 'ID', 'classima-core' ),
					'date'    => __( 'Date', 'classima-core' ),
					'name'    => __( 'Title', 'classima-core' ),
					'count'   => __( 'Count', 'classima-core' ),
					'custom'  => __( 'Custom Order', 'classima-core' ),
				],
				'default' => 'name',
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'sortby',
				'label'   => __( 'Sort By', 'classima-core' ),
				'options' => [
					'asc'  => __( 'Ascending', 'classima-core' ),
					'desc' => __( 'Descending', 'classima-core' ),
				],
				'default' => 'asc',
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'hide_empty',
				'label'       => __( 'Hide Empty', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Hide Categories that has no listings. Default: On', 'classima-core' ),
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'icon_type',
				'label'   => __( 'Icon Type', 'classima-core' ),
				'options' => [
					'image' => __( 'Image', 'classima-core' ),
					'icon'  => __( 'Icon', 'classima-core' ),
				],
				'default' => 'image',
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'count',
				'label'       => __( 'Listing Counts', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Counts. Default: On', 'classima-core' ),
			],
			[
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'num',
				'label'       => __( 'Numbers of sub-category', 'classima-core' ),
				'min'         => 0,
				'max'         => 100,
				'default'     => 3,
				'description' => __( 'Numbers of sub-category listed. Default: 3', 'classima-core' ),
			],
			[
				'mode' => 'section_end',
			],

			// Style Tab
			[
				'mode'  => 'section_start',
				'id'    => 'sec_style_color',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Color', 'classima-core' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Title', 'classima-core' ),
				'selectors' => [ '{{WRAPPER}} .headerTopCategoriesNav ul li span, {{WRAPPER}} .headerCategoriesMenu > span' => 'color: {{VALUE}}' ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'counter_color',
				'label'     => __( 'List Item', 'classima-core' ),
				'selectors' => [ '{{WRAPPER}} .headerTopCategoriesNav ul li a, {{WRAPPER}} .headerCategoriesMenu__dropdown li a' => 'color: {{VALUE}}' ],
			],
			[
				'mode' => 'section_end',
			],
			[
				'mode'  => 'section_start',
				'id'    => 'sec_style_type',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Typography', 'classima-core' ),
			],
			[
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classima-core' ),
				'selector' => '{{WRAPPER}} .headerTopCategoriesNav ul li span',
			],
			[
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'List Item', 'classima-core' ),
				'selector' => '{{WRAPPER}} .headerTopCategoriesNav ul li a',
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	private function rt_sort_by_order( $a, $b ) {
		return $a['order'] < $b['order'] ? false : true;
	}

	private function rt_term_post_count( $term_id ) {

		$args = [
			'nopaging'            => true,
			'fields'              => 'ids',
			'post_type'           => 'rtcl_listing',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'tax_query'           => [
				[
					'taxonomy' => 'rtcl_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				]
			]
		];

		$posts = get_posts( $args );

		return count( $posts );
	}

	private function rt_get_sub_cat( $cat_id, $number, $data ) {

		$results = [];

		if ( ! $number ) {
			return $results;
		}

		$args = [
			'taxonomy'   => 'rtcl_category',
			'parent'     => $cat_id,
			'number'     => $number,
			'hide_empty' => false,
			'orderby'    => 'count',
			'order'      => 'DESC',
		];

		$terms = get_terms( $args );

		foreach ( $terms as $term ) {
			$count     = $this->rt_term_post_count( $term->term_id );
			$icon_html = '';
			if ( $data['icon_type'] == 'icon' ) {
				$icon = get_term_meta( $term->term_id, '_rtcl_icon', true );
				if ( $icon ) {
					$icon_html = sprintf( '<span class="rtcl-icon rtcl-icon-%s"></span>', $icon );
				}
			} else {
				$image = get_term_meta( $term->term_id, '_rtcl_image', true );
				if ( $image ) {
					$image = wp_get_attachment_image_src( $image );

					$width  = $image[1];
					$height = $image[2];
					$image  = $image[0];

					$icon_html = sprintf( '<img src="%s" alt="%s" width="%s" height="%s" />', $image, $term->name, $width, $height );
				}
			}

			$results[] = [
				'name'      => $term->name,
				'count'     => $count,
				'icon_html' => $icon_html,
				'permalink' => Link::get_category_page_link( $term ),
			];
		}

		return $results;
	}

	public function rt_results( $data ) {

		$results = [];

		$args = [
			'parent'     => 0,
			'hide_empty' => $data['hide_empty'] ? true : false,
			'order'      => 'asc'
		];

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
					$image = wp_get_attachment_image_src( $image );

					$width  = $image[1];
					$height = $image[2];
					$image  = $image[0];

					$icon_html = sprintf( '<img src="%s" alt="%s" width="%s" height="%s" />', $image, $term->name, $width, $height );
				}
			}

			$count = $this->rt_term_post_count( $term->term_id );

			if ( $data['hide_empty'] && $count < 1 ) {
				continue;
			}

			$sub_cats = $this->rt_get_sub_cat( $term->term_id, $data['num'], $data );

			$results[] = [
				'name'      => $term->name,
				'order'     => (int) $order,
				'permalink' => Link::get_category_page_link( $term ),
				'count'     => $count,
				'icon_html' => $icon_html,
				'sub_cats'  => $sub_cats,
			];
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
		$data               = $this->get_settings();
		$data['rt_results'] = $this->rt_results( $data );

		if ( $data['style'] == '2' ) {
			$template = 'view-2';
		} else {
			$template = 'view-1';
		}

		return $this->rt_template( $template, $data );
	}
}