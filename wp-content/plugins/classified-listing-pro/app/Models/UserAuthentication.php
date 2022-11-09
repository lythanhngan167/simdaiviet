<?php

namespace RtclPro\Models;

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;
use WP_User;

class UserAuthentication
{

    /**
     * @var String $secret
     */
    public static $secret = "25#-asdv8+abox";

    /**
     * Lock user's account, send a verification email and ask them to verify their email address
     *
     * @param int $user_id
     */
    public static function send_verification_link($user_id) {
        $user = get_user_by('id', $user_id);

        self::lock_user($user);
        self::send_email($user);
    }


    /**
     * Send verification email
     *
     * @param WP_User $user
     */
    public static function send_email($user = null) {
        if (!$user || !is_a($user, WP_User::class)) {
            return;
        }

        $reset_key = get_user_meta($user->ID, "rtcl_verification_key", true);

        // Ignore if there is no lock
        if (!$reset_key || empty($reset_key)) {
            return;
        }

        rtcl()->mailer()->emails['User_Verify_Link_Email_To_User']->trigger($user, $reset_key);

    }



    /**
     * Validate hash
     *
     * @return bool|void
     */
    public static function hash_valid() {
        if (empty($_GET['verify_email']) || empty($_GET['user_id']) || !preg_match('/^[a-f0-9]{32}$/', $_GET['verify_email'])) {
            Functions::add_notice(esc_html__("Your account hash, user id must be set", "classified-listing-pro"), "error");

            return;
        }

        $user_id = absint($_GET['user_id']);

        // user already verified
        if (!Fns::needs_validation($user_id)) {
            Functions::add_notice(esc_html__("Your account email address already verified", "classified-listing-pro"), "error");

            return;
        }

        $hash = $_GET['verify_email'];

        if ($hash === get_user_meta($user_id, 'rtcl_verification_key', true)) {
            return true;
        } else {
            Functions::add_notice(esc_html__("Your account hash is not matched", "classified-listing-pro"), "error");

            return false;
        }
    }

    /**
     * Verify user's email
     *
     * @param bool $signon
     *
     * @return bool|void
     */
    public static function verify_if_valid($signon = false) {
        if (!self::hash_valid()) {
            return;
        }

        $user_id = absint($_GET['user_id']);
        $user = get_user_by('id', $user_id);

        // Unlock user from logging in
        self::unlock_user($user);
        Functions::add_notice(esc_html__("Your account email address is verified", "classified-listing-pro"));

        return true;
    }


    /**
     * Lock user
     *
     * @param WP_User $user
     */
    public static function lock_user($user) {
        add_user_meta($user->ID, 'rtcl_verification_key', self::generate_hash($user->data->user_email));
    }

    /**
     * Unlock user
     *
     * @param WP_User $user
     */
    public static function unlock_user($user) {
        delete_user_meta($user->ID, 'rtcl_verification_key');
    }


    /**
     * Generate a url-friendly verification hash
     *
     * @param string $email
     *
     * @return string
     */
    public static function generate_hash($email = '') {
        $key = $email . self::$secret . rand(0, 1000);

        return MD5($key);
    }
}