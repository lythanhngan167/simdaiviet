<?php
/**
 * Main Elementor ListingCategorySlider Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since    2.0.10
 */

namespace RtclPro\Controllers\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Rtcl\Abstracts\ElementorWidgetBase;
use Rtcl\Helpers\Functions;

/**
 * ListingCategorySlider Class
 */
class ListingCategorySlider extends ElementorWidgetBase {


	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		// TODO: Box border Radius need add.
		$this->rtcl_name      = __( 'Category Slider', 'classified-listing-pro' );
		$this->rtcl_base      = 'rtcl-listing-category-slider';
		$this->rtcl_translate = array(
			'cols' => apply_filters(
				'rtcl_listing_cat_slider_column',
				array(
					'1' => __( '1 Col', 'classified-listing-pro' ),
					'2' => __( '2 Col', 'classified-listing-pro' ),
					'3' => __( '3 Col', 'classified-listing-pro' ),
					'4' => __( '4 Col', 'classified-listing-pro' ),
					'5' => __( '5 Col', 'classified-listing-pro' ),
					'6' => __( '6 Col', 'classified-listing-pro' ),
				)
			),
		);
		parent::__construct( $data, $args );

	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {

		$category_dropdown = $this->taxonomy_list( 'parent' );
		$fields            = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'classified-listing-pro' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_cats_style',
				'label'   => __( 'Style', 'classified-listing-pro' ),
				'options' => $this->cat_box_style(),
				'default' => 'style-1',
			),
			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_cats',
				'label'       => __( 'Categories', 'classified-listing-pro' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'description' => __( 'Start typing category names. If empty then all parent categories will be displayed', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_category_limit',
				'label'       => __( 'Category Limit', 'classified-listing-pro' ),
				'default'     => '10',
				'description' => __( 'How Many Category will Display ?', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_sub_category',
				'label'     => __( 'Display Sub Category', 'classified-listing-pro' ),
				'label_on'  => __( 'On', 'classified-listing-pro' ),
				'label_off' => __( 'Off', 'classified-listing-pro' ),
				'default'   => 'yes',
				'condition' => array( 'rtcl_cats_style' => array( '2' ) ),
			),

			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_sub_category_limit',
				'label'       => __( 'Child Category Limit', 'classified-listing-pro' ),
				'default'     => '5',
				'description' => __( 'How Many Child Category will Display ?', 'classified-listing-pro' ),
				'condition'   => array(
					'rtcl_show_sub_category' => array( 'yes' ),
					'rtcl_cats_style'        => array( '2' ),
				),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_pad_counts',
				'label'     => __( 'Counts Include Children', 'classified-listing-pro' ),
				'label_on'  => __( 'On', 'classified-listing-pro' ),
				'label_off' => __( 'Off', 'classified-listing-pro' ),
				'default'   => 'yes',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_orderby',
				'label'   => __( 'Order By', 'classified-listing-pro' ),
				'options' => array(
					'none'    => __( 'None', 'classified-listing-pro' ),
					'term_id' => __( 'ID', 'classified-listing-pro' ),
					'date'    => __( 'Date', 'classified-listing-pro' ),
					'name'    => __( 'Title', 'classified-listing-pro' ),
					'count'   => __( 'Count', 'classified-listing-pro' ),
					'custom'  => __( 'Custom Order', 'classified-listing-pro' ),
				),
				'default' => 'name',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_order',
				'label'   => __( 'Sort By', 'classified-listing-pro' ),
				'options' => array(
					'asc'  => __( 'Ascending', 'classified-listing-pro' ),
					'desc' => __( 'Descending', 'classified-listing-pro' ),
				),
				'default' => 'asc',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_hide_empty',
				'label'       => __( 'Hide Empty', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => '',
				'description' => __( 'Hide Categories that has no listings. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_image',
				'label'       => __( 'Show Icon/Image', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Icon/Image. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_category_title',
				'label'       => __( 'Show Title', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Category Title. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'rtcl_icon_type',
				'label'     => __( 'Icon Type', 'classified-listing-pro' ),
				'options'   => array(
					'image' => __( 'Image', 'classified-listing-pro' ),
					'icon'  => __( 'Icon', 'classified-listing-pro' ),
				),
				'default'   => 'icon',
				'condition' => array( 'rtcl_show_image' => array( 'yes' ) ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_count',
				'label'       => __( 'Listing Counts', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Counts. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_description',
				'label'       => __( 'Category Description', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Description. Default: On', 'classified-listing-pro' ),
			),

			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_content_limit',
				'label'       => __( 'Description Word Limit', 'classified-listing-pro' ),
				'default'     => '12',
				'description' => __( 'Number of Words to display', 'classified-listing-pro' ),
				'condition'   => array( 'rtcl_description' => array( 'yes' ) ),
			),
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'id'          => 'display_child_category',
				'label'       => __( 'Display Child category', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Loop to first item. Default: On', 'classified-listing-pro' ),
				'condition'   => array( 'rtcl_cats_style' => array( 'style-2' ) ),

			),
			array(
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'rtcl_cat_box_alignment',
				'label'     => __( 'Content alignment', 'classified-listing-pro' ),
				'options'   => $this->alignment_options(),
				'default'   => 'center',
				'condition' => array( 'rtcl_cats_style' => array( 'style-1' ) ),

			),
			array(
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'rtcl_cat_box_style_2_alignment',
				'label'     => __( 'Content alignment', 'classified-listing-pro' ),
				'options'   => $this->alignment_options(),
				'default'   => 'left',
				'condition' => array( 'rtcl_cats_style' => 'style-2' ),
			),
			array(
				'mode' => 'section_end',
			),

			// Slider Option.
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_slider_settings',
				'label' => __( 'Slider Options', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_auto_height',
				'label'       => __( 'Auto Height', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'description' => __( 'Auto Height. Default: On', 'classified-listing-pro' ),
				'default'     => '',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_loop',
				'label'       => __( 'Loop', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => '',
				'description' => __( 'Loop to first item. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_autoplay',
				'label'       => __( 'Autoplay', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Enable or disable autoplay. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_stop_on_hover',
				'label'       => __( 'Stop on Hover', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Stop autoplay on mouse hover. Default: On', 'classified-listing-pro' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'slider_delay',
				'label'       => __( 'Autoplay Delay', 'classified-listing-pro' ),
				'options'     => array(
					'7000' => __( '7 Seconds', 'classified-listing-pro' ),
					'6000' => __( '6 Seconds', 'classified-listing-pro' ),
					'5000' => __( '5 Seconds', 'classified-listing-pro' ),
					'4000' => __( '4 Seconds', 'classified-listing-pro' ),
					'3000' => __( '3 Seconds', 'classified-listing-pro' ),
					'2000' => __( '2 Seconds', 'classified-listing-pro' ),
					'1000' => __( '1 Second', 'classified-listing-pro' ),
				),
				'default'     => '5000',
				'description' => __( 'Set any value for example 5 seconds to play it in every 5 seconds. Default: 5 Seconds', 'classified-listing-pro' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'slider_autoplay_speed',
				'label'       => __( 'Autoplay Slide Speed', 'classified-listing-pro' ),
				'default'     => 2000,
				'description' => __( 'Slide speed in milliseconds. Default: 200', 'classified-listing-pro' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'slider_space_between',
				'label'       => __( 'Space Between', 'classified-listing-pro' ),
				'default'     => 20,
				'description' => __( 'Space Between. Default: 20', 'classified-listing-pro' ),
			),
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'id'          => 'slider_nav',
				'label'       => __( 'Arrow Navigation', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => 'yes',
				'description' => __( 'Loop to first item. Default: On', 'classified-listing-pro' ),
			),
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'id'          => 'slider_dots',
				'label'       => __( 'Dot Navigation', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => '',
				'description' => __( 'Loop to first item. Default: On', 'classified-listing-pro' ),
			),
			/*
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'id'          => 'slider_rtl',
				'label'       => __( 'RTL', 'classified-listing-pro' ),
				'label_on'    => __( 'On', 'classified-listing-pro' ),
				'label_off'   => __( 'Off', 'classified-listing-pro' ),
				'default'     => '',
				'description' => __( 'Loop to first item. Default: On', 'classified-listing-pro' ),
			),
			*/
			array(
				'mode' => 'section_end',
			),
			// Responsive Columns.
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_responsive',
				'label' => __( 'Number of Responsive Columns', 'classified-listing-pro' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_col_xl',
				'label'   => __( 'Desktops: >1199px', 'classified-listing-pro' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_col_lg',
				'label'   => __( 'Desktops: >991px', 'classified-listing-pro' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_col_md',
				'label'   => __( 'Tablets: >767px', 'classified-listing-pro' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_col_sm',
				'label'   => __( 'Phones: >575px', 'classified-listing-pro' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '2',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_col_mobile',
				'label'   => __( 'Small Phones: <576px', 'classified-listing-pro' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '1',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'rtcl_el_slider_category_widget_general_field', $fields );
	}

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function cat_box_style() {
		$style = apply_filters(
			'rtcl_el_category_slider_style',
			array(
				'style-1' => __( 'Style 1', 'classified-listing-pro' ),
				'style-2' => __( 'Style 2', 'classified-listing-pro' ),
			)
		);

		return $style;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array(
			// Style Tab.
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Box style', 'classified-listing-pro' ),
			),

			array(
				'label'      => __( 'Gutter pading', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_gutter_padding',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Box pading', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_padding',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .cat-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Head Section Pading', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_head_gutter_padding',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'rtcl_cats_style' => 'style-2',
				),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .rtin-head-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Body Section Pading', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_description_gutter_padding',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'rtcl_cats_style' => 'style-2',
				),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .box-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'rtcl_wrapper_tabs_start',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_box_wrapper_tab_normal',
				'label' => esc_html__( 'Normal', 'elementor' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_header',
				'label'     => __( 'Head background', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .rtin-head-area' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_body',
				'label'     => __( 'Body background', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'mode'      => 'group',
				'label'     => __( 'Background', 'classified-listing-pro' ),
				'id'        => 'rtcl_background',
				'type'      => Group_Control_Background::get_type(),
				'selector'  => '{{WRAPPER}} .cat-item-wrap .cat-details',
				'condition' => array(
					'rtcl_cats_style' => 'style-1',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_border',
				'selector' => '{{WRAPPER}}  .cat-item-wrap .cat-details',
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing-pro' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_box_shadow',
				'selector' => '{{WRAPPER}}  .cat-item-wrap .cat-details',
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_box_wrapper_tab_hover',
				'label' => esc_html__( 'Hover', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_header_hover',
				'label'     => __( 'Head background', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap:hover .rtin-head-area' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_body_hover',
				'label'     => __( 'Body background', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap:hover .box-body' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'mode'      => 'group',
				'label'     => __( 'Background', 'classified-listing-pro' ),
				'id'        => 'rtcl_background_hover',
				'type'      => Group_Control_Background::get_type(),
				'selector'  => '{{WRAPPER}} .cat-item-wrap:hover .cat-details',
				'condition' => array(
					'rtcl_cats_style' => 'style-1',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_border_hover',
				'selector' => '{{WRAPPER}}  .cat-item-wrap .cat-details',
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing-pro' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_hover_box_shadow',
				'selector' => '{{WRAPPER}}  .cat-item-wrap:hover .cat-details',
			),
			array(
				'mode' => 'tab_end',
			),

			array(
				'mode' => 'tabs_end',
			),

			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_content_alignment',
				'label'   => __( 'Content Vertical Alignment', 'classified-listing-pro' ),
				'options' => array(
					'none'           => __( 'None', 'classified-listing-pro' ),
					'content-middle' => __( 'Middle', 'classified-listing-pro' ),
				),
				'default' => 'none',
			),

			array(
				'mode' => 'section_end',
			),

			// Image settings.
			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_icon',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Icon And Image', 'classified-listing-pro' ),
				'condition' => array(
					'rtcl_show_image' => 'yes',
				),
			),
			array(
				'label'     => __( 'Icon Area', 'classified-listing-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_image_area_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				// 'condition' => array(
				// 'rtcl_icon_type' => 'icon',
				// ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details .icon a' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Icon Size', 'classified-listing-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_font_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				// 'condition' => array(
				// 'rtcl_icon_type' => 'icon',
				// ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details .icon a .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cat-item-wrap .cat-details img'                => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Image Size', 'classified-listing-pro' ),
				'type'      => Group_Control_Image_Size::get_type(),
				'id'        => 'rtcl_icon_image_size',
				'mode'      => 'group',
				'default'   => 'large',
				'separator' => 'none',
				'condition' => array(
					'rtcl_icon_type' => 'image',
				),
			),
			array(
				'label'      => __( 'Border Radius', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_icon_image_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details .icon a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				// 'condition'  => array(
				// 'rtcl_icon_type' => 'icon',
				// ),
			),

			array(
				'label'      => __( 'Image Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_image_spacing',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .icon'  => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}  .cat-item-wrap .image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'image_icon_tabs_start',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'box_icon_tab_normal',
				'label' => esc_html__( 'Normal', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap  .cat-details .icon a .rtcl-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cat-item-wrap  .rtin-sub-cats li i'             => 'color: {{VALUE}}',
					'{{WRAPPER}} .cat-item-wrap  .rtin-sub-cats li a:hover'       => 'color: {{VALUE}}',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_icon_border',
				'selector' => '{{WRAPPER}} .cat-item-wrap  .cat-details .icon a',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_bg',
				'label'     => __( 'Icon Bg Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap  .cat-details .icon a' => 'background-color: {{VALUE}}',
				),
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing-pro' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_icon_box_shadow',
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details .icon a',
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'box_image_tab_hover',
				'label' => esc_html__( 'Hover', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_hover_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover  .cat-details .icon a .rtcl-icon' => 'color: {{VALUE}}' ),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_icon_hover_border',
				'selector' => '{{WRAPPER}} .cat-item-wrap:hover .cat-details .icon a ',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_bg_hover',
				'label'     => __( 'Icon Bg Hover Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap:hover  .cat-details .icon a' => 'background-color: {{VALUE}}',
				),
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing-pro' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_icon_box_shadow_hover',
				'selector' => '{{WRAPPER}} .cat-item-wrap:hover .cat-details .icon a',
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode' => 'section_end',
			),

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_title',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Title', 'classified-listing-pro' ),
				'condition' => array(
					'rtcl_show_category_title' => 'yes',
				),
			),

			array(
				'mode' => 'tabs_start',
				'id'   => 'title_tabs_start',
			),

			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_normal',
				'label' => esc_html__( 'Normal', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap .cat-details h3, {{WRAPPER}} .cat-item-wrap .cat-details h3 a' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_hover',
				'label' => esc_html__( 'Hover', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color_hover',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover .cat-details h3, {{WRAPPER}} .cat-item-wrap:hover .cat-details h3 a' => 'color: {{VALUE}}' ),
			),

			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_title_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details h3',
			),
			array(
				'label'      => __( 'Title Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_title_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .cat-details h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_counter',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Counter', 'classified-listing-pro' ),
				'condition' => array(
					'rtcl_show_count' => 'yes',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'counter_tabs_start',
			),

			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_counter_normal',
				'label' => esc_html__( 'Normal', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_counter_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap .cat-details .views' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_counter_hover',
				'label' => esc_html__( 'Hover', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_counter_color_hover',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover .cat-details .views' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_counter_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details .views',
			),
			array(
				'label'      => __( 'Counter Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_counter_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .cat-details .views' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_short_description',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Short Description', 'classified-listing-pro' ),
				'condition' => array(
					'rtcl_description' => 'yes',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'content_tabs_start',
			),

			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_content_normal',
				'label' => esc_html__( 'Normal', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_content_color_normal',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap .cat-details p' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_content_hover',
				'label' => esc_html__( 'Hover', 'elementor' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_content_color_hover',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover .cat-details p' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_content_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details p',
			),
			array(
				'label'      => __( 'Content Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_content_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_navigation',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Slider Navigation', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::SELECT,
				'id'        => 'rtcl_button_arrow_style',
				'label'     => __( 'Arrow Position', 'classified-listing-pro' ),
				'options'   => array(
					'style-1' => esc_html__( 'Center', 'classified-listing-pro' ),
					'style-2' => esc_html__( 'Left Top', 'classified-listing-pro' ),
					'style-3' => esc_html__( 'Right Top', 'classified-listing-pro' ),
				),
				'default'   => 'style-1',
				'condition' => array(
					'slider_nav' => 'yes',
				),
			),
			array(
				'label'      => __( 'Arrow Navigation Border Radius', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_arrow_navigation_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'background: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_bg_color_hover',
				'label'     => __(
					'Hover Background Color',
					'classified-listing-pro'
				),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn:hover' => 'background: {{VALUE}}' ),
			),
			array(
				'label'     => esc_html__( 'Dot Navigation Settings', 'classified-listing-pro' ),
				'id'        => 'navigation_control_heading',
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'slider_dots' => 'yes',
				),
			),

			array(
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'id'        => 'rtcl_button_dot_style',
				'label'     => __( 'Style', 'classified-listing-pro' ),
				'options'   => array(
					'style-1' => esc_html__( 'Style 1', 'classified-listing-pro' ),
					'style-2' => esc_html__( 'Style 2', 'classified-listing-pro' ),
					'style-3' => esc_html__( 'Style 3', 'classified-listing-pro' ),
					'style-4' => esc_html__( 'Style 4', 'classified-listing-pro' ),
				),
				'default'   => 'style-3',
				'condition' => array(
					'slider_dots' => 'yes',
				),
			),

			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_dot_navigation_spacing',
				'label'      => __( 'Dot Navigation Spacing', 'classified-listing-pro' ),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => '30',
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-pagination.swiper-pagination-bullets' => 'bottom: -{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'slider_dots' => 'yes',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_dot_navigation_bg_color',
				'label'     => __( 'Default Color', 'classified-listing-pro' ),
				'condition' => array(
					'slider_dots' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-pagination .swiper-pagination-bullet'                => 'background: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-2 .rtcl-slider-pagination .swiper-pagination-bullet'        => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet::after' => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_dot_navigation_bg_color_hover',
				'label'     => __( 'Active Color', 'classified-listing-pro' ),
				'condition' => array(
					'slider_dots' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}}  .rtcl-el-slider-wrapper .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'               => 'background: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active::after' => 'background-color: {{VALUE}}',

					'{{WRAPPER}} .rtcl-slider-pagination-style-2 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'border-color: {{VALUE}}',
				),
			),

			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'rtcl_el_slider_category_widget_style_field', $fields );
	}

	/**
	 * Widget result.
	 *
	 * @param [array] $data array of query.
	 *
	 * @return array
	 */
	public function widget_results( $data ) {
		$args = array(
			'taxonomy'     => rtcl()->category,
			'parent'       => 0,
			'orderby'      => ! empty( $data['rtcl_orderby'] ) ? $data['rtcl_orderby'] : 'date',
			'order'        => ! empty( $data['rtcl_order'] ) ? $data['rtcl_order'] : 'desc',
			'hide_empty'   => ! empty( $data['rtcl_hide_empty'] ) ? 1 : 0,
			'include'      => ! empty( $data['rtcl_cats'] ) ? $data['rtcl_cats'] : array(),
			'hierarchical' => false,
		);
		if ( 'custom' === $data['rtcl_orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_rtcl_order';
		}
		$terms = get_terms( $args );
		if ( ! empty( $data['rtcl_category_limit'] ) ) {
			$number = $data['rtcl_category_limit'];
			$terms  = array_slice( $terms, 0, $number );
		}
		return $terms;
	}


	/**
	 * Display Output.
	 *
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings();
		$terms    = $this->widget_results( $settings );

		$style = isset( $settings['rtcl_cats_style'] ) ? $settings['rtcl_cats_style'] : 'style-1';
		if ( ! in_array( $style, array_keys( $this->cat_box_style() ) ) ) {
			$style = 'style-1';
		}
		$template_style = 'elementor/listing-cat-slider/grid-' . $style;
		$data           = array(
			'template'              => $template_style,
			'style'                 => $style,
			'settings'              => $settings,
			'terms'                 => $terms,
			'default_template_path' => rtclPro()->get_plugin_template_path(),
		);
		$data           = apply_filters( 'rtcl_el_category_slider_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		$this->edit_mode_script();
	}
}
