<?php

namespace RtclPro\Helpers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\Payment;
use Rtcl\Models\Pricing;
use RtclPro\Models\Conversation;
use RtclPro\Models\Message;

class PNHelper
{
    const EVENT_LISTING_APPROVED = 'listing_approved';
    const EVENT_LISTING_EXPIRED = 'listing_expired';
    const EVENT_CHAT = 'chat';
    const EVENT_LISTING_CREATED = 'listing_created';
    const EVENT_ORDER_CREATED = 'order_created';
    //const EVENT_NEWS_LETTER = 'news_letter';


    const GENERAL_EVENTS = []; //self::EVENT_NEWS_LETTER

    const REGISTERED_EVENTS = [
        self::EVENT_LISTING_APPROVED,
        self::EVENT_LISTING_EXPIRED,
        self::EVENT_CHAT,
    ];

    const ADMIN_EVENTS = [
        self::EVENT_LISTING_CREATED,
        self::EVENT_ORDER_CREATED,
    ];

    const EVENTS = [
        self::EVENT_LISTING_APPROVED,
        self::EVENT_LISTING_EXPIRED,
        self::EVENT_CHAT,
        self::EVENT_LISTING_CREATED,
        self::EVENT_ORDER_CREATED,
//        self::EVENT_NEWS_LETTER
    ];


    public static function isRegisteredEvent($event) {
        return in_array($event, self::REGISTERED_EVENTS, true);
    }

    public static function isAdminEvent($event) {
        return in_array($event, self::ADMIN_EVENTS, true);
    }

    public static function isGeneralEvent($event) {
        return in_array($event, self::GENERAL_EVENTS, true);
    }

    public static function getEventList() {
        return array_combine(self::EVENTS, [
            esc_html__('Listing Approved', 'Classified-listing-pro'),
            esc_html__('Listing Expired', 'Classified-listing-pro'),
            esc_html__('Chat', 'Classified-listing-pro'),
            esc_html__('Listing Created (Admin)', 'Classified-listing-pro'),
            esc_html__('Order Created (Admin)', 'Classified-listing-pro'),
//            esc_html__('News Letter (General)', 'Classified-listing-pro'),
        ]);
    }

    public static function getAllowedEvents() {
        $allowedEvents = Functions::get_option_item('rtcl_app_settings', 'pn_events', []);
        if (is_array($allowedEvents)) {
            return $allowedEvents;
        }

        return [];
    }

    /**
     * @param Listing $listing
     *
     * @return mixed|void
     */
    public static function listingApprovedTitle($listing) {
        return apply_filters('rtcl_pn_listing_approved_title', esc_html__("Congratulations!", 'classified-listing-pro'), $listing);
    }

    /**
     * @param Listing $listing
     *
     * @return mixed|void
     */
    public static function listingApprovedBody($listing) {
        $body = '';
        if ($listing) {
            $body = sprintf(__('Your [%s] has been approved!', 'classified-listing-pro'), $listing->get_the_title());
        }

        return apply_filters('rtcl_pn_listing_approved_body', $body, $listing);
    }

    /**
     * @param Listing $listing
     *
     * @return mixed|void
     */
    public static function listingExpiredTitle($listing) {
        return apply_filters('rtcl_pn_listing_expired_title', esc_html__("Listing expired.", 'classified-listing-pro'), $listing);
    }

    /**
     * @param Listing $listing
     *
     * @return mixed|void
     */
    public static function listingExpiredBody($listing) {
        $body = '';
        if ($listing) {
            $body = sprintf(__('Your [%s] has been expired!', 'classified-listing-pro'), $listing->get_the_title());
        }

        return apply_filters('rtcl_pn_listing_expired_body', $body, $listing);
    }

    /**
     * @param Listing $listing
     *
     * @return mixed|void
     */
    public static function listingCreatedTitle($listing) {
        return apply_filters('rtcl_pn_listing_created_title', esc_html__("New Listing created.", 'classified-listing-pro'), $listing);
    }

    /**
     * @param Listing $listing
     *
     * @return mixed|void
     */
    public static function listingCreatedBody($listing) {
        $body = '';
        if ($listing) {
            $body = sprintf(__('New listing is created [%s] at your site!', 'classified-listing-pro'), $listing->get_the_title());
        }

        return apply_filters('rtcl_pn_listing_created_body', $body, $listing);
    }

    /**
     * @param Payment $order
     *
     * @return mixed|void
     */
    public static function orderCreatedTitle($order) {
        return apply_filters('rtcl_pn_order_created_title', esc_html__("New Order Placed.", 'classified-listing-pro'), $order);
    }

    /**
     * @param Payment $order
     *
     * @return mixed|void
     */
    public static function orderCreatedBody($order) {
        $body = '';
        if ($order) {
            $body = sprintf(__('New order is placed at your site [%s]', 'classified-listing-pro'), $order->pricing->getTitle());
        }

        return apply_filters('rtcl_pn_order_created_body', $body, $order);
    }


    /**
     * @param Message      $object
     * @param Conversation $con_object
     *
     * @return mixed|void
     */
    public static function chatTitle($object, $con_object) {
        return apply_filters('rtcl_pn_chat_title', esc_html__("You have a new Message.", 'classified-listing-pro'), $object, $con_object);
    }

    /**
     * @param Message      $object
     * @param Conversation $con_object
     *
     * @return mixed|void
     */
    public static function chatBody($object, $con_object) {
        $body = '';
        if (!empty($object->message)) {
            $body = $object->message;
        }

        return apply_filters('rtcl_pn_chat_body', $body, $object, $con_object);
    }

    /**
     * @param $event
     *
     * @return bool
     */
    public static function isAllowed($event) {
        $allowedEvents = self::getAllowedEvents();
        if (in_array($event, $allowedEvents, true)) {
            return true;
        }

        return false;
    }

    public static function getAdminUserIds() {
        return get_users(array('role__in' => ['administrator'], 'fields' => 'ID'));
    }

    /**
     * @param null $slug
     *
     * @return string
     */
    public static function get_app_schema_url($slug = null) {
        $schema = untrailingslashit(Functions::get_option_item('rtcl_app_settings', 'app_schema'));
        if (!$schema) {
            return '';
        }
        $schema = $schema . '://';
        if (!$slug) {
            return $schema . 'home';
        }
        $slug = untrailingslashit(ltrim($slug, '/\\'));

        return $schema . $slug;

    }

}
