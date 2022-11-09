<?php

namespace RtclPro\Controllers\Hooks;

use RtclPro\Helpers\PNHelper;
use WP_Term;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Options;
use Rtcl\Models\RtclCFGField;
use RtclPro\Emails\UnreadMessageEmail;
use RtclPro\Gateways\Stripe\GatewayStripe;
use RtclPro\Emails\UserVerifyLinkEmailToUser;
use RtclPro\Gateways\Authorize\GatewayAuthorize;
use RtclPro\Gateways\WooPayment\GatewayWooPayment;

class FilterHooks {
	public static function init() {
		add_filter( 'rtcl_register_settings_tabs', array( __CLASS__, 'app_tab_item' ) );
		add_filter( 'rtcl_settings_option_fields', array( __CLASS__, 'app_tab_options' ), 10, 2 );

		add_filter(
			'rtcl_location_type',
			function () {
				return Functions::location_type();
			},
			100
		);
		add_filter( 'rtcl_listing_promotions', array( __CLASS__, 'add_promotions' ) );

		add_filter( 'rtcl_listing_extra_class', array( __CLASS__, 'mark_as_sold_class' ), 10, 2 );
		add_filter( 'rtcl_load_payment_gateways', array( __CLASS__, 'add_pro_payment_gateways' ) );

		add_filter( 'rtcl_email_services', array( __CLASS__, 'add_email_services' ) );

		add_filter( 'rtcl_shortcode_listings_attributes', array( __CLASS__, 'add_view_at_shortcode_listings_attributes' ) );
		add_filter( 'rtcl_listings_view_class', array( __CLASS__, 'add_view_class_at_loop' ) );

		// Scripts params
		add_filter( 'rtcl_localize_params_public', array( __CLASS__, 'add_public_script_localize_params' ) );

		add_filter( 'rtcl_widget_search_fields', array( __CLASS__, 'widget_search_fields' ) );
		add_filter( 'rtcl_widget_search_update_values', array( __CLASS__, 'widget_search_update_values' ), 10, 2 );
		add_filter( 'rtcl_widget_search_default_values', array( __CLASS__, 'widget_search_default_values' ) );
		add_filter( 'rtcl_widget_search_values', array( __CLASS__, 'rtcl_widget_search_values' ), 10, 3 );

		add_filter( 'rtcl_widget_filter_fields', array( __CLASS__, 'widget_filter_fields' ) );
		add_filter( 'rtcl_widget_filter_values', array( __CLASS__, 'rtcl_widget_filter_values' ), 10, 3 );
		add_filter( 'rtcl_widget_filter_update_values', array( __CLASS__, 'widget_filter_update_values' ), 10, 2 );
		add_filter( 'rtcl_widget_filter_default_values', array( __CLASS__, 'widget_filter_default_values' ), 10, 2 );
		add_filter( 'rtcl_listing_query_meta_query', array( __CLASS__, 'widget_filter_rating_query' ) );

		add_filter( 'rtcl_widget_listings_data', array( __CLASS__, 'rtcl_widget_shortcode_filter_listings_data' ) );
		add_filter( 'rtcl_filter_listings_shortcode_data', array( __CLASS__, 'rtcl_widget_shortcode_filter_listings_data' ) );
		add_filter( 'rtcl_widget_listings_query_params', array( __CLASS__, 'widget_listings_query_params' ), 10, 2 );
		add_filter(
			'rtcl_filter_listings_shortcode_query_params',
			array(
				__CLASS__,
				'widget_listings_query_params',
			),
			10,
			2
		);
		add_filter( 'rtcl_widget_listings_fields', array( __CLASS__, 'widget_listings_fields' ) );
		add_filter( 'rtcl_cf_attributes_for_field_html', array( __CLASS__, 'cf_attributes_for_field' ), 10, 2 );
		add_filter( 'rtcl_get_listing_label_class', array( __CLASS__, 'add_label_class' ), 10, 2 );

		add_filter( 'rtcl_registration_name_validation', array( __CLASS__, 'remove_validation_for_name' ), 10, 2 );
		add_filter( 'rtcl_registration_phone_validation', array( __CLASS__, 'remove_validation_for_phone' ), 10, 2 );

		add_filter( 'rtcl_my_account_endpoint', array( __CLASS__, 'add_chat_end_point' ) );
		add_filter( 'rtcl_account_default_menu_items', array( __CLASS__, 'add_chat_menu_item' ) );
		add_filter( 'rtcl_addons', array( __CLASS__, 'remove_classified_listing_pro' ) );
		add_filter( 'rtcl_single_listing_script_dependencies', array( __CLASS__, 'sl_script_dependency' ) );
		add_filter( 'rtcl_single_listing_localized_params', array( __CLASS__, 'add_sl_localized_params' ) );

		add_filter( 'rtcl_registration_need_auth_new_user', array( __CLASS__, 'rtcl_registration_need_auth_new_user' ), 100, 2 );
		add_filter( 'rtcl_my_account_endpoint', array( __CLASS__, 'my_account_end_point_filter' ), 20 );
		add_filter( 'rtcl_is_enable_post_for_unregister', array( __CLASS__, 'is_enable_post_for_unregister' ), 100 );
		// GB Block Hooks
		add_filter( 'rtcl_gb_localize_script', array( __CLASS__, 'gb_block_pro_options' ), 10 );
		add_filter( 'rtcl_gb_category_box_data', array( __CLASS__, 'gb_listing_category_box_path' ), 10 );
		add_filter( 'rtcl_gb_listing_filter_data', array( __CLASS__, 'gb_listing_filter_path' ), 10 );
		add_filter( 'rtcl_gb_single_location_box_data', array( __CLASS__, 'gb_location_box_path' ), 10 );
		add_filter( 'rtcl_licenses', array( __CLASS__, 'license' ), 1 );
	}
	public static function license( $licenses ) {
			$licenses[] = array(
				'plugin_file' => RTCL_PRO_PLUGIN_FILE,
				'api_data'    => array(
					'key_name'    => 'license_key',
					'status_name' => 'license_status',
					'action_name' => 'rtcl_manage_licensing',
					'product_id'  => 81839,
					'version'     => RTCL_PRO_VERSION,
				),
				'settings'    => array(
					'title' => esc_html__( 'Main plugin license key', 'classified-listing-pro' ),
				),
			);
			return $licenses;
	}

