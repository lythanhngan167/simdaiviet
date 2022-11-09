<?php


namespace RtclPro\Controllers;


use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;
use RtclPro\Helpers\Installer;
use RtclPro\Helpers\PNHelper;
use RtclPro\Models\Conversation;
use RtclPro\Models\PushNotification;

class ChatController
{
    public static function init() {
        if (Fns::is_enable_chat()) {
            add_action('wp_ajax_rtcl_chat_ajax_delete_conversations', [
                __CLASS__,
                'rtcl_chat_ajax_hide_conversations'
            ]);
            add_action('wp_ajax_rtcl_chat_ajax_get_conversations', [__CLASS__, 'rtcl_chat_ajax_get_conversations']);
            add_action('wp_ajax_rtcl_chat_ajax_start_conversation', [
                __CLASS__,
                'rtcl_chat_ajax_start_conversation'
            ]);
            add_action('wp_ajax_nopriv_rtcl_chat_ajax_start_conversation', [
                __CLASS__,
                'rtcl_chat_ajax_start_conversation'
            ]);
            add_action('wp_ajax_rtcl_chat_ajax_send_message', [__CLASS__, 'rtcl_chat_ajax_send_message']);
            add_action('wp_ajax_rtcl_chat_ajax_visitor_send_message', [
                __CLASS__,
                'rtcl_chat_ajax_visitor_send_message'
            ]);
            add_action('wp_ajax_rtcl_chat_ajax_get_messages', [__CLASS__, 'rtcl_chat_ajax_get_messages']);
            add_action('wp_ajax_rtcl_chat_ajax_message_mark_as_read', [
                __CLASS__,
                'rtcl_chat_ajax_message_mark_as_read'
            ]);
            add_action('wp_ajax_rtcl_chat_ajax_get_unread_message_num', [
                __CLASS__,
                'rtcl_chat_ajax_get_unread_message_num'
            ]);
            add_filter('rtcl_chat_sanitize_message', [__CLASS__, 'rtcl_chat_sanitize_message']);
            //add_filter('rtcl_before_delete_listing', [__CLASS__, 'delete_chat_conversation']); TODO : Add this when foreign key is removed from database
        }

        if (is_admin()) {
            add_action('init', [__CLASS__, 'regenerate_chat_table']);
        }

    }

