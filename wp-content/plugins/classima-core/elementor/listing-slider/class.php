<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Listing_Slider extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ) {
		$this->rt_name      = __( 'Listing Slider', 'classima-core' );
		$this->rt_base      = 'rt-listing-slider';
		$this->rt_translate = array(
			'cols' => array(
				'1' => __( '1 Col', 'classima-core' ),
				'2' => __( '2 Col', 'classima-core' ),
				'3' => __( '3 Col', 'classima-core' ),
				'4' => __( '4 Col', 'classima-core' ),
				'5' => __( '5 Col', 'classima-core' ),
				'6' => __( '6 Col', 'classima-core' ),
			),
		);
		parent::__construct( $data, $args );
	}

	public function rt_fields() {
		$terms             = get_terms( array( 'taxonomy' => 'rtcl_category', 'fields' => 'id=>name' ) );
		$category_dropdown = array( '0' => __( 'All Categories', 'classima-core' ) );

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
				'id'      => 'style',
				'label'   => __( 'Style', 'classima-core' ),
				'options' => array(
					'1' => __( 'Style 1', 'classima-core' ),
					'2' => __( 'Style 2', 'classima-core' ),
					'3' => __( 'Style 3', 'classima-core' ),
					'4' => __( 'Style 4', 'classima-core' ),
					'5' => __( 'Style 5', 'classima-core' ),
					'6' => __( 'Style 6', 'classima-core' ),
				),
				'default' => '1',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'sec_title',
				'label'   => __( 'Section Title', 'classima-core' ),
				'default' => 'Lorem Ipsum',
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
				'default' => 'featured',
			),
			array(
				'type'       => Controls_Manager::SELECT2,
				'id'         => 'cat',
				'label'      => __( 'Categories', 'classima-core' ),
				'options'    => $category_dropdown,
				'default'    => '0',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!==',
							'value'    => 'custom',
						)
					)
				),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'cat_display',
				'label'     => __( 'Category Name Display', 'classima-core' ),
				'label_on'  => __( 'On', 'classima-core' ),
				'label_off' => __( 'Off', 'classima-core' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'field_display',
				'label'     => __( 'Show Custom Fields', 'classima-core' ),
				'label_on'  => __( 'On', 'classima-core' ),
				'label_off' => __( 'Off', 'classima-core' ),
				'default'   => 'no',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'type_display',
				'label'     => __( 'Show Ad Type', 'classima-core' ),
				'label_on'  => __( 'On', 'classima-core' ),
				'label_off' => __( 'Off', 'classima-core' ),
				'default'   => 'no',
			),
			array(
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'views_display',
				'label'      => __( 'Display Views', 'classima-core' ),
				'label_on'   => __( 'On', 'classima-core' ),
				'label_off'  => __( 'Off', 'classima-core' ),
				'default'    => 'no',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'style',
							'operator' => '==',
							'value'    => '6',
						)
					)
				),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'number',
				'label'       => __( 'Total Number of Items', 'classima-core' ),
				'default'     => '5',
				'description' => __( 'Write -1 to show all', 'classima-core' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!==',
							'value'    => 'custom',
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
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'random',
				'label'      => __( 'Change items on every page load', 'classima-core' ),
				'label_on'   => __( 'On', 'classima-core' ),
				'label_off'  => __( 'Off', 'classima-core' ),
				'default'    => 'yes',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!==',
							'value'    => 'custom',
						)
					)
				),
			),
			array(
				'type'       => Controls_Manager::SELECT2,
				'id'         => 'orderby',
				'label'      => __( 'Order By', 'classima-core' ),
				'options'    => array(
					'date'  => __( 'Date (Recents comes first', 'classima-core' ),
					'title' => __( 'Title', 'classima-core' ),
				),
				'default'    => 'date',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'type',
							'operator' => '!==',
							'value'    => 'custom',
						),
						array(
							'name'     => 'random',
							'operator' => '!==',
							'value'    => 'yes',
						)
					)
				),
			),
			array(
				'mode' => 'section_end',
			),

			// Responsive Columns
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_responsive',
				'label' => __( 'Number of Responsive Columns', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_lg',
				'label'   => __( 'Desktops: > 1199px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_md',
				'label'   => __( 'Desktops: > 991px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_sm',
				'label'   => __( 'Tablets: > 767px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_xs',
				'label'   => __( 'Phones: < 768px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '2',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_mobile',
				'label'   => __( 'Small Phones: < 480px', 'classima-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '1',
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
				'id'        => 'bgcolor',
				'label'     => __( 'Background', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'cat_color',
				'label'     => __( 'Category', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-cat' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-title a, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'meta_color',
				'label'     => __( 'Meta', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta li, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area .rtin-meta' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'price_color',
				'label'     => __( 'Price', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each-1 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each.listing-grid-each-2 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-3 .rtin-item .rtin-thumb .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-4 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area span.rtcl-price-amount' => 'color: {{VALUE}}' ),
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
				'id'       => 'cat_typo',
				'label'    => __( 'Category', 'classima-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-cat',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classima-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-title, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'meta_typo',
				'label'    => __( 'Meta', 'classima-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta li, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area .rtin-meta',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'price_typo',
				'label'    => __( 'Price', 'classima-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each span.rtcl-price-amount, {{WRAPPER}} .listing-grid-each-3 .rtin-item .rtin-thumb .rtcl-price-amount',
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
			$settings      = get_option( 'rtcl_moderation_settings' );
			$min_view      = ! empty( $settings['popular_listing_threshold'] ) ? (int) $settings['popular_listing_threshold'] : 500;
			$new_threshold = ! empty( $settings['new_listing_threshold'] ) ? (int) $settings['new_listing_threshold'] : 3;

			// Post type
			$args = array(
				'post_type'           => 'rtcl_listing',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page'      => $data['number'],
			);

			// Ordering
			if ( $data['random'] ) {
				$args['orderby'] = 'rand';
			} else {
				$args['orderby'] = $data['orderby'];
				if ( $data['orderby'] == 'title' ) {
					$args['order'] = 'ASC';
				}
			}

			// Taxonomy
			if ( ! empty( $data['cat'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'rtcl_category',
						'field'    => 'term_id',
						'terms'    => $data['cat'],
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
					$args['meta_key']   = 'featured';
					$args['meta_value'] = '1';
					break;

				case 'top':
					$args['meta_key']   = '_top';
					$args['meta_value'] = '1';
					break;

				case 'popular':
					$args['meta_key']     = '_views';
					$args['meta_value']   = $min_view;
					$args['meta_compare'] = '>=';
					break;
			}
		} else {

			$posts = array_map( 'trim', explode( ',', $data['ids'] ) );

			$args = array(
				'post_type'           => 'rtcl_listing',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'nopaging'            => true,
				'post__in'            => $posts,
				'orderby'             => 'post__in',
			);
		}

		return new WP_Query( $args );
	}

	protected function render() {
		$data = $this->get_settings();

		$cssstyle = '--margin-right: 20px;';

		$rand = substr( md5( mt_rand() ), 0, 7 );

		if ( ! empty( $data['col_lg'] ) ) {
			$width    = 100 / $data['col_lg'];
			$cssstyle .= "--lg-width: calc( {$width}% - 20px );";
		}

		if ( ! empty( $data['col_md'] ) ) {
			$width    = 100 / $data['col_md'];
			$cssstyle .= "--md-width: calc( {$width}% - 20px );";
		}

		if ( ! empty( $data['col_sm'] ) ) {
			$width    = 100 / $data['col_sm'];
			$cssstyle .= "--sm-width: calc( {$width}% - 20px );";
		}

		if ( ! empty( $data['col_xs'] ) ) {
			$width    = 100 / $data['col_xs'];
			$cssstyle .= "--xs-width: calc( {$width}% - 20px );";
		}

		if ( ! empty( $data['col_mobile'] ) ) {
			$width    = 100 / $data['col_mobile'];
			$cssstyle .= "--mobile-width: calc( {$width}% - 20px );";
		}

		$owl_data = [
			"navigation"   => [
				"nextEl" => ".rtin-custom-nav-$rand .owl-next",
				"prevEl" => ".rtin-custom-nav-$rand .owl-prev",
			],
			"loop"         => $data['slider_loop'] == 'yes',
			"speed"        => isset( $data['slider_autoplay_speed'] ) ? $data['slider_autoplay_speed'] : 200,
			"spaceBetween" => 20,
			"breakpoints"  => [
				0    => [
					"slidesPerView" => $data['col_mobile']
				],
				480  => [
					"slidesPerView" => $data['col_xs']
				],
				768  => [
					"slidesPerView" => $data['col_sm']
				],
				992  => [
					"slidesPerView" => $data['col_md']
				],
				1200 => [
					"slidesPerView" => $data['col_lg']
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

		$data['owl_data']  = json_encode( $owl_data );
		$data['rand']      = $rand;
		$data['css_style'] = $cssstyle;

		$data['query'] = $this->rt_build_query( $data );

		switch ( $data['style'] ) {
			case 'style2':
				$data['view'] = 'template-2';
				break;
			case 'style3':
				$data['view'] = 'template-3';
				break;
			default:
				$data['view'] = 'template-1';
		}

		$template = 'view';

		$this->rt_template( $template, $data );

		$this->edit_mode_script();
	}
}