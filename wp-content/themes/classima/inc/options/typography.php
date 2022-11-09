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
        'title'  => esc_html__( 'Typography', 'classima' ),
        'id'     => 'typo_section',
        'icon'   => 'el el-text-width',
        'fields' => array(
            array(
                'id'       => 'typo_body',
                'type'     => 'typography',
                'title'    => esc_html__( 'Body', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'   => false,
                'subsets'  => false,
                'default' => array(
                    'google'      => true,
                    'font-family' => 'Roboto',
                    'font-weight' => '400',
                    'font-size'   => '16px',
                    'line-height' => '28px',
                ),
            ),
            array(
                'id'       => 'typo_h1',
                'type'     => 'typography',
                'title'    => esc_html__( 'Header h1', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'    => false,
                'subsets'  => false,
                'default'  => array(
                    'google'      => true,
                    'font-family' => 'Nunito',
                    'font-weight' => '700',
                    'font-size'   => '36px',
                    'line-height' => '40px',
                ),
            ),
            array(
                'id'       => 'typo_h2',
                'type'     => 'typography',
                'title'    => esc_html__( 'Header h2', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'   => false,
                'subsets'  => false,
                'default' => array(
                    'google'      => true,
                    'font-family' => 'Nunito',
                    'font-weight' => '700',
                    'font-size'   => '28px',
                    'line-height' => '34px',
                ),
            ),
            array(
                'id'       => 'typo_h3',
                'type'     => 'typography',
                'title'    => esc_html__( 'Header h3', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'   => false,
                'subsets' => false,
                'default' => array(
                    'google'      => true,
                    'font-family' => 'Nunito',
                    'font-weight' => '700',
                    'font-size'   => '22px',
                    'line-height' => '28px',
                ),
            ),
            array(
                'id'       => 'typo_h4',
                'type'     => 'typography',
                'title'    => esc_html__( 'Header h4', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'   => false,
                'subsets'  => false,
                'default' => array(
                    'google'      => true,
                    'font-family' => 'Nunito',
                    'font-weight' => '700',
                    'font-size'   => '20px',
                    'line-height' => '28px',
                ),
            ),
            array(
                'id'       => 'typo_h5',
                'type'     => 'typography',
                'title'    => esc_html__( 'Header h5', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'   => false,
                'subsets'  => false,
                'default' => array(
                    'google'      => true,
                    'font-family' => 'Nunito',
                    'font-weight' => '700',
                    'font-size'   => '18px',
                    'line-height' => '26px',
                ),
            ),
            array(
                'id'       => 'typo_h6',
                'type'     => 'typography',
                'title'    => esc_html__( 'Header h6', 'classima' ),
                'text-align'  => false,
                'font-weight' => false,
                'color'   => false,
                'subsets'  => false,
                'default' => array(
                    'google'      => true,
                    'font-family' => 'Nunito',
                    'font-weight' => '700',
                    'font-size'   => '16px',
                    'line-height' => '28px',
                ),
            ),
            array(
                'id'       => 'section-mainmenu',
                'type'     => 'section',
                'title'    => esc_html__( 'Main Menu Items', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'menu_typo',
                'type'     => 'typography',
                'title'    => esc_html__( 'Menu Font', 'classima' ),
                'text-align' => false,
                'color'   => false,
                'subsets'  => false,
                'text-transform' => true,
                'default'     => array(
                    'google'      => true,
                    'font-family' => 'Roboto',
                    'font-weight' => '400',
                    'font-size'   => '16px',
                    'line-height' => '26px',
                    'text-transform' => 'none',
                ),
            ),
            array(
                'id'       => 'section-submenu',
                'type'     => 'section',
                'title'    => esc_html__( 'Sub Menu Items', 'classima' ),
                'indent'   => true,
            ), 
            array(
                'id'       => 'submenu_typo',
                'type'     => 'typography',
                'title'    => esc_html__( 'Submenu Font', 'classima' ),
                'text-align'   => false,
                'color'   => false,
                'subsets'  => false,
                'text-transform' => true,
                'default'     => array(
                    'google'      => true,
                    'font-family' => 'Roboto',
                    'font-weight' => '400',
                    'font-size'   => '14px',
                    'line-height' => '24px',
                    'text-transform' => 'none',
                ),
            ),
            array(
                'id'       => 'section-resmenu',
                'type'     => 'section',
                'title'    => esc_html__( 'Mobile Menu', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       => 'resmenu_typo',
                'type'     => 'typography',
                'title'    => esc_html__( 'Mobile Menu Font', 'classima' ),
                'text-align' => false,
                'color'   => false,
                'subsets'  => false,
                'text-transform' => true,
                'default'     => array(
                    'google'      => true,
                    'font-family' => 'Roboto',
                    'font-weight' => '400',
                    'font-size'   => '16px',
                    'line-height' => '24px',
                    'text-transform' => 'none',
                ),
            ),
        )
    )
);