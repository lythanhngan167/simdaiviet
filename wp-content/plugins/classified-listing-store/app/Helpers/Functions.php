<?php

namespace RtclStore\Helpers;

use Rtcl\Models\Payment;
use Rtcl\Helpers\Functions as RtclFunctions;
use RtclStore\Models\Store;


class Functions {

	static function is_enable_store_manager() {
		return RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable_store_manager', false, 'checkbox' );
	}

	/**
	 * @param Payment $payment
	 *
	 * @throws \Exception
	 */
	public static function apply_membership( $payment ) {
		if ( $payment && is_a( $payment, Payment::class ) && ! $payment->is_applied() ) {
			$user_id = $payment->get_customer_id();
			$member  = rtclStore()->factory->get_membership( $user_id );
			if ( $member ) {
				$member->apply_membership( $payment );
			}
		}
	}

	/**
	 * @param int $parent
	 *
	 * @return array
	 */
	static function get_store_category( $parent = 0 ) {
		$terms = [];

		$term_args = [
			'taxonomy'   => rtclStore()->category,
			'parent'     => $parent,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false
		];
		$terms     = get_terms( $term_args );

		return $terms;
	}

	/**
	 * @param int $store_id
	 *
	 * @return array
	 */
	static function get_store_selected_term_id( $store_id ) {
		$selectedTermId = $parent = [];
		$selectedTerms  = wp_get_post_terms(
			$store_id,
			rtclStore()->category,
			[
				'orderby' => 'term_id'
			]
		);

		if (!empty($selectedTerms)) {
			$childTerm[] =  end($selectedTerms);
		}

		if (!empty($childTerm) ) {
			foreach ( $childTerm as $term ) {
				if ( ! in_array( $term->term_id, $selectedTermId ) ) {
					$selectedTermId[] = $term->term_id;
				}
				$parent = get_term( $term, rtclStore()->category );
				while ( $parent->parent != '0' ) {
					$term_id = $parent->parent;
					if ( ! in_array( $term_id, $selectedTermId ) ) {
						$selectedTermId[] = $term_id;
					}
					$parent  = get_term( $term_id, rtclStore()->category );
				}
			}
			$selectedTermId = array_reverse( $selectedTermId );
		}

		return [
			'termId'    => $selectedTermId,
			'parent'    => $parent
		];
	}

	/**
	 * @return array
	 */
	static function get_first_level_category_array() {
		$terms    = [];
		$termObjs = RtclFunctions::get_sub_terms( rtcl()->category );
		if ( ! empty( $termObjs ) ) {
			$terms = wp_list_pluck( $termObjs, 'name', 'term_id' );
		}

		return $terms;
	}

	/**
	 * @param $user_id
	 *
	 * @return Store || null
	 */
	public static function get_user_store( $user_id ) {
		$user_id  = $user_id ? absint( $user_id ) : null;
		$post     = null;
		$getStore = get_posts( [
			'post_type'        => rtclStore()->post_type,
			'posts_per_page'   => 1,
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'meta_query'       => [
				[
					'key'     => 'store_owner_id',
					'value'   => $user_id,
					'type'    => 'numeric',
					'compare' => '=',
				]
			]
		] );

		if ( ! empty( $getStore ) && ! empty( $getStore[0] ) ) {
			$post = $getStore[0];
		}

		$store = self::is_store_enabled() && $post ? rtclStore()->factory->get_store( $post->ID ) : null;

		return apply_filters( 'rtcl_store_get_user_store', $store, $user_id );
	}

	/**
	 * @param int $user_id User id
	 *
	 * @return false|Store
	 */
	public static function get_manager_store( $user_id = 0 ) {
		if ( ( $user_id = $user_id ? absint( $user_id ) : get_current_user_id() ) && ( $store_id = absint( get_user_meta( $user_id, '_rtcl_store_id', true ) ) ) && $store = rtclStore()->factory->get_store( $store_id ) ) {
			return $store;
		}

		return false;
	}

	/**
	 * @return Store | null
	 */
	public static function get_current_user_store() {
		return apply_filters( 'rtcl_store_get_current_user_store', self::get_user_store( get_current_user_id() ) );
	}

	/**
	 * @param     $string
	 * @param int $limit
	 *
	 * @return string
	 */
	public static function limit_length( $string, $limit = 127 ) {
		if ( strlen( $string ) > $limit ) {
			$string = substr( $string, 0, $limit - 3 ) . '...';
		}

		return apply_filters( 'rtcl_store_limit_length', $string, $limit );
	}

