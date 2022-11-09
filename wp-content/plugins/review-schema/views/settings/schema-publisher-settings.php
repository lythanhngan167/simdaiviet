<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for Publisher Info
 */
$options = array(
	'publisher_info' => array(
		'title'       => esc_html__( 'Publisher Information', 'review-schema' ),
        'description'   => esc_html__('Publisher info is need for some schema category. Like: Product, NewsArticle etc', 'review-schema'),
		'type'        => 'title', 
    ), 
	'publisher_name'  => array(
        'title'   => esc_html__('Publisher name', 'review-schema'),
        'type'    => 'text',
        'class'   => 'regular-text', 
    ),
    'publisher_logo'  => array(
        'title'   => esc_html__('Publisher logo', 'review-schema'),
        'type'    => 'image', 
    )
);

return apply_filters( 'rtrs_schema_publisher_settings_options', $options );
