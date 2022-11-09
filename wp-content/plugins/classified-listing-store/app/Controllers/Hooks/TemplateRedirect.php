<?php

namespace RtclStore\Controllers\Hooks;

class TemplateRedirect {

    public static function init()
    {
//        add_action('template_redirect', array(__CLASS__, 'template_redirect'));

    }

    static function template_redirect(){
        $queried_post_type = get_query_var('post_type');
        if ( is_singular(rtclStore()->post_type) ) {
            wp_redirect( home_url( '/' . $queried_post_type . '/' ), 404 );
            exit;
        }

    }

}