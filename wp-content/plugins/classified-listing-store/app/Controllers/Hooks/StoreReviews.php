<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclStore\Helpers\Functions;

class StoreReviews
{
    /**
     * Hook in methods.
     */
    public static function init() {
        add_action('wp_update_comment_count', array(
            __CLASS__,
            'update_store_review_transients_at_update_comment_count'
        ));
    }

    public static function update_store_review_transients_at_update_comment_count($post_id) {
        if (!RtclFunctions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox') || !RtclFunctions::get_option_item('rtcl_membership_settings', 'enable_store_rating', true, 'checkbox')) {
            return;
        }
        if ($listing = rtcl()->factory->get_listing($post_id)) {
            if ($store = Functions::get_user_store($listing->get_owner_id())) {
                self::update_store_review_transients($store->get_id());
            }
        }
    }

    static function update_store_review_transients($store_id) {
        if (rtclStore()->post_type === get_post_type($store_id)) {
            self::calculate_store_rating($store_id);
        }
    }

    static function calculate_store_rating($store) {
        $store = Functions::get_store($store);
        $store_listing_ids = $store->get_listing_ids();
        $review = [
            'rating'         => 0,
            'review'         => 0,
            'average_rating' => 0
        ];
        if (!empty($store_listing_ids)) {
            $total_rating = 0;
            $total_review = 0;
            foreach ($store_listing_ids as $listing_id) {
                $review_count = get_post_meta($listing_id, '_rtcl_review_count', true);
                $average_rating = get_post_meta($listing_id, '_rtcl_average_rating', true);
                $total_rating += $review_count * $average_rating;
                $total_review += $review_count;
            }
            $average = $total_rating && $total_review ? number_format($total_rating / $total_review, 2, '.', '') : 0;
            $review = [
                'rating'         => $total_rating,
                'review'         => $total_review,
                'average_rating' => $average
            ];

            $store->set_rating_total($total_rating);
            $store->set_review_counts($total_review);
            $store->set_average_rating($average);
        }

        return $review;

    }

}