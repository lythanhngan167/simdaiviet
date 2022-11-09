<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for migration
 */
$options = array(
	'migration_info' => array(
		'title'       => esc_html__( 'Migration', 'review-schema' ),
        'description'   => esc_html__('Import data from others schema plugin', 'review-schema'),
		'type'        => 'title', 
    ), 
	'wp_seo_schema'  => array(
        'title'   => esc_html__('WP SEO Schema', 'review-schema'),
        'type'    => 'button'
    ),
	'schema'  => array(
        'title'   => esc_html__('Schema Plugin', 'review-schema'),
        'type'    => 'button' 
    )
);

return apply_filters( 'rtrs_schema_migration_settings_options', $options );
