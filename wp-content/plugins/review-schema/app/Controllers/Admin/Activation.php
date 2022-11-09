<?php

namespace Rtrs\Controllers\Admin; 
use Rtrs\Helpers\Functions; 

class Activation {

    public function __construct() { 
        register_activation_hook(RTRS_PLUGIN_FILE, array($this, 'plugin_activate'));
        add_action('admin_init', array($this, 'plugin_redirect') );
    }   

    function plugin_activate() {
        $this->reGenerateCss(); 
        add_option('rtrs_activation_redirect', true);
    }

    function plugin_redirect() {
        if ( get_option('rtrs_activation_redirect', false) ) {
            delete_option('rtrs_activation_redirect'); 
            wp_redirect( admin_url('admin.php?page=rtrs-reviews-get-help') );
        }
    }

    function reGenerateCss() { 
        //review post type
        $scPostIds = get_posts( array(
            'post_type'      => rtrs()->getPostType(),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids'
        ) ); 
    
        if ( is_array($scPostIds) && !empty($scPostIds) ) { 
            foreach ($scPostIds as $scPostId) { 
                Functions::generatorShortCodeCss($scPostId, 'review');
            }
        } 
        wp_reset_query();

        //rtrs_affiliate post type
        $scPostIds = get_posts( array(
            'post_type'      => rtrs()->getPostTypeAffiliate(),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids'
        ) ); 
    
        if ( is_array($scPostIds) && !empty($scPostIds) ) { 
            foreach ($scPostIds as $scPostId) { 
                Functions::generatorShortCodeCss($scPostId, 'affiliate');
            }
        } 
        wp_reset_query();        
    }  
}
