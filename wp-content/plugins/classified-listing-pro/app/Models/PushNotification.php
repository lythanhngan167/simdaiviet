<?php

namespace RtclPro\Models;

use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;
use Rtcl\Models\Listing;
use Rtcl\Models\Payment;
use RtclPro\Helpers\Fns;
use RtclPro\Helpers\PNHelper;
use ExpoSDK\Exceptions\ExpoException;
use ExpoSDK\Exceptions\ExpoMessageException;
use ExpoSDK\Exceptions\InvalidTokensException;

class PushNotification {

	/**
	 * @var string
	 */
	protected $table_name;

	/**
	 * @var array
	 */
	private $pushTokens = [];

	public function __construct() {
		$this->table_name = 'rtcl_push_notifications';
	}


	/**
	 * Send notification to admin users devices
	 *
	 * @param string $event
	 * @param array  $data
	 *
	 * @return void
	 */
	public function notify_admin($event, $data = []) {
		if (!PNHelper::isAllowed($event) || !PNHelper::isAdminEvent($event)) {
			return;
		}
		global $wpdb;
		$table = $wpdb->prefix . $this->table_name;
		$data  = wp_parse_args($data, [
			'listing' => null,
			'order'   => null
		]);
		$this->pushTokens = [];
		$adminUserIds     = PNHelper::getAdminUserIds();
		if (!empty($adminUserIds)) {
			$formatted_ids    = join(',', $adminUserIds);
			$this->pushTokens = $wpdb->get_col($wpdb->prepare(
				"SELECT push_token FROM {$table} WHERE user_id IN ( {$formatted_ids} ) AND events LIKE %s LIMIT 3",
				'%' . $wpdb->esc_like($event) . '%'
			));
		}

		if (!empty($this->pushTokens)) {
			if (is_callable([$this, 'notify_admin_' . $event])) {
				return $this->{'notify_admin_' . $event}($data);
			}
		}
	}


	/**
	 * Send notification to registered users devices
	 *
	 * @param string $event
	 * @param array  $data
	 *
	 * @return void
	 */
	public function notify_user($event, $data = []) {
		if (!PNHelper::isAllowed($event) || !PNHelper::isRegisteredEvent($event)) {
			return;
		}

		$data = wp_parse_args($data, [
			'user_id' => 0,
			'object'  => null
		]);
		global $wpdb;
		$table            = $wpdb->prefix . $this->table_name;
		$this->pushTokens = [];
		if (PNHelper::EVENT_CHAT === $event) {
			if (!empty($data['object']->con_id) && !empty($data['object']->source_id) && ($con = new Conversation(absint($data['object']->con_id))) && $con->exist()) {
				$data['con'] = $con;
				if ($data['object']->source_id == $con->sender_id) {
					$data['user_id'] = absint($con->recipient_id);
				} elseif ($data['object']->source_id == $con->recipient_id) {
					$data['user_id'] = absint($con->sender_id);
				}
				if (Fns::is_on_conversation($data['object']->con_id, $data['user_id'])) {
					return;
				}
			} else {
				return;
			}
		}
		$user_id = absint($data['user_id']);

		if ($user_id) {
			$this->pushTokens = $wpdb->get_col($wpdb->prepare(
				"SELECT push_token FROM {$table} WHERE user_id = %d AND events LIKE %s LIMIT 3",
				$user_id,
				'%' . $wpdb->esc_like($event) . '%'
			));
		}

		if (!empty($this->pushTokens)) {
			if (is_callable([$this, 'notify_user_' . $event])) {
				try {
					return $this->{'notify_user_' . $event}($data);
				} catch (ExpoException $e) {
					return;
				}
			}
		}
	}

	/**
	 * Notification to all devices
	 *
	 * @param string $event
	 * @param array  $data
	 *
	 * @return void
	 */
	public function notify($event, $data = []) {
		if (!PNHelper::isAllowed($event) || !PNHelper::isGeneralEvent($event)) {
			return;
		}

		global $wpdb;
		$table            = $wpdb->prefix . $this->table_name;
		$this->pushTokens = $wpdb->get_col($wpdb->prepare(
			"SELECT push_token FROM {$table} WHERE user_id IS NULL AND events LIKE %s",
			'%' . $wpdb->esc_like($event) . '%'
		));

		if (!empty($this->pushTokens)) {
			if (is_callable([$this, 'notify_' . $event])) {
				return $this->{'notify_' . $event}($data);
			}
		}
	}

	/**
	 * @param array $data
	 *
	 * @throws ExpoException
	 * @throws InvalidTokensException
	 * @throws ExpoMessageException
	 */
	private function notify_user_listing_approved($data) {
		/** @var Listing $listing */
		if (isset($data['object']) && $listing = $data['object']) {
			$message = (new ExpoMessage())
				->setTitle(PNHelper::listingApprovedTitle($listing))
				->setBody(PNHelper::listingApprovedBody($listing))
				->setData([
					'url' => PNHelper::get_app_schema_url('listings/' . $listing->get_id())
				])
				->playSound();
			$expo = new Expo();
			$expo->send($message)->to($this->pushTokens)->push();
		}
	}

