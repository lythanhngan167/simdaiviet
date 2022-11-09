<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;
use \RT_Postmeta;

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'RT_Postmeta' ) ) {
	return;
}

$Postmeta = RT_Postmeta::getInstance();

$prefix = CLASSIMA_CORE_THEME_PREFIX;

/*-------------------------------------
#. Layout Settings
---------------------------------------*/
$nav_menus = wp_get_nav_menus( array( 'fields' => 'id=>name' ) );
$nav_menus = array( 'default' => __( 'Default', 'classima-core' ) ) + $nav_menus;
$sidebars  = array( 'default' => __( 'Default', 'classima-core' ) ) + Helper::custom_sidebar_fields();

$Postmeta->add_meta_box( "{$prefix}_page_settings", __( 'Layout Settings', 'classima-core' ), array( 'page', 'post' ), '', '', 'high', array(
	'fields' => array(
		"{$prefix}_layout_settings" => array(
			'label'   => __( 'Layouts', 'classima-core' ),
			'type'    => 'group',
			'value'  => array(
				'layout' => array(
					'label'   => __( 'Layout', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default'       => __( 'Default', 'classima-core' ),
						'full-width'    => __( 'Full Width', 'classima-core' ),
						'left-sidebar'  => __( 'Left Sidebar', 'classima-core' ),
						'right-sidebar' => __( 'Right Sidebar', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'sidebar' => array(
					'label'    => __( 'Custom Sidebar', 'classima-core' ),
					'type'     => 'select',
					'options'  => $sidebars,
					'default'  => 'default',
				),
				'tr_header' => array(
					'label'   => __( 'Transparent Header', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default', 'classima-core' ),
						'on'	  => __( 'Enable', 'classima-core' ),
						'off'	  => __( 'Disable', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'top_bar' => array(
					'label'   => __( 'Top Bar', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default', 'classima-core' ),
						'on'	  => __( 'Enable', 'classima-core' ),
						'off'	  => __( 'Disable', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'header_style' => array(
					'label'   => __( 'Header Layout', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default',  'classima-core' ),
						'1'       => __( 'Layout 1', 'classima-core' ),
						'2'       => __( 'Layout 2', 'classima-core' ),
						'3'       => __( 'Layout 3', 'classima-core' ),
						'4'       => __( 'Layout 4', 'classima-core' ),
						'5'       => __( 'Layout 5', 'classima-core' ),
						'6'       => __( 'Layout 6', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'footer_style' => array(
					'label'   => __( 'Footer Style', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default',  'classima-core' ),
						'1'       => __( 'Style 1', 'classima-core' ),
						'2'       => __( 'Style 2', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'banner' => array(
					'label'   => __( 'Banner', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default', 'classima-core' ),
						'on'	  => __( 'Enable', 'classima-core' ),
						'off'	  => __( 'Disable', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'breadcrumb' => array(
					'label'   => __( 'Breadcrumb', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default', 'classima-core' ),
						'on'      => __( 'Enable', 'classima-core' ),
						'off'	  => __( 'Disable', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'banner_search' => array(
					'label'   => __( 'Banner Search', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default', 'classima-core' ),
						'on'      => __( 'Enabled', 'classima-core' ),
						'off'	  => __( 'Disabled', 'classima-core' ),
					),
					'default'  => 'default',
				),
				'bgtype' => array(
					'label'   => __( 'Banner Background Type', 'classima-core' ),
					'type'    => 'select',
					'options' => array(
						'default' => __( 'Default', 'classima-core' ),
						'bgimg'   => __( 'Background Image', 'classima-core' ),
						'bgcolor' => __( 'Background Color', 'classima-core' ),
					),
					'default' => 'default',
				),
				'bgimg' => array(
					'label' => __( 'Banner Background Image', 'classima-core' ),
					'type'  => 'image',
					'desc'  => __( 'If not selected, default will be used', 'classima-core' ),
				),
				'bgcolor' => array(
					'label' => __( 'Banner Background Color', 'classima-core' ),
					'type'  => 'color_picker',
					'desc'  => __( 'If not selected, default will be used', 'classima-core' ),
				),
			)
		)
	)
) );

/*-------------------------------------
#. Listing Specification
---------------------------------------*/
$Postmeta->add_meta_box( "{$prefix}_specification", __( 'Features', 'classima-core' ), array( 'rtcl_listing' ), '', '', 'high', array(
	'fields' => array(
		"{$prefix}_spec_info" => array(
			'type'  => 'group',
			'value'  => array(
				"specs" => array(
					'label' => __( 'Features List', 'classima-core' ),
					'type'  => 'textarea',
					'class' => 'h200',
					'desc'  => __( 'Write a feature in each line eg. <br/>Feature 1<br/>Feature 2<br/>...', 'classima-core' ),
				),
			)
		),
	),
));