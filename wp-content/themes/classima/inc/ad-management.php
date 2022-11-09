<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Functions;

class Ad_Management {

	protected static $instance = null;

	public function __construct() {
		// Common
		add_action( 'classima_header_top',     array( $this, 'header_top' ) );
		add_action( 'classima_before_footer',  array( $this, 'footer' ) );
		add_action( 'classima_before_sidebar', array( $this, 'before_sidebar' ) );
		add_action( 'classima_after_sidebar',  array( $this, 'after_sidebar' ) );
		add_action( 'classima_before_content', array( $this, 'before_content' ) );
		add_action( 'classima_after_content',  array( $this, 'after_content' ) );

		// Listing Archive
		add_action( 'classima_listing_before_items',  array( $this, 'listing_before_items' ) );
		add_action( 'classima_listing_after_items',   array( $this, 'listing_after_items' ) );

		// Listing Single
		add_action( 'classima_single_listing_before_contents', array( $this, 'single_listing_before_contents' ) );
		add_action( 'classima_single_listing_after_contents',  array( $this, 'single_listing_after_contents' ) );
		add_action( 'classima_single_listing_after_product',   array( $this, 'single_listing_after_product' ) );
		add_action( 'classima_single_listing_after_location',  array( $this, 'single_listing_after_location' ) );
		add_action( 'classima_single_listing_after_related',   array( $this, 'single_listing_after_related' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function header_top() {
		switch ( $this->get_page_type() ) {
			case 'listing_archive':
			$this->render_ad( 'ad_listing_header', 'ad-header-top' );
			break;
			case 'listing_single':
			$this->render_ad( 'ad_single_header', 'ad-header-top' );
			break;
			case 'blog':
			$this->render_ad( 'ad_blog_header', 'ad-header-top' );
			break;
			case 'post':
			$this->render_ad( 'ad_post_header', 'ad-header-top' );
			break;
			case 'page':
			$this->render_ad( 'ad_page_header', 'ad-header-top' );
			break;
		}
	}

	public function footer() {
		switch ( $this->get_page_type() ) {
			case 'listing_archive':
			$this->render_ad( 'ad_listing_footer', 'ad-footer container' );
			break;
			case 'listing_single':
			$this->render_ad( 'ad_single_footer', 'ad-footer container' );
			break;
			case 'blog':
			$this->render_ad( 'ad_blog_footer', 'ad-footer container' );
			break;
			case 'post':
			$this->render_ad( 'ad_post_footer', 'ad-footer container' );
			break;
			case 'page':
			$this->render_ad( 'ad_page_footer', 'ad-footer container' );
			break;
		}
	}

	public function before_sidebar() {
		switch ( $this->get_page_type() ) {
			case 'listing_archive':
			$this->render_ad( 'ad_listing_before_sidebar', 'ad-before-sidebar' );
			break;
			case 'listing_single':
			$this->render_ad( 'ad_single_before_sidebar', 'ad-before-sidebar' );
			break;
			case 'blog':
			$this->render_ad( 'ad_blog_before_sidebar', 'ad-before-sidebar' );
			break;
			case 'post':
			$this->render_ad( 'ad_post_before_sidebar', 'ad-before-sidebar' );
			break;
			case 'page':
			$this->render_ad( 'ad_page_before_sidebar', 'ad-before-sidebar' );
			break;
		}
	}

	public function after_sidebar() {
		switch ( $this->get_page_type() ) {
			case 'listing_archive':
			$this->render_ad( 'ad_listing_after_sidebar', 'ad-after-sidebar' );
			break;
			case 'listing_single':
			$this->render_ad( 'ad_single_after_sidebar', 'ad-after-sidebar' );
			break;
			case 'blog':
			$this->render_ad( 'ad_blog_after_sidebar', 'ad-after-sidebar' );
			break;
			case 'post':
			$this->render_ad( 'ad_post_after_sidebar', 'ad-after-sidebar' );
			break;
			case 'page':
			$this->render_ad( 'ad_page_after_sidebar', 'ad-after-sidebar' );
			break;
		}
	}

	public function before_content() {
		switch ( $this->get_page_type() ) {
			case 'post':
			$this->render_ad( 'ad_post_before_content', 'ad-before-content' );
			break;
			case 'page':
			$this->render_ad( 'ad_page_before_content', 'ad-before-content' );
			break;
		}
	}

	public function after_content() {
		switch ( $this->get_page_type() ) {
			case 'post':
			$this->render_ad( 'ad_post_after_content', 'ad-after-content' );
			break;
			case 'page':
			$this->render_ad( 'ad_page_after_content', 'ad-after-content' );
			break;
		}		
	}

	public function listing_before_items() {
		$this->render_ad( 'ad_listing_before_items', 'ad-listing-before-items' );
	}

	public function listing_after_items() {
		$this->render_ad( 'ad_listing_after_items', 'ad-listing-after-items' );
	}

	public function single_listing_before_contents() {
		$this->render_ad( 'ad_single_before_contents', 'ad-single-listing-before-contents' );
	}

	public function single_listing_after_contents() {
		$this->render_ad( 'ad_single_after_contents', 'ad-single-listing-after-contents' );
	}

	public function single_listing_after_product() {
		$this->render_ad( 'ad_single_after_product', 'ad-single-listing-after-block' );
	}

	public function single_listing_after_location() {
		$this->render_ad( 'ad_single_after_location', 'ad-single-listing-after-block' );
	}

	public function single_listing_after_related() {
		$this->render_ad( 'ad_single_after_related', 'ad-single-listing-after-block' );
	}

	private function render_ad( $base, $class='' ) {

		if ( !RDTheme::$ad_options[$base.'_activate'] ) {
			return;
		}
		if ( RDTheme::$ad_options[$base.'_type'] == 'image' ) {
			$html = $this->get_image_ad( $base );
		}
		else {
			$html = RDTheme::$ad_options[$base.'_code'];
		}

		if ( !$html ) {
			return;
		}

		$html = sprintf( '<div class="classima-ad %1$s">%2$s</div>' , $class, $html );
		echo do_shortcode( $html );
	}

	private function get_page_type() {
		if ( class_exists( 'RtclPro' ) && Functions::is_listings() ) {
			return 'listing_archive';
		}
		if ( is_singular( 'rtcl_listing' ) ) {
			return 'listing_single';
		}
		if ( is_home() || is_archive() ) {
			return 'blog';
		}
		if ( is_singular( 'post' ) ) {
			return 'post';
		}
		if ( is_page() ) {
			return 'page';
		}
		return '';
	}

	private function get_image_ad( $base ){
		if ( empty( RDTheme::$ad_options[$base.'_image']['id'] ) ) {
			return;
		}

		$img_html = wp_get_attachment_image( RDTheme::$ad_options[$base.'_image']['id'], 'full' );

		if ( !$img_html ) {
			return;
		}

		if ( !RDTheme::$ad_options[$base.'_url'] ) {
			$html = $img_html;
		}
		else {
			$attr = ' href="'.RDTheme::$ad_options[$base.'_url'].'"';
			if ( RDTheme::$ad_options[$base.'_newtab'] ) {
				$attr .= ' target="_blank"';
			}
			if ( RDTheme::$ad_options[$base.'_nofollow'] ) {
				$attr .= ' rel="nofollow"';
			}
			$html = sprintf( '<a%1$s>%2$s</a>' , $attr, $img_html );
		}

		return $html;
	}
}

Ad_Management::instance();