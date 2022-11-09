<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use \Redux;

$opt_name = Constants::$theme_options;


function rdtheme_redux_post_type_fields( $base ){
    return array(
        'layout' => array(
            'id'       => $base. '_layout',
            'type'     => 'button_set',
            'title'    => esc_html__( 'Layout', 'classima' ),
            'options'  => array(
                'left-sidebar'  => esc_html__( 'Left Sidebar', 'classima' ),
                'full-width'    => esc_html__( 'Full Width', 'classima' ),
                'right-sidebar' => esc_html__( 'Right Sidebar', 'classima' ),
            ),
            'default' => 'right-sidebar'
        ),
        'sidebar' => array(
            'id'       => $base. '_sidebar',
            'type'     => 'select',
            'title'    => esc_html__( 'Custom Sidebar', 'classima' ),
            'options'  => Helper::custom_sidebar_fields(),
            'default'  => 'sidebar',
            'required' => array( $base. '_layout', '!=', 'full-width' ),
        ),
        'tr_header' => array(
            'id'       => $base. '_tr_header',
            'type'     => 'select',
            'title'    => esc_html__( 'Transparent Header', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                'on'      => esc_html__( 'Enabled', 'classima' ),
                'off'     => esc_html__( 'Disabled', 'classima' ),
            ),
            'default'  => 'default',
        ),
        'top_bar' => array(
            'id'       => $base. '_top_bar',
            'type'     => 'select',
            'title'    => esc_html__( 'Top Bar', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                'on'      => esc_html__( 'Enabled', 'classima' ),
                'off'     => esc_html__( 'Disabled', 'classima' ),
            ),
            'default'  => 'default',
        ),
        'header_style' => array(
            'id'       => $base. '_header_style',
            'type'     => 'select',
            'title'    => esc_html__( 'Header Layout', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                '1'       => esc_html__( 'Layout 1', 'classima' ),
                '2'       => esc_html__( 'Layout 2', 'classima' ),
            ),
            'default'  => 'default',
        ),
        'footer_style' => array(
	        'id'       => $base. '_footer_style',
	        'type'     => 'select',
	        'title'    => esc_html__( 'Footer Style', 'classima'),
	        'options'  => array(
		        'default' => esc_html__( 'Default',  'classima' ),
		        '1'       => esc_html__( 'Style 1', 'classima' ),
		        '2'       => esc_html__( 'Style 2', 'classima' ),
	        ),
	        'default'  => 'default',
        ),
        'banner' => array(
            'id'       => $base. '_banner',
            'type'     => 'select',
            'title'    => esc_html__( 'Banner', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                'on'      => esc_html__( 'Enabled', 'classima' ),
                'off'     => esc_html__( 'Disabled', 'classima' ),
            ),
            'default'  => 'default',
        ),
        'breadcrumb' => array(
            'id'       => $base. '_breadcrumb',
            'type'     => 'select',
            'title'    => esc_html__( 'Breadcrumb', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                'on'      => esc_html__( 'Enabled', 'classima' ),
                'off'     => esc_html__( 'Disabled', 'classima' ),
            ),
            'default'  => 'default',
            //'required' => array( $base. '_banner', '!=', 'off' )
        ),
        'banner_search' => array(
            'id'       => $base. '_banner_search',
            'type'     => 'select',
            'title'    => esc_html__( 'Banner Search', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                'on'      => esc_html__( 'Enabled', 'classima' ),
                'off'     => esc_html__( 'Disabled', 'classima' ),
            ),
            'default'  => 'default',
            'required' => array( $base. '_banner', '!=', 'off' )
        ),
        'bgtype' => array(
            'id'       => $base. '_bgtype',
            'type'     => 'select',
            'title'    => esc_html__( 'Banner Background Type', 'classima'), 
            'options'  => array(
                'default' => esc_html__( 'Default',  'classima' ),
                'bgimg'    => esc_html__( 'Background Image', 'classima' ),
                'bgcolor'  => esc_html__( 'Background Color', 'classima' ),
            ),
            'default'  => 'default',
            'required' => array( $base. '_banner', '!=', 'off' )
        ),
        'bgimg' => array(
            'id'       => $base. '_bgimg',
            'type'     => 'media',
            'title'    => esc_html__( 'Banner Background Image', 'classima' ),
            'default'  => '',
            'required' => array( $base. '_bgtype', '=', 'bgimg' ),
        ),
        'bgcolor' => array(
            'id'       => $base. '_bgcolor',
            'type'     => 'color',
            'title'    => esc_html__( 'Banner Background Color', 'classima'), 
            'validate' => 'color',
            'transparent' => false,
            'default'  => '',
            'required' => array( $base. '_bgtype', '=', 'bgcolor' ),
        ),
    );
}

