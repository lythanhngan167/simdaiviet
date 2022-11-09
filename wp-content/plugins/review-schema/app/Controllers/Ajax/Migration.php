<?php

namespace Rtrs\Controllers\Ajax;

use WP_Query;

class Migration {
	public function __construct() {
		add_action('wp_ajax_rtrs_data_import', [$this, 'data_import']);
	}

	public function data_import() {
		$data_id = (! empty($_REQUEST['data_id'])) ? sanitize_text_field($_REQUEST['data_id']) : '';

		$result = false;
		switch ($data_id) {
			case 'wp_seo_schema':

				if (is_plugin_active('wp-seo-structured-data-schema/wp-seo-structured-data-schema.php')) {
					$result = $this->wp_seo_schema();
				}
				break;

			case 'schema':
				if (is_plugin_active('schema/schema.php')) {
					$result = $this->schema();
				}
				break;
		}

		if ($result) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error();
		}
	}

	public function wp_seo_schema() {
		//TODO: do it later in new version

		//import custom post
		$query = new WP_Query(
			[
				'post_type'      => 'post',
				'posts_per_page' => -1,
			]
		);

		$test_array = [];
		while ($query->have_posts()) {
			$query->the_post();

			$test_array[] = get_post_meta(get_the_ID());
		}

		//import settings
		global $KcSeoWPSchema;
		global $wpdb;
		$settings      = get_option($KcSeoWPSchema->options['settings']);
		$main_settings = get_option($KcSeoWPSchema->options['main_settings']);

		if (isset($settings)) {
			// business info tab
			$rtrs_schema_settings = get_option('rtrs_schema_settings');

			if (isset($settings['site_type'])) {
				$rtrs_schema_settings['category'] = esc_attr($settings['site_type']);
			}
			if (isset($settings['sitename'])) {
				$rtrs_schema_settings['name'] = esc_html($settings['sitename']);
			}
			if (isset($settings['siteaname'])) {
				$rtrs_schema_settings['alternateName'] = esc_html($settings['siteaname']);
			}
			if (isset($settings['site_image'])) {
				$rtrs_schema_settings['image'] = esc_html($settings['site_image']);
			}

			//address
			if (isset($settings['address']['street'])) {
				$rtrs_schema_settings['streetAddress'] = esc_html($settings['address']['street']);
			}
			if (isset($settings['address']['locality'])) {
				$rtrs_schema_settings['addressLocality'] = esc_html($settings['address']['locality']);
			}
			if (isset($settings['address']['region'])) {
				$rtrs_schema_settings['addressRegion'] = esc_html($settings['address']['region']);
			}
			if (isset($settings['address']['postalcode'])) {
				$rtrs_schema_settings['postalCode'] = esc_html($settings['address']['postalcode']);
			}
			if (isset($settings['address']['country'])) {
				$rtrs_schema_settings['addressCountry'] = esc_html($settings['address']['country']);
			}
			if (isset($settings['business_info']['latitude'])) {
				$rtrs_schema_settings['latitude'] = esc_html($settings['business_info']['latitude']);
			}
			if (isset($settings['business_info']['longitude'])) {
				$rtrs_schema_settings['longitude'] = esc_html($settings['business_info']['longitude']);
			}
			if (isset($settings['business_info']['description'])) {
				$rtrs_schema_settings['description'] = esc_html($settings['business_info']['description']);
			}
			if (isset($settings['business_info']['openingHours'])) {
				$rtrs_schema_settings['openingHours'] = esc_html($settings['business_info']['openingHours']);
			}
			if (isset($settings['site_telephone'])) {
				$rtrs_schema_settings['telephone'] = esc_html($settings['site_telephone']);
			}
			if (isset($settings['site_price_range'])) {
				$rtrs_schema_settings['priceRange'] = esc_html($settings['site_price_range']);
			}
			//end business info tab

			// social profile tab
			$rtrs_schema_sp_settings = get_option('rtrs_schema_social_profiles_settings');
			if (isset($settings['social']) && ! empty($settings['social'])) {
				foreach ($settings['social'] as $value) {
					if ($value['link']) {
						$rtrs_schema_sp_settings[$value['id']] = esc_url($value['link']);
					}
				}
			}
			//end social profile tab

			// social profile tab
			$rtrs_schema_cc_settings = get_option('rtrs_schema_corporate_contacts_settings');
			if (isset($settings['contact']['contactType'])) {
				$rtrs_schema_cc_settings['type'] = esc_attr(str_replace(' ', '_', $settings['contact']['contactType']));
			}
			if (isset($settings['contact']['telephone'])) {
				$rtrs_schema_cc_settings['telephone'] = esc_html($settings['contact']['telephone']);
			}
			if (isset($settings['siteurl'])) {
				$rtrs_schema_cc_settings['url'] = esc_html($settings['siteurl']);
			}
			//end social profile tab

			$rtrs_schema_tpp_settings = get_option('rtrs_schema_tpp_settings');
			if (isset($main_settings['wc_schema_disable'])) {
				$rtrs_schema_tpp_settings['wc_schema'] = 'yes';
			}
			if (isset($main_settings['edd_schema_microdata'])) {
				$rtrs_schema_tpp_settings['edd_schema'] = 'yes';
			}
			if (isset($main_settings['yoast_wpseo_json_ld'])) {
				$rtrs_schema_tpp_settings['yoast_schema'] = 'yes';
			}
			if (isset($main_settings['yoast_wpseo_json_ld_search'])) {
				$rtrs_schema_tpp_settings['yoast_search_schema'] = 'yes';
			}
			//end tpp tab

			$wpdb->query('START TRANSACTION');
			$errorDesc = [];

			if ($rtrs_schema_settings) {
				update_option('rtrs_schema_settings', $rtrs_schema_settings);
			}
			if ($rtrs_schema_sp_settings) {
				update_option('rtrs_schema_social_profiles_settings', $rtrs_schema_sp_settings);
			}
			if ($rtrs_schema_cc_settings) {
				update_option('rtrs_schema_corporate_contacts_settings', $rtrs_schema_cc_settings);
			}
			if ($rtrs_schema_tpp_settings) {
				update_option('rtrs_schema_tpp_settings', $rtrs_schema_tpp_settings);
			}

			if (count($errorDesc)) {
				echo implode("\n<br/>", $errorDesc);
				$wpdb->query('ROLLBACK');
			} else {
				$wpdb->query('COMMIT');

				return true;
			}
		}
	}

	public function schema() {
		return false;
	}
}
