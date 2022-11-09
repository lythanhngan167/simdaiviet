<?php

namespace RtclPro\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;

class QuickViewController
{

    /**
     * @var int|mixed
     */
    private static $version;
    /**
     * @var string|void
     */
    private static $ajaxurl;

    public static function init() {
        if (Fns::is_enable_quick_view()) {
            self::$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_PRO_VERSION;
            self::$ajaxurl = admin_url('admin-ajax.php');
            if ($current_lang = apply_filters('rtcl_ajaxurl_current_lang', null, self::$ajaxurl)) {
                self::$ajaxurl = add_query_arg('lang', $current_lang, self::$ajaxurl);
            }
            add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_script'], 50);


            // Quick view
            add_action('wp_ajax_rtcl_get_ajax_quick_view_data', [__CLASS__, 'get_quick_view_data']);
            add_action('wp_ajax_nopriv_rtcl_get_ajax_quick_view_data', [__CLASS__, 'get_quick_view_data']);

        }
    }


    public static function get_quick_view_data() {
        $listing_id = !empty($_REQUEST['listing_id']) ? absint($_REQUEST['listing_id']) : 0;
        $listing = rtcl()->factory->get_listing($listing_id);
        $data = $_REQUEST;
        $response = [
            'html' => '',
            'data' => ''
        ];
        if ($listing) {
            $response['data'] = $listing->get_id();
            $response['html'] = Functions::get_template_html('listing/quick-view', compact('listing', 'data'), '', rtclPro()->get_plugin_template_path());
        }

        wp_send_json(apply_filters('rtcl_ajax_quick_view_response_data', $response, $listing, $_REQUEST));
    }


    public static function enqueue_script() {
        wp_enqueue_script('rtcl-single-listing');
        wp_enqueue_script('rtcl-quick-view', rtclPro()->get_assets_uri("js/quick-view.min.js"), ['jquery'], self::$version);
        $quick_view_localize = apply_filters('rtcl_quick_view_localize_options', [
            'ajaxurl'      => self::$ajaxurl,
            'server_error' => esc_html__("Server Error!!", 'classified-listing-pro'),
            'selector'     => '.rtcl-quick-view',
            'max_width'    => 1000,
            'wrap_class'   => 'rtcl-qvw no-heading'
        ]);
        wp_localize_script('rtcl-quick-view', 'rtcl_quick_view', $quick_view_localize);
    }
}
