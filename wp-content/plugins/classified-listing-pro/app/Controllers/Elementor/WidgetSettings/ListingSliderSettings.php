<?php
/**
 * Main Elementor ListingSliderSettings Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since    2.0.10
 */

namespace RtclPro\Controllers\Elementor\WidgetSettings;

use Rtcl\Controllers\Hooks\TemplateHooks;
use Elementor\{
	Controls_Manager,
	Group_Control_Box_Shadow,
	Group_Control_Image_Size,
	Group_Control_Typography,
	Group_Control_Border,
};

use Rtcl\Abstracts\ElementorWidgetBase;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;
use Rtcl\Controllers\Elementor\ELWidgetsTraits\{
	ElSliderTrait,
	ListingStyleTrait,
	ListingContentVisibilityTrait
};

/**
 * ListingSliderSettings Class
 */
class ListingSliderSettings extends ElementorWidgetBase {
	/**
	 * Slider related functionality
	 */
	use ElSliderTrait;
	/**
	 * Listing style or view related trait
	 */
	use ListingStyleTrait;
	/**
	 * Content visiblity.
	 */
	use ListingContentVisibilityTrait;

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		// TODO: Top post first Priority.
		// TODO: Top post always Show in top.
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_listing_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Item Wrapper', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_wrapper_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget
				.listing-item' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing-pro' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_listing_wrapper_box_shadow',
				'selector' => '{{WRAPPER}} .rtcl.rtcl-elementor-widget
				.listing-item',
			),
			array(
				'label'    => __( 'Hover Box Shadow', 'classified-listing-pro' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_listing_wrapper_hover_box_shadow',
				'selector' => '{{WRAPPER}}  .rtcl.rtcl-elementor-widget
				.listing-item:hover',
			),
			array(
				'label'      => __( 'Wrapper Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget .listing-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Content Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_content_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .listing-item .item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'classified-listing-pro' ),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_border',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						),
					),
					'color'  => array(
						'default' => 'rgba(0, 0, 0, 0.05)',
					),
				),
				'selector'       => '{{WRAPPER}} .rtcl .rtcl-list-view .listing-item, {{WRAPPER}} .rtcl .rtcl-grid-view .listing-item',
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_image_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Image Wrapper', 'classified-listing-pro' ),
			),

			array(
				'mode'       => 'responsive',
				'label'      => __( 'Image Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_image_mobile_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .listing-item .listing-thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
					'rtcl_show_title' => 'yes',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_title_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .listing-item  .item-content  .rtcl-listing-title',
			),
			array(
				'label'      => __( 'Title Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_title_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .listing-item .item-content .listing-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Normal', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .listing-item .rtcl-listing-title a' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_hover',
				'label' => esc_html__( 'Hover', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color_hover',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .listing-item .rtcl-listing-title a:hover' => 'color: {{VALUE}}' ),
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
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_meta',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Meta', 'classified-listing-pro' ),
			),

			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_meta_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-meta-data li',
			),
			array(
				'label'      => __( 'Meta Spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_meta_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget .rtcl-listings .rtcl-listing-meta-data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'meta_tabs_start',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_meta_normal',
				'label' => esc_html__( 'Normal', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_icon_color',
				'label'     => __( 'Meta Icon Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-color: {{VALUE}}' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listing-meta-data li i' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_category_color',
				'label'     => __( 'Category Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl.rtcl-elementor-widget .category a' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_meta_hover',
				'label' => esc_html__( 'Hover', 'classified-listing-pro' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_hover_color',
				'label'     => __( 'Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-hover-color: {{VALUE}}',
					'{{WRAPPER}} .listing-item:hover .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_hover_icon_color',
				'label'     => __( 'Meta Icon Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-hove-color: {{VALUE}}',
					'{{WRAPPER}} .listing-item:hover .rtcl-listing-meta-data li i' => 'color: {{VALUE}}',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_category_color_hover',
				'label'     => __( 'Category Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl.rtcl-elementor-widget .category a:hover' => 'color: {{VALUE}}' ),
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
				'id'        => 'rtcl_sec_description',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Description', 'classified-listing-pro' ),
				'condition' => array( 'rtcl_show_description' => array( 'yes' ) ),

			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Description', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_description_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-short-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_description_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .rtcl-short-description',

			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_description_color',
				'label'     => __( 'Short Description Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-short-description' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_description_hover_color',
				'label'     => __( 'On Items Hover Description color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .listing-item:hover .rtcl-short-description' => 'color: {{VALUE}}' ),
			),

			array(
				'mode' => 'section_end',
			),

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_price',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Price', 'classified-listing-pro' ),
				'condition' => array(
					'rtcl_show_price' => array( 'yes' ),
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .listing-item .item-price .rtcl-price',
			),

			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_unit_label_typo',
				'label'    => __( 'Unit Label Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .listing-item .item-price .rtcl-price-unit-label',
				'condition' => array( 'rtcl_show_price_unit' => array( 'yes' ) ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_unit_label_color',
				'label'     => __( 'Unit Label Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .item-price .rtcl-price-unit-label' => 'color: {{VALUE}};',
				),
				'condition' => array( 'rtcl_show_price_unit' => array( 'yes' ) ),
			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Price padding', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_amount_wrapper_padding',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .listing-item .item-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'rtcl_listings_grid_style' => array( 'style-5' ),
				),
			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Price Margin', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_amount_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .listing-item .item-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_amount_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .listing-item .item-price' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				),
				'condition' => array(
					'rtcl_listings_grid_style' => array( 'style-5' ),
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_amount_text_color',
				'label'     => __( 'Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-price'       => 'color: {{VALUE}};',
				),
			),

			array(
				'mode' => 'section_end',
			),

			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_badge_section',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Badge ', 'classified-listing-pro' ),
			),
			array(
				'label'      => __( 'padding', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_badge_wrapper_padding',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-listing-badge-wrap .badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Margin', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_badge_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-listing-badge-wrap .badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			// array(
			// 	'mode'     => 'group',
			// 	'type'     => Group_Control_Typography::get_type(),
			// 	'id'       => 'rtcl_badge_sold_typo',
			// 	'label'    => __( 'Sold Out Typography', 'classified-listing-pro' ),
			// 	'selector' => '{{WRAPPER}}  .rtcl-sold-out',
			// ),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_sold_out_bg_color',
				'label'     => __( 'Sold Out Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-sold-out ' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_sold_out_text_color',
				'label'     => __( 'Sold Out Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-sold-out ' => 'color: {{VALUE}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_badge_typo',
				'label'    => __( 'Typography', 'classified-listing-pro' ),
				'selector' => '{{WRAPPER}} .listing-item  .item-content .badge',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .badge' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_text_color',
				'label'     => __( 'Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .badge' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_top_badge_bg_color',
				'label'     => __( 'Top Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .rtcl-badge-_top' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_top_badge_text_color',
				'label'     => __( 'Top Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .rtcl-badge-_top' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_featured_badge_bg_color',
				'label'     => __( 'Featured Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item.is-featured .listing-thumb:after, {{WRAPPER}} .listing-item.is-featured .rtcl-badge-featured ' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_featured_badge_text_color',
				'label'     => __( 'Featured Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .listing-item.is-featured .listing-thumb:after, {{WRAPPER}} .listing-item.is-featured .rtcl-badge-featured' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_new_badge_bg_color',
				'label'     => __( 'New Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .rtcl-badge-new' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_new_badge_text_color',
				'label'     => __( 'New Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-new' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_popular_badge_bg_color',
				'label'     => __( 'Popular Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .rtcl-badge-popular' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_popular_badge_text_color',
				'label'     => __( 'Popular Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-popular' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_bump_up_badge_bg_color',
				'label'     => __( 'Bump Up Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .rtcl-badge-_bump_up' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_bump_up_badge_text_color',
				'label'     => __( 'Bump Up Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-_bump_up' => 'color: {{VALUE}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_action_button',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Button', 'classified-listing-pro' ),
				// 'condition' => array( 'rtcl_listings_grid_style!' => array( 'style-5' ) ),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'button_tabs_start',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_normal',
				'label' => esc_html__( 'Normal', 'classified-listing-pro' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtin-el-button a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn' => 'background-color: {{VALUE}};',
				),
			),
			// action-btn
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_text_color',
				'label'     => __( 'Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtin-el-button a,{{WRAPPER}} .rtin-el-button a .rtcl-icon '                                                                                       => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button'                                                                                                       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button,{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button a .rtcl-icon '                                         => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a, {{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a .rtcl-icon' => 'color: {{VALUE}};',

				),
			),
			[
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'         => 'rtcl_button_border_color',
				'label'      => __( 'Border', 'classified-listing' ),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#e1e1e1',
					],
				],
				'selector'  => '{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a',
				'condition' => array( 'rtcl_listings_grid_style' => array( 'style-5' ) ),
			],
			// array(
			// 	'type'      => Controls_Manager::COLOR,
			// 	'id'        => 'rtcl_button_border_color',
			// 	'label'     => __( 'Border Color', 'classified-listing-pro' ),
			// 	'selectors' => array(
			// 		'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a' => 'border-color: {{VALUE}};',
			// 	),
			// 	'condition' => array( 'rtcl_listings_grid_style' => array( 'style-5' ) ),
			// ),

			array(
				'mode' => 'tab_end',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_hover',
				'label' => esc_html__( 'Hover', 'classified-listing-pro' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_bg_hover_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtin-el-button a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn:hover' => 'background-color: {{VALUE}};',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_hover_text_color',
				'label'     => __( 'Text Color In hover', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtin-el-button a:hover,{{WRAPPER}} .rtin-el-button a:hover .rtcl-icon '                                                                                       => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button:hover'                                                                                                             => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button:hover,{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button:hover a .rtcl-icon '                                         => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn:hover a, {{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn:hover a .rtcl-icon' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_hover_border_color',
				'label'     => __( 'Border Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn:hover a' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'rtcl_listings_grid_style' => array( 'style-5' ) ),

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
				'id'        => 'rtcl_sec_pagination',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Pagination', 'classified-listing-pro' ),
				'condition' => array( 'rtcl_listing_pagination' => array( 'yes' ) ),
			),
			array(
				'label'      => __( 'Pagination spacing', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_pagination_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listings-sc-wrapper .pagination ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item .page-link' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_active_bg_color',
				'label'     => __( 'Active Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item .page-link:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_text_color',
				'label'     => __( 'Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item .page-link' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_active_text_color',
				'label'     => __( 'Active Text Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item .page-link:hover' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_pagination_border',
				'selector' => '{{WRAPPER}} .page-link',
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'       => 'section_start',
				'id'         => 'rtcl_sec_navigation',
				'tab'        => Controls_Manager::TAB_STYLE,
				'label'      => __( 'Slider Navigation', 'classified-listing-pro' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'slider_dots',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'slider_nav',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			),

			array(
				'label'      => __( 'Arrow Navigation Border Radius', 'classified-listing-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_arrow_navigation_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider_nav' => 'yes',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_bg_color',
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'background: {{VALUE}}' ),
				'condition' => array(
					'slider_nav' => 'yes',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_bg_color_hover',
				'label'     => __( 'Hover Background Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn:hover' => 'background: {{VALUE}}' ),
				'condition' => array(
					'slider_nav' => 'yes',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_icon_color',
				'label'     => __( 'Icon Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'color: {{VALUE}}' ),
				'condition' => array(
					'slider_nav' => 'yes',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_icon_color_hover',
				'label'     => __( 'Icon Hover Color', 'classified-listing-pro' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn:hover' => 'color: {{VALUE}}' ),
				'condition' => array(
					'slider_nav' => 'yes',
				),
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
				'type'       => Controls_Manager::SLIDER,
				'separator'  => 'before',
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
				'label'     => __( 'Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-pagination .swiper-pagination-bullet'                => 'background: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-2 .rtcl-slider-pagination .swiper-pagination-bullet'        => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet::after' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'slider_dots' => 'yes',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_dot_navigation_bg_color_hover',
				'label'     => __( 'Active Background Color', 'classified-listing-pro' ),
				'selectors' => array(
					'{{WRAPPER}}  .rtcl-el-slider-wrapper .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'               => 'background: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-2 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'        => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'        => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'slider_dots' => 'yes',
				),
			),

			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'rtcl_el_listing_slider_widget_style_field', $fields, $this );
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$category_dropdown = $this->taxonomy_list();
		$location_dropdown = $this->taxonomy_list( 'all', 'rtcl_location' );
		$listing_order_by  = array(
			'title' => __( 'Title', 'classified-listing-pro' ),
			'date'  => __( 'Date', 'classified-listing-pro' ),
			'ID'    => __( 'ID', 'classified-listing-pro' ),
			'price' => __( 'Price', 'classified-listing-pro' ),
			'views' => __( 'Views', 'classified-listing-pro' ),
			'none'  => __( 'None', 'classified-listing-pro' ),
		);
		$listing_order_by  = apply_filters( 'rtcl_el_listing_order_by', $listing_order_by );

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'classified-listing-pro' ),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Style', 'classified-listing-pro' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_listings_grid_style',
				'options' => $this->grid_style(),
				'default' => 'style-1',
			),
			array(
				'type'     => Controls_Manager::SELECT2,
				'id'       => 'rtcl_listings_promotions',
				'label'    => __( 'Promotions', 'classified-listing-pro' ),
				'options'  => Options::get_listing_promotions(),
				'multiple' => true,
			),

			array(
				'type'     => Controls_Manager::SELECT2,
				'id'       => 'rtcl_listings_promotions_not_in',
				'label'    => __( 'Promotions Exclude', 'classified-listing-pro' ),
				'options'  => Options::get_listing_promotions(),
				'multiple' => true,
			),

			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_listing_types',
				'label'   => __( 'Listing Types', 'classified-listing-pro' ),
				'options' => array_merge(
					array(
						'all' => 'All',
					),
					Functions::get_listing_types(), // OR Options::get_default_listing_types().
				),
				'default' => 'all',
			),

			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_listings_by_categories',
				'label'       => __( 'Categories', 'classified-listing-pro' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'default'     => '',
				'description' => __( 'Start typing category names. If empty then all listings will display.', 'classified-listing-pro' ),
			),

			array(
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'rtcl_listings_categories_include_children',
				'label'      => __( 'Include Children Categories', 'classified-listing-pro' ),
				'label_on'   => __( 'On', 'classified-listing-pro' ),
				'label_off'  => __( 'Off', 'classified-listing-pro' ),
				'default'    => '',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'rtcl_listings_by_categories',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),

			),

			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_locations',
				'label'       => __( 'Locations', 'classified-listing-pro' ),
				'options'     => $location_dropdown,
				'multiple'    => true,
				'default'     => '',
				'description' => __( 'Start typing locations names.', 'classified-listing-pro' ),
			),
			array(
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'rtcl_listings_location_include_children',
				'label'      => __( 'Include Inner Location', 'classified-listing-pro' ),
				'label_on'   => __( 'On', 'classified-listing-pro' ),
				'label_off'  => __( 'Off', 'classified-listing-pro' ),
				'default'    => '',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'rtcl_locations',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_listing_per_page',
				'label'       => __( 'Listing Limit', 'classified-listing-pro' ),
				'default'     => '10',
				'description' => __( 'Number of listing to display', 'classified-listing-pro' ),
			),

			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_orderby',
				'label'   => __( 'Order By', 'classified-listing-pro' ),
				'options' => $listing_order_by,
				'default' => 'date',
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'rtcl_order',
				'label'     => __( 'Sort By', 'classified-listing-pro' ),
				'options'   => array(
					'asc'  => __( 'Ascending', 'classified-listing-pro' ),
					'desc' => __( 'Descending', 'classified-listing-pro' ),
				),
				'default'   => 'desc',
				'condition' => array( 'rtcl_orderby!' => array( 'rand' ) ),
			),
			array(
				'label'     => __( 'Image Size', 'classified-listing-pro' ),
				'type'      => Group_Control_Image_Size::get_type(),
				'id'        => 'rtcl_thumb_image',
				'exclude' => [ 'custom' ],
				'mode'      => 'group',
				'default'   => 'rtcl-thumbnail',
				'separator' => 'none',
			),
			array(
				'mode' => 'section_end',
			),
		);
		$fields = array_merge(
			$fields,
			$this->slider_content_visiblity(),
			$this->slider_options(),
			$this->slider_responsive()
		);
		return apply_filters( 'rtcl_el_listing_slider_widget_general_field', $fields );
	}

}
