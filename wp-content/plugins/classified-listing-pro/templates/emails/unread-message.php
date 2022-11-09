<?php
/**
 * Unread Message
 *
 * @package ClassifiedListing/Templates/Emails
 * @version 1.2.27
 *
 * @var RtclEmail $email
 * @var WP_User   $user
 * @var Listing   $listing
 * @var array     $data
 * @var string    $verify_link
 */

use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action('rtcl_email_header', $email); ?>

    <p><?php printf(esc_html__('Hello %s,', 'classified-listing-pro'), esc_html($data['recipient_name'])); ?></p>
    <p><?php printf(__('Latest unread message on ad %s from user %s', 'classified-listing-pro'),
            '<a href="' . esc_url($listing->get_the_permalink()) . '" ><strong>' . $listing->get_the_title() . '</strong></a>', '<strong>' . $data['sender_name'] . '</strong>'); ?>
    <p>
    <p><code><?php echo esc_html($data['message']) ?></code></p>
    <p><a href="<?php echo esc_url($data['conversation_url']) ?>"
          target="_blank"><?php esc_html_e('Reply To this conversation', 'classified-listing-pro') ?></a></p>

<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);
