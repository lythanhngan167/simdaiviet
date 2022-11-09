<?php

namespace RtclPro\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\RtclEmail;
use RtclPro\Helpers\Fns;
use RtclPro\Models\Conversation;

class UnreadMessageEmail extends RtclEmail
{
    public $data = array();

    function __construct() {

        $this->id = 'unread_message';
        $this->template_html = 'emails/unread-message';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_title}] New Messages Waiting ({listing_title})', 'classified-listing-pro');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('New Message "{listing_title}"', 'classified-listing-pro');
    }

    /**
     * Trigger the sending of this email.
     *
     * @param Conversation $conversation
     * @param string       $message
     *
     * @return void
     * @throws \Exception
     */
    public function trigger($conversation, $message = null) {

        if (!is_a($conversation, Conversation::class) || !$conversation->exist() || !$message) {
            return;
        }

        /*let's inform recipient if he is offline*/
        $recipient_id = get_current_user_id() == $conversation->sender_id ? $conversation->recipient_id : $conversation->sender_id;
        if (!Fns::is_online($recipient_id)) {
            $this->setup_locale();

            $sender_id = get_current_user_id() == $conversation->sender_id ? $conversation->sender_id : $conversation->recipient_id;
            $sender = get_user_by('ID', $sender_id);
            $recipient = get_user_by('ID', $recipient_id);
            $this->object = rtcl()->factory->get_listing($conversation->listing_id);
            $this->placeholders = wp_parse_args(array(
                '{listing_title}' => $this->object->get_the_title()
            ), $this->placeholders);
            $this->set_recipient($recipient->user_email);
            $this->data['sender_name'] = Functions::get_author_name($sender);
            $this->data['recipient_name'] = Functions::get_author_name($recipient);
            $this->data['message'] = $message;
            $this->data['conversation_url'] = add_query_arg([
                'con_id' => $conversation->get_id()
            ], Link::get_my_account_page_link('chat'));
            if ($this->get_recipient()) {
                $this->send();
            }

            $this->restore_locale();
        }
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
                'listing' => $this->object,
                'email'   => $this,
                'data'    => $this->data
            ], '', rtclPro()->get_plugin_template_path()
        );
    }

}