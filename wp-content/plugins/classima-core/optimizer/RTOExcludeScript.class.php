<?php 
// Security check
defined('ABSPATH') || die();

class RTOExcludeScript
{


    public function __construct()
    {

        // Avoid deferred loading
        add_filter('script_loader_tag', [&$this, 'remove_script'], 9, 3);

        add_filter('wp-optimize-minify-default-exclusions', [&$this, 'default_exclusion']);


    }

    public function remove_script($tag, $handle, $src){

        global $rt_optimize;
        
        if ( $rt_optimize->options->get_option('rt_wpo_exclude_list') == '') return $tag;

        $urls = array_map('trim', explode(',', ($rt_optimize->options->get_option('rt_wpo_exclude_list'))));

        foreach ($urls as $url) {
            if (false !== strpos($src, $url)) {
                return str_replace('<script ', '<script data-wp-optimize-escape="/jquery.js"', $tag);
            }
        }
        
        return $tag;


    }

    public function default_exclusion($list){

        global $rt_optimize;
        
        if ( $rt_optimize->options->get_option('rt_wpo_exclude_list') == '') return $list;

        $urls = array_map('trim', explode(',', ($rt_optimize->options->get_option('rt_wpo_exclude_list'))));

        foreach ($urls as $url) {
            $list[] = $url;
        }

        return $list;

    }

    
}

new RTOExcludeScript();