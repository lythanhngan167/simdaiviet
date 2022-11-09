<?php
/**
 * @author RadiusTheme
 *
 * The following code is a derivative work of the code from plugin "Safe SVG v-1.9.3"(https://wordpress.org/plugins/safe-svg/),
 * which is licensed GPLv2. This code therefore is also licensed under the terms
 * of the GNU Public License, verison 2.
 */

namespace radiustheme\Lib;

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_SVG {

	protected static $instance;
	public $sanitizer;

	public function __construct() {
		if ( !class_exists( '\enshrined\svgSanitize\Sanitizer' ) ) {
			require_once dirname(__FILE__) . '/autoload.php';
			$this->sanitizer = new \enshrined\svgSanitize\Sanitizer();
			$this->sanitizer->removeRemoteReferences( true );
			$this->sanitizer->removeXMLTag( true );
			$this->sanitizer->minify( true );
		}

		add_filter( 'upload_mimes',               array( $this, 'allow_svg_filetype' ) );
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'check_for_svg' ) ); /* Check if the file is an SVG, if so handle appropriately*/
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'fix_admin_preview' ), 10, 3 ); /* Admin Preview fix */
		add_filter( 'wp_check_filetype_and_ext',  array( $this, 'fix_mime_type_svg' ), 75, 4 ); /* Fixes the issue in WordPress 4.7.1 being unable to correctly identify SVGs */
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function is_svg( $attachment_id ) {
		$file = get_attached_file( $attachment_id );

		if ( !$file ) {
			return false;
		}

		$type = wp_check_filetype( wp_basename( $file ) );

		if ( $type['ext'] == 'svg' ) {
			return true;
		}
		
		return false;
	}

	public static function get_svg( $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		$svg = file_get_contents( $file );
		$svg = trim( $svg );
		
		return $svg;
	}

	public static function get_attachment_image( $attachment_id, $size = 'thumbnail', $icon = false, $attr = '' ) {
		if( self::is_svg( $attachment_id ) ) {
			$attachment = self::get_svg( $attachment_id );
		}
		else {
			$attachment = wp_get_attachment_image( $attachment_id, $size, $icon, $attr );
		}
		return $attachment;
	}

	private function sanitize( $file ) {
		$dirty = file_get_contents( $file );
		$clean = $this->sanitizer->sanitize( $dirty );
		$clean = trim( $clean );

		if ( $clean === false ) {
			return false;
		}

		file_put_contents( $file, $clean );

		return true;
	}

	public function allow_svg_filetype( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	public function check_for_svg( $file ) {
		if ( $file['type'] === 'image/svg+xml' ) {
			if ( ! $this->sanitize( $file['tmp_name'] ) ) {
				$file['error'] = "Sorry, this file couldn't be sanitized so for security reasons wasn't uploaded";
			}
		}
		return $file;
	}

	public function fix_mime_type_svg( $data = null, $file = null, $filename = null, $mimes = null ) {
		$ext = isset( $data['ext'] ) ? $data['ext'] : '';
		if ( strlen( $ext ) < 1 ) {
			$exploded = explode( '.', $filename );
			$ext      = strtolower( end( $exploded ) );
		}
		if ( $ext === 'svg' ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = 'svg';
		}

		return $data;
	}

	public function fix_admin_preview( $response, $attachment, $meta ) {

		if ( $response['mime'] == 'image/svg+xml' ) {

			$default_width  = 512;
			$default_height = 512;

			$args = array(
				'width'       => $default_width,
				'height'      => $default_height,
				'orientation' => 'portrait'
			);

			$response = array_merge( $response, $args );

			$possible_sizes = apply_filters( 'image_size_names_choose', array(
				'full'      => __( 'Full Size', 'classima-core' ),
				'thumbnail' => __( 'Thumbnail', 'classima-core' ),
				'medium'    => __( 'Medium', 'classima-core' ),
				'large'     => __( 'Large', 'classima-core' ),
			) );

			$sizes = array();

			foreach ( $possible_sizes as $size => $label ) {

				$sizes[ $size ] = array(
					'height'      => get_option( "{$size}_size_w", $default_height ),
					'width'       => get_option( "{$size}_size_h", $default_width ),
					'url'         => $response['url'],
					'orientation' => 'portrait',
				);
			}

			$response['sizes'] = $sizes;
			$response['icon']  = $response['url'];
		}

		return $response;
	}
}

WP_SVG::instance();