	public static function gb_block_pro_options( $data ) {
		$data['single_location']['style_options'][] = array(
			'value' => '3',
			'label' => __( 'Style 3', 'classified-listing' ),
		);
		$data['category']['style_options'][]        = array(
			'value' => '2',
			'label' => __( 'Style 2', 'classified-listing' ),
		);
		$data['listing']['grid_style_options'][]    = array(
			'value' => '2',
			'label' => __( 'Style 2', 'classified-listing' ),
		);
		$data['listing']['grid_style_options'][]    = array(
			'value' => '3',
			'label' => __( 'Style 3', 'classified-listing' ),
		);
		$data['listing']['grid_style_options'][]    = array(
			'value' => '4',
			'label' => __( 'Style 4', 'classified-listing' ),
		);
		$data['listing']['grid_style_options'][]    = array(
			'value' => '5',
			'label' => __( 'Style 5', 'classified-listing' ),
		);
		$data['listing']['list_style_options'][]    = array(
			'value' => '2',
			'label' => __( 'Style 2', 'classified-listing' ),
		);
		$data['listing']['list_style_options'][]    = array(
			'value' => '3',
			'label' => __( 'Style 3', 'classified-listing' ),
		);
		$data['listing']['list_style_options'][]    = array(
			'value' => '4',
			'label' => __( 'Style 4', 'classified-listing' ),
		);
		$data['listing']['list_style_options'][]    = array(
			'value' => '5',
			'label' => __( 'Style 5', 'classified-listing' ),
		);
		$data['location_type']                      = 'geo' === Functions::location_type() ? 'geo' : 'local';
		return $data;
	}

	public static function gb_listing_category_box_path( $data ) {
		$style = ! empty( $data['style'] ) ? $data['style'] : '1';
		if ( '1' !== $style ) {
			$data['default_template_path'] = rtclPro()->get_plugin_template_path();
		}
		return $data;
	}

	public static function gb_listing_filter_path( $data ) {
		$style = '1';
		$view  = $data['view'];
		if ( 'list' === $view ) {
			$style = ! empty( $data['style'] ) ? $data['style'] : '1';
		}
		if ( 'grid' === $view ) {
			$style = ! empty( $data['style'] ) ? $data['style'] : '1';
		}
		if ( '1' != $style ) {
			$data['template']              = 'block/listing-ads/' . $view . '/style-' . $style;
			$data['default_template_path'] = rtclPro()->get_plugin_template_path();
		}
		return $data;
	}

	public static function gb_location_box_path( $data ) {
		$style = ! empty( $data['style'] ) ? $data['style'] : '1';
		if ( $style == '3' ) {
			$data['template']              = 'block/single-location/style-' . $style;
			$data['default_template_path'] = rtclPro()->get_plugin_template_path();
		}
		return $data;
	}

