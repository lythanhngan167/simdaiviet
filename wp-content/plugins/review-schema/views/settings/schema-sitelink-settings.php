<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for Publisher Info
 */

$menus = get_terms('nav_menu');
$menu_items = [];
if ( !empty( $menus ) ) {
    foreach ( $menus as $menu ) { 
        $menu_items[$menu->term_id] = $menu->name;
    }
}

$options = array( 
	'sitelink_searchbox'  => array(
        'title'   => esc_html__('Enable sitelink search box', 'review-schema'),
        'type'    => 'checkbox', 
        'label'   => esc_html__('Your website search box will show in google search result.', 'review-schema')
	), 
	'site_nav' => array( 
        'title'    => esc_html__('Site Navigation', "review-schema"),
        'type'     => 'select',
        'class'    => 'regular-text',
        'options'  => $menu_items,
        'empty'    => esc_html__('Select One', "review-schema"), 
    )	
);

return apply_filters( 'rtrs_schema_sitelink_settings_options', $options );
