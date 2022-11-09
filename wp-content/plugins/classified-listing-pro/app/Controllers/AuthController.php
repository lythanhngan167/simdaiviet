<?php

namespace RtclPro\Controllers;


use WP_Error;
use WP_User;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclPro\Models\UserAuthentication;

/**
 * Class UserAuthentication
 *
 * @package Rtcl\Controllers
 */
class AuthController
{

    public static function init() {
        add_action('user_register', [__CLASS__, 'user_register'], 999);
        add_filter('authenticate', [__CLASS__, 'check_active_user'], 100, 2);
        add_action('wp_ajax_rtcl_resend_verify', [__CLASS__, 'rtcl_resend_verify_ajax_cb']);
        add_action('wp_ajax_nopriv_rtcl_resend_verify', [__CLASS__, 'rtcl_resend_verify_ajax_cb']);
    }

    /**
     * Resend verification link
     * Ajax callback
     */
    public static function rtcl_resend_verify_ajax_cb() {
        if (!Functions::verify_nonce()) {
            wp_send_json_error([
                "message" => esc_html__("Session Error!!", "classified-listing-pro")
            ]);
        }

        $user_id = (isset($_POST['user_id']) && !empty($_POST['user_id']) ? absint($_POST['user_id']) : null);

        if (!$user_id) {
            $user_login = (isset($_POST['user_login']) && !empty($_POST['user_login']) ? trim(esc_attr($_POST['user_login'])) : null);
            $user = get_user_by('login', $user_login) ?: get_user_by('email', $user_login);
            $user_id = $user ? (int)$user->ID : null;
        }

        if (!$user_id) {
            wp_send_json_error([
                'message' => esc_html__('Invalid request.', 'classified-listing-pro')
            ]);
        }

        // Admin request
        if (current_user_can('edit_users')) {
            $error = false;
            UserAuthentication::send_verification_link($user_id);
            $message = esc_html__('Verification link sent to user\'s email address', 'classified-listing-pro');
        } elseif (Fns::needs_validation($user_id)) {
            $attempts = absint(get_user_meta($user_id, 'rtcl_verify_link_attempts', true));
            // Avoid repetitively asking for re-send the verification link
            if ($attempts <= absint(Functions::get_option_item('rtcl_account_settings', 'verify_max_resend_allowed'))) {
                UserAuthentication::send_verification_link($user_id);
                update_user_meta($user_id, 'rtcl_verify_link_attempts', $attempts + 1);
                $error = false;
                $message = esc_html__('Verification link sent to your email address', 'classified-listing-pro');
            } else {
                $error = true;
                $message = esc_html__('You have tried re-sending verification link too many times, please contact site administrators.', 'classified-listing-pro');
            }

        } else {
            $error = true;
            $message = esc_html__('Your email address is already verified.', 'classified-listing-pro');
        }
        wp_send_json([
            'success' => !$error,
            'data'    => ['message' => $message]
        ]);
    }

    /**
     * Prevents users from logging in, if they have not verified their email address
     *
     * @param WP_User $user
     * @param String  $username
     *
     * @return WP_Error|WP_User
     */
    public static function check_active_user($user, $username) {
        if (is_wp_error($user) || !Functions::get_option_item('rtcl_account_settings', 'user_verification', '', 'checkbox')) {
            return $user;
        }

        $key = get_user_meta($user->ID, "rtcl_verification_key", true);

        if (!empty($key)) {
            return new WP_Error('email_not_verified', wp_kses(sprintf(__('You have not verified your email address, please check your email and click on verification link we sent you. <a href="javascript:;" id="rtcl-resend-verify-link" data-login="%s">Re-send the link</a>', 'classified-listing-pro'),
                $username
            ), ['a' => ['href' => true, 'id' => true, 'data-login' => true]]));
        }

        return $user;
    }

    /**
     * Creates a hash when new user registers and stores the hash as a meta value
     *
     * @param int $user_id
     */
    public static function user_register($user_id) {
        if (!Functions::get_option_item('rtcl_account_settings', 'user_verification', '', 'checkbox')) {
            return; // ignore adding verification key
        }

        UserAuthentication::send_verification_link($user_id);
    }

}