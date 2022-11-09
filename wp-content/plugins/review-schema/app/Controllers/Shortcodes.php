<?php

namespace Rtrs\Controllers; 
 
use Rtrs\Shortcodes\ReviewSchema;  

class Shortcodes {

    public static function init_short_code() {
        $shortcodes = array( 
            'rtrs-affiliate'   => __CLASS__ . '::review_schema', 
        );

        foreach( $shortcodes as $shortcode => $function ) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        } 
    }

    public static function shortcode_wrapper(
        $function,
        $atts = array(),
        $wrapper = array(
            'class'  => 'rtrs',
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
     * All shortcode.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */  
    public static function review_schema($atts) {
        return self::shortcode_wrapper(array(ReviewSchema::class, 'output'), $atts);
    } 
}