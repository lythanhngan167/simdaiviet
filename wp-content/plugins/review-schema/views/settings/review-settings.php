<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * General Settings
 */
use Rtrs\Helpers\Functions;

$post_types = Functions::getPostTypes();
array_shift($post_types);

$options = array(
    'gl_section' => array(
		'title'   => esc_html__( 'Google Captcha v3', 'review-schema' ),
		'type'    => 'title', 
    ), 
    'recaptcha_sitekey' => array(
        "type"    => "text", 
        'class'       => 'regular-text',
        "title"   => esc_html__("Recaptcha site key", 'review-schema'),  
		'description'  => wp_kses( __("How to get <a target='_blank' href='https://www.radiustheme.com/docs/review-schema/faq/how-to-add-google-captcha-v3-api-key/'>Recaptcha site & secret key?</a>", "review-schema"), [ 'a' => [ 'href' => [], 'target' => [] ] ] ),
    ), 
    'recaptcha_secretkey' => array(
        "type"    => "password",
        'class'       => 'regular-text',
        "title"   => esc_html__("Recaptcha secret key", 'review-schema'), 
    ),
    'review_multiple_section' => array(
		'title'   => esc_html__( 'Multiple Review Submission', 'review-schema' ),
		'type'    => 'title',  
    ), 
    'multiple_review'  => array(
        'title'   => esc_html__('Multiple review', 'review-schema'),
        'label'   => esc_html__('Allow', 'review-schema'),
        'type'    => 'checkbox',
        'default' => '',
    ),
    'review_edit_section' => array(
		'title'   => esc_html__( 'Edit Review Frontend', 'review-schema' ),
		'type'    => 'title', 
		'description'   => esc_html__( 'Which field you want to allow to edit for user', 'review-schema' ),
    ), 
    'review_edit'  => array(
        'title'   => esc_html__('Edit Review', 'review-schema'),
        'label'   => esc_html__('Allow', 'review-schema'),
        'type'    => 'checkbox',
        'default' => 'yes',
    ),
    'review_edit_field' => array(
		'title'   => esc_html__( 'Review edit field', 'review-schema' ),
		'type'    => 'multi_checkbox',
		'default' => array(
			'rating',
			'desc', 
		),
		'options' => array(
            'rating' => esc_html__('Rating', 'review-schema'),  
            'desc' => esc_html__('Description', 'review-schema'),  
            'title' => esc_html__('Title', 'review-schema'),  
            'pros_cons' => esc_html__('Pros & Cons', 'review-schema'),  
            'image' => esc_html__('Image', 'review-schema'),  
            'video' => esc_html__('Video', 'review-schema'),  
            //TODO: do it later
            // 'recommendation' => esc_html__('Recommendation', 'review-schema'),  
            'anonymous' => esc_html__('Anonymous Review', 'review-schema'),  
        )
	),
);

return apply_filters( 'rtrs_review_settings_options', $options );