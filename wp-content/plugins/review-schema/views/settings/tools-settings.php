<?php

if ( !defined('ABSPATH') ) exit;

use Rtrs\Helpers\Functions;

/**
 * Tools Settings
 */

$options = array(
    'site_section' => array(
        'title'       => esc_html__('Licensing', 'review-schema'),
        'type'        => 'title',
    ),
    'license_key' => array( 
        'title'    => esc_html__('Main plugin license key', "review-schema"), 
        'type'     => 'text',
        'class'    => 'regular-text', 
    ),   
);

return apply_filters('rtrs_tools_settings_options', $options);
