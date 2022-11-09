<?php

namespace Rtrs\Controllers\Ajax;
use Rtrs\Shortcodes\ReviewSchema;
use Rtrs\Helpers\Functions;

class Shortcode {
    public function __construct() {
        add_action('wp_ajax_rtrs_shortcode_layout_preview', array($this, 'shortcode_layout_preview')); 
        add_action('wp_ajax_rtrs_check_post_type', array($this, 'check_post_type'));  
    } 

    function shortcode_layout_preview() {  
        $shortcode_id = ( !empty( $_REQUEST['sc_id'] ) ) ? absint( $_REQUEST['sc_id'] ) : ''; 
        if ( $shortcode_id ) {  
            $params = array(
                'id' => $shortcode_id, 
            );
            ReviewSchema::output( $params );
        } 
        wp_send_json_success();
    } 

    function check_post_type() {  
        $post_id = ( !empty( $_REQUEST['post_id'] ) ) ? sanitize_text_field( $_REQUEST['post_id'] ) : '';
        $post_type = ( !empty( $_REQUEST['post_type'] ) ) ? sanitize_text_field( $_REQUEST['post_type'] ) : '';

        $scPostIds = get_posts( array(
            'post_type'      => rtrs()->getPostType(),
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'draft'],
            'fields'         => 'ids',
            'meta_query' => array( 
                array(
                    'key' => 'rtrs_post_type',
                    'value' => $post_type,
                    'compare' => '=',
                ) 
            )
        ) );   

        $current_post_type = get_post_meta($post_id, 'rtrs_post_type', true);

        if ( ( $current_post_type != $post_type ) && !empty($scPostIds) ) { 
            wp_send_json_error();
        } else {
            wp_send_json_success();
        }
    } 
}