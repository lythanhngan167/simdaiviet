<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

class Constants {

	public static $theme_version;
	public static $theme_author_uri;
	public static $theme_options;
	public static $theme_ad_options;

	public static $theme_base_dir;
	public static $theme_inc_dir;
	public static $theme_plugins_dir;

	public function __construct() {
		$theme_data = wp_get_theme( get_template() );

		self::$theme_version     = $theme_data->get( 'Version' );
		self::$theme_author_uri  = $theme_data->get( 'AuthorURI' );
		self::$theme_options     = 'classima';
		self::$theme_ad_options  = 'classima_ads';

		self::$theme_base_dir    = get_template_directory(). '/';
		self::$theme_inc_dir     = self::$theme_base_dir . 'inc/';
		self::$theme_plugins_dir = self::$theme_base_dir . 'plugin-bundle/';
	}
}

new Constants;