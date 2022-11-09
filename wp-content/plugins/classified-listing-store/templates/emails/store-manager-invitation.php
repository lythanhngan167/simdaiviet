<?php
/**
 * Store Manager request email
 *
 * @package ClassifiedListingStore/Templates/Emails
 * @version 1.3.34
 *
 * @var WP_User $user
 * @var Store   $store
 * @var array   $data
 */


use Rtcl\Helpers\Link;
use Rtcl\Models\RtclEmail;
use RtclStore\Models\Store;


if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
/** @var RtclEmail $email */
do_action('rtcl_email_header', $email); ?>
    <p><?php /** @var Store $store */
        printf(__('Hi %s', 'classified-listing-store'), $user->user_login); ?>,</p>
    <p><?php printf(__('You have received a store manager request from the store owner of <strong><a href="%s">%s</a></strong>.', 'classified-listing-store'), $store->get_the_permalink(), $store->get_the_title()); ?></p>
    <p><?php printf(__('You can approve this request from here: %s', 'classified-listing-store'), esc_url(add_query_arg([
            'rtcl_store_manager_key' => $data['key'],
            'rtcl_store_id'          => $store->get_id(),
            'rtcl_store_manager_id'  => $user->ID
        ], Link::get_account_endpoint_url()))) ?></p>
<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);
