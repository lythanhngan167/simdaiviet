<?php

namespace RtclPro\Models;

class Message
{
    private $con_table;
    private $message_table;
    private $message_id;
    public $con_id;
    public $message;
    public $source_id;
    public $is_read;
    public $created_at;

    function __construct($data = []) {
        $this->con_table = rtcl()->db()->prefix . 'rtcl_conversations';
        $this->message_table = rtcl()->db()->prefix . 'rtcl_conversation_messages';

        if (is_array($data) && !empty($data)) {
            $this->setData($data);
        } else if ($data && is_int($data)) {
            $this->message_id = $data;
            $this->setData();
        }
    }

    function exist() {
        return $this->message_id && $this->con_id;
    }

    function get_id() {
        return $this->message_id;
    }

    function get_conversation_id() {
        return $this->con_id;
    }


    public function getData() {
        $data = $this->__getData();
        $data = ['message_id' => $this->message_id] + $data;

        return (object)$data;
    }

    private function setData($raw_data = []) {
        if (is_array($raw_data) && !empty($raw_data)) {
            $raw_data = wp_parse_args($raw_data, [
                'con_id'     => isset($raw_data['con_id']) ? absint($raw_data['con_id']) : 0,
                'message'    => isset($raw_data['message']) ? apply_filters('rtcl_chat_sanitize_message', $raw_data['message']) : 0,
                'source_id'  => isset($raw_data['source_id']) ? $raw_data['source_id'] : get_current_user_id(),
                'is_read'    => isset($raw_data['is_read']) ? $raw_data['is_read'] : 0,
                'created_at' => isset($raw_data['created_at']) ? $raw_data['created_at'] : current_datetime()->format('Y-m-d H:i:s'),
            ]);
            $data = (object)$raw_data;
        } else {
            $data = $this->get_by_id();
        }
        if ($data && is_object($data)) {
            $this->message_id = !empty($data->message_id) ? $data->message_id : $this->message_id;
            $this->con_id = $data->con_id;
            $this->message = apply_filters('rtcl_chat_sanitize_message', $data->message);
            $this->source_id = $data->source_id;
            $this->is_read = $data->is_read;
            $this->created_at = $data->created_at;
        }
    }

    private function __getData() {
        return [
            'con_id'     => $this->con_id ? $this->con_id : 0,
            'source_id'  => $this->source_id ? $this->source_id : get_current_user_id(),
            'message'    => $this->message ? apply_filters('rtcl_chat_sanitize_message', $this->message) : '',
            'is_read'    => $this->is_read ? $this->is_read : 0,
            'created_at' => $this->created_at ? $this->created_at : current_datetime()->format('Y-m-d H:i:s')
        ];
    }

    function has_unread_messages() {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $unread = rtcl()->db()->get_col(rtcl()->db()->prepare("SELECT COUNT(message_id) FROM {$this->con_table} AS rc LEFT JOIN {$this->message_table} AS rcm ON rc.con_id = rcm.con_id WHERE ( ( sender_id = %d AND sender_delete = 0 ) OR ( recipient_id = %d AND recipient_delete = 0 ) ) AND is_read = 0 AND source_id != %d", $user_id, $user_id, $user_id));

            if ($unread[0] > 0) {
                return $unread[0];
            }
        }
    }

    function update() {
        $data = $this->__getData();
        if ($this->get_id()) {
            return rtcl()->db()->update(
                "{$this->message_table}",
                $data,
                array(
                    'message_id' => $this->get_id()
                )
            );
        }

        return false;
    }

    function save() {
        $data = $this->__getData();
        if ($data['con_id'] && $data['message']) {
            $result = rtcl()->db()->insert(
                $this->message_table,
                $data,
                array(
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%s'
                )
            );
            if ($result) {
                $this->message_id = rtcl()->db()->insert_id;
                $this->setData($data);

                return $this->message_id;
            }
        }

        return null;
    }

    private function get_by_id() {
        if ($this->get_id()) {
            $data = rtcl()->db()->get_row(rtcl()->db()->prepare("SELECT * FROM {$this->message_table} WHERE message_id = %d", $this->get_id()));
            if ($data) {
                $this->setData($data);
            }
        }

        return null;
    }

    public function conversation() {
        if ($this->exist()) {
            return new Conversation($this->con_id);
        }

        return null;
    }


}