<?php
/**
 * @author  RadiusTheme
 *
 * @since   1.0
 *
 * @version 1.0
 */

namespace RtclStore\Controllers\Elementor\Settings;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Rtcl\Abstracts\ElementorWidgetBase;

/**
 * ListingStoreSettings Class.
 */
class ListingStoreSettings extends ElementorWidgetBase {
	/**
	 * Set Query controlls
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->general_fields(),
			$this->content_visivlity()
		);

		return $fields;
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->wrapper_style(),
			$this->style_fields()
		);

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function content_visivlity() {
		$fields = [
			[
				'mode'    => 'section_start',
				'id'      => 'content_visiblity',
				'label'   => __('Content Visiblity', 'classified-listing-store'),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_image',
				'label'       => __('Show Image', 'classified-listing-store'),
				'label_on'    => __('On', 'classified-listing-store'),
				'label_off'   => __('Off', 'classified-listing-store'),
				'default'     => 'yes',
				'description' => __('Show or Hide Store Icon/Image. Default: On', 'classified-listing-store'),
			],

			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_title',
				'label'       => __('Show Title', 'classified-listing-store'),
				'label_on'    => __('On', 'classified-listing-store'),
				'label_off'   => __('Off', 'classified-listing-store'),
				'default'     => 'yes',
				'description' => __('Show or Hide Store Title. Default: On', 'classified-listing-store'),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_time',
				'label'       => __('Show Time', 'classified-listing-store'),
				'label_on'    => __('On', 'classified-listing-store'),
				'label_off'   => __('Off', 'classified-listing-store'),
				'default'     => 'yes',
				'description' => __('Show or Hide Store Time. Default: On', 'classified-listing-store'),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_count',
				'label'       => __('Show Count', 'classified-listing-store'),
				'label_on'    => __('On', 'classified-listing-store'),
				'label_off'   => __('Off', 'classified-listing-store'),
				'default'     => 'yes',
				'description' => __('Show or Hide Store Count. Default: On', 'classified-listing-store'),
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields() {

		$category_dropdown = $this->taxonomy_list('parent', 'store_category');

		$fields = [
			[
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __('General', 'classified-listing-store'),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_layout_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__('Layout', 'rtcl-elementor-builder')
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_store_view',
				'options' => [
					'list' => [
						'title' => esc_html__('List View', 'classified-listing-store'),
						'url'   => RTCL_STORE_URL . '/assets/images/list.png',
					],
					'grid' => [
						'title' => esc_html__('Grid View', 'classified-listing-store'),
						'url'   => RTCL_STORE_URL . '/assets/images/grid.png',
					],
				],
				'default' => 'grid',
			],
			[
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'posts_per_page',
				'label'       => __('Number of Items', 'classified-listing-store'),
				'default'     => '4',
				'description' => __('Write -1 to show all', 'classified-listing-store'),
			],
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'store_cat',
				'label'     => __( 'Categories', 'classified-listing-store' ),
				'options'   => $category_dropdown,
				'default'   => '0',
				'multiple'  => true,
			),
			[
				'type'    => Controls_Manager::SELECT,
				'id'      => 'store_orderby',
				'label'   => __('Order By', 'classified-listing-store'),
				'options' => [
					'date'  => __('Date', 'classified-listing-store'),
					'title' => __('Title', 'classified-listing-store'),
				],
				'default' => 'date',
			],
			[
				'type'    => Controls_Manager::SELECT,
				'id'      => 'store_order',
				'label'   => __('Sort By', 'classified-listing-store'),
				'options' => [
					'asc'  => __('Ascending', 'classified-listing-store'),
					'desc' => __('Descending', 'classified-listing-store'),
				],
				'default' => 'desc',
			],
			[
				'type'          => Controls_Manager::SELECT,
				'mode'          => 'responsive',
				'id'            => 'rtcl_store_column',
				'label'         => __('Column', 'classified-listing'),
				'options'       => $this->column_number(),
				'default'       => '4',
				'devices'       => ['desktop', 'tablet', 'mobile'],
				// 'desktop_default' => 4,
				// 'tablet_default' => 2,
				// 'mobile_default' => 1,
				'condition'     => ['rtcl_store_view' => ['grid']],
			],
			
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function wrapper_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_listing_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Item Wrapper', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_wrapper_bg_color',
				'label'     => __('Background Color', 'classified-listing'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-elementor-widget .store-item' => 'background-color: {{VALUE}};',
				],
			],
			[
				'label'    => __('Box Shadow', 'classified-listing'),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_listing_wrapper_box_shadow',
				'selector' => '{{WRAPPER}} .rtcl-elementor-widget .store-item',
			],
			[
				'label'    => __('Hover Box Shadow', 'classified-listing'),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_listing_wrapper_hover_box_shadow',
				'selector' => '{{WRAPPER}} .rtcl-elementor-widget .store-item:hover',
			],
			[
				'label'      => __('Wrapper Spacing', 'classified-listing'),
				'mode'       => 'responsive',
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_spacing',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget .store-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_wrapper_gutter_spacing',
				'label'      => __('Gutter Spacing', 'classified-listing'),
				'mode'       => 'responsive',
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
					'%'  => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '30',
				],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget'               => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __('Border', 'classified-listing'),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_border',
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
						'default' => 'rgba(0, 0, 0, 0.05)',
					],
				],
				// 'condition'      => ['rtcl_store_view' => ['grid']],
				'selector'       => '{{WRAPPER}} .rtcl-elementor-widget .store-item',
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function style_fields() {
		$fields = [
			// Style Tab
			[
				'mode'          => 'section_start',
				'id'            => 'sec_style_color',
				'tab'           => Controls_Manager::TAB_STYLE,
				'label'         => __('Title', 'classified-listing-store'),
				'condition'     => ['rtcl_show_title' => 'yes'],
			],
			[
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __('Title Typography', 'classified-listing-store'),
				'selector' => '{{WRAPPER}} .store-item .store-title',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __('Title', 'classified-listing-store'),
				'selectors' => ['{{WRAPPER}} .store-item .store-title' => 'color: {{VALUE}}'],
			],

			[
				'mode' => 'section_end',
			],
			[
				'mode'          => 'section_start',
				'id'            => 'sec_style_time',
				'tab'           => Controls_Manager::TAB_STYLE,
				'label'         => __('Time', 'classified-listing-store'),
				'condition'     => ['rtcl_show_time' => 'yes'],
			],
			[
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'date_time_typo',
				'label'    => __('Date Typography', 'classified-listing-store'),
				'selector' => '{{WRAPPER}} .store-item .store-time',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'date_time_color',
				'label'     => __('Date Color', 'classified-listing-store'),
				'selectors' => ['{{WRAPPER}} .store-item .store-time' => 'color: {{VALUE}}'],
			],
			[
				'mode' => 'section_end',
			],
			[
				'mode'          => 'section_start',
				'id'            => 'sec_style_type',
				'tab'           => Controls_Manager::TAB_STYLE,
				'label'         => __('Counter', 'classified-listing-store'),
				'condition'     => ['rtcl_show_count' => 'yes'],
			],
			[
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __('Counter Typography', 'classified-listing-store'),
				'selector' => '{{WRAPPER}} .store-item .store-count',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'counter_color',
				'label'     => __('Counter Color', 'classified-listing-store'),
				'selectors' => ['{{WRAPPER}} .store-item .store-count' => 'color: {{VALUE}}'],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
