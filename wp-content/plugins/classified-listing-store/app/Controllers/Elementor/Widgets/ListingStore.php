<?php
/**
 * @author  RadiusTheme
 *
 * @since   1.0
 *
 * @version 1.0
 */

namespace RtclStore\Controllers\Elementor\Widgets;

use Rtcl\Helpers\Functions;
use RtclStore\Controllers\Elementor\Settings\ListingStoreSettings;

/**
 * ListingStore class
 */
class ListingStore extends ListingStoreSettings {
	/**
	 * ListingStore Init
	 *
	 * @param array $data others data
	 * @param [type] $args Others args
	 */
	public function __construct($data = [], $args = null) {
		$this->rtcl_name = __('Listing Store', 'classified-listing-store');
		$this->rtcl_base = 'rtcl-listing-store';
		parent::__construct($data, $args);
	}

	/**
	 * Store Query
	 *
	 * @param [type] $settings Query
	 *
	 * @return array
	 */
	private function store_query($data) {
		$result = [];
		$args   = [
			'post_type'           => 'store',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $data['posts_per_page'],
		];

		// Taxonomy
		if (!empty($data['store_cat'])) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'store_category',
					'field'    => 'term_id',
					'terms'    => $data['store_cat'],
				],
			];
		}

		$args['orderby']    = $data['store_orderby'];
		$args['order']      = $data['store_order'];

		$items = get_posts($args);
		foreach ($items as $item) {
			$store    = new \RtclStore\Models\Store($item->ID);
			$result[] = [
				'logo'      => $store->get_the_logo(),
				'title'     => $store->get_the_title(),
				'permalink' => $store->get_the_permalink(),
				'count'     => $store->get_ad_count(),
				'time'      => get_the_time('Y', $item->ID),
				// 'time'     => get_the_time( get_option( 'date_format' ), $item->ID ),
				'slogan'    => get_post_meta($item->ID, 'slogan', true),
				'address'   => get_post_meta($item->ID, 'address', true),
			];
		}

		return $result;
	}

	protected function render() {
		$settings           = $this->get_settings();
		$settings['stores'] = $this->store_query($settings);
		$template_style     = 'elementor/'.$settings['rtcl_store_view'].'-store';
		$data               = [
			'template'              => $template_style,
			'instance'              => $settings,
			'default_template_path' => rtclStore()->get_plugin_template_path(),
		];
		$data           = apply_filters('rtcl_el_store_data', $data);
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
	}
}
