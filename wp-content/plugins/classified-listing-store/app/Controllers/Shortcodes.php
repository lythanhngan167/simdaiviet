<?php

namespace RtclStore\Controllers;

use WP_Query;
use RtclStore\Shortcodes\Stores;
use RtclStore\Shortcodes\MembershipPricingTable;
use Rtcl\Helpers\Functions as RtclFunctions;

class Shortcodes
{

    public static function init_short_code() {
        $shortcodes = array(
            'rtcl_stores'                   => __CLASS__ . '::stores',
            'rtcl_membership_pricing_table' => __CLASS__ . '::membership_pricing_table',
            'rtcl_store_page'               => __CLASS__ . '::store_page',
        );

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        }
    }

    public static function shortcode_wrapper(
        $function,
        $atts = array(),
        $wrapper = array(
            'class'  => 'rtcl',
            'before' => null,
            'after'  => null,
        )
    ) {
        ob_start();

        // @codingStandardsIgnoreStart
        echo empty($wrapper['before']) ? '<div class="' . esc_attr($wrapper['class']) . '">' : $wrapper['before'];
        call_user_func($function, $atts);
        echo empty($wrapper['after']) ? '</div>' : $wrapper['after'];

        // @codingStandardsIgnoreEnd

        return ob_get_clean();
    }

    /**
     * Store listing shortcode.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */
    public static function stores($atts) {
        $atts = (array)$atts;
        $shortcode = new Stores($atts);
        return $shortcode->get_content();
    }

    /**
     * Membership pricing table shortcode.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */
    public static function membership_pricing_table($atts) {
        return self::shortcode_wrapper(array(MembershipPricingTable::class, 'output'), $atts);
    }


    /**
     * Show a single store page.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */
    public static function store_page($atts) {
        if (empty($atts)) {
            return '';
        }

        if (!isset($atts['id'])) {
            return '';
        }

        $args = array(
            'posts_per_page'      => 1,
            'post_type'           => rtclStore()->post_type,
            'post_status'         => (!empty($atts['status'])) ? $atts['status'] : 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows'       => 1,
        );

        if (isset($atts['id'])) {
            $args['p'] = absint($atts['id']);
        }

        $single_store = new WP_Query($args);

        // For "is_single" to always make load comments_template() for reviews.
        $single_store->is_single = true;

        ob_start();

        global $wp_query;

        // Backup query object so following loops think this is a product page.
        $previous_wp_query = $wp_query;
        // @codingStandardsIgnoreStart
        $wp_query = $single_store;
        // @codingStandardsIgnoreEnd

        wp_enqueue_script( 'rtcl-store-public' );
        wp_enqueue_style( 'rtcl-store-public' );

        while ($single_store->have_posts()) {
            $single_store->the_post()
            ?>
            <div class="rtcl-single-store">
                <?php RtclFunctions::get_template_part('content', 'single-store'); ?>
            </div>
            <?php
        }

        // Restore $previous_wp_query and reset post data.
        // @codingStandardsIgnoreStart
        $wp_query = $previous_wp_query;
        // @codingStandardsIgnoreEnd
        wp_reset_postdata();

        return '<div class="rtcl">' . ob_get_clean() . '</div>';
    }
}