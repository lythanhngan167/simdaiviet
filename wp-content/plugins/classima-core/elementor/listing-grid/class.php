<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use \WP_Query;
use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Grid extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Grid', 'classima-core' );
		$this->rt_base = 'rt-listing-grid';
		$this->rt_translate = array(
			'cols'  => array(
				'12' => __( '1 Col', 'classima-core' ),
				'6'  => __( '2 Col', 'classima-core' ),
				'4'  => __( '3 Col', 'classima-core' ),
				'3'  => __( '4 Col', 'classima-core' ),
				'2'  => __( '6 Col', 'classima-core' ),
			),
		);
		parent::__construct( $data, $args );
	}

	public function rt_fields(){
		$terms  = get_terms( array( 'taxonomy' => 'rtcl_category', 'fields' => 'id=>name' ) );
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
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'style',
				'label'   => __( 'Style', 'classima-core' ),
				'options' => array(
					'1' => __( 'Style 1', 'classima-core' ),
					'2' => __( 'Style 2', 'classima-core' ),
					'3' => __( 'Style 3', 'classima-core' ),
					'4' => __( 'Style 4', 'classima-core' ),
					'5' => __( 'Style 5', 'classima-core' ),
					'6' => __( 'Style 6', 'classima-core' ),
					'7' => __( 'Style 7', 'classima-core' ),
					'8' => __( 'Style 8', 'classima-core' ),
					'9' => __( 'Style 9', 'classima-core' ),
				),
				'default' => '1',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'type',
				'label'   => __( 'Items to Show', 'classima-core' ),
				'options' => array(
					'all'      => __( 'All', 'classima-core' ),
					'featured' => __( 'Featured', 'classima-core' ),
					'new'      => __( 'New', 'classima-core' ),
					'popular'  => __( 'Popular', 'classima-core' ),
					'top'      => __( 'Top', 'classima-core' ),
					'custom'   => __( 'Custom', 'classima-core' ),
				),
				'default' => 'all',
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'cat',
				'label'     => __( 'Categories', 'classima-core' ),
				'options'   => $category_dropdown,
				'default'   => '0',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						)
					)
				),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'cat_display',
				'label'       => __( 'Category Name Display', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'style',
							'operator' => '!==',
							'value' => '5',
						)
					)
				),
			),
            array(
                'type'        => Controls_Manager::SWITCHER,
                'id'          => 'field_display',
                'label'       => __( 'Show Custom Fields', 'classima-core' ),
                'label_on'    => __( 'On', 'classima-core' ),
                'label_off'   => __( 'Off', 'classima-core' ),
                'default'     => 'no',
            ),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'type_display',
				'label'       => __( 'Show Ad Type', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'no',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'date_display',
				'label'       => __( 'Show Date', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'location_display',
				'label'       => __( 'Show Location', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'no',
			),
            array(
                'type'        => Controls_Manager::SWITCHER,
                'id'          => 'views_display',
                'label'       => __( 'Display Views', 'classima-core' ),
                'label_on'    => __( 'On', 'classima-core' ),
                'label_off'   => __( 'Off', 'classima-core' ),
                'default'     => 'no',
                'conditions' => array(
                	'relation'  => 'or',
                    'terms' => array(
                        array(
                            'name' => 'style',
                            'operator' => '==',
                            'value' => '6',
                        ),
	                    array(
		                    'name' => 'style',
		                    'operator' => '==',
		                    'value' => '9',
	                    )
                    )
                ),
            ),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'load_more_btn_display',
				'label'       => __( 'Display Load More Button', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'no',
				'conditions' => array(
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '==',
							'value' => '8',
						],
						[
							'name' => 'style',
							'operator' => '==',
							'value' => '9',
						]
					]
				),
			),
			array(
				'type'       => Controls_Manager::NUMBER,
				'id'         => 'number',
				'label'      => __( 'Number of Items', 'classima-core' ),
				'default'    => '8',
				'description' => __( 'Write -1 to show all', 'classima-core' ),
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						)
					)
				),
			),
			array(
				'type'        => Controls_Manager::TEXT,
				'id'          => 'ids',
				'label'       => __( "Listing ID's, seperated by commas", 'classima-core' ),
				'default'     => '',
				'condition'   => array( 'type' => array( 'custom' ) ),
				'description' => __( "Put the comma seperated ID's here eg. 23,26,89", 'classima-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'random',
				'label'       => __( 'Change items on every page load', 'classima-core' ),
				'label_on'    => __( 'On', 'classima-core' ),
				'label_off'   => __( 'Off', 'classima-core' ),
				'default'     => 'yes',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						)
					)
				),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'orderby',
				'label'     => __( 'Order By', 'classima-core' ),
				'options'   => array(
					'date'   => __( 'Date (Recents comes first)', 'classima-core' ),
					'title'  => __( 'Title', 'classima-core' ),
				),
				'default'   => 'date',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						),
						array(
							'name' => 'random',
							'operator' => '!==',
							'value' => 'yes',
						)
					)
				),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'order',
				'label'     => __( 'Order', 'classima-core' ),
				'options'   => array(
					'asc'   => __( 'Ascending', 'classima-core' ),
					'desc'  => __( 'Descending', 'classima-core' ),
				),
				'default'   => 'desc',
				'conditions' => array(
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						),
						array(
							'name' => 'random',
							'operator' => '!==',
							'value' => 'yes',
						)
					)
				),
			),
			array(
				'mode' => 'section_end',
			),

			// Responsive Columns
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_responsive',
				'label'   => __( 'Number of Responsive Columns', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_xl',
				'label'   => __( 'Desktops: >1199px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_lg',
				'label'   => __( 'Desktops: >991px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_md',
				'label'   => __( 'Tablets: >767px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_sm',
				'label'   => __( 'Phones: >575px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '6',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_mobile',
				'label'   => __( 'Small Phones: <576px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '12',
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
				'selectors' => array( '{{WRAPPER}} .rt-el-listing-grid .listing-grid-each .rtin-item' => 'background-color: {{VALUE}} !important' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'cat_color',
				'label'   => __( 'Category', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-el-listing-grid .listing-grid-each .rtin-item .rtin-content .rtin-cat' => 'color: {{VALUE}} !important' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-el-listing-grid .listing-grid-each .rtin-item .rtin-content .rtin-title a, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-title' => 'color: {{VALUE}} !important' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'meta_color',
				'label'   => __( 'Meta', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-el-listing-grid .listing-grid-each .rtin-item .rtin-content .rtin-meta li, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area .rtin-meta' => 'color: {{VALUE}} !important' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'price_color',
				'label'   => __( 'Price', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rt-el-listing-grid .listing-grid-each-1 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each.listing-grid-each-2 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-3 .rtin-item .rtin-thumb .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-4 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area span.rtcl-price-amount, {{WRAPPER}} .listing-grid-each-8 .rtin-item .rtin-content span.rtcl-price-amount' => 'color: {{VALUE}} !important' ),
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
				'id'       => 'cat_typo',
				'label'    => __( 'Category', 'classima-core' ),
				'selector' => '#content {{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-cat',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classima-core' ),
				'selector' => '#content {{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-title, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'meta_typo',
				'label'    => __( 'Meta', 'classima-core' ),
				'selector' => '#content {{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta li, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area .rtin-meta',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'price_typo',
				'label'    => __( 'Price', 'classima-core' ),
				'selector' => '#content {{WRAPPER}} .listing-grid-each span.rtcl-price-amount, {{WRAPPER}} .listing-grid-each-3 .rtin-item .rtin-thumb .rtcl-price-amount',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	private function rt_build_query( $data ) {

		if ( $data['type'] != 'custom' ) {

			// Get plugin settings
			$settings = get_option( 'rtcl_moderation_settings' );
			$min_view = !empty( $settings['popular_listing_threshold'] ) ? (int) $settings['popular_listing_threshold'] : 500;
			$new_threshold = !empty( $settings['new_listing_threshold'] ) ? (int) $settings['new_listing_threshold'] : 3;

			// Post type
			$args = array(
				'post_type'      => 'rtcl_listing',
				'post_status'    => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page' => $data['number'],
			);

			// Ordering
			if ( $data['random'] ) {
				$args['orderby'] = 'rand';
			} else {
				$args['orderby'] = $data['orderby'];
				$args['order'] = $data['order'];
			}

			// Taxonomy
			if ( !empty( $data['cat'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'rtcl_category',
						'field' => 'term_id',
						'terms' => $data['cat'],
					)
				);
			}

			// Date and Meta Query
			switch ( $data['type'] ) {
				case 'new':
					$args['date_query'] = array(
						array(
							'after' => $new_threshold . ' day ago',
						),
					);
					break;

				case 'featured':
                    $args['meta_key'] = 'featured';
                    $args['meta_value'] = '1';
					break;

				case 'top':
                    $args['meta_key'] = '_top';
                    $args['meta_value'] = '1';
					break;

				case 'popular':
                    $args['meta_key'] = '_views';
                    $args['meta_value'] = $min_view;
                    $args['meta_compare'] = '>=';
					break;
			}
		}

		else {

			$posts = array_map( 'trim' , explode( ',', $data['ids'] ) );

			$args = array(
				'post_type'      => 'rtcl_listing',
				'post_status'    => 'publish',
				'ignore_sticky_posts' => true,
				'nopaging'       => true,
				'post__in'       => $posts,
				'orderby'        => 'post__in',
			);
		}
		return new WP_Query( $args );
	}

	protected function render() {
		$data = $this->get_settings();

		$data['query'] = $this->rt_build_query( $data );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}