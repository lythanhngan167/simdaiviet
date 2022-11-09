<?php

namespace RtclPro\Api;

use RtclPro\Models\PushNotification;
use WP_REST_Request;

class RestActionHooks {
	public static function init() {
		add_action('rtcl_rest_set_local', [__CLASS__, 'set_local_wpml']);
		add_action('rtcl_rest_logout', [__CLASS__, 'update_push_notification_logout'], 10, 2);
		add_action('rtcl_rest_account_delete_success', [__CLASS__, 'delete_push_token_on_user_delete']);
	}


	/**
	 * @param int             $user_id
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public static function update_push_notification_logout($user_id, $request) {
		//Remove push config to general
		$push_token = $request->get_param('push_token');
		if ($push_token && $user_id) {
			$pn = new PushNotification();
			$pn->registerEvents($push_token);
		}
	}

	/**
	 * @param int             $user_id
	 *
	 * @return void
	 */
	public static function delete_push_token_on_user_delete($user_id) {
		if ($user_id) {
			$pn = new PushNotification();
			$pn->removePushTokenByUserId($user_id);
		}
	}

	/**
	 * @param array $headers
	 */
	public static function set_local_wpml($headers) {
		global $sitepress;
		if (empty($headers['X-LOCALE']) || !$sitepress || !method_exists($sitepress, 'switch_lang') || (!$lng = sanitize_key(wp_unslash($headers['X-LOCALE']))) || $lng === $sitepress->get_default_language()) {
			return;
		}
		$sitepress->switch_lang($lng, true);// Alternative do_action( 'wpml_switch_language', $_GET['lang'] );
	}
}
