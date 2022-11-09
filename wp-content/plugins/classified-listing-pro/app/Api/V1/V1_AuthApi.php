<?php

namespace RtclPro\Api\V1;

use AppleSignIn\ASDecoder;
use RtclPro\Helpers\Api;
use Rtcl\Helpers\Functions;
use Rtcl\Shortcodes\MyAccount;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class V1_AuthApi {
	public function register_routes() {
		register_rest_route('rtcl/v1', 'login', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [$this, 'login_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => [
				'username' => [
					'required'    => true,
					'type'        => 'string',
					'description' => esc_html__('User name / Email is required', 'classified-listing-pro'),
				],
				'password' => [
					'required'    => true,
					'type'        => 'string',
					'description' => esc_html__('User password required', 'classified-listing-pro'),
				]
			],
		]);

		register_rest_route('rtcl/v1', 'logout', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [$this, 'logout_callback'],
			'permission_callback' => [Api::class, 'permission_check']
		]);

		register_rest_route('rtcl/v1', 'account-delete', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [$this, 'account_delete_callback'],
			'permission_callback' => [Api::class, 'permission_check']
		]);


		register_rest_route('rtcl/v1', 'social-login', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [$this, 'social_login_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => [
				'access_token' => [
					'required'    => true,
					'type'        => 'string',
					'description' => 'Access Token field (access_token) is required',
				],
				'type'         => [
					'required'    => true,
					'type'        => 'string',
					'description' => 'Social login type (facebook,google) is required field',
				]
			],
		]);

		//TODO : Deprecated
		register_rest_route('rtcl/v1', 'register', array(
			'methods'             => 'POST',
			'callback'            => [$this, 'signup_callback'],
			'permission_callback' => [Api::class, 'permission_check']
		));

		register_rest_route('rtcl/v1', 'signup', array(
			'methods'             => 'POST',
			'callback'            => [$this, 'signup_callback'],
			'permission_callback' => [Api::class, 'permission_check']
		));

		register_rest_route('rtcl/v1', 'reset-password', array(
			'methods'             => 'POST',
			'callback'            => [$this, 'reset_password_callback'],
			'permission_callback' => [Api::class, 'permission_check'],
			'args'                => array(
				'user_login' => array(
					'required'    => true,
					'type'        => 'string',
					'description' => esc_html__('User name / Email is required', 'classified-listing-pro'),
				)
			),
		));
	}

	public function reset_password_callback(WP_REST_Request $request) {
		if (!$request->has_param('user_login')) {
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__('Username and password are required.', 'classified-listing-pro')
			);
			wp_send_json($response, 403);
		}
		if (!MyAccount::retrieve_password($request->get_param('user_login'))) {
			$message = Functions::get_notices('error');
			Functions::clear_notices();
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => $message[0]
			);
			wp_send_json($response, 403);
		}
		Functions::clear_notices();

		return rest_ensure_response(['user_login' => $request->get_param('user_login')]);
	}

	public function social_login_callback(WP_REST_Request $request) {
		if (!$request->has_param('access_token') || !$request->has_param('type')) {
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => 'Access token (type) and type (facebook / google) are required.'
			);
			wp_send_json($response, 403);
		}

		$type         = esc_attr($request->get_param('type'));
		$access_token = esc_attr($request->get_param('access_token'));

		$userData = [];

		switch ($type) {
			case 'facebook':
				$params = array(
					'access_token' => $access_token,
					'fields'       => 'id,name,email,picture,link,locale,first_name,last_name' // info to get
				);
				$wpResponse = wp_remote_get('https://graph.facebook.com/v2.7/me' . '?' . urldecode(http_build_query($params)));
				if (is_wp_error($wpResponse)) {
					$response = array(
						'status'        => "error",
						'error'         => 'FORBIDDEN',
						'code'          => $wpResponse->get_error_code(),
						'error_message' => $wpResponse->get_error_message()
					);
					wp_send_json($response, $wpResponse->get_error_code());
				}
				$fb_user = json_decode(wp_remote_retrieve_body($wpResponse));
				if (!isset($fb_user->id) || !isset($fb_user->email)) {
					$response = array(
						'status'        => "error",
						'error'         => 'FORBIDDEN',
						'code'          => '403',
						'error_message' => 'UNAUTHORIZED ACCESS'
					);
					wp_send_json($response, 403);
				}
				$userData = [
					'user_email' => $fb_user->email,
					'first_name' => $fb_user->first_name,
					'last_name'  => $fb_user->last_name
				];
				break;
			case 'google':
			case 'google_firebase':
				if ($type == 'google_firebase') {
					$result = wp_remote_get('https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $access_token);
				} else {
					$args = array(
						'headers' => array(
							'Authorization' => 'Bearer ' . $access_token,
						),
					);
					$result = wp_remote_get('https://www.googleapis.com/userinfo/v2/me', $args);
				}

				if (is_wp_error($result)) {
					$response = array(
						'status'        => "error",
						'error'         => 'FORBIDDEN',
						'code'          => $result->get_error_code(),
						'error_message' => $result->get_error_message()
					);
					wp_send_json($response, $result->get_error_code());
				}
				$g_user = json_decode(wp_remote_retrieve_body($result));
				if ((!isset($g_user->id) && !isset($g_user->sub)) || !isset($g_user->email)) {
					$response = array(
						'status'        => "error",
						'error'         => 'FORBIDDEN',
						'code'          => '403',
						'error_message' => 'UNAUTHORIZED ACCESS'
					);
					wp_send_json($response, 403);
				}
				$userData = [
					'user_email' => $g_user->email,
					'first_name' => !empty($g_user->given_name) ? $g_user->given_name : '',
					'last_name'  => !empty($g_user->family_name) ? $g_user->family_name : ''
				];
				break;
			case 'apple':
				if (!$apple_client_user = $request->get_param('apple_user')) {
					wp_send_json([
						'status'        => "error",
						'error'         => 'FORBIDDEN',
						'code'          => '403',
						'error_message' => __('Apple user is missing', 'classified-listing-pro')
					], 403);
				}
				$appleSignInPayload = ASDecoder::getAppleSignInPayload($access_token);
				if (!$appleSignInPayload->verifyUser($apple_client_user)) {
					wp_send_json([
						'status'        => "error",
						'error'         => 'FORBIDDEN',
						'code'          => '403',
						'error_message' => 'UNAUTHORIZED ACCESS'
					], 403);
				}
				$appleEmail = $appleSignInPayload->getEmail();
				$userData   = [
					'user_email' => $appleEmail,
					'first_name' => '',
					'last_name'  => ''
				];
				break;
			default:
				$response = array(
					'status'        => "error",
					'error'         => 'FORBIDDEN',
					'code'          => '403',
					'error_message' => 'Login failed because of no user data found.'
				);
				wp_send_json($response, 403);
				break;
		}

		if (empty($userData['user_email'])) {
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => 'Login failed because of user email not found found.'
			);
			wp_send_json($response, 403);
		}

		// if no user with this email, create him
		if (!email_exists($userData['user_email'])) {
			$new_user_id = Functions::create_new_user($userData['user_email'], '', '', $userData, 'api_social_login');
			if (is_wp_error($new_user_id)) {
				$response = array(
					'status'        => "error",
					'error'         => 'INVALID_USER_DATA',
					'code'          => $new_user_id->get_error_code(),
					'error_message' => $new_user_id->get_error_message()
				);
				wp_send_json($response, 400);
			}
			$user = get_user_by('ID', $new_user_id);
		} else {
			$user = get_user_by('email', $userData['user_email']);
		}
		wp_set_current_user($user->ID);

		$api_secret        = rtcl()->getApiSecret();
		$auth_data         = Api::create_jwt_token($api_secret, $user);
		$auth_data['user'] = Api::get_user_data($user);
		do_action('rtcl_rest_api_social_login_success', $user, $request, $auth_data);
		$auth_data = apply_filters('rtcl_rest_api_auth_data', $auth_data, $user);
		return rest_ensure_response($auth_data);
	}

	public function login_callback(WP_REST_Request $request) {
		if (!$request->has_param('username') || !$request->has_param('password')) {
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__('Username and password are required.', 'classified-listing-pro')
			);
			wp_send_json($response, 403);
		}
		$username = trim(wp_unslash($request->get_param('username')));
		$password = $request->get_param('password');
		$creds    = [
			'user_login'    => $username,
			'user_password' => $password
		];
		// Perform the login
		$user = wp_signon(apply_filters('rtcl_rest_login_credentials', $creds), is_ssl());
		if (is_wp_error($user)) {
			$response = [
				'status'  => "error",
				'error'   => $user->get_error_code(),
				'code'    => '400',
				'message' => trim(preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $user->get_error_message()))
			];
			wp_send_json($response, 400);
		}
		do_action('rtcl_rest_login_success', $user, $request);
		wp_set_current_user($user->ID);
		$api_secret        = rtcl()->getApiSecret();
		$auth_data         = Api::create_jwt_token($api_secret, $user);
		$auth_data['user'] = Api::get_user_data($user);
		$auth_data         = apply_filters('rtcl_rest_api_auth_data', $auth_data, $user);
		return rest_ensure_response($auth_data);
	}


	public function logout_callback(WP_REST_Request $request) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if (!$user_id || !$user = get_user_by('ID', $user_id)) {
			return new WP_Error(
				'rest_user_not_logged_in',
				esc_html__("You are not logged in.", 'classified-listing-pro'),
				['status' => 403]
			);
		}
		$errors = new WP_Error();
		do_action('rtcl_rest_logout', $user_id, $request, $errors);
		if ($errors->get_error_code()) {
			return $errors;
		}
		$auth_data['user'] = Api::get_user_data($user);
		$auth_data         = apply_filters('rtcl_rest_api_auth_data', $auth_data, $user);
		return rest_ensure_response($auth_data);
	}

	public function account_delete_callback(WP_REST_Request $request) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if (!$user_id || !$user = get_user_by('ID', $user_id)) {
			return new WP_Error(
				'rest_user_not_logged_in',
				esc_html__("You are not logged in.", 'classified-listing-pro'),
				['status' => 403]
			);
		}
		$errors = new WP_Error();
		do_action('rtcl_rest_account_delete_validation', $errors, $user_id, $request);
		if ($errors->get_error_code()) {
			return $errors;
		}
		require_once(ABSPATH.'wp-admin/includes/user.php');
		if (!wp_delete_user($user_id)) {
			$errors->add('rtcl_rest_user_cannot_delete', __('Could not delete user.', 'classified-listing-pro'), ['status' => 500]);
			return $errors;
		}
		do_action('rtcl_rest_account_delete_success', $user_id, $request);
		return rest_ensure_response(['user_id' => $user_id]);
	}

	public function signup_callback(WP_REST_Request $request) {
		if (!$request->has_param('username') || !$request->has_param('email') || !$request->has_param('password')) {
			$response = array(
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => 'Username and password and email are required.'
			);
			wp_send_json($response, 403);
		}
		$email    = sanitize_text_field($request->get_param('email'));
		$username = sanitize_text_field($request->get_param('username'));
		$password = sanitize_text_field($request->get_param('password'));

		$userData = [
			'first_name' => sanitize_text_field($request->get_param('first_name')),
			'last_name'  => sanitize_text_field($request->get_param('last_name')),
			'phone'      => sanitize_text_field($request->get_param('phone'))
		];
		$new_user_id = Functions::create_new_user($email, $username, $password, $userData);
		if (is_wp_error($new_user_id)) {
			$response = array(
				'status'        => "error",
				'error'         => 'INVALID_USER_DATA',
				'code'          => '400',
				'error_message' => $new_user_id->get_error_messages()
			);
			wp_send_json($response, 400);
		}
		if (apply_filters('rtcl_registration_need_auth_new_user', false, $new_user_id)) {
			return rest_ensure_response(['verification_mail' => $email]);
		}
		$api_secret = rtcl()->getApiSecret();
		$user       = get_user_by('ID', $new_user_id);
		$auth_data  = Api::create_jwt_token($api_secret, $user);
		do_action('rtcl_rest_api_signup_success', $user, $request, $auth_data);
		$auth_data['isLoggedIn'] = true;
		$auth_data['user']       = Api::get_user_data($email);
		$auth_data               = apply_filters('rtcl_rest_api_auth_data', $auth_data, $user);
		return rest_ensure_response($auth_data);
	}
}
