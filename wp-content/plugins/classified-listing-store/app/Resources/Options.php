<?php

namespace RtclStore\Resources;

class Options {

	static function get_store_orderby_options() {
		$options = [
			'title-desc' => esc_html__( "Z to A ( title )", 'classified-listing-store' ),
			'title-asc'  => esc_html__( "A to Z ( title )", 'classified-listing-store' ),
			'date-desc'  => esc_html__( "Recently added ( latest )", 'classified-listing-store' ),
			'date-asc'   => esc_html__( "Date added ( oldest )", 'classified-listing-store' )
		];

		return apply_filters( 'rtcl_store_orderby_options', $options );
	}

	static function store_social_media_options() {
		$options = [
			'facebook' => __( "Facebook", 'classified-listing-store' ),
			'twitter'  => __( "Twitter", 'classified-listing-store' ),
			'youtube'  => __( "Youtube", 'classified-listing-store' ),
			'linkedin' => __( "LinkedIn", 'classified-listing-store' )
		];

		return apply_filters( 'rtcl_store_social_media_options', $options );
	}

	public static function store_open_hour_days() {
		global $wp_locale;

		$weekStart = apply_filters( 'rtcl_start_of_week', get_option( 'start_of_week' ) );
		$weekday   = $wp_locale->weekday;
		for ( $i = 0; $i < $weekStart; $i ++ ) {
			$day = array_slice( $weekday, 0, 1, true );
			unset( $weekday[ $i ] );

			$weekday = $weekday + $day;
		}

		$days = [];

		foreach ( $weekday as $value ) {
			$key          = strtolower( $value );
			$days[ $key ] = $value;
		}

		return apply_filters( 'rtcl_store_open_hour_days', $days );
	}

	static function store_search_widget_fields() {
		$fields = [
			'title'              => [
				'label' => esc_html__( 'Title', 'classified-listing' ),
				'type'  => 'text'
			],
			'style'              => [
				'label'   => esc_html__( 'Style', 'classified-listing' ),
				'type'    => 'radio',
				'options' => [
					'vertical' => esc_html__( 'Vertical', 'classified-listing' ),
					'inline'   => esc_html__( 'inline', 'classified-listing' )
				]
			],
			'search_by_keyword'  => [
				'label' => esc_html__( 'Search by Keyword', 'classified-listing' ),
				'type'  => 'checkbox'
			],
			'search_by_category' => [
				'label' => esc_html__( 'Search by Category', 'classified-listing' ),
				'type'  => 'checkbox'
			]
		];

		return apply_filters( 'rtcl_widget_store_search_fields', $fields );
	}

}