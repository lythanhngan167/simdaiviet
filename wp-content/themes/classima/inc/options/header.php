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
        'title'   => esc_html__( 'Header', 'classima' ),
        'id'      => 'header_section',
        'heading' => '',
        'icon'    => 'el el-flag',
        'fields'  => array(
            array(
                'id'       => 'resmenu_width',
                'type'     => 'slider',
                'title'    => esc_html__( 'Responsive Header Screen Width', 'classima' ),
                'subtitle' => esc_html__( 'Screen width in which mobile menu activated. Recommended value is: 992', 'classima' ),
                'default'  => 992,
                'min'      => 0,
                'step'     => 1,
                'max'      => 2000,
            ),
            array(
                'id'       => 'top_bar',
                'type'     => 'switch',
                'title'    => esc_html__( 'Top Bar', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => false,
            ),
	        array(
		        'id'       => 'language_swatch',
		        'type'     => 'switch',
		        'title'    => esc_html__( 'WPML Language Switchers', 'classima' ),
		        'on'       => esc_html__( 'Enabled', 'classima' ),
		        'off'      => esc_html__( 'Disabled', 'classima' ),
		        'default'  => false,
		        'required' => array( 'top_bar', 'equals', true )
	        ),
            array(
                'id'       => 'sticky_menu',
                'type'     => 'switch',
                'title'    => esc_html__( 'Sticky Header', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
                'subtitle' => esc_html__( 'Show header at the top when scrolling down', 'classima' ),
            ),
            array(
                'id'       => 'tr_header',
                'type'     => 'switch',
                'title'    => esc_html__( 'Transparent Header', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => false,
                'subtitle' => esc_html__( 'You have to enable Banner or Slider in page to make it work properly', 'classima' ),
            ),
            array(
                'id'       => 'header_style',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Header Layout', 'classima' ),
                'default'  => '1',
                'options' => array(
                    '1' => array(
                        'title' => '<b>'. esc_html__( 'Layout 1', 'classima' ) . '</b>',
                        'img' => Helper::get_img( 'header-1.png' ),
                    ),
                    '2' => array(
                        'title' => '<b>'. esc_html__( 'Layout 2', 'classima' ) . '</b>',
                        'img' => Helper::get_img( 'header-2.png' ),
                    ),
                    '3' => array(
                        'title' => '<b>'. esc_html__( 'Layout 3', 'classima' ) . '</b>',
                        'img' => Helper::get_img( 'header-3.png' ),
                    ),
                    '4' => array(
                        'title' => '<b>'. esc_html__( 'Layout 4', 'classima' ) . '</b>',
                        'img' => Helper::get_img( 'header-4.png' ),
                    ),
                    '5' => array(
	                    'title' => '<b>'. esc_html__( 'Layout 5', 'classima' ) . '</b>',
	                    'img' => Helper::get_img( 'header-5.png' ),
                    ),
                    '6' => array(
	                    'title' => '<b>'. esc_html__( 'Layout 6', 'classima' ) . '</b>',
	                    'img' => Helper::get_img( 'header-6.png' ),
                    )
                ),
            ),
            array(
                'id'       => 'header_icon',
                'type'     => 'switch',
                'title'    => esc_html__( 'Header Login Icon', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'header_chat_icon',
                'type'     => 'switch',
                'title'    => esc_html__( 'Header Chat Icon', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
	        array(
		        'id'       => 'header_menu',
		        'type'     => 'switch',
		        'title'    => esc_html__( 'Header Menu', 'classima' ),
		        'subtitle' => esc_html__( 'Used on only header layout 5', 'classima' ),
		        'on'       => esc_html__( 'Enabled', 'classima' ),
		        'off'      => esc_html__( 'Disabled', 'classima' ),
		        'default'  => false,
	        ),
            array(
                'id'       => 'header_btn_txt',
                'type'     => 'text',
                'title'    => esc_html__( 'Header Button Text', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'header_btn_txt_mob',
                'type'     => 'text',
                'title'    => esc_html__( 'Header Button Text(Mobile)', 'classima' ),
                'subtitle' => esc_html__( 'Used on mobile menu', 'classima' ),
                'default'  => 'Post Ad',
            ),
            array(
                'id'       => 'header_btn_url',
                'type'     => 'text',
                'title'    => esc_html__( 'Header Button URL', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'banner',
                'type'     => 'switch',
                'title'    => esc_html__( 'Banner', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'breadcrumb',
                'type'     => 'switch',
                'title'    => esc_html__( 'Breadcrumb', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => true,
                'required' => array( 'banner', 'equals', true )
            ),
            array(
                'id'       => 'banner_search',
                'type'     => 'switch',
                'title'    => esc_html__( 'Banner Search', 'classima' ),
                'on'       => esc_html__( 'Enabled', 'classima' ),
                'off'      => esc_html__( 'Disabled', 'classima' ),
                'default'  => false,
                'required' => array( 'banner', 'equals', true )
            ),
            array(
                'id'       => 'bgtype',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Banner Background Type', 'classima' ),
                'options'  => array(
                    'bgimg'    => esc_html__( 'Background Image', 'classima' ),
                    'bgcolor'  => esc_html__( 'Background Color', 'classima' ),
                ),
                'default' => 'bgcolor',
                'required' => array( 'banner', 'equals', true )
            ),
            array(
                'id'       => 'bgimg',
                'type'     => 'media',
                'title'    => esc_html__( 'Banner Background Image', 'classima' ),
                'default'  => array(
                    'url'=> Helper::get_img( 'banner.jpg' )
                ),
                'required' => array( 'bgtype', 'equals', 'bgimg' )
            ), 
            array(
                'id'       => 'bgcolor',
                'type'     => 'color',
                'title'    => esc_html__( 'Banner Background Color', 'classima'), 
                'validate' => 'color',
                'transparent' => false,
                'default' => '#606060',
                'required' => array( 'bgtype', 'equals', 'bgcolor' )
            ),
            array(
                'id'       => 'bgopacity',
                'type'     => 'slider',
                'title'    => esc_html__( 'Banner Background Opacity (in %)', 'classima' ),
                'min'      => 0,
                'max'      => 100,
                'step'     => 1,
                'default'  => 60,
                'display_value' => 'label'
            ), 
        )
    ) 
);