<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use \Redux;

$opt_name = Constants::$theme_options;

Redux::setSection( $opt_name,
    array(
        'title'   => esc_html__( 'Error Page Settings', 'classima' ),
        'id'      => 'error_settings_section',
        'heading' => '',
        'icon'    => 'el el-error-alt',
        'fields'  => array( 
            array(
                'id'       => 'error_bodybanner',
                'type'     => 'media',
                'title'    => esc_html__( 'Featured Image', 'classima' ),
                'default'  => array(
                    'url'=> Helper::get_img( '404.png' )
                ),
            ), 
            array(
                'id'       => 'error_text',
                'type'     => 'text',
                'title'    => esc_html__( 'Error Text', 'classima' ),
                'default'  => esc_html__( "We can't find the page you're looking for", 'classima' ),
            ), 
            array(
                'id'       => 'error_buttontext',
                'type'     => 'text',
                'title'    => esc_html__( 'Button Text', 'classima' ),
                'default'  => esc_html__( 'Go To Home Page', 'classima' ),
            )
        )
    )
);