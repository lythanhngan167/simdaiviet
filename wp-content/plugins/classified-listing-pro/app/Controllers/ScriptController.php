<?php

namespace RtclPro\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;
use RtclPro\Helpers\Options;

class ScriptController
{
    static private $suffix;
    static private $version;
    static private $ajaxurl;

    public static function init() {
        self::$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
        self::$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_PRO_VERSION;
        self::$ajaxurl = admin_url('admin-ajax.php');
        if ($current_lang = apply_filters('rtcl_ajaxurl_current_lang', null, self::$ajaxurl)) {
            self::$ajaxurl = add_query_arg('lang', $current_lang, self::$ajaxurl);
        }
        add_filter('rtcl_public_root_var', [__CLASS__, 'public_root_var'], 10, 2);
        add_action('admin_init', [__CLASS__, 'register_admin_scripts']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'load_admin_script_setting_page']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'register_script']);
    }

    public static function register_admin_scripts() {
        wp_register_script('rtcl-pro-admin', rtclPro()->get_assets_uri("js/admin.min.js"), ['jquery', 'rtcl-common'], self::$version, true);
        //TODO: will active later when it is necessary
        //wp_register_style('rtcl-pro-admin', rtclPro()->get_assets_uri("css/admin.min.css"));
    }

    public static function register_script() {
        wp_register_script('photoswipe', rtclPro()->get_assets_uri("vendor/photoswipe/photoswipe.min.js"), '', '4.1.3');
        wp_register_script('photoswipe-ui-default', rtclPro()->get_assets_uri("vendor/photoswipe/photoswipe-ui-default.min.js"), ['photoswipe'], '4.1.3');
        wp_register_script('zoom', rtclPro()->get_assets_uri("vendor/zoom/jquery.zoom.min.js"), ['jquery'], '1.7.21');
        wp_register_style('photoswipe', rtclPro()->get_assets_uri("vendor/photoswipe/photoswipe.css"), '', self::$version);
        wp_register_style('photoswipe-default-skin', rtclPro()->get_assets_uri("vendor/photoswipe/default-skin/default-skin.css"), ['photoswipe'], self::$version);

        $depsScript = ['jquery', 'rtcl-common', 'rtcl-public'];

        wp_register_script('jquery-payment', rtclPro()->get_assets_uri("vendor/jquery.payment.min.js"), ['jquery'], '3.0.0');

        wp_register_script('rtcl-credit-card-form', rtclPro()->get_assets_uri("js/credit-card-form.min.js"), ['jquery-payment', 'rtcl-validator'], self::$version);

        if (Fns::is_enable_chat()) {
            /**
             * If user is logged in send beacon every 15 minutes to set online status
             * If user is logged in then check the Chat notification every 5 second
             */
            wp_register_script('rtcl-beacon', rtclPro()->get_assets_uri("js/beacon.min.js"), ['jquery'], self::$version, true);
            wp_register_script('rtcl-user-chat', rtclPro()->get_assets_uri("js/rtcl-user-chat.min.js"), ['jquery'], self::$version, true);
            wp_register_script('rtcl-chat', rtclPro()->get_assets_uri("js/rtcl-chat" . self::$suffix . ".js"), ['jquery'], self::$version, true);
            $chat_data = [
                'ajaxurl'          => self::$ajaxurl,
                'rest_api_url'     => Link::get_rest_api_url(),
                'date_time_format' => apply_filters('rtcl_chat_date_time_format', 'YYYY, Do MMM h:mm a'),
                'current_user_id'  => get_current_user_id(),
                'lang'             => [
                    'chat_txt'            => esc_html__("Tr?? chuy???n", "classified-listing-pro"),
                    'loading'             => esc_html__("??ang t???i ...", "classified-listing-pro"),
                    'confirm'             => esc_html__("B???n c?? ch???c mu???n x??a kh??ng.", "classified-listing-pro"),
                    'my_chat'             => esc_html__("Tr?? chuy???n c???a t??i", "classified-listing-pro"),
                    'chat_with'           => esc_html__("Tr?? chuy???n v???i", "classified-listing-pro"),
                    'delete_chat'         => esc_html__("X??a tr?? chuy???n", "classified-listing-pro"),
                    'select_conversation' => esc_html__("Vui l??ng ch???n m???t cu???c tr?? chuy???n", "classified-listing-pro"),
                    'no_conversation'     => esc_html__("B???n ch??a c?? cu???c tr?? chuy???n n??o.", "classified-listing-pro"),
                    'message_placeholder' => esc_html__("Nh???p tin nh???n ??? ????y", "classified-listing-pro"),
                    'no_permission'       => esc_html__("Kh??ng c?? quy???n b???t ?????u tr?? chuy???n.", "classified-listing-pro"),
                    'server_error'        => esc_html__("L???i m??y ch???", "classified-listing-pro"),
                ]
            ];
            if (is_user_logged_in()) {
                $depsScript[] = 'rtcl-beacon';
            }

            wp_localize_script('rtcl-chat', 'rtcl_chat', apply_filters('rtcl_localize_chat_data', $chat_data));
            wp_localize_script('rtcl-user-chat', 'rtcl_chat', apply_filters('rtcl_localize_chat_data', $chat_data));
            if (is_singular(rtcl()->post_type)) {
                wp_enqueue_script('rtcl-chat');
            }

            global $wp;

            if (Functions::is_account_page()) {
                if (isset($wp->query_vars['chat'])) {
                    wp_enqueue_script('rtcl-user-chat');
                }
            }
        }

        // wp_dequeue_style( 'rtcl-public' );
        wp_register_script('rtcl-pro-public', rtclPro()->get_assets_uri("js/public" . self::$suffix . ".js"), $depsScript, self::$version, true);
        wp_register_style('rtcl-pro-public', rtclPro()->get_assets_uri("css/public.min.css"), ['rtcl-public'], self::$version);

        if (is_singular(rtcl()->post_type)) {
            wp_enqueue_style('photoswipe-default-skin');
            add_action('wp_footer', [__CLASS__, 'photoswipe_placeholder']);
        }

        wp_enqueue_style('rtcl-pro-public');
        wp_enqueue_script('rtcl-pro-public');
    }


    public static function photoswipe_placeholder() {
        Functions::get_template('listing/photoswipe', [], '', rtclPro()->get_plugin_template_path());
    }

    public static function load_admin_script_setting_page() {

        if (!empty($_GET['post_type']) && $_GET['post_type'] == rtcl()->post_type && !empty($_GET['page']) && $_GET['page'] == 'rtcl-settings') {
            wp_enqueue_script('rtcl-pro-admin');
        }

    }

    public static function public_root_var($rootVar, $options) {
        if (is_array($options) && !empty($options)) {
            if (!empty($options['top'])) {
                $rootVar .= '--rtcl-badge-top-bg-color:' . $options['top'] . ';';
            }
            if (!empty($options['top_text'])) {
                $rootVar .= '--rtcl-badge-top-color:' . $options['top_text'] . ';';
            }
            if (!empty($options['popular'])) {
                $rootVar .= '--rtcl-badge-popular-bg-color:' . $options['popular'] . ';';
            }
            if (!empty($options['popular_text'])) {
                $rootVar .= '--rtcl-badge-popular-color:' . $options['popular_text'] . ';';
            }
            if (!empty($options['bump_up'])) {
                $rootVar .= '--rtcl-badge-bump_up-bg-color:' . $options['bump_up'] . ';';
            }
            if (!empty($options['bump_up_text'])) {
                $rootVar .= '--rtcl-badge-bump_up-color:' . $options['bump_up_text'] . ';';
            }
        }

        return $rootVar;
    }

}