<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.5.4
 */

use Rtcl\Helpers\Functions;

if ( ! isset( $content_width ) ) {
	$content_width = 1240;
}

class Classima_Main {

	public $theme = 'classima';
	public $action = 'classima_theme_init';

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'plugin_update_notices' ) );
		$this->includes();
	}

	public function load_textdomain() {
		load_theme_textdomain( $this->theme, get_template_directory() . '/languages' );
	}

	public function includes() {
		require_once get_template_directory() . '/inc/constants.php';
		require_once get_template_directory() . '/inc/helper.php';
		require_once get_template_directory() . '/inc/includes.php';

		do_action( $this->action );
	}

	public function plugin_update_notices() {
		$plugins = array();

		if ( defined( 'CLASSIMA_CORE' ) ) {
			if ( version_compare( CLASSIMA_CORE, '1.5', '<' ) ) {
				$plugins[] = 'Classima Core';
			}
		}

		if ( defined( 'RTCL_VERSION' ) ) {
			if ( version_compare( RTCL_VERSION, '1.5.55', '<' ) ) {
				$plugins[] = 'Classified Listing Pro';
			}
		}

		if ( defined( 'RTCL_STORE_VERSION' ) ) {
			if ( version_compare( RTCL_STORE_VERSION, '1.3.20', '<' ) ) {
				$plugins[] = 'Classified Listing Store';
			}
		}

		foreach ( $plugins as $plugin ) {
			$notice = '<div class="error"><p>' . sprintf( __( "Please update plugin <b><i>%s</b></i> to the latest version otherwise some functionalities will not work properly. You can update it from <a href='%s'>here</a>", 'classima' ), $plugin, menu_page_url( 'classima-install-plugins', false ) ) . '</p></div>';
			echo wp_kses_post( $notice );
		}
	}
}

new Classima_Main;