	public static function app_tab_item( $tabs ) {
		$tabs['app'] = esc_html__( 'App', 'classified-listing-pro' );

		return $tabs;
	}

	// Add App tab options
	public static function app_tab_options( $fields, $active_tab ) {
		if ( 'app' == $active_tab ) {
			$fields = array(
				'pn'           => array(
					'title' => esc_html__( 'App push notifications', 'classified-listing-pro' ),
					'type'  => 'title',
				),
				'app_schema'   => array(
					'title'       => esc_html__( 'App schema', 'classified-listing-pro' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'myapp', 'classified-listing-pro' ),
					'description' => wp_kses(
						__( 'At your react native app folder -> app.json "expo": {"scheme": <b>"myapp"</b>}', 'classified-listing-pro' ),
						array(
							'b' => array(),
						)
					),
				),
				'pn_events'    => array(
					'title'       => esc_html__( 'Allow Events', 'classified-listing-pro' ),
					'type'        => 'multi_checkbox',
					'options'     => PNHelper::getEventList(),
					'description' => esc_html__( 'Allow to handle data to app.', 'classified-listing-pro' ),
				),
				'iap_disabled' => array(
					'title'       => esc_html__( 'Disable In App Purchase', 'classified-listing-pro' ),
					'type'        => 'checkbox',
					'description' => esc_html__( 'Disable in app purchase, This will turn off membership and promotion feature from app.', 'classified-listing-pro' ),
				),
			);

			$fields = apply_filters( 'rtcl_app_settings_options', $fields );
		}

		return $fields;
	}


	/**
	 *
	 * @return mixed
	 */
	public static function is_enable_post_for_unregister() {
		return Functions::get_option_item( 'rtcl_account_settings', 'enable_post_for_unregister', false, 'checkbox' );
	}


	/**
	 * @param $endpoints
	 *
	 * @return mixed
	 */
	public static function my_account_end_point_filter( $endpoints ) {
		if ( Functions::get_option_item( 'rtcl_account_settings', 'user_verification', '', 'checkbox' ) ) {
			$endpoints['verify'] = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount_verify', 'verify' );
		}

		return $endpoints;
	}

	/**
	 * @param $auth
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public static function rtcl_registration_need_auth_new_user( $auth, $user_id ) {
		if ( Functions::get_option_item( 'rtcl_account_settings', 'user_verification', '', 'checkbox' ) && Fns::needs_validation( $user_id ) ) {
			return true;
		}

		return $auth;
	}

	/**
	 * @param array $dependencies
	 *
	 * @return array
	 */
	public static function sl_script_dependency( $dependencies ) {
		array_push( $dependencies, 'photoswipe-ui-default', 'zoom' );

		return $dependencies;
	}

	public static function add_sl_localized_params( $params ) {
		$params['zoom_enabled']       = apply_filters( 'rtcl_single_listing_zoom_enabled', ! Functions::get_option_item( 'rtcl_misc_settings', 'disable_gallery_zoom', false, 'checkbox' ) );
		$params['photoswipe_enabled'] = apply_filters( 'rtcl_single_listing_photoswipe_enabled', ! Functions::get_option_item( 'rtcl_misc_settings', 'disable_gallery_photoswipe', false, 'checkbox' ) );
		$params['photoswipe_options'] = apply_filters(
			'rtcl_single_listing_photoswipe_options',
			array(
				'shareEl'               => false,
				'closeOnScroll'         => false,
				'history'               => false,
				'hideAnimationDuration' => 0,
				'showAnimationDuration' => 0,
			)
		);
		$params['zoom_options']       = apply_filters( 'rtcl_single_listing_zoom_options', array() );

		return $params;
	}

	public static function remove_classified_listing_pro( $addons ) {
		unset( $addons['classified_listing_pro'] );

		return $addons;
	}

	/**
	 * @param array $default_menu_items
	 *
	 * @return array
	 */
	public static function add_chat_menu_item( $default_menu_items ) {
		if ( Fns::is_enable_chat() ) {
			$position   = array_search( 'favourites', array_keys( $default_menu_items ) );
			$newOptions = array( 'chat' => esc_html__( 'Chat', 'classified-listing-pro' ) );

			if ( $position > -1 ) {
				Functions::array_insert( $default_menu_items, $position, $newOptions );
			} else {
				$default_menu_items = array_merge( $default_menu_items, $newOptions );
			}
		}

		return $default_menu_items;
	}


