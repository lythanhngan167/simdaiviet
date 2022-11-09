<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use RtclStore\Helpers\Functions as StoreFunctions;

class Init
{
    public static function init() {
        add_action('init', [__CLASS__, 'addConfigurations'], 5);
        add_action('rtcl_after_register_post_type', [__CLASS__, 'rtcl_register_taxonomy']);
        add_action('rtcl_after_register_post_type', [__CLASS__, 'register_store_post_type']);

        add_action('rtcl_permalink_structure', [__CLASS__, 'add_store_permalink_structure']);
        if (StoreFunctions::is_enable_store_manager()) {
            add_action('template_redirect', [__CLASS__, 'approve_manager_invitation_request']);
        }
    }

    static function approve_manager_invitation_request() {
        $page_check = apply_filters( 'rtcl_is_add_permition_as_store_manager', Functions::is_account_page() );
        if (isset($_GET['rtcl_store_manager_key']) && isset($_GET['rtcl_store_manager_id']) && absint($_GET['rtcl_store_manager_id']) && isset($_GET['rtcl_store_id']) && absint($_GET['rtcl_store_id']) && $page_check && wp_is_uuid($_GET['rtcl_store_manager_key'])) {
            if (!is_user_logged_in()) {
                Functions::add_notice(__("You need to logged in first to approved the request!!", "classified-listing-store"), 'error');
                return;
            }
            if (($store = rtclStore()->factory->get_store($_GET['rtcl_store_id'])) && ($user = get_user_by('id', $_GET['rtcl_store_manager_id']))) {
                $invitation_list = $store->get_manager_invitation_list();
                if (!isset($invitation_list[$user->ID]) || $user->ID !== get_current_user_id() || $invitation_list[$user->ID] !== $_GET['rtcl_store_manager_key']) {
                    Functions::add_notice(__("Invitation link is expired!!", "classified-listing-store"), 'error');
                    return;
                }
                $store->approve_manager($user);
                Functions::add_notice(__("Invitation accept successfully.", "classified-listing-store"));

	            do_action('rtcl_store_manager_created', $_GET['rtcl_store_id'], $user->ID);
            }
        }
    }

    static function set_hierarchy_depth($args) {
        global $post_type_object;

        if ($post_type_object->name == rtclStore()->post_type) {
            $args['depth'] = 1;
        }
        return $args;
    }

    static function add_store_permalink_structure($saved_permalinks) {

        if ($store_base = Functions::get_option_item('rtcl_advanced_settings', 'permalink_store')) {
            $saved_permalinks['permalink_store'] = untrailingslashit($store_base);
        }
        if ($store_category_base = Functions::get_option_item('rtcl_advanced_settings', 'store_category_base')) {
            $saved_permalinks['store_category_base'] = untrailingslashit($store_category_base);
        }

        return wp_parse_args(
            $saved_permalinks,
            [
                'permalink_store'     => _x('store', 'slug', 'classified-listing-store'),
                'store_category_base' => _x('store-category', 'slug', 'classified-listing-store'),
            ]
        );
    }

    static function addConfigurations() {
        $image_sizes = array();
        $image_sizes['rtcl-store-banner'] = Functions::get_option_item('rtcl_misc_settings', 'store_banner_size', array());
        $image_sizes['rtcl-store-logo'] = Functions::get_option_item('rtcl_misc_settings', 'store_logo_size', array());

        foreach ($image_sizes as $image_key => $image_size) {
            if (!empty($image_size)) {
                add_image_size($image_key, $image_size["width"], $image_size["height"], isset($image_size["crop"]) && $image_size["crop"] === 'yes' ? true : false);
            }
        }
    }

