<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use Rtcl\Models\Pricing;
use RtclStore\Helpers\Functions as StoreFunctions;

class MembershipHook {

	public static function init() {
		if ( StoreFunctions::is_membership_enabled() ) {
			add_action( 'rtcl_listing_form_after_save_or_update', [ __CLASS__, 'update_posting_count' ], 1, 3 );

			add_action( 'rtcl_before_add_edit_listing_before_category_condition', [
				__CLASS__,
				'verify_membership_before_category'
			] );
			add_action( 'rtcl_before_add_edit_listing_into_category_condition', [
				__CLASS__,
				'verify_membership_into_category'
			], 10, 2 );

			add_filter( 'rtcl_checkout_process_new_order_args', [ __CLASS__, 'add_meta_to_membership_order' ], 20, 2 );
		}
	}

	/**
	 * @param array $new_payment_args
	 * @param Pricing $pricing
	 *
	 * @return array
	 */
	static function add_meta_to_membership_order( $new_payment_args, $pricing ) {
		if ( $pricing && 'membership' === $pricing->getType() ) {
			$new_payment_args['meta_input']['payment_type'] = 'membership';
			$membership_promotions                          = get_post_meta( $pricing->getId(), '_rtcl_membership_promotions', true );
			if ( ! empty( $membership_promotions ) ) {
				$new_payment_args['meta_input']['_rtcl_membership_promotions'] = $membership_promotions;
			}
		}

		return $new_payment_args;
	}

	/**
	 * @param Listing $listing
	 * @param string $type
	 * @param int $cat_id
	 */
	static function update_posting_count( $listing, $type, $cat_id ) {
		if ( ! is_a( $listing, Listing::class ) || "new" !== $type || ! $cat_id ) {
			return;
		}

		$user_id = $listing->get_owner_id();
		$data    = [
			'post_id'    => $listing->get_id(),
			'user_id'    => $user_id,
			'cat_id'     => $cat_id,
			'created_at' => $listing->get_listing()->post_date
		];
		$member  = rtclStore()->factory->get_membership( $user_id );
		if ( $member->has_membership() ) {
			if ( $member->is_valid_for_free( $cat_id ) && ( $member->is_valid_to_post_as_free() || Functions::get_option_item( 'rtcl_membership_settings', 'unlimited_free_ads_membership', false, 'checkbox' ) ) ) {
				self::update_posting_log( $data );
			} else {
				$member->update_post_count();
			}
		} else {
			self::update_posting_log( $data );
		}
	}

