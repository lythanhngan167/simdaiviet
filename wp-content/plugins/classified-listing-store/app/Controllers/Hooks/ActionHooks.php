<?php

namespace RtclStore\Controllers\Hooks;

use WP_Error;

class ActionHooks
{
    public static function init() {
        add_action('rtcl_store_contact_form_validation', [__CLASS__, 'store_contact_form_validation'], 10, 2);
        add_action('delete_user', [__CLASS__, 'remove_membership_data']);
    }

    public static function remove_membership_data($user_id) {
        global $wpdb;
        $membership_table = $wpdb->prefix . "rtcl_membership";
        $membership = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$membership_table} WHERE user_id = %d", $user_id )
        );
        if($membership){
            $wpdb->delete(
                $wpdb->prefix . "rtcl_membership_meta",
                ['membership_id' => $membership->id],
                ['%d']
            );
            $wpdb->delete(
                $wpdb->prefix . "rtcl_membership",
                ['id' => $membership->id],
                ['%d']
            );
        }
    }


    /**
     * @param WP_Error $error
     * @param array    $data
     */
    public static function store_contact_form_validation($error, $data) {
        if (empty($data['store_id']) || empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            $error->add('rtcl_field_required', esc_html__('Need to fill all the required field.', 'classified-listing-store'));
        }
    }
}