    static function rtcl_register_taxonomy() {
        if (!Functions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox')) {
            return;
        }
        $permalinks = Functions::get_permalink_structure();

        $cat_labels = array(
            'name'                       => esc_html_x('Store Categories', 'Taxonomy General Name', 'classified-listing-store'),
            'singular_name'              => esc_html_x('Category', 'Taxonomy Singular Name', 'classified-listing-store'),
            'menu_name'                  => esc_html__('Store Categories', 'classified-listing-store'),
            'all_items'                  => esc_html__('All Categories', 'classified-listing-store'),
            'parent_item'                => esc_html__('Parent Category', 'classified-listing-store'),
            'parent_item_colon'          => esc_html__('Parent Category:', 'classified-listing-store'),
            'new_item_name'              => esc_html__('New Category Name', 'classified-listing-store'),
            'add_new_item'               => esc_html__('Add New Category', 'classified-listing-store'),
            'edit_item'                  => esc_html__('Edit Category', 'classified-listing-store'),
            'update_item'                => esc_html__('Update Category', 'classified-listing-store'),
            'view_item'                  => esc_html__('View Category', 'classified-listing-store'),
            'separate_items_with_commas' => esc_html__('Separate Categories with commas', 'classified-listing-store'),
            'add_or_remove_items'        => esc_html__('Add or remove Categories', 'classified-listing-store'),
            'choose_from_most_used'      => esc_html__('Choose from the most used', 'classified-listing-store'),
            'popular_items'              => null,
            'search_items'               => esc_html__('Search Categories', 'classified-listing-store'),
            'not_found'                  => esc_html__('Not Found', 'classified-listing-store'),
        );

        $cat_args = array(
            'labels'            => $cat_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'query_var'         => true,
            'capabilities'      => array(
                'manage_terms' => 'manage_rtcl_store',
                'edit_terms'   => 'manage_rtcl_store',
                'delete_terms' => 'manage_rtcl_store',
                'assign_terms' => 'manage_rtcl_store'
            ),
            'rewrite'           => array(
                'slug'         => $permalinks['store_category_base'],
                'with_front'   => false,
                'hierarchical' => true,
            )
        );

        register_taxonomy(rtclStore()->category, rtclStore()->post_type, apply_filters('rtcl_register_store_category_args', $cat_args));

    }

    static function register_store_post_type() {

        if (!Functions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox')) {
            return;
        }
        $permalinks = Functions::get_permalink_structure();
        $labels = array(
            'name'               => _x('Store', 'Post Type General Name', 'classified-listing-store'),
            'singular_name'      => _x('Store', 'Post Type Singular Name', 'classified-listing-store'),
            'menu_name'          => __('Store', 'classified-listing-store'),
            'name_admin_bar'     => __('Store', 'classified-listing-store'),
            'all_items'          => __('Stores', 'classified-listing-store'),
            'add_new_item'       => __('Add New Store', 'classified-listing-store'),
            'add_new'            => __('Add New', 'classified-listing-store'),
            'new_item'           => __('New Store', 'classified-listing-store'),
            'edit_item'          => __('Edit Store', 'classified-listing-store'),
            'update_item'        => __('Update Store', 'classified-listing-store'),
            'view_item'          => __('View Store', 'classified-listing-store'),
            'search_items'       => __('Search Store', 'classified-listing-store'),
            'not_found'          => __('No stores found', 'classified-listing-store'),
            'not_found_in_trash' => __('No stores found in Trash', 'classified-listing-store'),
        );

        $store_page_id = Functions::get_page_id('store');

        if (current_theme_supports('rtcl')) {
            $has_archive = $store_page_id && get_post($store_page_id) ? urldecode(get_page_uri($store_page_id)) : 'stores';
        } else {
            $has_archive = false;
        }


        // If theme support changes, we may need to flush permalinks since some are changed based on this flag.
        $theme_support = current_theme_supports('rtcl') ? 'yes' : 'no';
        if (get_option('current_theme_supports_rtcl') !== $theme_support && update_option('current_theme_supports_rtcl', $theme_support)) {
            update_option('rtcl_queue_flush_rewrite_rules', 'yes');
        }
        $args = array(
            'label'               => __('Stores', 'classified-listing-store'),
            'description'         => __('Store Description', 'classified-listing-store'),
            'labels'              => $labels,
            'supports'            => ['title', 'editor', 'comments'],
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=' . rtcl()->post_type,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => $has_archive,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => $permalinks['permalink_store'] ? array(
                'slug'       => $permalinks['permalink_store'],
                'with_front' => false,
                'feeds'      => true,
            ) : false,
            'capabilities'        => array(
                'edit_post'          => 'manage_rtcl_store',
                'read_post'          => 'manage_rtcl_store',
                'delete_post'        => 'manage_rtcl_store',
                'edit_posts'         => 'manage_rtcl_store',
                'edit_others_posts'  => 'manage_rtcl_store',
                'delete_posts'       => 'manage_rtcl_store',
                'publish_posts'      => 'manage_rtcl_store',
                'read_private_posts' => 'manage_rtcl_store'
            )
        );

        register_post_type(rtclStore()->post_type, apply_filters('rtcl_store_register_post_type_args', $args));

        do_action('rtcl_store_after_register_post_type');
    }
}