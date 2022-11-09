<?php

namespace RtclStore\Helpers;

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Api;
use RtclStore\Models\Store;
use WP_Query;

class StoreApi {
	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_query_store_data( $args = [] ): array {
		$data    = [];
		$results = new WP_Query( $args );

		$pagination = null;
		if ( ! empty( $results->posts ) ) {
			foreach ( $results->posts as $post_id ) {
				$listing = rtclStore()->factory->get_store( $post_id );
				$data[]  = StoreApi::get_store_data( $listing );
			}

			$pagination = [
				'total'        => $results->found_posts,
				'count'        => $results->post_count,
				'per_page'     => $results->query['posts_per_page'],
				'current_page' => $results->query['paged'],
				'total_pages'  => $results->max_num_pages
			];
		}

		return [
			'data'       => $data,
			'pagination' => $pagination
		];
	}

	/**
	 * @param Store $store
	 *
	 * @return array|null
	 */
	public static function get_store_data( Store $store ) {
		if ( ! $store || ! is_a( $store, Store::class ) ) {
			return null;
		}
		$data = [
			'id'             => $store->get_id(),
			'title'          => $store->get_the_title(),
			'logo'           => $store->get_logo_url(),
			'listings_count' => $store->get_ad_count(),
		];

		return apply_filters( 'rtcl_store_rest_api_store_data', $data, $store );
	}

	public static function get_single_store_data( Store $store ) {
		if ( ! $store || ! is_a( $store, Store::class ) ) {
			return null;
		}
		$data            = self::get_store_data( $store );
		$singleStoreData = [
			'slug'           => $store->get_slug(),
			'banner'         => $store->get_banner_url(),
			'description'    => strip_tags( strip_shortcodes( $store->get_the_description() ) ),
			'slogan'         => $store->get_the_slogan(),
			'email'          => $store->get_email(),
			'address'        => $store->get_address(),
			'phone'          => $store->get_phone(),
			'owner_id'       => $store->owner_id(),
			'owner'          => Api::get_user_data( get_user_by('ID', $store->owner_id()) ),
			'website'        => $store->get_website(),
			'created_at'     => $store->created_at(),
			'created_at_gmt' => $store->created_at_gmt(),
			'opening_hours'  => [
				'type'  => $store->get_open_hour_type(),
				'hours' => $store->get_open_hours(),
			],
			'social'         => $store->get_social_media(),
			'review'         => [
				'total'   => $store->get_review_counts(),
				'average' => $store->get_average_rating(),
			],
			'config'         => [
				'banner' => Functions::get_option_item( 'rtcl_misc_settings', 'store_banner_size', [
					'width'  => 992,
					'height' => 300,
					'crop'   => 'yes'
				] ),
				'logo'   => Functions::get_option_item( 'rtcl_misc_settings', 'store_logo_size', [
					'width'  => 200,
					'height' => 150,
					'crop'   => 'yes'
				] )
			]
		];
		$storeData       = $data + $singleStoreData;

		return apply_filters( 'rtcl_store_rest_api_single_store_data', $storeData, $store );
	}

	public static function get_my_store_data( Store $store ) {
		if ( ! $store || ! is_a( $store, Store::class ) ) {
			return null;
		}
		$data        = self::get_single_store_data( $store );
		$storeData   = [
			'slug' => $store->get_slug()
		];
		$myStoreData = $data + $storeData;

		return apply_filters( 'rtcl_store_rest_api_my_store_data', $myStoreData, $store );
	}

}