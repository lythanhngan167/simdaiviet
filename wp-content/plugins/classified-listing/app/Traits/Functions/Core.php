<?php

namespace Rtcl\Traits\Functions;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Geolocation;

trait Core
{

	public static function verify_nonce() {
		$nonce = isset($_REQUEST[rtcl()->nonceId]) ? $_REQUEST[rtcl()->nonceId] : null;
		$nonceText = rtcl()->nonceText;
		if (wp_verify_nonce($nonce, $nonceText)) {
			return true;
		}

		return false;
	}


	/**
	 * Get the user's default location.
	 *
	 * Filtered, and set to base location or left blank. If cache-busting,
	 * this should only be used when 'location' is set in the querystring.
	 *
	 * @return array
	 * @since 2.0.15
	 */
	public static function get_user_default_location() {
		$set_default_location_to = get_option('rtcl_default_customer_address', 'base');
		$default_location = '' ;//=== $set_default_location_to ? '' : get_option('woocommerce_default_country', 'US:CA');
		$location = [];//wc_format_country_state_string(apply_filters('rtcl_user_default_location', $default_location));
		$set_default_location_to = 'geolocation';
		// Geolocation takes priority if used and if geolocation is possible.
		if ('geolocation' === $set_default_location_to || 'geolocation_ajax' === $set_default_location_to) {
			$ua = self::get_user_agent();

			// Exclude common bots from geolocation by user agent.
			if (!stristr($ua, 'bot') && !stristr($ua, 'spider') && !stristr($ua, 'crawl')) {
				$geolocation = Geolocation::geolocate_ip('', true, false);
				if (!empty($geolocation['country'])) {
					$location = $geolocation;
				}
			}
		}

		return apply_filters('rtcl_user_default_location_array', $location);
	}

	/**
	 * Get user agent string.
	 *
	 * @return string
	 * @since  2.0.15
	 */
	public static function get_user_agent() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? Functions::clean(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : ''; // @codingStandardsIgnoreLine
	}
}