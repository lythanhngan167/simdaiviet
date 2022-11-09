<?php

namespace RtclStore\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Page;
use RtclStore\Helpers\Functions as StoreFunctions;

class Script
{

    function __construct() {
        add_action('admin_init', array($this, 'register_script_for_both'), 100);
        add_action('admin_init', array($this, 'register_script_backend'), 200);
        add_action('admin_enqueue_scripts', array($this, 'load_admin_script_for_store_post_type'), 300);
        add_action('admin_enqueue_scripts', array($this, 'load_admin_script_for_pricing_post_type'), 300);
        add_action('admin_enqueue_scripts', array($this, 'load_admin_script_for_settings'), 300);
        add_action('admin_enqueue_scripts', array($this, 'load_admin_script_for_manage_membership'), 300);
        add_action('wp_enqueue_scripts', array($this, 'register_script_for_both'), 1);
        add_action('wp_enqueue_scripts', array($this, 'register_script_frontend'), 2);
        add_action('wp_enqueue_scripts', array($this, 'load_script'), 5);
    }

    function register_script_for_both() {
        wp_register_script('bootstrap-timepicker', RTCL_STORE_URL . '/assets/js/bootstrap-timepicker.js', array('jquery'), '0.5.2', true);
    }

    function register_script_backend() {
        $version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_STORE_VERSION;
        wp_register_style('rtcl-store-admin', RTCL_STORE_URL . '/assets/css/admin.css', ['rtcl-bootstrap'], $version);
        wp_register_script('rtcl-store-admin', RTCL_STORE_URL . '/assets/js/admin.js', array(
            'jquery',
            'bootstrap-timepicker'
        ), $version, true);
        wp_register_script('rtcl-admin-pricing', RTCL_STORE_URL . '/assets/js/admin-pricing.js', ['jquery'], $version, true);

        wp_register_style('rtcl-membership-app', RTCL_STORE_URL . '/assets/css/membership.app.css', array(
            'rtcl-bootstrap',
            'rtcl-admin'
        ), $version);
        wp_register_script('rtcl-membership-app', RTCL_STORE_URL . '/assets/js/membership.app.js', ['jquery'], $version, true);

        $max_image_size = Functions::get_max_upload();
        global $post;
        wp_localize_script('rtcl-store-admin', 'rtcl_store', apply_filters('rtcl_store_admin_localize', array(
            "ajaxurl"               => admin_url("admin-ajax.php"),
            "store_time_options"    => apply_filters('rtcl_store_time_options', [
                "icons" => [
                    "up"   => 'rtcl-icon-up-open',
                    "down" => 'rtcl-icon-down-open'
                ]
            ]),
            'confirm_text'          => __("Are You sure to delete?", 'classified-listing-store'),
            rtcl()->nonceId         => wp_create_nonce(rtcl()->nonceText),
            "max_image_size"        => $max_image_size,
            'store_id'              => is_object($post) ? $post->ID : 0,
            "image_allowed_type"    => (array)Functions::get_option_item('rtcl_misc_settings', 'image_allowed_type', array(
                'png',
                'jpeg',
                'jpg'
            )),
            "error_common"          => __("Error while upload image", "classified-listing-store"),
            "error_image_size"      => sprintf(__("Image size is more then %s.", "classified-listing-store"), Functions::formatBytes($max_image_size)),
            "error_image_extension" => __("File extension not supported.", "classified-listing-store"),
            "server_error"          => __("Server Error.", "classified-listing-store"),
        )));

        wp_localize_script('rtcl-membership-app', 'rtcl_store', array(
            "ajaxurl"         => admin_url("admin-ajax.php"),
            'confirm_alert'   => __("Are You sure to delete?", 'classified-listing-store'),
            'selection_alert' => __("Please select a row to delete", 'classified-listing-store'),
            rtcl()->nonceId   => wp_create_nonce(rtcl()->nonceText),
        ));
    }

