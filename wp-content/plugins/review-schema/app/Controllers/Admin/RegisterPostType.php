<?php

namespace Rtrs\Controllers\Admin; 

class RegisterPostType {

    public function __construct() {
        add_action('init', [$this, 'register_post_types'], 5); 
    }  

    public static function register_post_types() {
          
        if ( !is_blog_installed() || post_type_exists(rtrs()->getPostType()) ) {
            return;
        }

        do_action('rtrs_register_post_type');
 
        $label = array(
            'name' => esc_html_x('Review Schema', 'Post Type General Name', 'review-schema'),
            'singular_name' => esc_html_x('Review Schema', 'Post Type Singular Name', 'review-schema'),
            'menu_name' => esc_html__('Review Schema', 'review-schema'),
            'parent_item_colon' => esc_html__('Parent Review Schema:', 'review-schema'),
            'all_items' => esc_html__('All Review Schema', 'review-schema'),
            'view_item' => esc_html__('View Review Schema', 'review-schema'),
            'add_new_item' => esc_html__('Add New Review Schema', 'review-schema'),
            'add_new' => esc_html__('New Review Schema', 'review-schema'),
            'edit_item' => esc_html__('Edit Review Schema', 'review-schema'),
            'update_item' => esc_html__('Update Review Schema', 'review-schema'),
            'search_items' => esc_html__('Search Review Schema', 'review-schema'),
            'not_found' => esc_html__('No review schema found', 'review-schema'),
            'not_found_in_trash' => esc_html__('No review schema found in Trash', 'review-schema'),
        );

        $rtrs_support = array('title'); 
        $args = array(
            'label' => esc_html__('Review Schema', 'review-schema'),
            'description' => esc_html__('Review Schema', 'review-schema'),
            'labels' => $label,
            'supports' => $rtrs_support, 
            'hierarchical' => false,
            'public' => false, 
            'show_ui' => current_user_can('administrator'),
            'show_in_menu' => 'review-schema',
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon'  => RTRS_URL . '/assets/imgs/icon-20x20.png',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'page',
        );
        register_post_type(rtrs()->getPostType(), apply_filters('rtrs_register_post_type_args', $args));

        do_action('rtrs_after_register_post_type');

        do_action('rtrs_register_post_type_affilite');
 
        $label = array(
            'name' => esc_html_x('Affiliate', 'Post Type General Name', 'review-schema'),
            'singular_name' => esc_html_x('Affiliate', 'Post Type Singular Name', 'review-schema'),
            'menu_name' => esc_html__('Affiliate', 'review-schema'),
            'parent_item_colon' => esc_html__('Parent Affiliate:', 'review-schema'),
            'all_items' => esc_html__('All Affiliates', 'review-schema'),
            'view_item' => esc_html__('View Affiliate', 'review-schema'),
            'add_new_item' => esc_html__('Add New Affiliate', 'review-schema'),
            'add_new' => esc_html__('New Affiliate', 'review-schema'),
            'edit_item' => esc_html__('Edit Affiliate', 'review-schema'),
            'update_item' => esc_html__('Update Affiliate', 'review-schema'),
            'search_items' => esc_html__('Search Affiliate', 'review-schema'),
            'not_found' => esc_html__('No affiliate found', 'review-schema'),
            'not_found_in_trash' => esc_html__('No affiliate found in Trash', 'review-schema'),
        );

        $rtrs_support = array('title'); 
        $args = array(
            'label' => esc_html__('Affiliate', 'review-schema'),
            'description' => esc_html__('Affiliate', 'review-schema'),
            'labels' => $label,
            'supports' => $rtrs_support, 
            'hierarchical' => false,
            'public' => false, 
            'show_ui' => current_user_can('administrator'), 
            'show_in_menu' => 'review-schema',
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'menu_position' => 20,
            'menu_icon'  => RTRS_URL . '/assets/imgs/icon-20x20.png',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'page',
        );
        register_post_type(rtrs()->getPostTypeAffiliate(), apply_filters('rtrs_register_post_type_affilite_args', $args));
        
        do_action('rtrs_after_register_post_type_affilite'); 
    }
}
