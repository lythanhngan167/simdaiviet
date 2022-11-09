<?php

namespace RtclStore\Controllers\Hooks;

use RtclStore\Models\Store;

class AfterSetupTheme
{
    static function template_functions() {
        add_action('the_post', [__CLASS__, 'setup_store_data']);
    }


    /**
     * When the_post is called, put product data into a global.
     *
     * @param mixed $post Post Object.
     *
     * @return Store | false
     */
    static function setup_store_data($post) {
        unset($GLOBALS['store']);

        if (is_int($post)) {
            $post = get_post($post);
        }

        if (empty($post->post_type) || rtclStore()->post_type !== $post->post_type) {
            return false;
        }

        $GLOBALS['store'] = rtclStore()->factory->get_store($post);

        return $GLOBALS['store'];
    }
}