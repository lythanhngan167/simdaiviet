<?php 

if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Media Settings
 */

$image_type = apply_filters( 'rtrs_media_image_type', array(
	'image/jpg' => esc_html__('jpg', 'review-schema'), 
	'image/jpeg' => esc_html__('jpeg', 'review-schema'), 
	'image/png' => esc_html__('png', 'review-schema'), 
	'image/gif' => esc_html__('gif', 'review-schema'),  
) ); 

$options = array(
	'img_section' => array(
		'title'  => esc_html__( 'Image Upload Settings', 'review-schema' ),
		'type'   => 'title', 
	), 
	'img_max_size'  => array(
        'title'   => esc_html__('Image Max Size', 'review-schema'),
        'type'    => 'number',  
        'default' => 1024,  
		'css'     => 'width: 70px',
		'description' => esc_html__('Change the value as KB, Like 1M = 1024KB', 'review-schema')
    ), 
	'img_type' => array(
		'title'   => esc_html__( 'Supported Image Type', 'review-schema' ),
		'type'    => 'multi_checkbox',
		'default' => array(
			'image/jpg',
			'image/jpeg',
			'image/png',
			'image/gif'  
		),
		'options' => $image_type
	), 
);

return apply_filters( 'rtrs_media_settings_options', $options );