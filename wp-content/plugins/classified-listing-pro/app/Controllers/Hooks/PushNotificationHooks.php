<?php

namespace RtclPro\Controllers\Hooks;

use RtclPro\Helpers\PNHelper;
use RtclPro\Models\PushNotification;

class PushNotificationHooks
{

    public static function init() {
        add_action('transition_post_status', [__CLASS__, 'notify_device_for_listing_status_changes'], 99, 3);
        add_action('wp_insert_post', [__CLASS__, 'notify_device_for_listing_order_created'], 10, 3);
    }

    public static function notify_device_for_listing_status_changes($new_status, $old_status, $post) {
        if (rtcl()->post_type !== $post->post_type) {
            return;
        }
        $listing = rtcl()->factory->get_listing($post);
        $pn = new PushNotification();

        if ('publish' == $new_status) {
            $pn->notify_user(PNHelper::EVENT_LISTING_APPROVED, [
                'user_id' => $listing->get_author_id(),
                'object' => $listing
            ]);
            return;
        }
        if ('rtcl-expired' == $new_status) {
            $pn->notify_user(PNHelper::EVENT_LISTING_EXPIRED, [
                'user_id' => $listing->get_author_id(),
                'object' => $listing
            ]);
        }
    }

    public static function notify_device_for_listing_order_created($post_id, $post, $update) {
        if ($update || rtcl()->post_type_pricing !== $post->post_type || rtcl()->post_type !== $post->post_type) {
            return;
        }
        $pn = new PushNotification();
        if (rtcl()->post_type !== $post->post_type) {
            $listing = rtcl()->factory->get_listing($post);
            $pn->notify_admin(PNHelper::EVENT_LISTING_CREATED, [
                'object' => $listing
            ]);
        }
        if (rtcl()->post_type_payment !== $post->post_type) {
            $order = rtcl()->factory->get_order($post);
            $pn->notify_admin(PNHelper::EVENT_ORDER_CREATED, [
                'object' => $order
            ]);
        }
    }
}