	/**
	 * @param array $endpoints
	 *
	 * @return array
	 */
	public static function add_chat_end_point( $endpoints ) {
		$endpoints['chat'] = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount_chat_endpoint', 'chat' );

		return $endpoints;
	}


	public static function remove_validation_for_name( $validation, $source ) {
		if ( 'api_social_login' === $source ) {
			$validation = false;
		}

		return $validation;
	}

	public static function remove_validation_for_phone( $validation, $source ) {
		if ( 'api_social_login' === $source ) {
			$validation = false;
		}

		return $validation;
	}

	/**
	 * @param array   $class
	 * @param Listing $listing
	 */
	public static function add_label_class( $class, $listing ) {
		if ( $listing->get_meta( '_top' ) ) {
			$class[] = 'is-top';
		}
		if ( $listing->get_meta( '_bump_up' ) ) {
			$class[] = 'is-bump-up';
		}

		if ( Fns::is_popular( $listing ) ) {
			$class[] = 'is-popular';
		}

		if ( Fns::is_enable_top_listings() && Functions::get_loop_prop( 'as_top' ) ) {
			$class[] = 'as-top';
		}

		return $class;
	}

	/**
	 * @param array        $attributes
	 * @param RtclCFGField $field
	 *
	 * @return array
	 */
	public static function cf_attributes_for_field( $attributes, $field ) {
		$rawConditions = is_a( $field, RtclCFGField::class ) ? $field->getConditions() : array();
		if ( ! empty( $rawConditions ) && is_array( $rawConditions ) ) {
			$conditions = array();
			foreach ( $rawConditions as $rawGroup ) {
				$group = array();
				foreach ( $rawGroup as $rule ) {
					$rule['field'] = apply_filters( 'rtcl_wpml_cf_field_id', $rule['field'], $rule, $rawGroup, $field );
					$group[]       = $rule;
				}
				$conditions[] = $group;
			}
			$attributes['data-rt-depends'] = $conditions;
		}

		return $attributes;
	}

	/**
	 * @param array $promotions
	 *
	 * @return array
	 */
	public static function add_promotions( $promotions ) {
		$promotions['_top']     = esc_html__( 'Top', 'classified-listing-pro' );
		$promotions['_bump_up'] = esc_html__( 'Bump Up', 'classified-listing-pro' );

		return $promotions;
	}

	public static function widget_listings_fields( $fields ) {
		$fields['type']['options']        = array(
			'featured_only' => esc_html__( 'Featured only', 'classified-listing-pro' ),
			'top_only'      => esc_html__( 'Top Only', 'classified-listing-pro' ),
			'feature_top'   => esc_html__( 'Featured and Top', 'classified-listing-pro' ),
			'all'           => esc_html__( 'All Type', 'classified-listing-pro' ),
		);
		$fields['view']['options']['map'] = esc_html__( 'Map', 'classified-listing-pro' );

		return $fields;
	}

	public static function rtcl_widget_shortcode_filter_listings_data( $data ) {
		if ( ! empty( $data['instance']['view'] ) && $data['instance']['view'] === 'map' && ! empty( $data['rtcl_query']->posts ) ) {
			wp_enqueue_script( 'rtcl-map' );
			$items = array();
			foreach ( $data['rtcl_query']->posts as $post_id ) {
				$listing = rtcl()->factory->get_listing( $post_id );
				$items[] = Functions::get_map_data( $listing );
			}

			$data['instance']['items']     = $items;
			$data['template']              = 'widgets/listings-map';
			$data['default_template_path'] = rtclPro()->get_plugin_template_path();
		}

		return $data;
	}

	public static function widget_listings_query_params( $params, $instance ) {
		$meta_queries = array();
		switch ( $instance['type'] ) {
			case 'top_only':
				$meta_queries[] = array(
					'key'     => '_top',
					'value'   => 1,
					'compare' => '=',
				);
				break;

			case 'feature_top':
				$meta_queries[] = array(
					'key'     => 'featured',
					'value'   => 1,
					'compare' => '=',
				);
				$meta_queries[] = array(
					'key'     => '_top',
					'value'   => 1,
					'compare' => '=',
				);
				break;

			default:
				break;
		}
		if ( ! empty( $meta_queries ) ) {
			if ( is_array( $params['meta_query'] ) && ! empty( $params['meta_query'] ) ) {
				if ( count( $params['meta_query'] ) < 2 ) {
					$params['meta_query'] = array_merge( $params['meta_query'], $meta_queries, array( 'relation' => 'AND' ) );
				} else {
					$params['meta_query'] = array_merge( $params['meta_query'], $meta_queries );
				}
			} else {
				$params['meta_query'] = $meta_queries;
			}
		}

		if ( $instance['view'] === 'map' ) {
			$params['fields'] = 'ids';
		}

		return $params;
	}

