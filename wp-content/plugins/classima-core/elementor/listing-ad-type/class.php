<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use Rtcl\Helpers\Functions;
use \WP_Query;
use function Sodium\compare;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Ad_Type extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Type Tab', 'classima-core' );
		$this->rt_base = 'rt-listing-ad-type';
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

	private function rt_load_scripts(){
		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'isotope' );
	}

	public function rt_fields(){
		$terms  = get_terms( array( 'taxonomy' => 'rtcl_category', 'fields' => 'id=>name' ) );
		$category_dropdown = array();

        $type_dropdown = Functions::get_listing_types();

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
				),
				'default' => '1',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'type',
				'label'   => __( 'Select Listing Type', 'classima-core' ),
				'options' => $type_dropdown,
				'multiple' => true,
				'default' => array( 'sell', 'buy')
			),
            array(
                'type'      => Controls_Manager::SELECT2,
                'id'        => 'cat',
                'label'     => __( 'Categories', 'classima-core' ),
                'options'   => $category_dropdown,
                'multiple'  => true,
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
				'type'       => Controls_Manager::NUMBER,
				'id'         => 'number',
				'label'      => __( 'Number of Items per Type', 'classima-core' ),
				'default'    => '8',
				'description' => __( 'Write -1 to show all', 'classima-core' ),
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
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'orderby',
				'label'     => __( 'Order By', 'classima-core' ),
				'options'   => array(
					'date'   => __( 'Date (Recents comes first', 'classima-core' ),
					'title'  => __( 'Title', 'classima-core' ),
				),
				'default'   => 'date',
				'condition' => array( 'random' => array( '' ) ),
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
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'cat_color',
				'label'   => __( 'Category', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-cat' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-title a, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'meta_color',
				'label'   => __( 'Meta', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta li, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area .rtin-meta' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'price_color',
				'label'   => __( 'Price', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each-1 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each.listing-grid-each-2 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-3 .rtin-item .rtin-thumb .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-4 .rtin-item .rtin-content .rtin-price .rtcl-price-amount, {{WRAPPER}} .listing-grid-each-5 .rtin-item .rtin-content .rtin-meta-area span.rtcl-price-amount' => 'color: {{VALUE}}' ),
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

	private function rt_isotope_query( $data ) {

		$result = array();

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
		}
		else {
			$args['orderby'] = $data['orderby'];
			if ( $data['orderby'] == 'title' ) {
				$args['order'] = 'ASC';
			}
		}

		// Date and Meta Query results
		
		$args2 = array();

        if ( ! empty( $data['cat'] ) ) {
            $args2['tax_query'] = array(
                array(
                    'taxonomy' => 'rtcl_category',
                    'field' => 'term_id',
                    'terms' => $data['cat'],
                )
            );
        }

        foreach ( $data['type'] as $key=>$value) {
            $args2['meta_query'] = array(
                array(
                    'key'       => 'ad_type',
                    'value'     => $value,
                    'compare'   => '='
                )
            );
            $result[$value] = new WP_Query( $args + $args2 );
            $args2 = array();
        }

		return $result;
	}

	private function rt_isotope_navigation( $data ) {

        $type_list = Functions::get_listing_types();

        $navs = array();

        if ( ! empty( $type_list ) ) {
            $navs = $type_list;
        }

		$navs = apply_filters( 'classipost_isotope_navigations', $navs );

		foreach ( $navs as $key => $value ) {
			if ( !in_array( $key , $data['type'] ) ) {
				unset($navs[$key]);
			}
		}

		return $navs;
	}

	protected function render() {
		$data = $this->get_settings();
		$this->rt_load_scripts();

		$data['queries']  = $this->rt_isotope_query( $data );
		$data['navs']     = $this->rt_isotope_navigation( $data );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}