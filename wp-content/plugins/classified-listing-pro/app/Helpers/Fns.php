<?php

namespace RtclPro\Helpers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use WP_Query;

class Fns
{

	/**
	 * @param Listing $listing
	 *
	 * @return bool
	 */
	static function is_popular($listing)
	{
		$views = absint(get_post_meta($listing->get_id(), '_views', true));
		$popular_threshold = Functions::get_option_item('rtcl_moderation_settings', 'popular_listing_threshold', 0, 'number');
		if ($views >= $popular_threshold) {
			return true;
		}

		return false;
	}

	/**
	 * Display the classes for the listing div
	 *
	 * @param string|array $classes One or more classes to add to the class list.
	 *
	 * @since 1.5.4
	 */
	static function top_listings_wrap_class($classes = [])
	{
		$classes[] = 'rtcl-listings';
		$classes[] = apply_filters('rtcl_listings_view_class', 'rtcl-list-view');
		$classes[] = apply_filters('rtcl_top_listings_grid_columns', 'columns-4');
		$classes = apply_filters('rtcl_top_listings_wrap_class', $classes);
		$classes = array_map('esc_attr', array_unique(array_filter($classes)));
		if (!empty($classes)) {
			echo 'class="' . esc_attr(implode(' ', $classes)) . '"';
		}
	}

	static function top_listings_query()
	{
		$post_per_pare = Functions::get_option_item('rtcl_moderation_settings', 'listing_top_per_page', 2);
		$query_args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => absint($post_per_pare),
			'orderby'        => 'rand'
		];
		/**
		 * @var $query WP_Query
		 */
		$query = $GLOBALS['wp_query'];

		if (isset($_GET['q'])) {
			$query_args['s'] = Functions::clean($_GET['q']);
		}
		if (!empty($query->tax_query->queries)) {
			$query_args['tax_query'] = $query->tax_query->queries;
		}
		if (!empty($query->tax_query->queries)) {
			$query_args['meta_query'] = $query->meta_query->queries;
		}
		$query_args['meta_query'][] = [
			'key'     => '_top',
			'value'   => 1,
			'compare' => '='
		];

		if (count($query_args['meta_query']) > 1) {
			$query_args['meta_query']['relation'] = "AND";
		}
		$query = new WP_Query(apply_filters('rtcl_top_listings_query_args', $query_args));