	public static function widget_filter_fields( $fields ) {
		$new_fields = array(
			'show_icon_image_for_category' => array(
				'label' => esc_html__( 'Show category image / icon', 'classified-listing-pro' ),
				'type'  => 'checkbox',
			),
			'search_by_rating'             => array(
				'label' => esc_html__( 'Search by Rating', 'classified-listing-pro' ),
				'type'  => 'checkbox',
			),
			'search_by_custom_fields'      => array(
				'label' => esc_html__( 'Search by Custom Fields', 'classified-listing-pro' ),
				'type'  => 'checkbox',
			),
		);
		$target_key = 'search_by_listing_types';

		$position = array_search( $target_key, array_keys( $fields ) );
		if ( $position > -1 ) {
			Functions::array_insert( $fields, $position, $new_fields );
		} else {
			$fields = array_merge( $fields, $new_fields );
		}

		return $fields;
	}

	public static function rtcl_widget_filter_values( $data, $args, $instance ) {
		$custom_field_filter = '';
		if ( ! empty( $instance['search_by_custom_fields'] ) ) {
			$filterTypes  = array(
				'text',
				'textarea',
				'number',
				'checkbox',
				'select',
				'radio',
				'date',
			);
			$current_term = get_queried_object();
			if ( is_a( $current_term, WP_Term::class ) && rtcl()->category === $current_term->taxonomy ) {
				$filters = ! empty( $_GET['filters'] ) ? $_GET['filters'] : array();
				$c_ids   = Functions::get_custom_field_ids( $current_term->term_id );
				if ( ! empty( $c_ids ) ) {
					$i = 1;
					foreach ( $c_ids as $c_id ) {
						$field = rtcl()->factory->get_custom_field( $c_id );
						if ( ! $field ) {
							continue;
						}
						if ( ! empty( $conditions = $field->getConditions() ) && is_array( $conditions ) ) {
							$isCValid = true;
							foreach ( $conditions as $group ) {
								$isGValid = true;
								foreach ( $group as $rule ) {
									$isValid    = true;
									$r_field_id = ! empty( $rule['field'] ) ? absint( $rule['field'] ) : 0;
									$operator   = ! empty( $rule['operator'] ) ? $rule['operator'] : '';
									if ( ! $r_field_id || ! $operator ) {
										continue;
									}
									$r_field_id = apply_filters( 'rtcl_wpml_cf_field_id', $r_field_id, $rule, $group, $field );
									$r_value    = ! empty( $rule['value'] ) ? $rule['value'] : '';
									$d_value    = ! empty( $filters[ '_field_' . $r_field_id ] ) ? $filters[ '_field_' . $r_field_id ] : '';
									if ( $operator === '==empty' ) { // hasNoValue
										$isValid = empty( $d_value );
									} elseif ( $operator === '!=empty' ) { // hasValue  -- ANY value
										$isValid = ! empty( $d_value );
									} elseif ( $operator === '==' ) { // equalTo
										if ( is_array( $d_value ) ) {
											$isValid = in_array( $r_value, $d_value );
										} else {
											$isValid = strtolower( $d_value ) == strtolower( $r_value );
										}
									} elseif ( $operator === '!=' ) { // notEqualTo
										if ( is_array( $d_value ) ) {
											$isValid = ! in_array( $r_value, $d_value );
										} else {
											$isValid = strtolower( $d_value ) !== strtolower( $r_value );
										}
									} elseif ( $operator === '==pattern' && ! empty( $r_value ) ) { // patternMatch
										if ( is_array( $d_value ) ) {
											$isPatternValid = false;
											foreach ( $d_value as $_ ) {
												preg_match( "/$r_value/", $_, $matches );
												if ( ! empty( $matches ) ) {
													$isPatternValid = true;
													break;
												}
											}
											$isValid = $isPatternValid;
										} else {
											preg_match( "/$r_value/", $d_value, $matches );
											$isValid = ! empty( $matches );
										}
									} elseif ( $operator === '==contains' ) { // contains
										if ( is_array( $d_value ) ) {
											$isContainsValid = false;
											foreach ( $d_value as $_ ) {
												if ( strpos( (string) $r_value, (string) $_ ) !== false ) {
													$isContainsValid = true;
													break;
												}
											}
											$isValid = $isContainsValid;
										} else {
											$isValid = strpos( (string) $r_value, (string) $d_value ) !== false;
										}
									}
									if ( ! $isValid ) {
										$isGValid = false;
										break;
									}
								}
								if ( $isGValid ) {
									$isCValid = true;
									break;
								} else {
									$isCValid = false;
								}
							}
							if ( ! $isCValid ) {
								continue;
							}
						}
						if ( in_array( $field->getType(), $filterTypes ) && $field->isSearchable() ) {
							$field_html = $isOpen = null;
							$metaKey    = $field->getMetaKey();
							if ( $field->getType() == 'number' ) {
								$fMinValue   = ! empty( $filters[ $metaKey ]['min'] ) ? esc_attr( $filters[ $metaKey ]['min'] ) : null;
								$fMaxValue   = ! empty( $filters[ $metaKey ]['max'] ) ? esc_attr( $filters[ $metaKey ]['max'] ) : null;
								$isOpen      = $fMinValue || $fMaxValue ? ' is-open' : null;
								$field_html .= sprintf(
									'<div class="form-group row">
                                                                    <div class="col-md-6">
                                                                        <div class="ui-field">
                                                                            <input id="filters[%1$s][min]" name="filters[%1$s][min]" type="number" value="%2$s" class="ui-input form-control" placeholder="%3$s">									
                                                                        </div>											
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="ui-field">
                                                                            <input id="filters[%1$s][max]" name="filters[%1$s][max]" type="number" value="%4$s" class="ui-input form-control" placeholder="%5$s">
                                                                        </div>
                                                                    </div>
                                                                </div>',
									$metaKey,
									$fMinValue,
									esc_html__( 'Min.', 'classified-listing-pro' ),
									$fMaxValue,
									esc_html__( 'Max.', 'classified-listing-pro' )
								);
							} elseif ( $field->getType() == 'date' ) {
								$value       = ! empty( $filters[ $metaKey ] ) ? esc_attr( $filters[ $metaKey ] ) : null;
								$isOpen      = $value ? ' is-open' : null;
								$field_html .= sprintf(
									'<div class="form-group">
																<div class="ui-field">
																	<input id="filters[%1$s]" autocomplete="false" name="filters[%1$s]" type="text" value="%2$s" data-options="%4$s" class="ui-input form-control rtcl-date" placeholder="%3$s">									
																</div>	
														</div>',
									esc_attr( $metaKey ),
									esc_attr( $value ),
									esc_html__( 'Date', 'classified-listing-pro' ),
									htmlspecialchars(
										wp_json_encode(
											$field->getDateFieldOptions(
												array(
													'singleDatePicker' => $field->getDateSearchableType() === 'single',
													'autoUpdateInput'  => false,
												)
											)
										)
									)
								);
							} elseif ( in_array( $field->getType(), array( 'text', 'textarea' ), true ) ) {
								$values      = ! empty( $filters[ $metaKey ] ) ? esc_attr( $filters[ $metaKey ] ) : null;
								$isOpen      = $values ? ' is-open' : null;								
								// Suppor translatepress .
								$placeholder_text = sprintf( esc_html__( 'Search by %s', 'classified-listing-pro' ), $field->getLabel() );
								$field_html .= sprintf(
									'<div class="form-group">
                                                                    <input id="filters%1$s" name="filters[%1$s]" type="text" value="%2$s" class="ui-input form-control" placeholder="%3$s">
                                                                </div>',
									$metaKey,
									$values,
									apply_filters( 'rtcl_filter_custom_text_field_placeholder', $placeholder_text , $field )
								);
							} else {
								$values  = ! empty( $filters[ $metaKey ] ) ? $filters[ $metaKey ] : array();
								$isOpen  = count( $values ) ? ' is-open' : null;
								$options = $field->getOptions();
								if ( ! empty( $options['choices'] ) ) {
									$field_html .= "<ul class='ui-link-tree is-collapsed'>";
									foreach ( $options['choices'] as $key => $option ) {
										$checked     = in_array( $key, $values ) ? ' checked ' : '';
										$field_html .= "<li class='ui-link-tree-item {$field->getMetaKey()}-{$key}'>";
										$field_html .= "<input id='filters{$metaKey}-values-{$key}' name='filters[{$metaKey}][]' {$checked} value='{$key}' type='checkbox' class='ui-checkbox filter-submit-trigger'>";
										// Comment By Rashid. Support translatepress.
										// $field_html .= "<a href='#' class='filter-submit-trigger'>" . esc_html__(
										// 	$option,
										// 	'classified-listing-pro'
										// ) . '</a>';
										$field_html .= "<a href='#' class='filter-submit-trigger'>" . esc_html( $option ) . '</a>';
										$field_html .= '</li>';
									}
									$field_html .= '<li class="is-opener"><span class="rtcl-more"><i class="rtcl-icon rtcl-icon-plus-circled"></i><span class="text">' . esc_html__(
										'Show More',
										'classified-listing-pro'
									) . '</span></span></li>';
									$field_html .= '</ul>';
								}
							}

							$custom_field_filter .= apply_filters(
								'rtcl_widget_filter_custom_field_html',
								sprintf(
									'<div class="rtcl-custom-field-filter rtcl-custom-field-filter-%s ui-accordion-item %s">
									                <a class="ui-accordion-title">
									                    <span>%s</span>
									                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
									                </a>
									                <div class="ui-accordion-content">%s</div>
									            </div>',
									$field->getType(),
									$isOpen,
									$field->getLabel(),
									$field_html
								),
								$field,
								$c_id,
								$filters
							);
						}

						$i++;
					}
				}
			}
		}
		$data['custom_field_filter'] = $custom_field_filter;

		if ( ! empty( $instance['search_by_rating'] ) && Functions::get_option_item( 'rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox' ) ) {
			$filters = ! empty( $_GET['filters'] ) ? $_GET['filters'] : array();
			$rating  = ! empty( $filters['rating'] ) ? $filters['rating'] : '';

			$rating_html = apply_filters(
				'rtcl_filter_widget_rating_list',
				array(
					'5.0' => esc_html__( '5.0', 'classified-listing-pro' ),
					'4.5' => esc_html__( '4.5 & up', 'classified-listing-pro' ),
					'4.0' => esc_html__( '4.0 & up', 'classified-listing-pro' ),
					'3.5' => esc_html__( '3.5 & up', 'classified-listing-pro' ),
					'3.0' => esc_html__( '3.0 & up', 'classified-listing-pro' ),
				)
			);

			$rating_filter = '<ul class="ui-link-tree is-collapsed">';

			foreach ( $rating_html as $key => $text ) {
				$key            = (float) $key;
				$width          = ( $key / 5 ) * 100;
				$rating_filter .= '<li class="ui-link-tree-item' . esc_attr( $rating == $key ? ' selected' : '' ) . '" data-id="' . esc_attr( $key ) . '">
									<div class="star-rating"><span style="width:' . esc_attr( $width ) . '%"></span></div>
									<span class="rating-filter-label">' . esc_html( $text ) . '</span>
								</li>';
			}

			$rating_filter .= '</ul>';

			$rating_filter .= '<input type="hidden" name="filters[rating]" value="' . esc_attr( $rating ) . '"/>';

			$data['rating_filter'] = sprintf(
				'<div class="rtcl-rating-filter ui-accordion-item is-open">
								<a class="ui-accordion-title">
                                    <span>%s</span>
                                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
                                </a>
                                <div class="ui-accordion-content">%s</div>
                            </div>',
				apply_filters( 'rtcl_widget_filter_rating_title', esc_html__( 'Ratings', 'classified-listing-pro' ) ),
				$rating_filter
			);
		}

		return $data;
	}

