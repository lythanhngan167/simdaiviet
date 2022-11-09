<?php

namespace RtclPro\Emails;

use WP_User;
use Rtcl\Helpers\Link;
use Rtcl\Models\RtclEmail;
use Rtcl\Helpers\Functions;

class UserVerifyLinkEmailToUser extends RtclEmail
{
    public $user = null;
    public $verify_link = '';

    function __construct() {

        $this->id = 'user_verify_link';
        $this->template_html = 'emails/user-verify-link-email';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_title}] Verify your email address', 'classified-listing-pro');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('Verify your email address', 'classified-listing-pro');
    }


    /**
     * Trigger the sending of this email.
     *
     * @param       $user WP_User
     * @param       $reset_key
     *
     * @return void
     * @throws \Exception
     */
    public function trigger($user, $reset_key = null) {

        if (!is_a($user, WP_User::class) || !$reset_key) {
            return;
        }

        $this->setup_locale();
        $this->user = $user;
        $this->verify_link = add_query_arg([
            'user_id'      => $user->ID,
            'verify_email' => $reset_key
        ], Link::get_account_endpoint_url('verify'));
        $this->set_recipient($user->user_email);

        if ($this->get_recipient()) {
            $this->send();
        }

        $this->restore_locale();

    }


    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        return Functions::get_template_html(
            $this->template_html,
            [
                'email'       => $this,
                'user'        => $this->user,
                'verify_link' => $this->verify_link
            ],
            '', rtclPro()->get_plugin_template_path()
        );
    }


}