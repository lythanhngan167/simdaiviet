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
        'title'   => esc_html__( 'Contact & Socials', 'classima' ),
        'id'      => 'social_settings_section',
        'heading' => '',
        'icon'    => 'el el-twitter',
        'fields'  => array( 
            array(
                'id'       => 'phone',
                'type'     => 'text',
                'title'    => esc_html__( 'Phone', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'email',
                'type'     => 'text',
                'title'    => esc_html__( 'Email', 'classima' ),
                'validate' => 'email',
                'default'  => '',
            ),
            array(
                'id'       => 'address',
                'type'     => 'textarea',
                'title'    => esc_html__( 'Address', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_facebook',
                'type'     => 'text',
                'title'    => esc_html__( 'Facebook', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_twitter',
                'type'     => 'text',
                'title'    => esc_html__( 'Twitter', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_linkedin',
                'type'     => 'text',
                'title'    => esc_html__( 'Linkedin', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_youtube',
                'type'     => 'text',
                'title'    => esc_html__( 'Youtube', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_pinterest',
                'type'     => 'text',
                'title'    => esc_html__( 'Pinterest', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_instagram',
                'type'     => 'text',
                'title'    => esc_html__( 'Instagram', 'classima' ),
                'default'  => '',
            ),
            array(
                'id'       => 'social_rss',
                'type'     => 'text',
                'title'    => esc_html__( 'RSS', 'classima' ),
                'default'  => '',
            ),
        )
    )
);