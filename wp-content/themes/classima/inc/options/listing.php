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
        'title'   => esc_html__( 'Listing Settings', 'classima' ),
        'id'      => 'listing_settings_section',
        'icon'    => 'el el-align-left',
        'heading' => '',
        'fields'  => array(
            array(
                'id'       => 'section-listing-grid',
                'type'     => 'section',
                'title'    => esc_html__( 'Grid View', 'classima' ),
                'indent'   => true,
            ),
            array(
                'id'       =>'listing_grid_style',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Grid View Style', 'classima' ),
                'options'  => array(
                    '1' => esc_html__( 'Style 1', 'classima' ),
                    '2' => esc_html__( 'Style 2', 'classima' ),
                    '3' => esc_html__( 'Style 3', 'classima' ),
                    '4' => esc_html__( 'Style 4', 'classima' ),
                    '7' => esc_html__( 'Style 5', 'classima' ),
                    '8' => esc_html__( 'Style 6', 'classima' ),
                    '9' => esc_html__( 'Style 7', 'classima' ),
                ),
                'default' => '1'
            ),
            array(
                'id'       =>'grid_desktop_column',
                'type'     => 'select',
                'title'    => esc_html__( 'Desktop', 'classima' ),
                'options'  => array(
                    '12'     => esc_html__( '1 Column', 'classima' ),
                    '6'     => esc_html__( '2 Column', 'classima' ),
                    '4'     => esc_html__( '3 Column', 'classima' ),
                    '3'     => esc_html__( '4 Column', 'classima' ),
                ),
                'default' => '4'
            ),
            array(
                'id'       =>'grid_tablet_column',
                'type'     => 'select',
                'title'    => esc_html__( 'Tablet', 'classima' ),
                'options'  => array(
                    '12'     => esc_html__( '1 Column', 'classima' ),
                    '6'     => esc_html__( '2 Column', 'classima' ),
                    '4'     => esc_html__( '3 Column', 'classima' ),
                    '3'     => esc_html__( '4 Column', 'classima' ),
                ),
                'default' => '6'
            ),
            array(
                'id'       =>'grid_mobile_column',
                'type'     => 'select',
                'title'    => esc_html__( 'Mobile', 'classima' ),
                'options'  => array(
                    '12'     => esc_html__( '1 Column', 'classima' ),
                    '6'     => esc_html__( '2 Column', 'classima' ),
                    '4'     => esc_html__( '3 Column', 'classima' ),
                    '3'     => esc_html__( '4 Column', 'classima' ),
                ),
                'default' => '12'
            ),
            array(
                'id'       => 'section-listing-list',
                'type'     => 'section',
                'indent'   => false,
            ),
            array(
                'id'       =>'listing_list_style',
                'type'     => 'button_set',
                'title'    => esc_html__( 'List View Style', 'classima' ),
                'options'  => array(
                    '1' => esc_html__( 'Style 1', 'classima' ),
                    '2' => esc_html__( 'Style 2', 'classima' ),
                    '3' => esc_html__( 'Style 3', 'classima' ),
                    '4' => esc_html__( 'Style 4', 'classima' ),
                    '7' => esc_html__( 'Style 5', 'classima' ),
                ),
                'default' => '2'
            ),
            array(
                'id'       =>'single_listing_style',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Single Listing Style', 'classima' ),
                'options'  => array(
                    '1' => esc_html__( 'Style 1', 'classima' ),
                    '2' => esc_html__( 'Style 2', 'classima' ),
                    '3' => esc_html__( 'Style 3', 'classima' ),
                    '4' => esc_html__( 'Style 4', 'classima' ),
                ),
                'default' => '2'
            ),
            array(
                'id'      => 'listing_custom_fields',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Custom Fields in Listings', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => false,
            ),
            array(
                'id'      => 'listing_related',
                'type'    => 'switch',
                'title'   => esc_html__( 'Display Related Listing', 'classima' ),
                'on'      => esc_html__( 'On', 'classima' ),
                'off'     => esc_html__( 'Off', 'classima' ),
                'default' => true,
            ),
            array(
                'id'       =>'listing_related_style',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Related Listing Style', 'classima' ),
                'options'  => array(
                    '1' => esc_html__( 'Style 1', 'classima' ),
                    '2' => esc_html__( 'Style 2', 'classima' ),
                ),
                'default' => '1'
            ),
            array(
                'id'      => 'listing_search_items',
                'type'    => 'checkbox',
                'class'   => 'redux-custom-inline',
                'title'   => esc_html__( 'Listing Search Items', 'classima'), 
                'options' => array(
                    'location'  => 'Location',
                    'radius'    => 'Radius',
                    'category'  => 'Category',
                    'type'      => 'Type',
                    'keyword'   => 'Keyword',
                ),
                'default' => array(
                    'location'  => '1',
                    'radius'    => '0',
                    'category'  => '1',
                    'keyword'   => '1',
                    'type'      => '0',
                ),
            ),
            array(
                'id'       =>'listing_search_style',
                'type'     => 'select',
                'title'    => esc_html__( 'Listing Search Style', 'classima' ),
                'options'  => array(
                    'popup'      => esc_html__( 'Popup', 'classima' ),
                    'standard'   => esc_html__( 'Standard', 'classima' ),
                    'suggestion' => esc_html__( 'Auto Suggestion', 'classima' ),
                    'dependency' => esc_html__( 'Dependency Selection', 'classima' ),
                ),
                'default' => 'popup'
            ),
        )
    ) 
);