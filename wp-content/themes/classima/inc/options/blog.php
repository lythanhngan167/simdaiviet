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
        'title'   => esc_html__( 'Blog Settings', 'classima' ),
        'id'      => 'blog_settings_section',
        'icon'    => 'el el-tags',
        'heading' => '',
        'fields'  => array(
            array(
                'id'       =>'blog_style',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Blog/Archive Layout', 'classima' ),
                'default'  => 'style1',
                'options'  => array(
                    'style1' => array(
                        'title' => '<b>'. esc_html__( 'Layout 1', 'classima' ) . '</b>',
                        'img'   => Helper::get_img( 'blog1.jpg' ),
                    ),
                    'style2' => array(
                        'title' => '<b>'. esc_html__( 'Layout 2', 'classima' ) . '</b>',
                        'img'   => Helper::get_img( 'blog2.jpg' ),
                    ),
                ),
            ),
            array(
                'id'       => 'blog_date',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display Date', 'classima' ),
                'on'       => esc_html__( 'On', 'classima' ),
                'off'      => esc_html__( 'Off', 'classima' ),
                'default'  => true,
            ), 
            array(
                'id'       => 'blog_author_name',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display Author Name', 'classima' ),
                'on'       => esc_html__( 'On', 'classima' ),
                'off'      => esc_html__( 'Off', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'blog_comment_num',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display Comment Number', 'classima' ),
                'on'       => esc_html__( 'On', 'classima' ),
                'off'      => esc_html__( 'Off', 'classima' ),
                'default'  => true,
            ),
            array(
                'id'       => 'blog_cats',
                'type'     => 'switch',
                'title'    => esc_html__( 'Display Categories', 'classima' ),
                'on'       => esc_html__( 'On', 'classima' ),
                'off'      => esc_html__( 'Off', 'classima' ),
                'default'  => false,
            ),
            array(
                'id'      => 'blog_button',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Button', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => false,
            ),
        )
    ) 
);