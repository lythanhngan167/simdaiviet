<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for social profiles Info
 */
$options = array( 
    'social_profile' => array(
		'title'       => esc_html__( 'Social Profiles', 'review-schema' ),
        'description'   => esc_html__('Provide your social profile information to a Google Knowledge panel', 'review-schema'),
		'type'        => 'title', 
    ),
    'social_profiles' => array(
        "type"   => "group",
        "is_pro" => false,
        "title"  => esc_html__("Social URL", 'review-schema'),  
        "fields" => [
            'url' => array(
                'type'  => 'text',
                'class' => 'regular-text',
                'placeholder' => 'https://facebook.com/radiustheme',
                'title' => esc_html__('Social URL', "review-schema"),
            ), 
        ]
    )
);

return apply_filters( 'rtrs_schema_social_profiles_settings_options', $options );
