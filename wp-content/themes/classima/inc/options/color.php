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
        'title'   => esc_html__( 'Colors', 'classima' ),
        'id'      => 'color_section',
        'heading' => '',
        'icon'    => 'el el-eye-open',
        'fields'  => array(
            array(
                'id'       => 'section-color-sitewide',
                'type'     => 'section',
                'title'    => esc_html__( 'Sitewide Colors', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'primary_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Primary Color', 'classima' ),
                'default'  => '#f85c70',
            ),
            array(
                'id'       => 'secondary_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Secondary Color', 'classima' ),
                'default'  => '#e43d40',
            ),
            array(
                'id'       => 'sitewide_color',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Other Colors', 'classima' ),
                'options'  => array(
                    'primary' => esc_html__( 'Primary Color', 'classima' ),
                    'custom'  => esc_html__( 'Custom', 'classima' ),
                ),
                'default'  => 'primary',
                'subtitle' => esc_html__( 'Selecting Primary Color will hide some color options from the below settings and replace them with Primary/Secondery Color', 'classima' ),
            ),
            array(
                'id'       => 'section-color-topbar',
                'type'     => 'section',
                'title'    => esc_html__( 'Top Bar', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'top_bar_bgcolor',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Top Bar Background Color', 'classima' ),
                'default'  => '#1e3148',
            ),
            array(
                'id'       => 'section-color-menu',
                'type'     => 'section',
                'title'    => esc_html__( 'Main Menu', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'menu_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Menu Color', 'classima' ),
                'default'  => '#2a2a2a',
            ),
            array(
                'id'       => 'menu_hover_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Menu Hover Color', 'classima' ),
                'default'  => '#f85c70', // primary
                'required' => array( 'sitewide_color', '=', 'custom' )
            ),
            array(
                'id'       => 'section-color-submenu',
                'type'     => 'section',
                'title'    => esc_html__( 'Sub Menu', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'submenu_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Submenu Color', 'classima' ),
                'default'  => '#444444',
            ),
            array(
                'id'       => 'submenu_hover_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Submenu Hover Color', 'classima' ),
                'default'  => '#ffffff',
            ), 
            array(
                'id'       => 'submenu_hover_bgcolor',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Submenu Hover Background Color', 'classima' ),
                'default'  => '#f85c70', // primary
                'required' => array( 'sitewide_color', '=', 'custom' )
            ),
            array(
                'id'       => 'section-color-banner',
                'type'     => 'section',
                'title'    => esc_html__( 'Breadcrumb', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'breadcrumb_link_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Breadcrumb Link Color', 'classima' ),
                'default'  => '#ffffff',
            ),
            array(
                'id'       => 'breadcrumb_link_hover_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Breadcrumb Link Hover Color', 'classima' ),
                'default'  => '#f85c70',  // primary
                'required' => array( 'sitewide_color', '=', 'custom' )
            ),
            array(
                'id'       => 'breadcrumb_active_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Active Breadcrumb Color', 'classima' ),
                'default'  => '#cacaca',
            ),
            array(
                'id'       => 'breadcrumb_seperator_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Breadcrumb Seperator Color', 'classima' ),
                'default'  => '#ffffff',
            ),
            array(
                'id'       => 'section-color-footer',
                'type'     => 'section',
                'title'    => esc_html__( 'Footer Area', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'footer_bgcolor',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Footer Background Color', 'classima' ),
                'default'  => '#1a1a1a',
            ), 
            array(
                'id'       => 'footer_title_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Footer Title Text Color', 'classima' ),
                'default'  => '#ffffff',
            ), 
            array(
                'id'       => 'footer_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Footer Body Text Color', 'classima' ),
                'default'  => '#a5a5a5',
            ), 
            array(
                'id'       => 'footer_link_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Footer Body Link Color', 'classima' ),
                'default'  => '#a5a5a5',
            ), 
            array(
                'id'       => 'footer_link_hover_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Footer Body Link Hover Color', 'classima' ),
                'default'  => '#f85c70',   // primary
                'required' => array( 'sitewide_color', '=', 'custom' )
            ),
            array(
                'id'       => 'section-color-copyright',
                'type'     => 'section',
                'title'    => esc_html__( 'Copyright Area', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'copyright_bgcolor',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Copyright Background Color', 'classima' ),
                'default'  => '#111212',
            ),
            array(
                'id'       => 'copyright_color',
                'type'     => 'color',
                'transparent' => false,
                'title'    => esc_html__( 'Copyright Text Color', 'classima' ),
                'default'  => '#ababab',
            ),
        )
    )
);