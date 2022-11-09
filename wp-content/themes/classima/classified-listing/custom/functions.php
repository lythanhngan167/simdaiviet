<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.7
 */

namespace radiustheme\Classima;

use Rtcl\Controllers\Ajax\PublicUser;
use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Pagination;
use Rtcl\Controllers\Hooks\TemplateHooks as FreeTemplateHooks;
use RtclStore\Controllers\Hooks\TemplateHooks;

class Listing_Functions {

	protected static $instance = null;

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_support' ) );
		add_filter( 'get_the_archive_title', array( $this, 'archive_title' ) );
		add_action( 'widgets_init', array( $this, 'unregister_sidebars' ) );

		add_filter( 'template_include', [ $this, 'template_include' ] ); // override page template
		add_action( 'save_post', [ $this, 'listing_form_save' ], 12, 2 ); // save extra listing form fields
		add_filter( 'rtcl_default_placeholder_url', [ $this, 'placeholder_img_url' ] ); // change placeholder image
		add_action( 'classima_listing_list_view_after_content', [
			$this,
			'my_account_listing_contents'
		] ); // my account contents
		add_action( 'classima_listing_list_view_after_content', [
			$this,
			'fav_listing_delete_btn'
		] ); // delete from fav button
		add_action( 'template_redirect', [
			$this,
			'store_enable_pagination'
		], 0 ); // store enable pagination by force
		// Store Filter
		add_filter( 'rtcl_stores_grid_columns_class', [ $this, 'classima_rtcl_stores_grid_columns_class' ] );

		add_filter( 'classima_single_listing_time_format', [
			$this,
			'classima_change_listing_time_format'
		], 20, 1 );
		add_filter( 'classima_listing_grid_col_class', [ $this, 'classima_listing_archive_grid' ], 10, 2 );

		add_filter( 'rtcl_store_time_options', [ $this, 'classima_rtcl_store_time_options_rt_cb' ] );
		add_filter( 'rtcl_add_price_type_at_price', '__return_empty_string' ); // Remove price type from single listing

		// Override plugin options
		add_filter( 'rtcl_general_settings', [ $this, 'override_general_settings' ] );
		add_filter( 'rtcl_style_settings', [ $this, 'override_style_settings' ] );
		add_filter( 'rtcl_moderation_settings_options', [ $this, 'form_fields_options' ] );
		add_filter( 'rtcl_general_settings_options', [ $this, 'listing_orderby_random_option' ] );

		// Filter Widget
		add_filter( 'rtcl_widget_filter_fields', [ $this, 'filter_widget_style_field' ] );
		add_filter( 'rtcl_widget_filter_default_values', [ $this, 'widget_filter_default_values' ], 10, 3 );
		add_filter( 'rtcl_widget_filter_update_values', [ $this, 'widget_filter_update_values' ], 10, 2 );
		//add_action( "rtcl_widget_filter_form", [ $this, 'widget_filter_form_category_item' ], 20 );

		// Remove Store Archive Action
		remove_action( 'rtcl_store_loop_item', [ TemplateHooks::class, 'loop_item_content_start' ], 5 );
		remove_action( 'rtcl_store_loop_item', [ TemplateHooks::class, 'loop_item_content_end' ], 100 );

		// Profile page
		remove_action( 'rtcl_edit_account_form', [
			FreeTemplateHooks::class,
			'edit_account_form_location_field'
		], 50 );
		remove_action( 'rtcl_edit_account_form_end', [
			FreeTemplateHooks::class,
			'edit_account_form_submit_button'
		], 10 );
		if ( method_exists( 'Rtcl\Helpers\Functions', 'has_map' ) && Functions::has_map() ) {
			if ( 'geo' === Functions::location_type() ) {
				remove_action( 'rtcl_edit_account_form', [
					FreeTemplateHooks::class,
					'edit_account_form_geo_location'
				], 50 );
			}
			remove_action( 'rtcl_edit_account_form', [ FreeTemplateHooks::class, 'edit_account_map_field' ], 60 );
		}
		// Modify Quick View Action
		add_action( 'rtcl_quick_view_summary', [ $this, 'listing_excerpt' ], 60 );