Redux::setSection( $opt_name,
    array(
        'title' => esc_html__( 'Layout Defaults', 'classima' ),
        'id'    => 'layout_defaults',
        'icon'  => 'el el-th',
    )
);

// Page
$rdtheme_page_fields = rdtheme_redux_post_type_fields( 'page' );
$rdtheme_page_fields['layout']['default'] = 'full-width';
Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Page', 'classima' ),
        'id'         => 'pages_section',
        'subsection' => true,
        'fields'     => $rdtheme_page_fields     
    )
);

//Post Archive
$rdtheme_post_archive_fields = rdtheme_redux_post_type_fields( 'blog' );
$rdtheme_post_archive_fields['banner_search']['default'] = 'off';
Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Blog / Archive', 'classima' ),
        'id'         => 'blog_section',
        'subsection' => true,
        'fields'     => $rdtheme_post_archive_fields
    )
);

// Single Post
$rdtheme_single_post_fields = rdtheme_redux_post_type_fields( 'single_post' );
$rdtheme_single_post_fields['banner_search']['default'] = 'off';
Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Post Single', 'classima' ),
        'id'         => 'single_post_section',
        'subsection' => true,
        'fields'     => $rdtheme_single_post_fields           
    ) 
);

// Search
$rdtheme_search_fields = rdtheme_redux_post_type_fields( 'search' );
Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Search Layout', 'classima' ),
        'id'         => 'search_section',
        'subsection' => true,
        'fields'     => $rdtheme_search_fields            
    )
);

// Error 404 Layout
$rdtheme_error_fields = rdtheme_redux_post_type_fields( 'error' );
unset($rdtheme_error_fields['layout']);
Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Error 404 Layout', 'classima' ),
        'id'         => 'error_section',
        'subsection' => true,
        'fields'     => $rdtheme_error_fields           
    )
);

// Listing
$rdtheme_fields = rdtheme_redux_post_type_fields( 'listing_archive' );
$rdtheme_fields['layout']['options'] = array(
    'left-sidebar'  => esc_html__( 'Left Sidebar', 'classima' ),
    'right-sidebar' => esc_html__( 'Right Sidebar', 'classima' ),
);
$rdtheme_fields['layout']['default'] = 'left-sidebar';

Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Listing Archive', 'classima' ),
        'id'         => 'listing_archive_section',
        'subsection' => true,
        'fields'     => $rdtheme_fields            
    )
);

// Listing Single
$rdtheme_fields = rdtheme_redux_post_type_fields( 'listing_single' );
$rdtheme_fields['layout']['options'] = array(
    'left-sidebar'  => esc_html__( 'Left Sidebar', 'classima' ),
    'right-sidebar' => esc_html__( 'Right Sidebar', 'classima' ),
);

Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Listing Single', 'classima' ),
        'id'         => 'listing_single_section',
        'subsection' => true,
        'fields'     => $rdtheme_fields            
    )
);

// Listing Account
$rdtheme_fields = rdtheme_redux_post_type_fields( 'listing_account' );
$rdtheme_fields['layout']['options'] = array(
    'left-sidebar'  => esc_html__( 'Left Sidebar', 'classima' ),
    'right-sidebar' => esc_html__( 'Right Sidebar', 'classima' ),
);
$rdtheme_fields['layout']['default'] = 'left-sidebar';

Redux::setSection( $opt_name,
    array(
        'title'      => esc_html__( 'Listing Account Page', 'classima' ),
        'id'         => 'listing_account_section',
        'subsection' => true,
        'fields'     => $rdtheme_fields            
    )
);