	public static function widget_filter_update_values( $instance, $new_instance ) {
		$instance['show_icon_image_for_category'] = ! empty( $new_instance['show_icon_image_for_category'] ) ? 1 : 0;
		$instance['search_by_custom_fields']      = ! empty( $new_instance['search_by_custom_fields'] ) ? 1 : 0;
		$instance['search_by_rating']             = ! empty( $new_instance['search_by_rating'] ) ? 1 : 0;

		return $instance;
	}

	public static function widget_filter_default_values( $defaults ) {
		$defaults['show_icon_image_for_category'] = 1;
		$defaults['search_by_custom_fields']      = 1;
		$defaults['search_by_rating']             = 0;

		return $defaults;
	}

	public static function widget_filter_rating_query( $meta_query ) {
		$filters = ! empty( $_GET['filters'] ) ? $_GET['filters'] : array();
		if ( ! empty( $filters['rating'] ) ) {
			$rating       = (float) $filters['rating'];
			$meta_query[] = array(
				'key'     => '_rtcl_average_rating',
				'value'   => $rating,
				'compare' => '>=',
			);
		}
		return $meta_query;
	}

	public static function widget_search_fields( $fields ) {
		$fields['style']['options'] = Options::widget_search_style_options();
		$fields['style']['type']    = 'select';
		$new_fields                 = array(
			'orientation' => array(
				'label'   => esc_html__( 'Orientation', 'classified-listing-pro' ),
				'type'    => 'radio',
				'options' => array(
					'vertical' => esc_html__( 'Vertical', 'classified-listing-pro' ),
					'inline'   => esc_html__( 'inline', 'classified-listing-pro' ),
				),
			),
		);
		$position                   = array_search( 'style', array_keys( $fields ) );
		if ( $position > -1 ) {
			Functions::array_insert( $fields, $position, $new_fields );
		} else {
			$fields = array_merge( $fields, $new_fields );
		}

		return $fields;
	}