		return apply_filters('rtcl_top_listings_query', $query);
	}

	static function is_enable_top_listings()
	{
		return Functions::get_option_item('rtcl_moderation_settings', 'listing_enable_top_listing', false, 'checkbox');
	}

	public static function comments($comment, $args, $depth)
	{
		$GLOBALS['comment'] = $comment; // WPCS: override ok.
		Functions::get_template('listing/review', array(
			'comment' => $comment,
			'args'    => $args,
			'depth'   => $depth,
		), '', rtclPro()->get_plugin_template_path());
	}


	/**
	 * Get HTML for star rating.
	 *
	 * @param float $rating Rating being shown.
	 * @param int   $count  Total number of ratings.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public static function get_star_rating_html($rating, $count = 0)
	{
		$html = '<span style="width:' . (($rating / 5) * 100) . '%">';

		if (0 < $count) {
			/* translators: 1: rating 2: rating count */
			$html .= sprintf(_n('Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'classified-listing-pro'), '<strong class="rating">' . esc_html($rating) . '</strong>', '<span class="rating">' . esc_html($count) . '</span>');
		} else {
			/* translators: %s: rating */
			$html .= sprintf(esc_html__('Rated %s out of 5', 'classified-listing-pro'), '<strong class="rating">' . esc_html($rating) . '</strong>');
		}

		$html .= '</span>';

		return apply_filters('rtcl_get_star_rating_html', $html, $rating, $count);
	}

	/**
	 * Get HTML for ratings.
	 * s     *
	 *
	 * @param float $rating Rating being shown.
	 * @param int   $count  Total number of ratings.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public static function get_rating_html($rating, $count = 0)
	{
		if (0 < $rating) {
			$title = sprintf(_n('Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'classified-listing-pro'), esc_html($rating), esc_html($count));
			$html = '<div class="star-rating" title="' . $title . '">';
			$html .= self::get_star_rating_html($rating, $count);
			$html .= '</div>';
		} else {
			$html = '';
		}

		return apply_filters('rtcl_listing_get_rating_html', $html, $rating, $count);
	}

	/**
	 * @deprecated
	 * @use Rtcl\Helpers\Functions::is_enable_map()
	 */
	public static function is_enable_map()
	{
		_deprecated_function(__METHOD__, '2.0.9', 'Rtcl\Helpers\Functions::is_enable_map()');
		return Functions::is_enable_map();
	}

	/**
	 * @return bool|int|mixed|null
	 * @deprecated
	 * @use Rtcl\Helpers\Functions::has_map()
	 */
	static function has_map()
	{
		_deprecated_function(__METHOD__, '2.0.9', 'Rtcl\Helpers\Functions::has_map()');
		return Functions::has_map();
	}

	/**
	 * @return bool|int|mixed|null
	 * @deprecated
	 * @use Rtcl\Helpers\Functions::get_map_type()
	 */
	static function get_map_type()
	{
		_deprecated_function(__METHOD__, '2.0.9', 'Rtcl\Helpers\Functions::get_map_type()');
		return Functions::get_map_type();
	}

	/**
	 * @param integer $listing_id
	 *
	 * @return bool
	 * @deprecated
	 * @use Rtcl\Helpers\Functions::hide_map()
	 */
	static function hide_map($listing_id)
	{
		_deprecated_function(__METHOD__, '2.0.9', 'Rtcl\Helpers\Functions::hide_map()');
		return Functions::hide_map($listing_id);
	}

	static function is_online($author_id)
	{
		$online_status = get_user_meta($author_id, 'online_status', true);

		return !empty($online_status) && $online_status >= current_time('timestamp');
	}

	static function is_enable_chat()
	{
		return Functions::get_option_item('rtcl_chat_settings', 'enable', false, 'checkbox');
	}

	static function is_enable_chat_unread_message_email()
	{
		return self::is_enable_chat() && Functions::get_option_item('rtcl_chat_settings', 'unread_message_email', false, 'checkbox');
	}

	public static function is_enable_compare()
	{
		return Functions::get_option_item('rtcl_general_settings', 'enable_compare', false, 'checkbox');
	}

	public static function is_enable_quick_view()
	{
		return Functions::get_option_item('rtcl_general_settings', 'enable_quick_view', false, 'checkbox');
	}

	public static function check_license()
	{
		return apply_filters('rtcl_check_license', true);
	}

	/**
	 * @return mixed
	 * @deprecated
	 * @use Rtcl\Helpers\Functions::location_type()
	 */
	public static function location_type()
	{
		_deprecated_function(__METHOD__, '2.0.9', 'FRtcl\Helpers\unctions::location_type()');
		return Functions::location_type();
	}

	public static function is_enable_mark_as_sold()
	{
		return Functions::get_option_item('rtcl_general_settings', 'enable_mark_as_sold', false, 'checkbox');
	}

	/**
	 *
	 * @param integer $listing_id
	 *
	 * @return bool
	 */
	public static function is_mark_as_sold($listing_id)
	{
		return (bool)absint(get_post_meta($listing_id, '_rtcl_mark_as_sold', true));
	}

	/**
	 * Check if has any option for register user only
	 *
	 * @return bool
	 */
	public static function registered_user_only($key)
	{
		return $key && Functions::get_option_item('rtcl_moderation_settings', 'registered_only', $key, 'multi_checkbox');
	}


	/**
	 * Does user needs email validation?
	 *
	 * @param $user_id
	 *
	 * @return bool
	 */
	public static function needs_validation($user_id)
	{
		return boolval(get_user_meta($user_id, 'rtcl_verification_key', true));
	}


	static function is_wc_payment_enabled()
	{
		return Functions::is_wc_activated() && Functions::get_option_item('rtcl_payment_woo-payment', 'enabled', false, 'checkbox');
	}

	static function is_woo_order_autocomplete_disable()
	{
		return Functions::get_option_item('rtcl_payment_woo-payment', 'order_autocomplete_disable', false, 'checkbox');
	}

	/**
	 * @param int $con_id conversation id
	 * @param int $user_id
	 *
	 * @return bool
	 */
	static function is_on_conversation($con_id, $user_id = 0)
	{
		$user_id = !empty($user_id) ? absint($user_id) : get_current_user_id();
		$con_status = get_user_meta($user_id, '_rtcl_conversation_status', true);
		if (!$con_status) {
			return false;
		}
		$con_status = explode(':', $con_status);
		if (count($con_status) !== 2) {
			return false;
		}
		$conv_status_time = $con_status[0];
		$conv_status_id = absint($con_status[1]);
		return $conv_status_id === $con_id && !empty($conv_status_time) && $conv_status_time >= current_time('timestamp');
	}

	/**
	 * @param int $con_id conversation id
	 * @param int $user_id
	 *
	 * @return void
	 */
	public static function update_chat_conversation_status($con_id, $user_id = 0)
	{
		$user_id = !empty($user_id) ? absint($user_id) : get_current_user_id();
		$time = current_time('timestamp') + (int)apply_filters('rtcl_chat_conversation_status_seconds', 15);
		update_user_meta($user_id, '_rtcl_conversation_status', "$time:$con_id");
	}
}
