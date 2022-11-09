<?php

namespace RtclStore\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Models\RtclEmail;

class StoreUpdateEmailToAdmin extends RtclEmail {

	protected $user = null;

	function __construct() {
		$this->id            = 'store_update_to_admin';
		$this->template_html = 'emails/store-update-email-to-admin';

		// Call parent constructor.
		parent::__construct();
	}


	/**
	 * Get email subject.
	 * @return string
	 */
	public function get_default_subject() {
		return __( '[{site_title}] store information is updated', 'classified-listing-store' );
	}

	/**
	 * Get email heading.
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Store information is updated', 'classified-listing-store' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param               $store_owner_id
	 * @param \WP_Post      $post
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function trigger( $store_owner_id, $post = null ) {

		if ( ! $store_owner_id || ! is_a( $post, \WP_Post::class ) ) {
			return;
		}

		$this->setup_locale();

		$this->user   = get_userdata( $store_owner_id );
		$this->object = $post;

		$this->set_recipient( Functions::get_admin_email_id_s() );

		if ( $this->get_recipient() ) {
			$this->send();
		}

		$this->restore_locale();

	}


	/**
	 * Get content html.
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return Functions::get_template_html(
			$this->template_html, array(
				'post'          => $this->object,
				'user'          => $this->user,
				'email'         => $this,
				'sent_to_admin' => true,
			), '', rtclStore()->get_plugin_template_path()
		);
	}

}
