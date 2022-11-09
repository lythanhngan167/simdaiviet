<?php

namespace RtclStore\Shortcodes;

use Rtcl\Helpers\Functions;

class MembershipPricingTable {

	public static function output( $atts )
	{
		$settings                 = shortcode_atts( array(
			'posts_per_page' => - 1,
			'orderby'        => 'menu_order',
			'order'          => 'asc',
			'item_per_row'   => 4,
			'include'        => '',
			'exclude'        => '',
			'class'          => array() // class="884:test_mode|another_class,56:test-apple"
		), $atts );
		$settings['item_per_row'] = ( isset( $settings['item_per_row'] ) && in_array( $settings['item_per_row'], array(
			1,
			2,
			3,
			4,
			6
		) ) ? abs( $settings['item_per_row'] ) : $settings['item_per_row'] );
		$args                     = array(
			'post_type'        => rtcl()->post_type_pricing,
			'post_status'      => 'publish',
			'posts_per_page'   => ( isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : - 1 ),
			'orderby'          => 'menu_order',
			'order'            => 'asc',
			'no_found_rows'    => true,
			'meta_query'       => array(
				array(
					'key'   => 'pricing_type',
					'value' => 'membership',
				)
			),
			'suppress_filters' => false
		);

		if ( ! empty( $settings['include'] ) ) {
			$settings['include'] = explode( ',', $settings['include'] );
			$args['include']     = $settings['include'];
		}
		if ( ! empty( $settings['exclude'] ) ) {
			$settings['exclude'] = explode( ',', $settings['exclude'] );
			$args['exclude']     = $settings['exclude'];
		}

		if ( ! empty( $settings['class'] ) ) {
			$class      = array();
			$classArray = explode( ',', $settings['class'] );
			foreach ( $classArray as $classA ) {
				$temp = explode( ':', $classA );
				if ( is_array( $temp ) && count( $temp ) == 2 ) {
					$class[ absint( $temp[0] ) ] = explode( '|', $temp[1] );;
				}
			}
			$settings['class'] = $class;
		}
		$payment_options = get_posts( $args );
		wp_enqueue_style('rtcl-store-public');
		Functions::get_template( "store/membership-pricing-table", compact( 'payment_options', 'settings' ) , '', rtclStore()->get_plugin_template_path());

	}
}