	/**
	 * @throws ExpoMessageException
	 * @throws InvalidTokensException
	 * @throws ExpoException
	 */
	private function notify_user_listing_expired($data) {
		/** @var Listing $listing */
		if (isset($data['object']) && $listing = $data['object']) {
			$message = (new ExpoMessage())
				->setTitle(PNHelper::listingExpiredTitle($listing))
				->setBody(PNHelper::listingExpiredBody($listing))
				->setData([
					'url' => PNHelper::get_app_schema_url('myListings')
				])
				->playSound();
			$expo = new Expo();
			$expo->send($message)->to($this->pushTokens)->push();
		}
	}

	/**
	 * @throws ExpoMessageException
	 * @throws InvalidTokensException
	 * @throws ExpoException
	 */
	private function notify_admin_listing_created($data) {
		/** @var Listing $listing */
		if (isset($data['object']) && $listing = $data['object']) {
			$message = (new ExpoMessage())
				->setTitle(PNHelper::listingCreatedTitle($listing))
				->setBody(PNHelper::listingCreatedBody($listing))
				->setData([
					'url' => PNHelper::get_app_schema_url()
				])
				->playSound();
			$expo = new Expo();
			$expo->send($message)->to($this->pushTokens)->push();
		}
	}

	/**
	 * @throws ExpoMessageException
	 * @throws ExpoException
	 * @throws InvalidTokensException
	 */
	private function notify_admin_order_created($data) {
		/** @var Payment $order */
		if (isset($data['object']) && $order = $data['object']) {
			$message = (new ExpoMessage())
				->setTitle(PNHelper::orderCreatedTitle($order))
				->setBody(PNHelper::orderCreatedBody($order))
				->setData([
					'url' => PNHelper::get_app_schema_url('myListings')
				])
				->playSound();
			$expo = new Expo();
			$expo->send($message)->to($this->pushTokens)->push();
		}
	}

	/**
	 * @throws ExpoMessageException
	 * @throws ExpoException
	 * @throws InvalidTokensException
	 */
	private function notify_user_chat($data) {
		/** @var Payment $order */
		$object     = isset($data['object']) && is_object($data['object']) ? $data['object'] : null;
		$con_object = isset($data['con']) && is_object($data['con']) ? $data['con'] : null;
		if (!empty($object->con_id) && !empty($con_object->listing_id)) {
			$url_slug = 'conversation/' . $object->con_id . '/' . $con_object->listing_id;
			$message  = (new ExpoMessage())
				->setTitle(PNHelper::chatTitle($object, $con_object))
				->setBody(PNHelper::chatBody($object, $con_object))
				->setData([
					'url' => PNHelper::get_app_schema_url($url_slug)
				])
				->playSound();

			$expo = new Expo();

			$expo->send($message)->to($this->pushTokens)->push();
		}
	}

	/**
	 * @param string         $push_token
	 * @param array          $events
	 * @param integer | null $user_id
	 *
	 * @return bool|array
	 */
	public function registerEvents($push_token, $events = [], $user_id = null) {
		if (!$push_token) {
			return false;
		}
		global $wpdb;
		$table           = $wpdb->prefix . $this->table_name;
		$events          = $this->validateEvents($events);
		$formattedEvents = empty($events) ? null : json_encode($events);
		if (!is_null($user_id)) {
			$user_id = absint($user_id);
			$user_id = $user_id ?: null;
		}
		$pnObject = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE push_token = %s", $push_token));
		if ($pnObject) {
			$success = $wpdb->update(
				$table,
				[
					'user_id'    => $user_id,
					'events'     => $formattedEvents,
					'updated_at' => wp_date('Y-m-d H:i:s')
				],                                          // Data
				['id' => $pnObject->id]                                   // Where format
			);
		} else {
			$success = $wpdb->insert(
				$table,
				[
					'push_token' => $push_token,
					'user_id'    => $user_id,
					'events'     => $formattedEvents,
				]                                         // Data
			);
		}
		if ($success) {
			return [
				'id'         => $pnObject ? $pnObject->id : $wpdb->insert_id,
				'push_token' => $push_token,
				'user_id'    => $user_id,
				'events'     => $events,
				'type'       => $pnObject ? 'updated' : 'inserted'
			];
		}

		return false;
	}


	/**
	 * @param array $events
	 *
	 * @return array
	 */
	private function validateEvents($events) {
		if (is_array($events) && !empty($events)) {
			return array_filter($events, function ($item) {
				return is_string($item) && in_array($item, PNHelper::EVENTS, true);
			});
		}

		return [];
	}

	/**
	 * Delete all push token by user id
	 *
	 * @param [type] $user_id
	 * @return void
	 */
	public function removePushTokenByUserId($user_id) {
		global $wpdb;
		$table   = $wpdb->prefix . $this->table_name;
		$success = $wpdb->delete(
			$table,
			['user_id' => $user_id]
		);
		return $success;
	}
}