		add_action( 'init', function () {
			$layout = isset( RDTheme::$options['listing_grid_style'] ) ? RDTheme::$options['listing_grid_style'] : '';
			if ( '8' == $layout || '9' == $layout ) {
				remove_action( 'rtcl_listing_badges', [ FreeTemplateHooks::class, 'listing_featured_badge' ], 20 );
			}
			// user listing load more
			remove_action( 'wp_ajax_rtcl_user_ad_load_more', [ PublicUser::class, 'rtcl_user_ad_load_more' ] );
			remove_action( 'wp_ajax_nopriv_rtcl_user_ad_load_more', [ $this, 'user_ad_load_more' ] );
			add_action( 'wp_ajax_rtcl_user_ad_load_more', [ $this, 'user_ad_load_more' ] );
			add_action( 'wp_ajax_nopriv_rtcl_user_ad_load_more', [ $this, 'user_ad_load_more' ] );
		} );

		add_action( 'wp_ajax_nopriv_listing_load_more_ad', [ $this, 'listing_load_more_ad' ] );
		add_action( 'wp_ajax_listing_load_more_ad', [ $this, 'listing_load_more_ad' ] );

	}

	function user_ad_load_more() {
		$complete       = false;
		$html           = '';
		$current_page   = isset( $_POST['current_page'] ) ? absint( $_POST['current_page'] ) : 0;
		$max_num_pages  = isset( $_POST['max_num_pages'] ) ? absint( $_POST['max_num_pages'] ) : 0;
		$user_id        = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
		$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : - 1;

		if ( $current_page && $max_num_pages && $user_id && $max_num_pages > $current_page ) {
			$current_page ++;
			$complete       = true;
			$args           = [
				'post_type'      => rtcl()->post_type,
				'post_status'    => 'publish',
				'posts_per_page' => $posts_per_page ? $posts_per_page : - 1,
				'author'         => $user_id,
				'paged'          => $current_page,
				'meta_query'     => [
					[
						'key'     => '_rtcl_manager_id',
						'compare' => 'NOT EXISTS'
					]
				]
			];
			$user_ads_query = new \WP_Query( $args );

			if ( ! empty( $user_ads_query->posts ) ) {
				ob_start();
				Listing_Functions::listing_query( 'list', $user_ads_query );
				$html .= ob_get_clean();

			}
		} else {
			$current_page = $max_num_pages;
		}

		wp_send_json( [
			'complete'     => $complete,
			'current_page' => $current_page,
			'html'         => $html
		] );
	}

	function filter_widget_style_field( $fields ) {
		$position = array_search( 'title', array_keys( $fields ) );
		if ( $position > - 1 ) {
			$field['filter_style'] = [
				'label'   => esc_html__( 'Style', 'classima' ),
				'type'    => 'select',
				'options' => [
					'style1' => esc_html__( 'Style 1', 'classified-listing' ),
					'style2' => esc_html__( 'Style 2', 'classified-listing' )
				]
			];
			Functions::array_insert( $fields, $position, $field );
        }

		return $fields;
	}

	function widget_filter_default_values( $defaults, $instance, $obj ) {
		$defaults['filter_style'] = 'style1';

		return $defaults;
	}

	function widget_filter_update_values( $instance, $new_instance ) {
		$instance['filter_style'] = ! empty( $new_instance['filter_style'] ) ? $new_instance['filter_style'] : 'style1';

		return $instance;
	}

	public function widget_filter_form_category_item( $object ) {

		if ( ! empty( $object->get_instance()['search_by_category'] ) ) {
			$args = [
				'taxonomy' => rtcl()->category,
				'parent'   => 0,
				'instance' => $object->get_instance()
			];

			printf( '<div class="rtcl-category-filter ui-accordion-item is-open">
					                <a class="ui-accordion-title">
					                    <span>%s</span>
					                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
					                </a>
					                <div class="ui-accordion-content%s"%s>%s</div>
					            </div>',
				apply_filters( 'rtcl_widget_filter_category_title', esc_html__( "Category", "classified-listing" ) ),
				! empty( $args['instance']['ajax_load'] ) ? ' rtcl-ajax-load' : '',
				! empty( $args['instance']['ajax_load'] ) ? sprintf( ' data-settings="%s"', htmlspecialchars( wp_json_encode( $args ) ) ) : '',
				empty( $args['instance']['ajax_load'] ) ? Functions::get_sub_terms_filter_html( $args ) : ""
			);
		}

	}

	function listing_load_more_ad() {

		$layout        = $_POST["layout"];
		$offset        = $_POST["offset"];
		$display       = $_POST["display"];
		$data          = $_POST["queryArg"];
		$col_class     = $_POST["col_class"];
		$post_per_page = $_POST["post_per_page"];

		if ( $data['type'] != 'custom' ) {
			$settings      = get_option( 'rtcl_moderation_settings' );
			$min_view      = ! empty( $settings['popular_listing_threshold'] ) ? (int) $settings['popular_listing_threshold'] : 500;
			$new_threshold = ! empty( $settings['new_listing_threshold'] ) ? (int) $settings['new_listing_threshold'] : 3;

			$args = [
				'post_type'           => 'rtcl_listing',
				'status'              => 'publish',
				'posts_per_page'      => $post_per_page,
				'offset'              => $offset,
				'ignore_sticky_posts' => true,
			];

			// Ordering
			if ( $data['random'] ) {
				$args['orderby'] = 'rand';
			} else {
				$args['orderby'] = $data['orderby'];
				$args['order']   = $data['order'];
			}

			// Taxonomy
			if ( ! empty( $data['cat'] ) ) {
				$args['tax_query'] = [
					[
						'taxonomy' => 'rtcl_category',
						'field'    => 'term_id',
						'terms'    => $data['cat'],
					]
				];
			}

			// Date and Meta Query
			switch ( $data['type'] ) {
				case 'new':
					$args['date_query'] = [
						[
							'after' => $new_threshold . ' day ago',
						],
					];
					break;

				case 'featured':
					$args['meta_key']   = 'featured';
					$args['meta_value'] = '1';
					break;

				case 'top':
					$args['meta_key']   = '_top';
					$args['meta_value'] = '1';
					break;

				case 'popular':
					$args['meta_key']     = '_views';
					$args['meta_value']   = $min_view;
					$args['meta_compare'] = '>=';
					break;
			}
		} else {

			$posts = array_map( 'trim', explode( ',', $data['ids'] ) );

			$args = [
				'post_type'           => 'rtcl_listing',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'post__in'            => $posts,
				'orderby'             => 'post__in',
			];
		}

		$post = new \WP_Query( $args );

		while ( $post->have_posts() ) {
			$post->the_post();
			?>
            <div class="<?php echo esc_attr( $col_class ); ?>">
				<?php Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) ); ?>
            </div>
			<?php
		}
		wp_reset_postdata();
		die(); // use die instead of exit
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function theme_support() {
		add_theme_support( 'rtcl' );
	}

	public function listing_excerpt( $listing ) {
		if ( ! $listing ) {
			return;
		}
		$listing->the_excerpt();
	}

	public function unregister_sidebars() {
		unregister_sidebar( 'rtcl-archive-sidebar' );
		unregister_sidebar( 'rtcl-single-sidebar' );
    }

	public function archive_title( $title ) {
		if ( is_post_type_archive( 'rtcl_listing' ) || is_tax( 'rtcl_category' ) || is_tax( 'rtcl_location' ) ) {
			if ( is_tax( 'rtcl_category' ) || is_tax( 'rtcl_location' ) ) {
				$title = single_cat_title( '', false );
			} else {
				$id    = Functions::get_page_id( 'listings' );
				$title = get_the_title( $id );
			}
		}

		return $title;
	}

	public function classima_rtcl_stores_grid_columns_class() {
		return 'columns-6';
	}

	public function placeholder_img_url() {
		return Helper::get_img( 'noimage-listing-thumb.jpg' );
	}

	public function override_general_settings( $settings ) {
		$settings['load_bootstrap'] = '';

		return $settings;
	}

	public function change_favourtie_text( $text ) {
		if ( ! Functions::is_listing() ) {
			return '';
		}

		return $text;
	}

	public function override_style_settings( $settings ) {
		$primary_color   = Helper::get_primary_color(); // #1aa78e
		$secondary_color = Helper::get_secondary_color(); // #fcaf01

		$args = [
			'primary'           => $primary_color,
			'link'              => $primary_color,
			'link_hover'        => $secondary_color,
			'button'            => $primary_color,
			'button_hover'      => $secondary_color,
			'button_text'       => '#ffffff',
			'button_hover_text' => '#ffffff',
		];

		$settings = wp_parse_args( $args, $settings );

		return $settings;
	}

	public function classima_rtcl_store_time_options_rt_cb( $data ) {

		$format = isset( RDTheme::$options['time_format'] ) ? RDTheme::$options['time_format'] : true;

		if ( $format == false ) {
			$data['showMeridian'] = false;
		}

		return $data;
	}

	public function listing_orderby_random_option( $options ) {
		$options['orderby']['options']['rand'] = __( 'Random', 'classima' );

		return $options;
	}

	public function hide_favourite_text( $text ) {
		if ( RDTheme::$options['single_listing_style'] == '4' ) {
			return false;
		}

		return $text;
	}

	public function classima_listing_archive_grid( $col_class, $map ) {

		$col_class = isset( RDTheme::$options['grid_desktop_column'] ) ? 'col-xl-' . RDTheme::$options['grid_desktop_column'] : 'col-xxl-4 col-xl-4';
		$col_class .= isset( RDTheme::$options['grid_tablet_column'] ) ? ' col-md-' . RDTheme::$options['grid_tablet_column'] . ' col-sm-' . RDTheme::$options['grid_tablet_column'] : ' col-md-6 col-sm-6';
		$col_class .= isset( RDTheme::$options['grid_mobile_column'] ) ? ' col-' . RDTheme::$options['grid_mobile_column'] : ' col-12';

		return $col_class;
	}

	public function classima_change_listing_time_format( $string ) {

		$time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		if ( empty( $time_format ) ) {
			$time_format = $string;
		}

		return $time_format;
	}

	public function form_fields_options( $options ) {
		$options['hide_form_fields']['options']['features'] = esc_html__( 'Features', 'classima' );

		return $options;
	}

	private function is_listings_map_page() {
		global $post;
		$pattern = '/\[rtcl_listings\s+map=1(\s+)?\]/'; // catches [rtcl_listings map=1]
		$result  = preg_match( $pattern, $post->post_content );

		return $result;
	}

	public function template_include( $template ) {
		if ( class_exists( 'RtclPro' ) && class_exists( 'Rtcl' ) ) {
			if ( Functions::is_account_page() ) {
				$new_template = Helper::get_custom_listing_template( 'listing-account', false );
				$new_template = locate_template( [ $new_template ] );

				return $new_template;
			}
		}

		return $template;
	}

	public static function can_show_ad_type() {
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_type = Functions::get_option_item( 'rtcl_moderation_settings', $display_option, 'ad_type', 'multi_checkbox' );

		return apply_filters( 'rtcl_listing_can_show_ad_type', $can_show_type );
	}

	public function my_account_listing_contents( $listing ) {
		if ( Functions::is_account_page( 'listings' ) ) {
			Helper::get_custom_listing_template( 'myaccount-contents', true, compact( 'listing' ) );
		}
	}

	public function fav_listing_delete_btn( $listing ) {
		if ( ! Functions::is_account_page( 'favourites' ) ) {
			return;
		}
		?>
        <div class="rtin-action-btn">
            <a href="#" class="btn rtcl-delete-favourite-listing"
               data-id="<?php echo esc_attr( $listing->get_id() ); ?>"><?php esc_html_e( 'Remove from Favourites', 'classima' ) ?></a>
        </div>
		<?php
	}

	public function listing_form_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! Functions::current_user_can( 'edit_' . rtcl()->post_type, $post_id ) ) {
			return;
		}

		if ( ! Functions::verify_nonce() ) {
			return;
		}

		if ( isset( $_POST['classima_spec_info'] ) ) {
			update_post_meta( $post_id, 'classima_spec_info', stripslashes_deep( $_POST['classima_spec_info'] ) );
		}
	}

	public function store_enable_pagination() {
		if ( is_singular( 'store' ) ) {
			remove_action( 'template_redirect', 'redirect_canonical' );
		}
	}

	public static function listing_count_text( $post_num ) {
		if ( $post_num ) {
			if ( $post_num['total'] == 1 ) {
				$post_num_text = esc_html__( 'Showing 1 result', 'classima' );
			} else {
				$post_num_text = sprintf( esc_html__( 'Showing %1$d–%2$d of %3$d results', 'classima' ), $post_num['first'], $post_num['last'], $post_num['total'] );
			}
		} else {
			$post_num_text = esc_html__( 'Showing 0 result', 'classima' );
		}

		return $post_num_text;
	}

	public static function listing_post_num( $rtcl_query ) {

		$total   = $rtcl_query->found_posts;
		$current = $rtcl_query->post_count;

		if ( $current ) {
			$posts_per_page       = $rtcl_query->query_vars['posts_per_page'];
			$paged                = ! empty( $rtcl_query->query['paged'] ) ? $rtcl_query->query['paged'] : 1;
			$num_of_skipped_items = $posts_per_page * ( $paged - 1 );

			$first = $num_of_skipped_items + 1;
			$last  = $num_of_skipped_items + $current;

			$result = [
				'first' => $first,
				'last'  => $last,
				'total' => $total,
			];
		} else {
			$result = false;
		}

		return $result;
	}

	public static function listing_query( $view, $rtcl_query, $rtcl_top_query = false, $map = false ) {
		if ( $view == 'grid' ) { ?>
            <div class="row auto-clear">
				<?php
				$col_class = $map ? 'col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12' : 'col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12';
				$col_class = apply_filters( 'classima_listing_grid_col_class', $col_class, $map );

				if ( Fns::is_enable_top_listings() ) {
					if ( is_object( $rtcl_top_query ) && $rtcl_top_query->have_posts() ) {
						$top_listing = true;
						while ( $rtcl_top_query->have_posts() ): $rtcl_top_query->the_post(); ?>
                            <div class="<?php echo esc_attr( $col_class ); ?>">
								<?php Functions::get_template( 'custom/grid', compact( 'top_listing', 'map' ) ); ?>
                            </div>
						<?php
						endwhile;
						wp_reset_postdata();
					}
				}

				while ( $rtcl_query->have_posts() ): $rtcl_query->the_post(); ?>
                    <div class="<?php echo esc_attr( $col_class ); ?>">
						<?php Functions::get_template( 'custom/grid', compact( 'map' ) ); ?>
                    </div>
				<?php
				endwhile;
				wp_reset_postdata(); ?>
            </div>
			<?php
		} else {
			$layout  = null;
			$display = [];
			if ( $map ) {
				$layout  = 'map';
				$display = [
					'excerpt' => false,
				];
			}
			if ( Fns::is_enable_top_listings() ) {
				if ( is_object( $rtcl_top_query ) && $rtcl_top_query->have_posts() ) {
					$top_listing = true;
					while ( $rtcl_top_query->have_posts() ) : $rtcl_top_query->the_post();
						Functions::get_template( 'custom/list', compact( 'top_listing', 'map', 'layout', 'display' ) );
					endwhile;
					wp_reset_postdata();
				}
			}

			while ( $rtcl_query->have_posts() ) : $rtcl_query->the_post();
				Functions::get_template( 'custom/list', compact( 'map', 'layout', 'display' ) );
			endwhile;
			wp_reset_postdata();
		}
	}

	public static function get_single_contact_address( $listing ) {

		$listing_id        = $listing->get_id();
		$listing_locations = $listing->get_locations();

		$render = $loc = '';

		$location_type = Functions::location_type();
		if ( 'local' === $location_type ) {
			$address = get_post_meta( $listing_id, 'address', true );
			$address = $address && Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'address', 'multi_checkbox' ) ? $address : '';

			$zipcode = get_post_meta( $listing_id, 'zipcode', true );
			$zipcode = $zipcode && Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'zipcode', 'multi_checkbox' ) ? $zipcode : '';

			$locations = [];
			if ( count( $listing_locations ) ) {
				foreach ( $listing_locations as $location ) {
					$locations[] = $location->name;
				}
				$locations = array_reverse( $locations );
				$loc       = implode( ', ', $locations );
			}

			if ( $address ) {
				$render .= sprintf( '<div>%s</div>', $address );
			}

			if ( $address && $loc && $zipcode ) {
				$render .= sprintf( '<div>%s, %s</div>', $loc, $zipcode );
			} elseif ( $address && $loc ) {
				$render .= sprintf( '<div>%s</div>', $loc );
			} elseif ( $zipcode ) {
				$render .= sprintf( '<div>%s</div>', $zipcode );
			}
		} else {
			$render = get_post_meta( $listing_id, '_rtcl_geo_address', true );
		}

		return $render;
	}

	public static function the_phone( $phone = '', $whatsapp_number = '' ) {
		if ( ! $phone && ( ! $whatsapp_number || Functions::is_field_disabled( 'whatsapp_number' ) ) ) {
			return;
		}
		$mobileClass   = wp_is_mobile() ? " rtcl-mobile" : null;
		$phone_options = [];
		if ( $phone ) {
			$phone_options = [
				'safe_phone'   => mb_substr( $phone, 0, mb_strlen( $phone ) - 3 ) . apply_filters( 'rtcl_phone_number_placeholder', 'XXX' ),
				'phone_hidden' => mb_substr( $phone, - 3 )
			];
		}
		if ( $whatsapp_number && ! Functions::is_field_disabled( 'whatsapp_number' ) ) {
			$phone_options['safe_whatsapp_number'] = mb_substr( $whatsapp_number, 0, mb_strlen( $whatsapp_number ) - 3 ) . apply_filters( 'rtcl_phone_number_placeholder', 'XXX' );
			$phone_options['whatsapp_hidden']      = mb_substr( $whatsapp_number, - 3 );
		}
		$phone_options = apply_filters( 'rtcl_phone_number_options', $phone_options, [
			'phone'           => $phone,
			'whatsapp_number' => $whatsapp_number
		] )
		?>
        <div class="rtcl-contact-reveal-wrapper reveal-phone<?php echo esc_attr( $mobileClass ); ?>"
             data-options="<?php echo htmlspecialchars( wp_json_encode( $phone_options ) ); ?>">
            <div class='numbers'>
				<?php
				if ( $phone ) {
					echo esc_html( $phone_options['safe_phone'] );
				} elseif ( $whatsapp_number ) {
					echo esc_html( $phone_options['safe_whatsapp_number'] );
				}
				?>
            </div>
            <small class='text-muted'><?php esc_html_e( 'Click to reveal phone number', 'classima' ); ?></small>
        </div>
		<?php
	}

	public static function get_listing_type( $listing ) {

		$listing_types = Functions::get_listing_types();
		$listing_types = empty( $listing_types ) ? [] : $listing_types;

		$type = $listing->get_ad_type();

		if ( $type && ! empty( $listing_types[ $type ] ) ) {
			$result = [
				'label' => $listing_types[ $type ],
				'icon'  => 'fa-tags'
			];
		} else {
			$result = false;
		}

		return $result;
	}

	public static function store_query() {
		global $post;

		$args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => Functions::get_option_item( 'rtcl_general_settings', 'listings_per_page', 20 ),
			'author'         => get_post_meta( $post->ID, 'store_owner_id', true ),
			'paged'          => Pagination::get_page_number(),
		];

		$general_settings = Functions::get_option( 'rtcl_general_settings' );
		$atts             = [
			'orderby' => ! empty( $general_settings['orderby'] ) ? $general_settings['orderby'] : 'date',
			'order'   => ! empty( $general_settings['order'] ) ? $general_settings['order'] : 'DESC',
		];

		$current_order = Pagination::get_listings_current_order( $atts['orderby'] . '-' . $atts['order'] );
		switch ( $current_order ) {
			case 'title-asc' :
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			case 'title-desc' :
				$args['orderby'] = 'title';
				$args['order']   = 'DESC';
				break;
			case 'date-asc' :
				$args['orderby'] = 'date';
				$args['order']   = 'ASC';
				break;
			case 'date-desc' :
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'price-asc' :
				$args['meta_key'] = 'price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc' :
				$args['meta_key'] = 'price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'views-asc' :
				$args['meta_key'] = '_views';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'views-desc' :
				$args['meta_key'] = '_views';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'rand' :
				$args['orderby'] = 'rand';
				break;
		}

		return new \WP_Query( apply_filters( 'rtcl_classima_store_query', $args ) );
	}

	public static function user_ads_query() {
		$general_settings = Functions::get_option( 'rtcl_general_settings' );
		$author           = get_user_by( 'slug', get_query_var( 'author_name' ) );
		$user_id          = $author->ID;

		$args = [
			'post_type'      => rtcl()->post_type,
			'posts_per_page' => ! empty( $general_settings['listings_per_page'] ) ? absint( $general_settings['listings_per_page'] ) : 10,
			'paged'          => Pagination::get_page_number(),
			'author'         => $user_id,
			'meta_query'     => [
				[
					'key'     => '_rtcl_manager_id',
					'compare' => 'NOT EXISTS'
				]
			]
		];

		return new \WP_Query( $args );
	}

}

Listing_Functions::instance();