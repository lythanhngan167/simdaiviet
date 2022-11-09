<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Functions;

class Layouts {

	protected static $instance = null;

	public $base;
	public $type;
	public $meta_value;

	public function __construct() {
		$this->base  = 'classima';
		
		add_action( 'template_redirect', array( $this, 'layout_settings' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function layout_settings() {
		$is_listing = $is_listing_archive = $is_listing_account = false;

		if ( class_exists( 'RtclPro' ) && class_exists( 'Rtcl' ) ) {
			$is_listing_archive = Functions::is_listings() || Functions::is_listing_taxonomy();
			$is_listing_account = Functions::is_account_page();
		}

		if ( $is_listing_archive || $is_listing_account ) {
			$is_listing = true;
		}

		// Single Pages
		if( ( is_single() || is_page() ) && !$is_listing ) {
			$post_type        = get_post_type();
			$post_id          = get_the_id();
			$this->meta_value = get_post_meta( $post_id, "{$this->base}_layout_settings", true );
			
			switch( $post_type ) {
				case 'page':
				$this->type = 'page';
				break;
				case 'post':
				$this->type = 'single_post';
				break;
				case 'rtcl_listing':
				$this->type = 'listing_single';
				break;
				default:
				$this->type = 'page';
				break;
			}

			RDTheme::$layout              = $this->meta_layout_option( 'layout' );
			RDTheme::$sidebar             = $this->meta_layout_option( 'sidebar' );
			RDTheme::$has_tr_header       = $this->meta_layout_global_option( 'tr_header', true );
			RDTheme::$has_top_bar         = $this->meta_layout_global_option( 'top_bar', true );
			RDTheme::$header_style        = $this->meta_layout_global_option( 'header_style' );
			RDTheme::$footer_style        = $this->meta_layout_global_option( 'footer_style' );
			RDTheme::$has_banner          = $this->meta_layout_global_option( 'banner', true );
			RDTheme::$has_breadcrumb      = $this->meta_layout_global_option( 'breadcrumb', true );
			RDTheme::$has_banner_search   = $this->meta_layout_global_option( 'banner_search', true );
			RDTheme::$bgtype              = $this->meta_layout_global_option( 'bgtype' );
			RDTheme::$bgimg               = $this->bgimg_option( 'bgimg' );
			RDTheme::$bgcolor             = $this->meta_layout_global_option( 'bgcolor' );
		}

		// Blog and Archive
		elseif( is_home() || is_archive() || is_search() || is_404() || $is_listing ) {

			if( is_search() ) {
				$this->type = 'search';
			}
			elseif( is_404() ) {
				$this->type = 'error';
				RDTheme::$options[$this->type . '_layout'] = 'full-width';
			}
			elseif( $is_listing_archive ) {
				$this->type = 'listing_archive';
			}
			elseif( $is_listing_account ) {
				$this->type = 'listing_account';
			}
			else {
				$this->type = 'blog';
			}

			RDTheme::$layout              = $this->layout_option( 'layout' );
			RDTheme::$sidebar             = $this->layout_option( 'sidebar' );
			RDTheme::$has_tr_header       = $this->layout_global_option( 'tr_header', true );
			RDTheme::$has_top_bar         = $this->layout_global_option( 'top_bar', true );
			RDTheme::$header_style        = $this->layout_global_option( 'header_style' );
			RDTheme::$footer_style        = $this->layout_global_option( 'footer_style' );
			RDTheme::$has_banner          = $this->layout_global_option( 'banner', true );
			RDTheme::$has_breadcrumb      = $this->layout_global_option( 'breadcrumb', true );
			RDTheme::$has_banner_search   = $this->layout_global_option( 'banner_search', true );
			RDTheme::$bgtype              = $this->layout_global_option( 'bgtype' );
			RDTheme::$bgimg               = $this->bgimg_option( 'bgimg', false );
			RDTheme::$bgcolor             = $this->layout_global_option( 'bgcolor' );
		}

		RDTheme::$bgimg = $this->bgimg_option( 'bgimg' );
	}

	private function bgimg_option( $key, $is_single = true ){
		$layout_key = $this->type.'_'.$key;

		if ( $is_single ) {
			$meta = !empty( $this->meta_value[$key] ) ? $this->meta_value[$key] : '';
		}
		else {
			$meta = '';
		}
		
		$op_layout  = isset(RDTheme::$options[$layout_key]) ? RDTheme::$options[$layout_key] : [];
		$op_global  = isset(RDTheme::$options[$key]) ? RDTheme::$options[$key] : [];

		if ( $meta ) {
			$src = wp_get_attachment_image_src( $meta, 'full', true );
			$img = $src[0];
		}
		elseif ( !empty( $op_layout['url'] ) ) {
			$img = $op_layout['url'];
		}
		elseif ( !empty( $op_global['url'] ) ) {
			$img = $op_global['url'];
		}
		else {
			$img = Helper::get_img( 'banner.jpg' );
		}

		return $img;
	}

	// Single
	private function meta_layout_global_option( $key, $is_bool = false  ) {
		$layout_key = $this->type.'_'.$key;

		$meta       = !empty( $this->meta_value[$key] ) ? $this->meta_value[$key] : 'default';
		$op_layout  = RDTheme::$options[$layout_key] ? RDTheme::$options[$layout_key] : 'default';
		$op_global  = RDTheme::$options[$key];

		if ( $meta != 'default' ) {
			$result = $meta;
		}
		elseif ( $op_layout != 'default' ) {
			$result = $op_layout;
		}
		else {
			$result = $op_global;
		}

		if ( $is_bool ) {
			$result = ( $result == 1 || $result == 'on' ) ? true : false;
		}

		return $result;
	}

	// Single
	private function meta_layout_option( $key  ) {
		$layout_key = $this->type.'_'.$key;

		$meta       = !empty( $this->meta_value[$key] ) ? $this->meta_value[$key] : 'default';
		$op_layout  = RDTheme::$options[$layout_key];

		if ( $meta != 'default' ) {
			$result = $meta;
		}
		else {
			$result = $op_layout;
		}

		return $result;
	}

	// Archive
	private function layout_global_option( $key, $is_bool = false  ) {
		$layout_key = $this->type.'_'.$key;

		$op_layout  = RDTheme::$options[$layout_key] ? RDTheme::$options[$layout_key] : 'default';
		$op_global  = RDTheme::$options[$key];

		if ( $op_layout != 'default' ) {
			$result = $op_layout;
		}
		else {
			$result = $op_global;
		}

		if ( $is_bool ) {
			$result = ( $result == 1 || $result == 'on' ) ? true : false;
		}

		return $result;
	}

	// Archive
	private function layout_option( $key  ) {
		$layout_key = $this->type.'_'.$key;
		$op_layout  = RDTheme::$options[$layout_key];

		return $op_layout;
	}
}

Layouts::instance();