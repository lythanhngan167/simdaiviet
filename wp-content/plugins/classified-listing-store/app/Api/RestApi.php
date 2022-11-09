<?php

namespace RtclStore\Api;

use WP_User;
use WP_REST_Request;
use Rtcl\Models\Listing;
use Rtcl\Models\Pricing;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use Rtcl\Models\PaymentGateway;
use RtclStore\Api\V1\V1_StoreApi;
use RtclStore\Helpers\Functions as StoreFunctions;

class RestApi {
	public function __construct() {
		add_action('rest_api_init', [&$this, 'register_store_rest_api']);
		add_filter('rtcl_rest_api_config_data', [__CLASS__, 'add_store_config']);
		add_action('rtcl_rest_api_plan_data', [__CLASS__, 'add_plan_membership_promotion_data'], 10, 2);
		add_action('rtcl_rest_api_checkout_membership_promotions', [__CLASS__, 'checkout_membership_promotions']);
		add_filter('rtcl_rest_api_checkout_process_new_order_args', [__CLASS__, 'add_membership_order_meta_data'], 10, 4);
		add_filter('rtcl_rest_plans_args', [__CLASS__, 'add_membership_plans_args'], 10, 2);

		if (StoreFunctions::is_store_enabled()) {
			add_filter('rtcl_rest_api_listing_data', [__CLASS__, 'add_store_data_to_listing_data'], 10, 2);
			add_filter('rtcl_rest_api_user_data', [__CLASS__, 'add_store_only_membership'], 10, 2);
		}
	}

	public function register_store_rest_api() {
		new V1_StoreApi();
	}

	/**
	 * @param array           $new_order_args
	 * @param Pricing         $plan
	 * @param PaymentGateway  $gateway
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public static function add_membership_order_meta_data($new_order_args, $plan, $gateway, $request) {
		if ("membership" === $request->get_param('type') && 'membership' === $plan->getType()) {
			$new_order_args['meta_input']['payment_type'] = 'membership';
			$membership_promotions                        = get_post_meta($plan->getId(), '_rtcl_membership_promotions', true);
			if (!empty($membership_promotions)) {
				$new_order_args['meta_input']['_rtcl_membership_promotions'] = $membership_promotions;
			}
		}
		return $new_order_args;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public static function checkout_membership_promotions($request) {
		$type           = $request->get_param('type');
		$promotion_type = $request->get_param('promotion_type');
		if ('promotion' === $type && 'membership' === $promotion_type) {
			$promotions      = $request->get_param('membership_promotions');
			$listing_id      = $request->get_param('listing_id');
			$listing         = rtcl()->factory->get_listing($listing_id);
			$user_id         = get_current_user_id();
			$membership      = rtclStore()->factory->get_membership($user_id);
			$promotions_data = apply_filters('rtcl_rest_api_membership_promotions_data', [
				'promotions' => $promotions,
				'listing_id' => $listing->get_id()
			], $membership, $request);
			$errors = new \WP_Error();
			do_action('rtcl_rest_api_membership_promotions_process_data', $promotions_data, $membership, $errors, $request);
			$errors = apply_filters('rtcl_rest_api_membership_promotions_validation_errors', $errors, $promotions_data, $membership, $request);

			if ($membership) {
				$response = $membership->apply_promotion($promotions_data, $errors);
				if (empty($response['success'])) {
					$errors->add('rtcl_membership_promotion_error', esc_html__("Client error while promotion being processed.", "classified-listing-store"));
				}
			} else {
				$errors->add('rtcl_membership_promotion_no_membership', esc_html__("You have no membership.", "classified-listing-store"));
			}
			if (is_wp_error($errors) && $errors->has_errors()) {
				wp_send_json([
					'status'        => "error",
					'error'         => 'FORBIDDEN',
					'code'          => '403',
					'error_message' => $errors->get_error_message()
				], 403);
			}
			wp_send_json(['success' => true, 'listing_id' => $listing_id], 200);
		}
	}

	/**
	 * @param array   $plan_data
	 * @param Pricing $plan
	 */
	public static function add_plan_membership_promotion_data($plan_data, $plan) {
		if ('membership' === $plan->getType()) {
			$plan_data['regular_ads']             = absint(get_post_meta($plan->getId(), 'regular_ads', true));
			$plan_data['promotion']['membership'] = get_post_meta($plan->getId(), '_rtcl_membership_promotions', true);
		}

		return $plan_data;
	}

	/**
	 * @param array           $args
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public static function add_membership_plans_args($args, $request) {
		$type = $request->get_param('type');
		if ('membership' === $type) {
			$args = [
				'post_type'        => rtcl()->post_type_pricing,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'no_found_rows'    => true,
				'meta_query'       => [
					[
						'key'   => 'pricing_type',
						'value' => 'membership',
					]
				],
				'suppress_filters' => false
			];
		}
		return $args;
	}

	public static function add_store_config($config) {
		$config['membership_enabled']               = StoreFunctions::is_membership_enabled();
		$config['store_enabled']                    = StoreFunctions::is_store_enabled();
		$time_options                               = apply_filters('rtcl_store_time_options', []);
		$config["store"]['time_options']            = !empty($time_options) ? $time_options : (object)[];
		$config["registered_only"]['store_contact'] = Fns::registered_user_only('store_contact');
		return $config;
	}

	public static function add_store_data_to_listing_data($data, $listing) {
		if (is_a($listing, Listing::class) && $store = StoreFunctions::get_user_store($listing->get_owner_id())) {
			$store_data = [
				'id'    => $store->get_id(),
				'title' => $store->get_the_title()
			];
			$data['store'] = apply_filters('rtcl_store_rest_api_store_data_to_listing_data', $store_data, $store);
		}
		return $data;
	}

	/**
	 * @param array   $data
	 * @param WP_User $user
	 *
	 * @return array
	 */
	public static function add_store_only_membership($data, $user) {
		$member = rtclStore()->factory->get_membership($user->ID);
		if ($member && $member->has_membership()) {
			$data['membership'] = [
				'is_expired'    => $member->is_expired(),
				'expired_at'    => $member->get_expiry_date(),
				'remaining_ads' => $member->get_remaining_ads(),
				'posted_ads'    => $member->get_posted_ads(),
				'promotions'    => $member->get_promotions()
			];
		}
		if (Functions::get_option_item('rtcl_membership_settings', 'enable_store_only_membership', false, 'checkbox')) {
			if (!$member || !$member->has_membership() || $member->is_expired()) {
				return $data;
			}
		}

		$data['store'] = true;

		return $data;
	}
}