    static function regenerate_chat_table() {
        if (isset($_GET['rtcl_regenerate_chat_table']) && Functions::verify_nonce()) {
            global $wpdb;

            $tables = [
                $wpdb->prefix . "rtcl_conversations",
                $wpdb->prefix . "rtcl_conversation_messages"
            ];
            $wpdb->query("SET SESSION foreign_key_checks = 0");
            foreach ($tables as $table) {
                if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) {
                    $wpdb->query("DROP TABLE IF EXISTS {$table}");
                }
            }
            $wpdb->query("SET SESSION foreign_key_checks = 1");

            $wpdb->hide_errors();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $schemas = Installer::get_chat_table_schema();
            if (!empty($schemas)) {
	            dbDelta( $schemas );
            }
            Functions::add_notice(__("Chat table has been regenerated", 'classified-listing-pro'));
        }
    }

    static function delete_chat_conversation($listing_id) {
        global $wpdb;
        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT con_id FROM {$wpdb->prefix}rtcl_conversations WHERE listing_id = %d LIMIT 500",
            $listing_id
        ));
        if (!empty($ids)) {
            $wpdb->query(sprintf('DELETE FROM %s WHERE con_id IN (%s)', $wpdb->prefix . 'rtcl_conversations', implode(',', $ids)));
        }
    }

    static function rtcl_chat_sanitize_message($message) {
        // Strip all tags
        $message = strip_tags($message);

        // Limit the letter
        $limit = apply_filters('rtcl_chat_sanitize_message_character_limit', 300);
        if (strlen($message) > $limit) {
            $message = mb_substr($message, 0, $limit, "utf-8");
        }

        return $message;
    }

    static function rtcl_chat_ajax_get_unread_message_num() {
        echo self::has_unread_messages();

        die();
    }

    /*
    * Has unread messages?
    */
    static public function has_unread_messages() {
        $count = '';
        if (is_user_logged_in()) {
            global $wpdb;

            $user_id = get_current_user_id();
            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(message_id) FROM {$wpdb->prefix}rtcl_conversations AS rc LEFT JOIN {$wpdb->prefix}rtcl_conversation_messages AS rcm ON rc.con_id = rcm.con_id WHERE ( ( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 ) ) AND is_read = 0 AND source_id != %d", $user_id, $user_id, $user_id));
        }

        return apply_filters('rtcl_chat_has_unread_messages_count', $count);
    }

    static function rtcl_chat_ajax_message_mark_as_read() {
        $message_id = isset($_POST['message_id']) ? absint($_POST['message_id']) : 0;
        if (is_user_logged_in() && $message_id && $user_id = get_current_user_id()) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}rtcl_conversation_messages SET is_read = 1 WHERE message_id = %d", $message_id));
        }
        wp_send_json_success();
    }

    static function rtcl_chat_ajax_hide_conversations() {
        $con_ids = isset($_POST['con_ids']) && is_array($_POST['con_ids']) ? $_POST['con_ids'] : [];
        $response = ['success' => false];
        if (is_user_logged_in() && !empty($con_ids) && $user_id = get_current_user_id()) {
            global $wpdb;
            $con_ids = implode(',', $con_ids);
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}rtcl_conversations SET sender_delete = ( CASE WHEN sender_id = %d THEN 1 ELSE sender_delete END ), recipient_delete = ( CASE WHEN recipient_id = %d THEN 1 ELSE recipient_delete END ) WHERE con_id IN ( " . esc_sql($con_ids) . " )", $user_id, $user_id));
            $response['success'] = true;
        }

        wp_send_json($response);
    }


    static function rtcl_chat_ajax_get_conversations() {
        $response = ['success' => false, 'data' => []];
        if (is_user_logged_in() && $user_id = get_current_user_id()) {
            $response['success'] = true;
            $response['data'] = self::_fetch_conversations($user_id);
        }

        wp_send_json($response);
    }

    static function _fetch_conversations($user_id) {
        if (!$user_id) {
            return [];
        }

        global $wpdb;

        $query = $wpdb->prepare("
			SELECT SQL_CALC_FOUND_ROWS rc.*, message, is_read, source_id, rcm.message as last_message, rcm.created_at as last_message_created_at, display_name, user_login, CASE WHEN sender_id = %d THEN recipient_id ELSE sender_id END AS other_id 
			FROM {$wpdb->prefix}rtcl_conversations AS rc 
				LEFT JOIN {$wpdb->prefix}rtcl_conversation_messages AS rcm ON rcm.message_id = last_message_id 
				LEFT JOIN {$wpdb->prefix}users AS users ON users.ID = ( CASE WHEN sender_id = %d THEN recipient_id ELSE sender_id END ) 
			WHERE (( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 ))", $user_id, $user_id, $user_id, $user_id);

        $query .= $wpdb->prepare("ORDER BY rcm.created_at DESC LIMIT %d", 50);
        $conversations = $wpdb->get_results($query);
        $conversations_data = [];
        if (!empty($conversations)) {
	        foreach ($conversations as $conversation) {
                if ($listing = rtcl()->factory->get_listing($conversation->listing_id)) {
                    $conversation->listing = [
                        'id'       => absint($conversation->listing_id),
                        'title'    => $listing->get_the_title(),
                        'url'      => $listing->get_the_permalink(),
                        'images'   => Functions::get_listing_images($conversation->listing_id),
                        'amount'   => strip_tags($listing->get_formatted_price()),
                        'location' => $listing->get_locations(),
                        'category' => $listing->get_categories(),
                    ];
	                $unread = $wpdb->get_col($wpdb->prepare("SELECT COUNT(message_id) FROM {$wpdb->prefix}rtcl_conversations AS rc LEFT JOIN {$wpdb->prefix}rtcl_conversation_messages AS rcm ON rc.con_id = rcm.con_id WHERE ( ( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 ) ) AND is_read = 0 AND source_id != %d AND rc.con_id = %d", $user_id, $user_id, $user_id, $conversation->con_id));
	                $conversation->unread_count = !empty($unread[0]) ? $unread[0] : 0;
                    $conversations_data[] = $conversation;
                }
            }
        }

        return $conversations_data;
    }

    static function rtcl_chat_ajax_start_conversation() {
        $listing_id = isset($_REQUEST['listing_id']) ? absint($_REQUEST['listing_id']) : 0;
        $visitor_id = get_current_user_id();
        $response['success'] = false;
        if (is_user_logged_in() && $listing_id && ($listing = rtcl()->factory->get_listing($listing_id)) && $listing->exists() && $visitor_id !== $listing->get_author_id()) {
            $author_id = $listing->get_author_id();
            $response['success'] = true;
            if ($con_id = self::has_conversation_started($visitor_id, $author_id, $listing_id)) {
                $response['con_id'] = $con_id;
                $conversation = new Conversation($con_id);
                $response['con_messages'] = $conversation->messages();
            }
        }

        wp_send_json($response);

    }

    static public function has_conversation_started($visitor_id, $author_id, $listing_id) {
        $listing_id = empty($listing_id) ? get_the_ID() : $listing_id;
        $db = rtcl()->db();
        $con_table = $db->prefix . 'rtcl_conversations';
        $id = $db->get_var($db->prepare("SELECT con_id FROM {$con_table} WHERE ( ( sender_id = %d AND recipient_id = %d ) OR ( sender_id = %d AND recipient_id = %d ) ) AND sender_delete = 0 AND recipient_delete = 0 AND listing_id = %d", $visitor_id, $author_id, $author_id, $visitor_id, $listing_id));
        if (!empty($id)) {
            return absint($id);
        }

        return false;
    }

    static function rtcl_chat_ajax_get_messages() {
        $con_id = !empty($_POST['con_id']) ? absint($_POST['con_id']) : 0;
        $limit = !empty($_POST['limit']) ? absint($_POST['limit']) : 50;
        $response = [
            'success'  => false,
            'messages' => []
        ];
        if ($con_id && self::_is_valid_conversation($con_id)) {
            Fns::update_chat_conversation_status($con_id);
            $response['success'] = true;
            $response['messages'] = self::_fetch_conversation_messages($con_id, $limit);
        }
        wp_send_json($response);
    }

    /**
     * @param $con_id
     *
     * @param $limit
     *
     * @return array|object|null
     */
    static function _fetch_conversation_messages($con_id, $limit = 50) {
        $message_table = rtcl()->db()->prefix . 'rtcl_conversation_messages';

        return rtcl()->db()->get_results(rtcl()->db()->prepare("SELECT * FROM {$message_table} WHERE con_id = %d LIMIT %d", $con_id, $limit));
    }

    static function _set_message_read($con_id, $message_id) {
        if ($con_id && $message_id && $user_id = get_current_user_id()) {
            global $wpdb;

            return $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}rtcl_conversation_messages SET is_read = 1 WHERE con_id = %d AND message_id = %d", $con_id, $message_id));
        }

        return false;
    }

    static function _delete_conversation($con_id) {
        if (is_user_logged_in() && !empty($con_id) && $user_id = get_current_user_id()) {
            global $wpdb;
            $con_ids = !is_array($con_id) ? [$con_id] : $con_id;
            $con_ids = implode(',', $con_ids);

            return $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}rtcl_conversations SET sender_delete = ( CASE WHEN sender_id = %d THEN 1 ELSE sender_delete END ), recipient_delete = ( CASE WHEN recipient_id = %d THEN 1 ELSE recipient_delete END ) WHERE con_id IN ( " . esc_sql($con_ids) . " )", $user_id, $user_id));
        }

        return false;
    }

    static function _is_valid_conversation($con_id) {
        if ($user_id = get_current_user_id()) {
            $conversations_table = rtcl()->db()->prefix . 'rtcl_conversations';

            return rtcl()->db()->get_var(rtcl()->db()->prepare("SELECT con_id FROM {$conversations_table} WHERE con_id = %d AND ( ( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 ) )", absint($con_id), $user_id, $user_id));
        }

        return false;
    }

    /**
     * @param $con_id
     * @param $listing_id
     * @param $text
     *
     * @return false|object
     */
    static function _send_message($con_id, $listing_id, $text) {
        if (is_user_logged_in() && $text && $listing_id && ($listing = rtcl()->factory->get_listing($listing_id)) && ($conversation = new Conversation($con_id)) && $conversation->exist() && $conversation->listing_id === $listing->get_id()) {
            return $conversation->sent_message($text);
        }

        return false;
    }


    static function rtcl_chat_ajax_send_message() {
        $listing_id = !empty($_POST['listing_id']) ? absint($_POST['listing_id']) : 0;
        $message = !empty($_POST['message']) ? $_POST['message'] : '';
        $con_id = !empty($_POST['con_id']) ? absint($_POST['con_id']) : 0;
        $response = ['success' => false];
        if (is_user_logged_in() && $listing_id && ($listing = rtcl()->factory->get_listing($listing_id)) && ($conversation = new Conversation($con_id)) && $conversation->exist() && $conversation->listing_id === $listing->get_id()) {
            $response = $conversation->sent_message($message);
            if ($response) {
                $response->success = true;
            }
        }

        wp_send_json($response);
    }

    static function rtcl_chat_ajax_visitor_send_message() {
        $listing_id = !empty($_POST['listing_id']) ? absint($_POST['listing_id']) : '';
        $visitor_id = get_current_user_id();
        $response = ['success' => false];
        if ($visitor_id && $listing_id && ($listing = rtcl()->factory->get_listing($listing_id)) && $visitor_id !== $listing->get_author_id()) {
            $message = !empty($_POST['message']) ? $_POST['message'] : '';
            $con_id = !empty($_POST['con_id']) ? absint($_POST['con_id']) : 0;
            if (!empty($con_id)) {
                $conversation = new Conversation($con_id);
                if (($started_con_id = $conversation->has_started($visitor_id, $listing->get_author_id(), $listing_id)) && $started_con_id === $conversation->get_id()) {
                    $response = $conversation->sent_message($message);
                    $response->success = true;
                }
            } else {
                $response = self::initiate_new_conversation_write_message(array(
                    'listing_id'   => $listing_id,
                    'sender_id'    => get_current_user_id(),
                    'recipient_id' => $listing->get_author_id()
                ), $message);
                $response->success = true;
            }

        }

        wp_send_json($response);
    }

    /**
     * @param array $conversation_data
     * @param       $message
     *
     * @return mixed
     */
    static function initiate_new_conversation_write_message($conversation_data, $message) {
        if (!empty($conversation_data)) {
            $conversation = new Conversation($conversation_data);
            if ($conversation->save()) {
                $response = $conversation->sent_message($message);
                if (Fns::is_enable_chat_unread_message_email()) {
                    rtcl()->mailer()->emails['Unread_Message_Email']->trigger($conversation, $message);
                }
                $pn = new PushNotification();
                $pn->notify_user(PNHelper::EVENT_CHAT, [
                    'user_id' => $conversation->recipient_id,
                    'object'  => $response
                ]);
            }

            return $response;
        }

        return [];
    }

}
