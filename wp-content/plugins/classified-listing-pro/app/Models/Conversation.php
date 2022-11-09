<?php


namespace RtclPro\Models;


use RtclPro\Helpers\PNHelper;

class Conversation
{

    private $con_table;
    private $message_table;
    private $message;
    private $con_id;
    public $listing_id;
    public $sender_id;
    public $recipient_id;
    public $last_message_id = 0;
    public $sender_review = 0;
    public $recipient_review = 0;
    public $invert_review = 0;
    public $sender_delete = 0;
    public $recipient_delete = 0;


    function __construct($data = []) {
        $this->con_table = rtcl()->db()->prefix . 'rtcl_conversations';
        $this->message_table = rtcl()->db()->prefix . 'rtcl_conversation_messages';

        if (is_array($data) && !empty($data)) {
            $this->setData($data);
        } else if ($data && is_int($data)) {
            $this->con_id = absint($data);
            $this->setData();
        }

    }

    public function get_id() {
        return $this->con_id;
    }

    public function exist() {
        return $this->con_id && $this->listing_id;
    }

    public function getData() {
        $data = $this->__getData();
        $data = ['con_id' => $this->con_id] + $data;

        return (object)$data;
    }

    private function setData($raw_data = []) {
        if (is_array($raw_data) && !empty($raw_data)) {
            $raw_data = wp_parse_args($raw_data, [
                'listing_id'       => isset($raw_data['listing_id']) ? absint($raw_data['listing_id']) : 0,
                'sender_id'        => isset($raw_data['sender_id']) ? absint($raw_data['sender_id']) : 0,
                'recipient_id'     => isset($raw_data['recipient_id']) ? absint($raw_data['recipient_id']) : 0,
                'sender_delete'    => 0,
                'recipient_delete' => 0,
                'last_message_id'  => 0,
                'sender_review'    => 0,
                'recipient_review' => 0,
                'invert_review'    => 0
            ]);
            $data = (object)$raw_data;
        } else {
            $data = $this->get_by_id();
        }
        if ($data && is_object($data)) {
            $this->con_id = !empty($data->con_id) ? absint($data->con_id) : $this->con_id;
            $this->listing_id = absint($data->listing_id);
            $this->sender_id = absint($data->sender_id);
            $this->recipient_id = absint($data->recipient_id);
            $this->sender_delete = $data->sender_delete;
            $this->recipient_delete = $data->recipient_delete;
            $this->last_message_id = absint($data->last_message_id);
            $this->sender_review = $data->sender_review;
            $this->recipient_review = $data->recipient_review;
            $this->invert_review = $data->invert_review;
        }
    }

    private function __getData() {
        return [
            'listing_id'       => absint($this->listing_id),
            'sender_id'        => absint($this->sender_id),
            'recipient_id'     => absint($this->recipient_id),
            'sender_delete'    => absint($this->sender_delete),
            'recipient_delete' => absint($this->recipient_delete),
            'last_message_id'  => absint($this->last_message_id),
            'sender_review'    => absint($this->sender_review),
            'recipient_review' => absint($this->recipient_review),
            'invert_review'    => absint($this->invert_review)
        ];
    }


    function sent_message($chat_msg) {
        if ($chat_msg) {
            $message = new Message();
            $message->con_id = $this->get_id();
            $message->message = $chat_msg;
            $message->save();
            if ($message->exist()) {
                $this->last_message_id = $message->get_id();
                $this->update();
                $dataObject = $message->getData();
                $pn = new PushNotification();
                $pn->notify_user(PNHelper::EVENT_CHAT, [
                    'object' => $dataObject
                ]);
                return $dataObject;
            }
        }

    }


    /**
     * @param integer $visitor_id
     * @param integer $author_id
     * @param integer $listing_id
     *
     * @return false|int conversion id
     */
    function has_started($visitor_id, $author_id, $listing_id) {
        $listing_id = empty($listing_id) ? get_the_ID() : $listing_id;

        $id = rtcl()->db()->get_var(rtcl()->db()->prepare("SELECT con_id FROM {$this->con_table} WHERE ( ( sender_id = %d AND recipient_id = %d ) OR ( sender_id = %d AND recipient_id = %d ) ) AND sender_delete = 0 AND recipient_delete = 0 AND listing_id = %d", $visitor_id, $author_id, $author_id, $visitor_id, $listing_id));
        if (!empty($id)) {
            return absint($id);
        }

        return false;
    }

    /**
     * @return array|object|void|null
     */
    private function get_by_id() {
        if ($this->get_id()) {
            return rtcl()->db()->get_row(rtcl()->db()->prepare("SELECT * FROM {$this->con_table} WHERE con_id = %d", $this->get_id()));
        }
    }

    /**
     * @param int $limit
     *
     * @return array|object|null
     */
    public function messages($limit = 50) {
        if ($this->exist()) {
            return rtcl()->db()->get_results(rtcl()->db()->prepare("SELECT * FROM {$this->message_table} WHERE con_id = %d LIMIT %d", $this->get_id(), $limit));
        }

        return null;
    }

    public function update() {
        $data = $this->__getData();
        if ($this->get_id() && $data['listing_id'] && $data['sender_id'] && $data['recipient_id']) {
            return rtcl()->db()->update(
                $this->con_table,
                $data,
                array(
                    'con_id' => $this->get_id()
                )
            );
        }

        return false;
    }

    public function save() {
        $data = $this->__getData();
        if ($data['listing_id'] && $data['sender_id'] && $data['recipient_id']) {
            $existing = $this->has_started($data['sender_id'], $data['recipient_id'], $data['listing_id']);
            if (!$existing) {
                $result = rtcl()->db()->insert(
                    $this->con_table,
                    $data,
                    array(
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d'
                    )
                );
                if ($result) {
                    $this->con_id = rtcl()->db()->insert_id;
                    $this->setData((object)$data);

                    return $this->con_id;
                }

            }
        }

        return false;
    }
}