	/**
	 * @param \WP_Post|false $store
	 *
	 * @return bool
	 */
	static function is_store_expired( $store = false ) {
		if ( ! $store ) {
			global $post;
			$store = ! empty( $post->ID ) ? rtclStore()->factory->get_store( $post->ID ) : null;
		}
		if ( $store && is_a( $store, Store::class ) && RtclFunctions::get_option_item( 'rtcl_membership_settings', 'display_store_only_valid_membership', false, 'checkbox' ) ) {
			if ( $user_id = absint( get_post_meta( $store->get_id(), 'store_owner_id', true ) ) ) {
				$member = rtclStore()->factory->get_membership( $user_id );
				if ( $member && ( ! $member->has_membership() || $member->is_expired() ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param string $term
	 *
	 * @return bool
	 */
	static function is_store_category( $term = '' ) {
		return is_tax( rtclStore()->category, $term );
	}

	/**
	 * @return bool
	 */
	static function is_store_taxonomy() {
		return is_tax( get_object_taxonomies( rtclStore()->post_type ) );
	}

	/**
	 * Is it is Store Archive page
	 *
	 * @return bool
	 */
	static function is_store() {
		return is_post_type_archive( rtclStore()->post_type ) || ( ( $store_page_id = RtclFunctions::get_page_id( 'store' ) ) && is_page( $store_page_id ) );
	}

	/**
	 * Is it Single Store
	 *
	 * @return boolean
	 */
	static function is_single_store() {
		return is_singular( [ rtclStore()->post_type ] );
	}

	/**
	 * Is Store option is enabled
	 *
	 * @return mixed|void
	 */
	static function is_store_enabled() {
		return apply_filters( 'rtcl_store_option_is_store_enabled', RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable_store', false, 'checkbox' ) );
	}


	/**
	 * Main function for returning Store, uses the StoreFactory class.
	 *
	 * @param mixed $store Post object or post ID of the product.
	 *
	 * @return Store|null|false
	 */
	public static function get_store( $store = false ) {
		return rtclStore()->factory->get_store( $store );
	}

	/**
	 * @return String $format Store time format
	 */
	public static function get_store_time_format() {
		$options = apply_filters( 'rtcl_store_time_options', [
			"icons" => [
				"up"   => 'rtcl-icon-up-open',
				"down" => 'rtcl-icon-down-open'
			]
		] );
		$format  = "g:i A";
		if ( isset( $options['showMeridian'] ) && $options['showMeridian'] === false ) {
			$format = 'H:i';
		}

		return apply_filters( 'rtcl_store_time_format', $format );
	}

	public static function is_membership_enabled() {
		return RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable', false, 'checkbox' );
	}

	public static function is_enable_free_ads() {
		return self::is_membership_enabled() && RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable_free_ads', false, 'checkbox' );
	}

	/**
	 * Output the pagination.
	 */
	static function pagination() {
		if ( ! self::get_loop_prop( 'is_paginated' ) ) {
			return;
		}

		$args = array(
			'total'   => self::get_loop_prop( 'total_pages' ),
			'current' => self::get_loop_prop( 'current_page' ),
			'base'    => esc_url_raw( add_query_arg( 'store-page', '%#%', false ) ),
			'format'  => '?store-page=%#%',
		);

		if ( ! self::get_loop_prop( 'is_shortcode' ) ) {
			$args['format'] = '';
			$args['base']   = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
		}

		RtclFunctions::get_template( 'listing/loop/pagination', $args );
	}


	/**
	 * Gets a property from the rtcl_loop global.
	 *
	 * @param string $prop Prop to get.
	 * @param string $default Default if the prop does not exist.
	 *
	 * @return mixed
	 * @since 1.2.31
	 */
	static function get_loop_prop( $prop, $default = '' ) {
		self::setup_loop(); // Ensure shop loop is setup.

		return isset( $GLOBALS['rtcl_store_loop'], $GLOBALS['rtcl_store_loop'][ $prop ] ) ? $GLOBALS['rtcl_store_loop'][ $prop ] : $default;
	}

	/**
	 * Sets a property in the rtcl_store_loop global.
	 *
	 * @param string $prop Prop to set.
	 * @param string $value Value to set.
	 *
	 * @since 1.5.5
	 */
	static function set_loop_prop( $prop, $value = '' ) {
		if ( ! isset( $GLOBALS['rtcl_store_loop'] ) ) {
			self::setup_loop();
		}
		$GLOBALS['rtcl_store_loop'][ $prop ] = $value;
	}

	/**
	 * Resets the rtcl_loop global.
	 *
	 * @since 1.5.5
	 */
	static function reset_loop() {
		unset( $GLOBALS['rtcl_store_loop'] );
	}

	/**
	 * Sets up the rtcl_loop global from the passed args or from the main query.
	 *
	 * @param array $args Args to pass into the global.
	 *
	 * @since 1.5.5
	 */
	static function setup_loop( $args = array() ) {
		$default_args = array(
			'loop'         => 0,
			'is_shortcode' => false,
			'is_paginated' => true,
			'is_search'    => false,
			'total'        => 0,
			'total_pages'  => 0,
			'per_page'     => 0,
			'current_page' => 1,
		);

		// If this is a main RTCL query, use global args as defaults.
		if ( $GLOBALS['wp_query']->get( 'rtcl_store_query' ) ) {
			$default_args = array_merge(
				$default_args,
				array(
					'is_search'    => $GLOBALS['wp_query']->is_search(),
					'total'        => $GLOBALS['wp_query']->found_posts,
					'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
					'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
					'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
				)
			);
		}

		// Merge any existing values.
		if ( isset( $GLOBALS['rtcl_store_loop'] ) ) {
			$default_args = array_merge( $default_args, $GLOBALS['rtcl_store_loop'] );
		}

		$GLOBALS['rtcl_store_loop'] = wp_parse_args( $args, $default_args );
	}

	public static function store_loop_start( $echo = true ) {
		self::set_loop_prop( 'loop', 0 );
		$loop_start = apply_filters( 'rtcl_store_loop_start', RtclFunctions::get_template_html( 'store/loop/loop-start', null, '', rtclStore()->get_plugin_template_path() ) );

		if ( $echo ) {
			echo $loop_start; // WPCS: XSS ok.
		} else {
			return $loop_start;
		}
	}

	public static function store_loop_end( $echo = true ) {

		$loop_end = apply_filters( 'rtcl_store_loop_end', RtclFunctions::get_template_html( 'store/loop/loop-end', null, '', rtclStore()->get_plugin_template_path() ) );

		if ( $echo ) {
			echo $loop_end; // WPCS: XSS ok.
		} else {
			return $loop_end;
		}
	}


	/**
	 * Retrieves the classes for the post div as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @param int|\WP_Post|Store $store Store ID or store object.
	 *
	 * @return array
	 * @since 1.2.31
	 */
	static function get_store_class( $class = '', $store = null ) {
		if ( is_null( $store ) && ! empty( $GLOBALS['store'] ) ) {
			// Product was null so pull from global.
			$store = $GLOBALS['store'];
		}

		if ( $store && ! is_a( $store, Store::class ) ) {
			$store = rtclStore()->factory->get_store( $store );
		}
		$class = $class && ! is_array( $class ) ? preg_split( '#\s+#', $class ) : [];

		$post_classes = array_map( 'esc_attr', $class );

		if ( ! $store ) {
			return $post_classes;
		}


		$post_classes = apply_filters( 'post_class', $post_classes, $class, $store->get_id() );

		$classes = array_merge(
			$post_classes,
			[ 'post-' . $store->get_id() ],
			$store->get_label_class(),
			RtclFunctions::get_listing_taxonomy_class( $store->get_category_ids(), rtclStore()->category )
		);

		return array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
	}

	/**
	 * Display the classes for the listing div.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @param int|\WP_Post|Store $store_id Listing ID or product object.
	 *
	 * @since 1.5.4
	 */
	static function store_class( $class = [], $store_id = null ) {
		$classes = self::get_store_class( $class, $store_id );
		if ( ! empty( $classes ) ) {
			echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		}
	}

	/**
	 * Display the classes for the listing div
	 *
	 * @param string|array $classes One or more classes to add to the class list.
	 *
	 * @since 1.5.4
	 */
	static function store_loop_start_class( $classes = [] ) {
		$classes[] = 'rtcl-stores';
		$classes[] = apply_filters( 'rtcl_stores_grid_columns_class', 'columns-4' );
		$classes   = array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
		$classes   = apply_filters( 'rtcl_store_loop_start_class', $classes );
		if ( ! empty( $classes ) ) {
			echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		}
	}

	static function page_title( $echo = true ) {

		if ( is_search() ) {
			/* translators: %s: search query */
			$page_title = sprintf( esc_html__( 'Search results: &ldquo;%s&rdquo;', 'classified-listing-store' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				/* translators: %s: page number */
				$page_title .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'classified-listing-store' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_tax() ) {

			$page_title = single_term_title( '', false );

		} else {
			$listings_page_id = RtclFunctions::get_page_id( 'store' );
			$page_title       = get_the_title( $listings_page_id );
		}

		$page_title = apply_filters( 'rtcl_page_title', $page_title );

		if ( $echo ) {
			echo $page_title; // WPCS: XSS ok.
		} else {
			return $page_title;
		}
	}

}