	static private function update_posting_log( $data ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'rtcl_posting_log',
			$data,
			[
				'%d',
				'%d',
				'%d',
				'%s'
			]
		);
	}

	static function verify_membership_before_category( $post_id ) {
		if ( $post_id ) {
			return;
		}
		if ( StoreFunctions::is_enable_store_manager() && ( $store = StoreFunctions::get_manager_store() ) && get_current_user_id() !== $store->owner_id() ) {
			$member = rtclStore()->factory->get_membership( $store->owner_id() );
		} else {
			$member = rtclStore()->factory->get_membership();
		}

		$enable_free_ads                          = Functions::get_option_item( 'rtcl_membership_settings', 'enable_free_ads', false, 'checkbox' );
		$enable_unlimited_free_ads_for_membership = Functions::get_option_item( 'rtcl_membership_settings', 'unlimited_free_ads_membership', false, 'checkbox' );
		if ( $member && $member->has_membership() ) {
			$allow_free_ads = false;
			if ( ( $enable_free_ads && $member->is_valid_to_post_as_free() ) || ( $enable_free_ads && $enable_unlimited_free_ads_for_membership ) ) {
				$remaining_free_ads = $enable_unlimited_free_ads_for_membership ? __( "unlimited", 'classified-listing-store' ) : $member->get_remaining_ads_as_free();
				Functions::add_notice(
					apply_filters( 'rtcl_remaining_free_ads_success_text',
						sprintf( __( 'Bạn có %s bài viết miễn phí.', 'classified-listing-store' ), $remaining_free_ads ),
						$remaining_free_ads, $member )
				);
				$allow_free_ads = true;
			}
			if ( $remaining_ads = $member->is_valid_to_post() ) {
				Functions::add_notice( apply_filters( 'rtcl_remaining_regular_ads_success_text',
					sprintf( __( 'Bạn có %s bài viết thông thường.', 'classified-listing-store' ), $remaining_ads ),
					$remaining_ads, $member ) );
			} else {
				if ( ! $allow_free_ads ) {
					Functions::add_notice( apply_filters( 'rtcl_remaining_ads_error_text',
						sprintf( __( 'You have no remaining ads at your current membership. <a href="%s">Update your membership</a>.', 'classified-listing-store' ), Link::get_checkout_endpoint_url( 'membership' ) ),
						$member ), 'error' );
				}
			}
		} else {
			if ( $enable_free_ads ) {
				if ( $member && $member->is_valid_to_post_as_free() ) {
					$remaining_free_ads = $member->get_remaining_ads_as_free();
					Functions::add_notice( apply_filters( 'rtcl_remaining_free_ads_success_text', sprintf( __( 'You have %s free ads.', 'classified-listing-store' ), $remaining_free_ads ),
						$remaining_free_ads, $member ) );
				} elseif ( ! $member && Functions::get_option_item( 'rtcl_account_settings', 'enable_post_for_unregister', false, 'checkbox' ) ) {
					Functions::add_notice( apply_filters( 'rtcl_remaining_free_ads_success_text', sprintf( __( 'You have %s free ads.', 'classified-listing-store' ), $enable_free_ads ),
						$enable_free_ads, $member ) );
				} else {
					Functions::add_notice( apply_filters( 'rtcl_remaining_free_ads_error_text',
						sprintf( __( 'You have no free ads remaining. You can buy a membership to post ad. <a href="%s">Buy a Membership.</a>', 'classified-listing-store' ),
							Link::get_checkout_endpoint_url( 'membership' ) ),
						$member ), 'error' );
				}
			} else {
				Functions::add_notice( apply_filters( 'rtcl_membership_buy_membership_error_text',
					sprintf( __( 'You can buy a membership to post ad. <a href="%s">Buy a Membership</a>', 'classified-listing-store' ), Link::get_checkout_endpoint_url( 'membership' ) ),
					$member ), 'error' );
			}
		}


	}

	static function verify_membership_into_category( $post_id, $category_id ) {
		if ( ! $post_id && $category_id ) {
			if ( StoreFunctions::is_enable_store_manager() && ( $store = StoreFunctions::get_manager_store() ) && get_current_user_id() !== $store->owner_id() ) {
				$member = rtclStore()->factory->get_membership( $store->owner_id() );
			} else {
				$member = rtclStore()->factory->get_membership();
			}
			$category_id = Functions::get_term_top_most_parent_id( $category_id, rtcl()->category );
			$cat         = get_term_by( 'id', $category_id, rtcl()->category );
			if ( $member && $member->has_membership() ) {
				if ( ! $member->is_valid_to_post_at_category( $category_id ) ) {
					if ( $member->is_valid_for_free( $category_id ) ) {
						if ( ! $member->is_valid_to_post_as_free() && Functions::get_option_item( 'rtcl_membership_settings', 'unlimited_free_ads_membership', false, 'checkbox' ) ) {
							Functions::add_notice( apply_filters( 'rtcl_category_error_message', sprintf(
								__( 'You are not allow to post at %s category. <a href="%s">Update your membership.</a>', "classified-listing-store" ),
								$cat ? $cat->name : '--',
								Link::get_checkout_endpoint_url( 'membership' )
							), $cat ), 'error' );
						}

					} else {
						Functions::add_notice( apply_filters( 'rtcl_category_error_message', sprintf(
							__( 'You are not allow to post at %s category. <a href="%s">Update your membership.</a>', "classified-listing-store" ),
							$cat ? $cat->name : '--',
							Link::get_checkout_endpoint_url( 'membership' )
						), $cat ), 'error' );
					}
				}
			} else {
				if ( Functions::get_option_item( 'rtcl_membership_settings', 'enable_free_ads', false, 'checkbox' ) ) {
					if ( $member && ! $member->is_valid_to_post_at_category_as_free( $category_id ) ) {
						Functions::add_notice( apply_filters( 'rtcl_category_error_message_free', sprintf(
							__( 'You are not allow to post at %s category as free. <a href="%s">Buy a membership.</a>', "classified-listing-store" ),
							$cat ? $cat->name : '--',
							Link::get_checkout_endpoint_url( 'membership' )
						), $cat ), 'error' );
					}
				}
			}
		}
	}

	public static function verify_membership() {
		//        $free_ads = absint(Functions::get_option_item('rtcl_membership_settings', 'free_ads', 0));
		$member = rtclStore()->factory->get_membership();
		if ( $member && ! $member->has_membership() ) {
			Functions::add_notice( __( "Only members can add new listing", "classified-listing-store" ), 'error' );
		}
	}

}