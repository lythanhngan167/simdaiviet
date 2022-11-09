<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;

class MenuHooks
{

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_store_category_menu'));
        add_action('admin_menu', array(__CLASS__, 'add_membership_menu'), 51);
    }

    static function add_store_category_menu() {
        if (!Functions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox')) {
            return;
        }
	    $store_label = apply_filters('rtcl_store_category_label', __("Store Categories", "classified-listing-store"));
        add_submenu_page(
            'edit.php?post_type=' . rtcl()->post_type,
	        $store_label,
	        $store_label,
            'manage_rtcl_options',
            add_query_arg([
                'taxonomy'  => rtclStore()->category,
                'post_type' => rtclStore()->post_type
            ], 'edit-tags.php'), false);
    }

    static function add_membership_menu() {
        add_submenu_page(
            'edit.php?post_type=' . rtcl()->post_type,
            __('Membership', 'classified-listing-store'),
            __('Membership', 'classified-listing-store'),
            'manage_rtcl_options',
            'membership',
            array(__CLASS__, 'manage_membership_list')
        );
    }

    static function manage_membership_list() { ?>
        <link rel="stylesheet"
              href="https://unpkg.com/react-bootstrap-table-next@0.1.15/dist/react-bootstrap-table2.min.css">
        <div class="wrap rtcl">
            <h2><?php esc_html_e("Manage membership", "classified-listing-store") ?></h2>
            <div class="membership-app">
                <div id="app"></div>
            </div>
        </div>
        <?php
    }
}