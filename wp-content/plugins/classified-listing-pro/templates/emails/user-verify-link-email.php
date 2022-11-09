<?php
/**
 * User verify email
 *
 * @package ClassifiedListing/Templates/Emails
 * @version 1.2.27
 *
 * @var RtclEmail $email
 * @var WP_User   $user
 * @var string    $verify_link
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action('rtcl_email_header', $email); ?>

    <p><?php printf(esc_html__('Hello %s,', 'classified-listing-pro'), esc_html(Functions::get_author_name($user))); ?></p>
    <p><?php printf(__("Thanks for registering on our site, please <a href='%s' target='_blank'>click here to confirm your email address</a>.", 'classified-listing-pro'), $verify_link); ?></p>
    <p><?php esc_html_e('If you didn\'t make this request, just ignore this email.', 'classified-listing-pro'); ?></p>
    <p><?php esc_html_e('Thanks for reading.', 'classified-listing-pro'); ?></p>

<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);
