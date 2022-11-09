<?php

namespace RtclPro\Controllers;

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

class CompareController
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
        if (Fns::is_enable_compare()) {
            self::$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_PRO_VERSION;
            self::$ajaxurl = admin_url('admin-ajax.php');
            if ($current_lang = apply_filters('rtcl_ajaxurl_current_lang', null, self::$ajaxurl)) {
                self::$ajaxurl = add_query_arg('lang', $current_lang, self::$ajaxurl);
            }

            add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_script'], 50);
            add_action('wp_footer', [__CLASS__, 'compare_template']);

            // Compare
            add_action('wp_ajax_rtcl_add_to_compare', [__CLASS__, 'add_to_compare']);
            add_action('wp_ajax_nopriv_rtcl_add_to_compare', [__CLASS__, 'add_to_compare']);
            add_action('wp_ajax_rtcl_remove_from_compare', [__CLASS__, 'remove_from_compare']);
            add_action('wp_ajax_nopriv_rtcl_remove_from_compare', [__CLASS__, 'remove_from_compare']);
            add_action('wp_ajax_rtcl_clear_from_compare', [__CLASS__, 'rtcl_clear_from_compare']);
            add_action('wp_ajax_nopriv_rtcl_clear_from_compare', [__CLASS__, 'rtcl_clear_from_compare']);
            add_filter('rtcl_advanced_settings_options', [__CLASS__, 'add_compare_end_point_options']);
            add_filter('the_content', [__CLASS__, 'compared_listings_content'], 100);
        }
    }

    public static function compared_listings_content($content) {
        global $post;
        if (is_page() && ($compare_page_id = Functions::get_page_id('compare_page')) && $compare_page_id === $post->ID) {
	        if ( empty( rtcl()->session ) ) {
		        rtcl()->initialize_session();
	        }
        	$compare_ids = rtcl()->session->get('rtcl_compare_ids', []);
            $content = Functions::get_template_html('compare/content', compact('compare_ids'), '', rtclPro()->get_plugin_template_path()) . $content;
        }
        return $content;
    }

    public static function add_compare_end_point_options($options) {

        $position = array_search('checkout', array_keys($options));
        if ($position > -1) {
            $option = array(
                'compare_page' => array(
                    'title'       => esc_html__('Compare page', 'classified-listing-pro'),
                    'type'        => 'select',
                    'class'       => 'rtcl-select2',
                    'blank_text'  => esc_html__("Select a page", 'classified-listing-pro'),
                    'options'     => Functions::get_pages(),
                    'description' =>esc_html__('This is the page where all the compared listings are displayed.', 'classified-listing-pro'),
                    'css'         => 'min-width:300px;',
                )
            );
            Functions::array_insert($options, $position, $option);
        }

        return $options;
    }

    public static function add_to_compare() {
        $listing_id = !empty($_POST['listing_id']) ? absint($_POST['listing_id']) : 0;
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        $compare_ids = rtcl()->session->get('rtcl_compare_ids', []);
        $response = [
            'listing_id'               => $listing_id,
            'html'                     => '',
            'type'                     => '',
            'limit_alert_message'      => '',
            'current_listings'         => 0,
            'no_item_selected_message' => esc_html__("No Items Selected", 'classified-listing-pro'),
        ];
        if ($listing_id) {
            if (in_array($listing_id, $compare_ids)) {
                $compare_ids = array_filter($compare_ids, function ($id) use ($listing_id) {
                    return $listing_id != $id;
                });
                $response['type'] = 'remove';
            } else {
                $limit = Functions::get_compare_limit();
                if ($limit > count($compare_ids)) {
                    $compare_ids[] = $listing_id;
                    $response['type'] = 'add';
                } else {
                    $response['limit_alert_message'] = apply_filters('rtcl_compare_limit_alert_message', sprintf(esc_html__("You can not add more then %d listings to the compare."), $limit), $limit, $compare_ids);
                }
            }
        }
        if ($current_listings = count($compare_ids)) {
            rtcl()->session->set('rtcl_compare_ids', $compare_ids);
            $response['current_listings'] = $current_listings;
            $response['html'] = Functions::get_template_html('compare/popup', compact('compare_ids'), '', rtclPro()->get_plugin_template_path());
        } else {
            rtcl()->session->set('rtcl_compare_ids', null);
        }
        wp_send_json($response);
    }

    public static function remove_from_compare() {
        $listing_id = !empty($_POST['listing_id']) ? absint($_POST['listing_id']) : 0;
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        $compare_ids = rtcl()->session->get('rtcl_compare_ids', []);
        if ($listing_id && is_array($compare_ids) && count($compare_ids) > 0 && in_array($listing_id, $compare_ids)) {
            $compare_ids = array_filter($compare_ids, function ($id) use ($listing_id) {
                return $listing_id != $id;
            });
        }
        if (count($compare_ids)) {
            rtcl()->session->set('rtcl_compare_ids', $compare_ids);
        } else {
            rtcl()->session->set('rtcl_compare_ids', null);
        }
        wp_send_json([
            'listing_id'               => $listing_id,
            'current_listings'         => count($compare_ids),
            'no_item_selected_message' => esc_html__("No Items Selected", 'classified-listing-pro'),
            'html'                     => Functions::get_template_html('compare/popup', compact('compare_ids'), '', rtclPro()->get_plugin_template_path())
        ]);
    }

    public static function rtcl_clear_from_compare() {
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        rtcl()->session->set('rtcl_compare_ids', null);
        wp_send_json_success(['no_item_selected_message' => esc_html__("No Items Selected", 'classified-listing-pro')]);
    }

    public static function enqueue_script() {
        wp_enqueue_script('rtcl-compare', rtclPro()->get_assets_uri("js/compare.min.js"), ['jquery', 'rtcl-common'], self::$version);
        $compare_localize = apply_filters('rtcl_compare_localize_options', [
            'ajaxurl'      => self::$ajaxurl,
            'server_error' => esc_html__("Server Error!!", 'classified-listing-pro'),
        ]);
        wp_localize_script('rtcl-compare', 'rtcl_compare', $compare_localize);
    }

    public static function compare_template() {
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        $compare_ids = rtcl()->session->get('rtcl_compare_ids', []);
        Functions::get_template('compare/popup', ['compare_ids' => $compare_ids], '', rtclPro()->get_plugin_template_path());
    }
}