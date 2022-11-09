<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.3.5
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Link;

class General_Setup {

	protected static $instance = null;

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_action( 'wp_head', array( $this, 'noscript_hide_preloader' ), 1 );
		add_action( 'wp_head', array( $this, 'pingback' ) );
		add_action( 'wp_head', [ $this, 'og_metatags' ] );
		add_action( 'wp_body_open', array( $this, 'preloader' ) );
		add_action( 'wp_footer', array( $this, 'scroll_to_top_html' ), 1 );
		add_filter( 'get_search_form', array( $this, 'search_form' ) );
		add_filter( 'comment_form_fields', array( $this, 'move_textarea_to_bottom' ) );
		add_filter( 'post_class', array( $this, 'hentry_config' ) );
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
		add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
		// Notice
		add_action( 'admin_notices', [ $this, 'rtcl_merge_notice' ] );
		// Elementor supports
		add_filter( 'elementor/widgets/wordpress/widget_args', array( $this, 'elementor_widget_args' ) );
		add_action( 'elementor/theme/register_locations', array( $this, 'elementor_register_locations' ) );
		// Restrict Admin Area
		add_action( 'after_setup_theme', array( $this, 'restrict_admin_area' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function theme_setup() {
		// Theme supports
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'wp-block-styles' );
		// Image sizes
		$sizes = array(
			'rdtheme-size1' => array( 1210, 605, true ), // When Full width
			'rdtheme-size2' => array( 450, 260, true ), // Blog style 2
			'rdtheme-size3' => array( 900, 450, true ), // When sidebar present
		);

		$sizes = apply_filters( 'classima_image_size', $sizes );

		foreach ( $sizes as $size => $value ) {
			add_image_size( $size, $value[0], $value[1], $value[2] );
		}

		// Register menus
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', 'classima' ),
		) );
	}

	public function register_sidebars() {

		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar', 'classima' ),
			'id'            => 'sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );

		$footer_widget_titles = array(
			'1' => esc_html__( 'Footer 1', 'classima' ),
			'2' => esc_html__( 'Footer 2', 'classima' ),
			'3' => esc_html__( 'Footer 3', 'classima' ),
			'4' => esc_html__( 'Footer 4', 'classima' ),
		);

		foreach ( $footer_widget_titles as $id => $name ) {
			register_sidebar( array(
				'name'          => $name,
				'id'            => 'footer-' . $id,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widgettitle">',
				'after_title'   => '</h3>',
			) );
		}

		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar - Single Listing', 'classima' ),
			'id'            => 'sidebar-single-listing',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar - Archive Listing', 'classima' ),
			'id'            => 'sidebar-archive-listing',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar - My Account', 'classima' ),
			'id'            => 'sidebar-myaccount',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );
	}

	public function body_classes( $classes ) {
		// Header
		$classes[] = 'header-style-' . RDTheme::$header_style;
		$classes[] = 'footer-style-' . RDTheme::$footer_style;

		if ( RDTheme::$has_top_bar && ( is_active_sidebar( 'top-left' ) || is_active_sidebar( 'top-right' ) ) ) {
			$classes[] = 'has-topbar';
		}

		if ( RDTheme::$has_tr_header ) {
			$classes[] = 'trheader';
		}

		if ( RDTheme::$has_banner ) {
			$classes[] = 'banner-enabled';
		}

		// Sidebar
		if ( RDTheme::$layout == 'left-sidebar' ) {
			$classes[] = 'has-sidebar left-sidebar';
		} elseif ( RDTheme::$layout == 'right-sidebar' ) {
			$classes[] = 'has-sidebar right-sidebar';
		} else {
			$classes[] = 'no-sidebar';
		}

		return $classes;
	}

	public function noscript_hide_preloader() {
		// Hide preloader if js is disabled
		echo '<noscript><style>#preloader{display:none;}</style></noscript>';
	}

	public function pingback() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}

	public function og_metatags() {
		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		$title = get_the_title();

		if ( is_singular('post') ) {
			echo '<meta property="og:url" content="' . get_the_permalink() . '" />';
			echo '<meta property="og:type" content="article" />';
			echo '<meta property="og:title" content="' . $title . '" />';

			if ( ! empty( $post->post_content ) ) {
				echo '<meta property="og:description" content="' . wp_trim_words( $post->post_content,
						150 ) . '" />';
			}
			$attachment_id = get_post_thumbnail_id( $post->ID );
			if ( ! empty( $attachment_id ) ) {
				$thumbnail = wp_get_attachment_image_src( $attachment_id, 'full' );
				if ( ! empty( $thumbnail ) ) {
					echo '<meta property="og:image" content="' . $thumbnail[0] . '" />';
				}
			}
			echo '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '" />';
			echo '<meta name="twitter:card" content="summary" />';
		}
	}

	public function preloader() {
		// Preloader
		if ( RDTheme::$options['preloader'] ) {
			if ( ! empty( RDTheme::$options['preloader_image']['url'] ) ) {
				$preloader_img = RDTheme::$options['preloader_image']['url'];
			} else {
				$preloader_img = Helper::get_img( 'preloader.gif' );
			}
			echo '<div id="preloader" style="background-image:url(' . esc_url( $preloader_img ) . ');"></div>';
		}
	}

	public function scroll_to_top_html() {
		// Back-to-top link
		if ( RDTheme::$options['back_to_top'] ) {
			echo '<a href="#" class="scrollToTop"><i class="fa fa-angle-double-up"></i></a>';
		}
	}

	public function search_form() {
		$output = '
		<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
		<div class="custom-search-input">
		<div class="input-group">
		<input type="text" class="search-query form-control" placeholder="' . esc_attr__( 'Search here ...', 'classima' ) . '" value="' . get_search_query() . '" name="s" />
		<span class="input-group-btn">
		<button class="btn" type="submit">
		<span class="fas fa-search"></span>
		</button>
		</span>
		</div>
		</div>
		</form>
		';

		return $output;
	}

	public function move_textarea_to_bottom( $fields ) {
		$temp = $fields['comment'];
		unset( $fields['comment'] );
		$fields['comment'] = $temp;

		return $fields;
	}

	public function hentry_config( $classes ) {
		if ( is_search() || is_page() ) {
			$classes = array_diff( $classes, array( 'hentry' ) );
		}

		return $classes;
	}

	public function excerpt_more() {
		if ( is_search() ) {
			$readmore = '<a href="' . get_the_permalink() . '"> [' . esc_html__( 'read more ...', 'classima' ) . ']</a>';

			return $readmore;
		}

		return ' ...';
	}

	public function excerpt_length( $length ) {
		if ( ( is_home() || is_archive() ) && RDTheme::$options['blog_style'] == 'style2' ) {
			return 25;
		}

		return $length;
	}

	public function rtcl_merge_notice() {
		$rtcl_dir = '';

		if ( defined( 'WP_PLUGIN_DIR' ) ) {
			$rtcl_dir = WP_PLUGIN_DIR . '/classified-listing-pro/classified-listing-pro.php';
		}

		if ( file_exists( $rtcl_dir ) ) {
			$rtcl_info = get_plugin_data( $rtcl_dir );
			$version   = $rtcl_info['Version'];
			if ( version_compare( $version, '2.0.0', '<' ) ) {
				$message = sprintf( __( 'You have to must update <strong>Classified Listing Pro</strong> plugin, otherwise functionality will not work properly.', 'classima' ) );
				printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
			}
		}
	}

	public function restrict_admin_area() {
		if ( RDTheme::$options['restrict_admin_area'] && ! current_user_can( 'administrator' ) ) {
			show_admin_bar( false );
		}
	}

	public function elementor_widget_args( $args ) {
		$args['before_widget'] = '<div class="widget %2$s">';
		$args['after_widget']  = '</div>';
		$args['before_title']  = '<h3>';
		$args['after_title']   = '</h3>';

		return $args;
	}

	function elementor_register_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location( 'header' );
		$elementor_theme_manager->register_location( 'footer' );
	}
}

General_Setup::instance();