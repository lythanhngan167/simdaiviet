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
        'title'   => esc_html__( 'Post Settings', 'classima' ),
        'id'      => 'post_settings_section',
        'icon'    => 'el el-file-edit',
        'heading' => '',
        'fields'  => array(
            array(
                'id'      => 'post_date',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Post Date', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ),
            array(
                'id'      => 'post_author_name',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Author Name', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ),
            array(
                'id'      => 'post_cats',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Categories', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ),
            array(
                'id'      => 'post_comment_num',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Comment Number', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ), 
            array(
                'id'      => 'post_tags',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Tags', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ),
            array(
                'id'      => 'post_social',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Social Sharing', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ),
            array(
                'id'      => 'post_share',
                'type'    => 'checkbox',
                'class'   => 'redux-custom-inline',
                'title'   => esc_html__( 'Social Sharing Icons', 'classima'), 
                'options' => array(
                    'facebook'  => 'Facebook',
                    'twitter'   => 'Twitter',
                    'linkedin'  => 'Linkedin',
                    'pinterest' => 'Pinterest',
                    'tumblr'    => 'Tumblr',
                    'reddit'    => 'Reddit',
                    'vk'        => 'Vk',
                ),
                'default' => array(
                    'facebook'  => '1',
                    'twitter'   => '1',
                    'gplus'     => '1',
                    'linkedin'  => '1',
                    'pinterest' => '1',
                    'tumblr'    => '0',
                    'reddit'    => '0',
                    'vk'        => '0',
                ),
                'required' => array( 'post_social', '=', true )
            ),
            array(
                'id'      => 'post_about_author',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display About Author', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => false,
            ),
	        array(
		        'id'      => 'post_related',
		        'type'    => 'switch',
		        'title'   => esc_html__( 'Display Related Post', 'classima' ),
		        'on'      => esc_html__( 'On', 'classima' ),
		        'off'     => esc_html__( 'Off', 'classima' ),
		        'default' => false,
	        ),
        )
    )
);