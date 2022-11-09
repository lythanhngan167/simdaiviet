<?php

namespace Rtrs\Hooks;

class Backend {
	public function __construct() {
		add_filter('ajax_query_attachments_args', [$this, 'wpse_hide_cv_media_overlay_view']);
	}

	/**
	 * Hide attachment files from the Media Library's overlay (modal) view
	 * if they have a certain meta key set.
	 *
	 * @param array $args An array of query variables.
	 */
	public function wpse_hide_cv_media_overlay_view($args) {
		// Bail if this is not the admin area.
		if (! is_admin()) {
			return;
		}

		// Modify the query.
		$args['meta_query'] = [
			[
				'key'     => 'attach_type',
				'compare' => 'NOT EXISTS',
			],
		];

		return $args;
	}
}
