<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Testimonial_2 extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Testimonial 2', 'classima-core' );
		$this->rt_base = 'rt-testimonial-2';
		parent::__construct( $data, $args );
	}

	public function rt_fields(){
		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::REPEATER,
				'id'      => 'items',
				'label'   => __( 'Add as many items as you want', 'classima-core' ),
				'fields'  => array(
					array(
						'type'  => Controls_Manager::TEXT,
						'name'  => 'name',
						'label' => __( 'Name', 'classima-core' ),
						'default' => 'John Doe',
					),
					array(
						'type'  => Controls_Manager::TEXT,
						'name'  => 'designation',
						'label' => __( 'Designation', 'classima-core' ),
						'default' => 'Designer',
					),
					array(
						'type'    => Controls_Manager::TEXTAREA,
						'name'      => 'content',
						'label'   => __( 'Content', 'classima-core' ),
						'default' => 'Lorem Ipsum has been standard daand scrambled. Rimply dummy text of the printing and typesetting industry',
					),
					array(
						'type'    => Controls_Manager::MEDIA,
						'name'      => 'image',
						'label'   => __( 'Thumbnail', 'classima-core' ),
					),
				),
			),
			array(
				'mode' => 'section_end',
			),

			// Slider options
			array(
				'mode'        => 'section_start',
				'id'          => 'sec_slider',
				'label'       => __( 'Slider Options', 'classima-core' ),
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
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'slider_interval',
				'label'   => __( 'Autoplay Interval', 'classima-core' ),
				'options' => array(
					'5000' => __( '5 Seconds', 'classima-core' ),
					'4000' => __( '4 Seconds', 'classima-core' ),
					'3000' => __( '3 Seconds', 'classima-core' ),
					'2000' => __( '2 Seconds', 'classima-core' ),
					'1000' => __( '1 Second',  'classima-core' ),
				),
				'default' => '5000',
				'description' => __( 'Set any value for example 5 seconds to play it in every 5 seconds. Default: 5 Seconds', 'classima-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'slider_autoplay_speed',
				'label'   => __( 'Autoplay Slide Speed', 'classima-core' ),
				'default' => 200,
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
				'mode'    => 'section_start',
				'id'      => 'sec_style',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Style', 'classima-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'name_color',
				'label'   => __( 'Name Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-name' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'designation_color',
				'label'   => __( 'Designation Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-designation' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'content_color',
				'label'   => __( 'Content Color', 'classima-core' ),
				'selectors' => array( '{{WRAPPER}} .rtin-content' => 'color: {{VALUE}}' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'name_typo',
				'label'    => __( 'Name Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-name',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'designation_typo',
				'label'    => __( 'Designation Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-designation',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'content_typo',
				'label'    => __( 'Content Typography', 'classima-core' ),
				'selector' => '{{WRAPPER}} .rtin-content',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();

		$owl_data = array( 
			'nav'                => true,
			'navText'            => array( "<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>" ),
		);

		$owl_data = [
			"navigation"        => [
				"nextEl"            => ".rt-el-testimonial-nav .swiper-button-next",
				"prevEl"            => ".rt-el-testimonial-nav .swiper-button-prev",
			],
			"loop"              => $data['slider_loop'] == 'yes',
			"speed"             => isset($data['slider_autoplay_speed']) ? $data['slider_autoplay_speed'] : 200,
			"spaceBetween"      => 20,
			"breakpoints"       => [
				0   => [
					"slidesPerView" => 1
				]
			]
		];

		if ($data['slider_autoplay']) {
			$owl_data["autoplay"] = [
				"delay" => $data['slider_interval'],
				"disableOnInteraction"  => false,
				"pauseOnMouseEnter"     => $data['slider_stop_on_hover']
			];
		}

		$data['owl_data'] = json_encode( $owl_data );

		$template = 'view';

		$this->rt_template( $template, $data );

		$this->edit_mode_script();

	}
}