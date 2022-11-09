<?php
/**
 * Store contact mail to store owner
 *
 * @package ClassifiedListingStore/Templates/Emails
 * @version 1.2.0
 */


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
        printf(__('Hi %s', 'classified-listing-store'), $store->owner_name()); ?>,</p>
    <p><?php printf(__('You have received a reply from your store at <strong>%s</strong>.', 'classified-listing-store'), $store->get_the_title()); ?></p>
    <p><?php printf(__('<strong>Name</strong> : %s', 'classified-listing-store'), $data['name']) ?></p>
    <p><?php printf(__('<strong>Email</strong> : %s', 'classified-listing-store'), $data['email']) ?></p>
    <p><?php printf(__('<strong>Phone</strong> : %s', 'classified-listing-store'), $data['phone']) ?></p>
    <p><?php printf(__('<strong>Message</strong> : %s', 'classified-listing-store'), nl2br($data['message'])) ?></p>
<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);