	public static function rtcl_widget_search_values( $data, $args, $instance ) {
		$data['style']       = isset( $instance['style'] ) && array_key_exists( $instance['style'], Options::widget_search_style_options() ) ? $instance['style'] : 'suggestion';
		$data['orientation'] = isset( $instance['orientation'] ) && ! empty( $instance['orientation'] ) ? $instance['orientation'] : 'inline';
		if ( get_query_var( 'rtcl_location' ) && $location = get_term_by( 'slug', get_query_var( 'rtcl_location' ), rtcl()->location ) ) {
			$data['selected_location'] = $location;
		}

		if ( get_query_var( 'rtcl_category' ) && $location = get_term_by( 'slug', get_query_var( 'rtcl_category' ), rtcl()->category ) ) {
			$data['selected_category'] = $location;
		}

		$data['active_count'] = $data['can_search_by_category'] + $data['can_search_by_location'] + $data['can_search_by_listing_types'] + $data['can_search_by_price'];

		$data['classes']               = array(
			'rtcl',
			'rtcl-widget-search',
			'rtcl-widget-search-' . $data['orientation'],
			'rtcl-widget-search-style-' . $data['style'],
		);
		$data['instance']              = $instance;
		$data['args']                  = $args;
		$data['data']                  = $data;
		$data['template']              = 'widgets/search';
		$data['default_template_path'] = rtclPro()->get_plugin_template_path();

		return $data;
	}

