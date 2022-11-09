<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions as RtclFunctions;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options;
use RtclStore\Helpers\Functions;

class StatusChange
{

    public static function init() {
        add_action('transition_post_status', [__CLASS__, 'payment_status_change_to_complete'], 100, 3);
        add_action('transition_post_status', [__CLASS__, 'listing_status_change_to_publish'], 100, 3);
    }

    /**
     * @param $new_status
     * @param $old_status
     * @param $post
     *
     * @throws \Exception
     */
    static public function payment_status_change_to_complete($new_status, $old_status, $post) {

        if (rtcl()->post_type_payment !== $post->post_type) {
            return;
        }
        $payment = rtcl()->factory->get_order($post->ID);
        if ($new_status === 'rtcl-completed' && $old_status !== 'rtcl-completed' && $payment && "membership" === $payment->pricing->getType()) {
            if (!$payment->is_applied()) {
                Functions::apply_membership($payment);
            }

            // Hook for developers
            do_action('rtcl_membership_order_completed', $payment, $new_status, $old_status);
        }

    }

    /**
     * @param string   $new_status
     * @param string   $old_status
     * @param \WP_Post $post
     *
     * @throws \Exception
     */
    static public function listing_status_change_to_publish($new_status, $old_status, $post) {

        if (rtcl()->post_type !== $post->post_type) {
            return;
        }

        // Check if we are transitioning from any to publish
        if ('publish' !== $old_status && 'publish' == $new_status) {
            $pending_promotions = get_post_meta($post->ID, '_rtcl_pending_promotions', true);
            if (is_array($pending_promotions) && !empty($pending_promotions)) {
                $promotions = [];
                foreach ($pending_promotions as $promotion_key => $promotion_validate) {
                    if (in_array($promotion_key, array_keys(Options::get_listing_promotions()), true) && $validate = absint($promotion_validate)) {
                        $promotions[$promotion_key] = $validate;
                    }
                }
                if (!empty($promotions)) {
                    RtclFunctions::update_listing_promotions($post->ID, $promotions);
                }
            }
            if (!empty($pending_promotions)) {
                delete_post_meta($post->ID, '_rtcl_pending_promotions');
            }
        }
    }
}
