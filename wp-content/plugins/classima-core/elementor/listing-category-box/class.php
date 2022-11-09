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

class Listing_Category_Box extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ) {
		$this->rt_name      = __( 'Listing Category Box', 'classima-core' );
		$this->rt_base      = 'rt-listing-cat-box';
		$this->rt_translate = [
			'cols' => [
				'12' => __( '1 Col', 'classima-core' ),
				'6'  => __( '2 Col', 'classima-core' ),
				'4'  => __( '3 Col', 'classima-core' ),
				'3'  => __( '4 Col', 'classima-core' ),
				'2'  => __( '6 Col', 'classima-core' ),
			],
		];
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
					'3' => __( 'Style 3', 'classima-core' ),
					'4' => __( 'Style 4', 'classima-core' ),
				],
				'default' => '1',
			],
			[
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'cats',
				'label'       => __( 'Categories', 'classima-core' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'description' => __( 'Start typing category names. If empty then all parent categories will be displayed', 'classima-core' ),
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
				'condition'   => [ 'style' => [ '1' ] ],
			],
			[
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'content_limit',
				'label'       => __( 'Content Limit', 'classima-core' ),
				'default'     => '12',
				'description' => __( 'Number of Words to display', 'classima-core' ),
				'condition'   => [ 'style' => [ '4' ] ],
			],
			[
				'mode' => 'section_end',
			],

			// Responsive Columns
			[
				'mode'  => 'section_start',
				'id'    => 'sec_responsive',
				'label' => __( 'Number of Responsive Columns', 'classima-core' ),
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_xl',
				'label'   => __( 'Desktops: >1199px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_lg',
				'label'   => __( 'Desktops: >991px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_md',
				'label'   => __( 'Tablets: >767px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_sm',
				'label'   => __( 'Phones: >575px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '6',
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_mobile',
				'label'   => __( 'Small Phones: <576px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '12',
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
				'selectors' => [ '{{WRAPPER}} .rtin-item .rtin-title, {{WRAPPER}} .rtin-item .rtin-title a' => 'color: {{VALUE}}' ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'counter_color',
				'label'     => __( 'Counter', 'classima-core' ),
				'selectors' => [ '{{WRAPPER}} .rtin-item .rtin-count' => 'color: {{VALUE}}' ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'content_color',
				'label'     => __( 'Content', 'classima-core' ),
				'selectors' => [ '{{WRAPPER}} .rtin-item .rtin-sub-cats a, {{WRAPPER}} .rtin-item .rtin-content' => 'color: {{VALUE}}' ],
				'condition' => [ 'style' => [ '1', '4' ] ],
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
				'selector' => '{{WRAPPER}} .rtin-item .rtin-title',
			],
			[
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-item .rtin-count',
			],
			[
				'mode'      => 'group',
				'type'      => \Elementor\Group_Control_Typography::get_type(),
				'id'        => 'content_typo',
				'label'     => __( 'Content', 'classima-core' ),
				'selector'  => '{{WRAPPER}} .rtin-item .rtin-sub-cats a, {{WRAPPER}} .rtin-item .rtin-content',
				'condition' => [ 'style' => [ '1', '4' ] ],
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

	private function rt_get_sub_cat( $cat_id, $number ) {

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
			$icon  = get_term_meta( $term->term_id, '_rtcl_icon', true );
			$image = get_term_meta( $term->term_id, '_rtcl_image', true );
			if ( $image ) {
				$image = wp_get_attachment_image_src( $image );
			}
			$count     = $this->rt_term_post_count( $term->term_id );
			$results[] = [
				'name'      => $term->name,
				'count'     => $count,
				'permalink' => Link::get_category_page_link( $term ),
				'icon'      => $icon ? $icon : '',
				'image'     => isset( $image[0] ) ? $image[0] : ''
			];
		}

		return $results;
	}

	public function rt_results( $data ) {

		$results = [];

		$args = [
			'parent'     => 0,
			'include'    => $data['cats'] ? $data['cats'] : [],
			'hide_empty' => $data['hide_empty'] ? true : false,
			'order'      => 'asc'
		];


		if ( $data['orderby'] == 'custom' ) {
			$args['orderby']  = 'meta_value_num';
			$args['order']    = $data['sortby'] ? $data['sortby'] : 'asc';
			$args['meta_key'] = '_rtcl_order';
		} else {
			$args['orderby'] = $data['orderby'] ? $data['orderby'] : 'date';
			$args['order']   = $data['sortby'] ? $data['sortby'] : 'asc';
		}
		$terms = get_terms( 'rtcl_category', $args );

		// Now also shows something on XML import
		if ( $data['cats'] && ! $terms ) {
			$args['include'] = [];
			$terms           = get_terms( 'rtcl_category', $args );
		}

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

			if ( $data['style'] == '2' ) {
				$sub_cats = '';
			} else {
				$sub_cats = $this->rt_get_sub_cat( $term->term_id, $data['num'] );
			}

			$results[] = [
				'name'        => $term->name,
				'description' => $term->description,
				'order'       => (int) $order,
				'permalink'   => Link::get_category_page_link( $term ),
				'count'       => $count,
				'icon_html'   => $icon_html,
				'sub_cats'    => $sub_cats,
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
		} elseif ( $data['style'] == '3' ) {
			$template = 'view-3';
		} elseif ( $data['style'] == '4' ) {
			$template = 'view-4';
		} else {
			$template = 'view-1';
		}

		return $this->rt_template( $template, $data );
	}
}