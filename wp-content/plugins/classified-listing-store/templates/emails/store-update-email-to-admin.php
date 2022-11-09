<?php
/**
 * Admin Notification at Store update
 * @package ClassifiedListingStore/Templates/Emails
 * @version 1.2.0
 */


use Rtcl\Helpers\Functions;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php esc_html_e( 'Hi Admin', 'classified-listing-store' ); ?>,</p>
    <p><?php printf( "%s Store is information is updated by %s ( %s ) to your site %s.",
			$post->post_title,
			$user->user_login,
			$user->user_email,
			Functions::get_blogname() ) ?></p>
    <p><?php esc_html_e( "Please do not respond to this message. It is automatically generated and is for information purposes only.", 'classified-listing-store' ) ?></p>
    <p><?php esc_html_e( 'Thanks for reading.', 'classified-listing-store' ); ?></p>


<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