    function register_script_frontend() {
        $version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_STORE_VERSION;
        wp_register_script('rtcl-store', RTCL_STORE_URL . '/assets/js/store.js', array(
            'jquery',
            'rtcl-validator',
            'bootstrap-timepicker'
        ), $version, true);

        wp_register_script('rtcl-store-public', RTCL_STORE_URL . '/assets/js/store-public.js', array(
            'jquery',
            'rtcl-validator'
        ), $version, true);

        wp_register_style('rtcl-store', RTCL_STORE_URL . '/assets/css/store.css', array('rtcl-public'), $version);
        wp_register_style('rtcl-store-public', RTCL_STORE_URL . '/assets/css/store-public.css', array('rtcl-public'), $version);

        $max_image_size = Functions::get_max_upload();
        global $post;
        wp_localize_script('rtcl-store', 'rtcl_store', apply_filters('rtcl_store_localize', array(
            "store_time_options"    => apply_filters('rtcl_store_time_options', [
                "icons" => [
                    "up"   => 'rtcl-icon-up-open',
                    "down" => 'rtcl-icon-down-open'
                ]
            ]),
            "ajaxurl"               => admin_url("admin-ajax.php"),
            'confirm_text'          => __("Are You sure to delete?", 'classified-listing-store'),
            rtcl()->nonceId         => wp_create_nonce(rtcl()->nonceText),
            "max_image_size"        => $max_image_size,
            'store_id'              => is_singular(rtclStore()->post_type) ? $post->ID : 0,
            "image_allowed_type"    => (array)Functions::get_option_item('rtcl_misc_settings', 'image_allowed_type', array(
                'png',
                'jpeg',
                'jpg'
            )),
            "error_common"          => __("Error while upload image", "classified-listing-store"),
            "error_image_size"      => sprintf(__("Image size is more then %s.", "classified-listing-store"), Functions::formatBytes($max_image_size)),
            "error_image_extension" => __("File extension not supported.", "classified-listing-store"),
            "server_error"          => __("Server Error.", "classified-listing-store"),
        )));
        wp_localize_script('rtcl-store-public', 'rtcl_store_public', apply_filters('rtcl_store_public_localize', array(
            "ajaxurl"       => admin_url("admin-ajax.php"),
            'is_rtl'        => is_rtl(),
            rtcl()->nonceId => wp_create_nonce(rtcl()->nonceText),
            'store_id'      => is_singular(rtclStore()->post_type) ? $post->ID : 0
        )));
    }

    function load_admin_script_for_store_post_type() {
        global $pagenow, $post_type;
        // validate page
        if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php'))) {
            return;
        }
        if (rtclStore()->post_type != $post_type) {
            return;
        }

        wp_enqueue_style('rtcl-admin');
        wp_enqueue_script('rtcl-admin');
        wp_enqueue_style('rtcl-store-admin');
        wp_enqueue_script('rtcl-store-admin');
    }

    function load_admin_script_for_settings() {
        if (!empty($_GET['post_type']) && $_GET['post_type'] == rtcl()->post_type && !empty($_GET['page']) && $_GET['page'] == 'rtcl-settings' && (isset($_GET['tab']) && ($_GET['tab'] === 'membership' || $_GET['tab'] === 'tools'))) {
            wp_enqueue_style('rtcl-store-admin');
        }
    }

    function load_admin_script_for_pricing_post_type() {
        global $pagenow, $post_type;
        // validate page
        if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php'))) {
            return;
        }
        if (rtcl()->post_type_pricing != $post_type) {
            return;
        }

        wp_enqueue_script('rtcl-admin-pricing');
        wp_enqueue_style('rtcl-store-admin');
    }

    function load_script() {
        global $wp;

        if (Functions::is_account_page() && (isset($wp->query_vars['store']) || isset($wp->query_vars['page']) || (StoreFunctions::is_enable_store_manager() && isset($wp->query_vars['listings'])))) {
            wp_enqueue_script('rtcl-store');
            wp_enqueue_style('rtcl-store');
        }

        if (Functions::is_checkout_page() && (isset($wp->query_vars['membership']) || isset($wp->query_vars['submission']) || isset($wp->query_vars['payment-receipt']))) {
            wp_enqueue_style('rtcl-store-public');
        }

        if (StoreFunctions::is_store() || StoreFunctions::is_single_store() || StoreFunctions::is_store_taxonomy() || isset($wp->query_vars['submission'])) {
            wp_enqueue_script('rtcl-store-public');
            wp_enqueue_style('rtcl-store-public');
        }

    }

    function load_admin_script_for_manage_membership() {
        if (!empty($_GET['post_type']) && $_GET['post_type'] == rtcl()->post_type && !empty($_GET['page']) && $_GET['page'] == 'membership') {
            wp_enqueue_style('rtcl-membership-app');
            wp_enqueue_script('rtcl-membership-app');
        }
    }
}
