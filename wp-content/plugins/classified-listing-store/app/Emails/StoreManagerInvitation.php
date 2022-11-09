<?php

namespace RtclStore\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

class StoreManagerInvitation extends RtclEmail
{
    protected $user;
    protected $data = ['key' => ''];

    function __construct() {
        $this->id = 'store_manager_invitation';
        $this->template_html = 'emails/store-manager-invitation';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return __('[{site_title}] Store Manager Invitation : Store', 'classified-listing-store');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return __('Store Manager Invitation Request', 'classified-listing-store');
    }

    /**
     * Trigger the sending of this email.
     *
     * @param int   $id Store id
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function trigger($id, $data = []) {
        $trigger = false;
        if (!$id || (!$store = rtclStore()->factory->get_store($id)) || !isset($data['user_id']) || !isset($data['key']) || !wp_is_uuid($data['key']) || (!$user = get_user_by('id', $data['user_id']))) {
            return false;
        }
        $this->setup_locale();
        $this->object = $store;
        $this->user = $user;
        $this->data = ['key' => $data['key']];
        $this->set_recipient($user->user_email);

        if ($this->get_recipient()) {
            $trigger = $this->send();
        }

        $this->restore_locale();
        return $trigger;
    }


    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        return Functions::get_template_html(
            $this->template_html, array(
                'store' => $this->object,
                'user'  => $this->user,
                'data'  => $this->data,
                'email' => $this,
            ), '', rtclStore()->get_plugin_template_path()
        );
    }
}
