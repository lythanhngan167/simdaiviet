<?php
namespace Rtrsp\Shortcodes;  

use Rtrsp\Traits\SingletonTrait;

class ShortcodeFilter {  
    
    use SingletonTrait;

    function init() {  
        // Review filter.
        add_filter('rtrs_google_review_shorting', array(&$this, 'review_shorting'), 10, 2); 
        add_filter('rtrs_facebook_review_shorting', array(&$this, 'review_shorting'), 11, 2); 
        add_filter('rtrs_yelp_review_shorting', array(&$this, 'review_shorting'), 12, 2); 
        add_filter('rtrs_multi_business', array(&$this, 'multi_business'), 12, 4); 
        // review text filter
        add_filter('rtrs_review_text', array(&$this, 'review_text'), 10, 2); 
    }  
    
    function review_shorting( $review_data, $shortcode_id ) {
        global $minimum_rating;
        // $business_type = get_post_meta( $shortcode_id, 'business_type', true );
        $minimum_rating = get_post_meta( $shortcode_id, 'minimum_rating', true );
        $order_by = get_post_meta( $shortcode_id, 'order_by', true ); 

        // order by latest
        if ( $order_by == "date" || $order_by == "rating" ) {
            usort($review_data, function($object1, $object2) { 
                return $object2['time'] > $object1['time']; 
            } );  
        }

        // order by oldest
        if ( $order_by == "date-dsc" ) {
            usort($review_data, function($object1, $object2) { 
                return $object2['time'] < $object1['time']; 
            } );  
        } 

        // order by rating
        if ( $order_by == "rating" ) {
            usort($review_data, function($object1, $object2) { 
                return $object2['rating'] > $object1['rating']; 
            } );
        }
        
        // remove minimum rating
        if ( $minimum_rating ) {
            $review_data = array_values( array_filter($review_data, function($object) { 
                global $minimum_rating;
                return $object['rating'] > $minimum_rating; 
            } ) ); 
        }  
 
        return $review_data;
    } 

    function multi_business( $google_data, $facebook_data, $yelp_data, $sc_meta_multi_business ) {
        $multi_business = []; 
        $google_count = ( isset( $google_data) ) ? count($google_data) : 0;
        $facebook_count = ( isset( $facebook_data) ) ? count($facebook_data) : 0;
        $yelp_count = ( isset( $yelp_data) ) ? count($yelp_data) : 0;

        for( $i = 0; $i < $google_count || $i < $facebook_count || $i < $yelp_count; $i++) {
            if ( in_array('google', $sc_meta_multi_business ) ) {
                if ($i < count($google_data)) $multi_business[] = $google_data[$i];
            }
            if ( in_array('facebook', $sc_meta_multi_business ) ) {
                if ($i < count($facebook_data)) $multi_business[] = $facebook_data[$i];
            }
            if ( in_array('yelp', $sc_meta_multi_business ) ) {
                if ($i < count($yelp_data)) $multi_business[] = $yelp_data[$i];
            } 
        }
        return $multi_business;
    } 

    function review_text( $desc, $shortcode_id ) {
        //exclude word from review
        if ( $exclude_word = get_post_meta( $shortcode_id, 'exclude_word', true ) ) { 
            $exclude_array = explode(",", $exclude_word );  
            $desc = str_replace($exclude_array, array(''), $desc);  
        }
        return $desc;
    } 

} 