	public static function widget_search_update_values( $instance, $new_instance ) {
		$instance['style']       = ! empty( $new_instance['style'] ) && array_key_exists( $new_instance['style'], Options::widget_search_style_options() ) ? strip_tags( $new_instance['style'] ) : 'suggestion';
		$instance['orientation'] = isset( $new_instance['orientation'] ) && ! empty( $new_instance['orientation'] ) ? strip_tags( $new_instance['orientation'] ) : 'inline';

		return $instance;
	}

	public static function widget_search_default_values( $default_values ) {
		$default_values['style']       = 'popup';
		$default_values['orientation'] = 'inline';

		return $default_values;
	}

	public static function add_public_script_localize_params( $localize ) {
		$moderation_settings               = Functions::get_option( 'rtcl_moderation_settings' );
		$localize['has_map']               = ! empty( $moderation_settings['has_map'] ) && $moderation_settings['has_map'] == 'yes';
		$localize['online_status_seconds'] = (int) apply_filters( 'rtcl_user_online_status_seconds', 900 );

		return $localize;
	}

	public static function add_view_class_at_loop( $class ) {
		if ( isset( $_GET['view'] ) && in_array( $_GET['view'], array( 'grid', 'list' ), true ) ) {
			$view = esc_attr( $_GET['view'] );
		} else {
			$view = Functions::get_option_item( 'rtcl_general_settings', 'default_view', 'list' );
		}

		return 'grid' === $view ? 'rtcl-grid-view' : 'rtcl-list-view';
	}

	public static function add_view_at_shortcode_listings_attributes( $atts ) {
		$atts['view'] = Functions::get_option_item( 'rtcl_general_settings', 'default_view', 'list' );

		return $atts;
	}

	public static function add_email_services( $emailServices ) {
		$emailServices['Unread_Message_Email']           = new UnreadMessageEmail();
		$emailServices['User_Verify_Link_Email_To_User'] = new UserVerifyLinkEmailToUser();

		return $emailServices;
	}


	/**
	 * @param array   $classes
	 * @param Listing $listing
	 *
	 * @return array
	 */
	public static function mark_as_sold_class( $classes, $listing ) {
		if ( Fns::is_enable_mark_as_sold() && Fns::is_mark_as_sold( $listing->get_id() ) ) {
			if ( is_array( $classes ) ) {
				array_push( $classes, 'is-sold' );
			}
		}

		return $classes;
	}

	public static function add_pro_payment_gateways( $gateways ) {
		$gateways[] = GatewayAuthorize::class;
		$gateways[] = GatewayStripe::class;

		if ( Functions::is_wc_activated() ) {
			$gateways[] = GatewayWooPayment::class;
		}

		return $gateways;
	}
}
