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
        'title'   => esc_html__( 'Footer', 'classima' ),
        'id'      => 'footer_section',
        'heading' => '',
        'icon'    => 'el el-caret-down',
        'fields'  => array(
	        array(
		        'id'       => 'footer_style',
		        'type'     => 'image_select',
		        'title'    => esc_html__( 'Footer Style', 'classima' ),
		        'default'  => '1',
		        'options' => array(
			        '1' => array(
				        'title' => '<b>'. esc_html__( 'Style 1', 'classima' ) . '</b>',
				        'img' => Helper::get_img( 'footer-1.png' ),
			        ),
			        '2' => array(
				        'title' => '<b>'. esc_html__( 'Style 2', 'classima' ) . '</b>',
				        'img' => Helper::get_img( 'footer-2.png' ),
			        )
		        ),
	        ),
            array(
                'id'       => 'section-copyright-area',
                'type'     => 'section',
                'title'    => esc_html__( 'Copyright Area', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'copyright_area',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display Copyright Area', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'copyright_text',
                'type'     => 'textarea',
                'title'    => esc_html__( 'Copyright Text', 'classima' ),
                'default'  => sprintf( '&copy; Copyright Classima %s. Designed and Developed by <a target="_blank" href="%s">RadiusTheme</a>' , date('Y'), esc_url( Constants::$theme_author_uri ) ),
                'required' => array( 'copyright_area', 'equals', true )
            ),
            array(
                'id'       => 'payment_icons',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display Payment Icons', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => false,
                'required' => array( 'copyright_area', 'equals', true )
            ),
            array(
                'id'       => 'payment_img',
                'type'     => 'gallery',
                'title'    => esc_html__( 'Payment Icons Gallery', 'classima' ),
                'required' => array( 'payment_icons', 'equals', true )
            ),
        )
    )
);