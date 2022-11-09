<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

namespace radiustheme\Classima;

use \Redux;

$opt_name = Constants::$theme_options;

Redux::setSection( $opt_name,
    array(
        'title'   => esc_html__( 'General', 'classima' ),
        'id'      => 'general_section',
        'heading' => '',
        'icon'    => 'el el-network',
        'fields'  => array(
            array(
                'id'       => 'logo',
                'type'     => 'media',
                'title'    => esc_html__( 'Main Logo', 'classima' ),
                'default'  => array(
                    'url'=> Helper::get_img( 'logo-dark.png' )
                ),
            ),
            array(
                'id'       => 'logo_light',
                'type'     => 'media',
                'title'    => esc_html__( 'Light Logo', 'classima' ),
                'default'  => array(
                    'url'=> Helper::get_img( 'logo-light.png' )
                ),
                'subtitle' => esc_html__( 'Used when Transparent Header is enabled', 'classima' ),
            ),
            array(
                'id'       => 'logo_width',
                'type'     => 'select',
                'title'    => esc_html__( 'Logo Area Width', 'classima'), 
                'subtitle' => esc_html__( 'Width is defined by the number of bootstrap columns. Please note, navigation menu width will be decreased with the increase of logo width', 'classima' ),
                'options'  => array(
                    '1' => esc_html__( '1 Column', 'classima' ),
                    '2' => esc_html__( '2 Column', 'classima' ),
                    '3' => esc_html__( '3 Column', 'classima' ),
                    '4' => esc_html__( '4 Column', 'classima' ),
                ),
                'default'  => '2',
            ),
            array(
                'id'       => 'breadcrumb',
                'type'     => 'switch',
                'title'    => esc_html__( 'Breadcrumb', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'time_format',
                'type'     => 'switch',
                'title'    => esc_html__( 'Time Format', 'classima' ),
                'on'       => esc_html__( '12 Hour', 'classima' ),
                'off'      => esc_html__( '24 Hour', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'preloader',
                'type'     => 'switch',
                'title'    => esc_html__( 'Preloader', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'preloader_image',
                'type'     => 'media',
                'title'    => esc_html__( 'Preloader Image', 'classima' ),
                'subtitle' => esc_html__( 'Please upload your choice of preloader image. Transparent GIF format is recommended', 'classima' ),
                'default'  => array(
                    'url'=> Helper::get_img( 'preloader.gif' )
                ),
                'required' => array( 'preloader', 'equals', true )
            ),
            array(
                'id'       => 'container_width',
                'type'     => 'slider',
                'title'    => esc_html__( 'Container Width', 'classima' ),
                'default'  => 1200,
                'min'      => 0,
                'step'     => 1,
                'max'      => 2000,
            ),
            array(
                'id'       => 'back_to_top',
                'type'     => 'switch',
                'title'    => esc_html__( 'Back to Top Arrow', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'restrict_admin_area',
                'type'     => 'switch',
                'title'    => esc_html__( 'Hide Admin Bar', 'classima' ),
                'subtitle' => esc_html__( 'Hide Admin Bar for subscribers', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
        )            
    ) 
);