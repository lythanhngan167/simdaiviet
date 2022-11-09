<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;

class NotificationHooks
{

    public static function init() {
        add_action('rtcl_store_meta_data_saved', array(__CLASS__, 'store_meta_data_saved_email_admin'), 10, 2);
    }

    /**
     * @param $store_owner_id
     * @param $post \WP_Post
     *
     * @throws \Exception
     */
    public static function store_meta_data_saved_email_admin($store_owner_id, $post) {
        if ($store_owner_id && Functions::get_option_item('rtcl_email_settings', 'notify_admin', 'store_update', 'multi_checkbox')) {
            rtcl()->mailer()->emails['Store_Update_Email_To_Admin']->trigger($store_owner_id, $post);
        }
    }

}