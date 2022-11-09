<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Google_Map extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Google Map', 'classima-core' );
		$this->rt_base = 'rt-google-map';
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
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'iframe',
				'label'   => __( 'Google Map Code', 'classima-core' ),
				'default' => '',
				'description' => sprintf( __( 'To create your own google map, follow the steps below:<br/>Step 1: Visit %s<br/>Step 2: Search for a location using the search bar of the top-left corner<br/>Step 3: After you find the location, click on the "Share" icon from the left panel<br/>Step 4: A popup will come up. From there go to the "Embed map" tab. You will find your Google Map code. Copy and paste this code in here', 'classima-core' ), '<a href="https://www.google.com/maps" target="_blank">https://www.google.com/maps</a>' ),
			),
			array(
				'type'    => Controls_Manager::SLIDER,
				'id'      => 'height',
				'label'   => __( 'Height', 'classima-core' ),
				'range'   => array(
					'px' => array(
						'min' => 40,
						'max' => 800,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 400,
				),
				'selectors' => array( '{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}}' ),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}