<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for Third Party Plugin
 */
$options = array(
	'tpp_section' => array(
		'title'       => esc_html__( 'Third party plugin settings', 'review-schema' ),
		'type'        => 'title', 
	), 
	'wc_schema'  => array(
        'title'   => esc_html__('WooCommerce default schema', 'review-schema'),
        'type'    => 'checkbox',
        'default' => '',
        'label'   => esc_html__('This will disable WooCommerce plugin default schema.', 'review-schema')
    ),
	'edd_schema'  => array(
        'title'   => esc_html__('Easy Digital Download default schema', 'review-schema'),
        'type'    => 'checkbox',
        'default' => '',
        'label'   => esc_html__('This will disable Easy Digital Download plugin default schema.', 'review-schema')
    ), 
    'yoast_search_schema'  => array(
        'title'   => esc_html__('Yoast SEO sitelinks searchbox', 'review-schema'),
        'type'    => 'checkbox',
        'default' => '',
        'label'   => esc_html__('This will disable sitelinks searchbox default by Yoast SEO plugin.', 'review-schema')
    ), 
	'yoast_schema'  => array(
        'title'   => esc_html__('Yoast SEO Default Schema JSON-LD', 'review-schema'),
        'type'    => 'checkbox',
        'default' => '',
        'label'   => esc_html__('This will disable all default schema default by Yoast SEO plugin.', 'review-schema')
    )
);

return apply_filters( 'rtrs_schema_tpp_settings_options', $options );
