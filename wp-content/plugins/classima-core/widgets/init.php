<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

class Custom_Widgets_Init {

	public $widgets;
	protected static $instance = null;

	public function __construct() {

		// Widgets -- filename=>classname /@dev
		$this->widgets =  array(
			'about' => 'About_Widget',
		);

		add_action( 'widgets_init', array( $this, 'custom_widgets' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function custom_widgets() {
		if ( !class_exists( 'RT_Widget_Fields' ) ) return;

		foreach ( $this->widgets as $filename => $classname ) {

			$template_name = '/widgets/' . $filename . '.php';

			if ( file_exists( STYLESHEETPATH . $template_name ) ) {
				$file = STYLESHEETPATH . $template_name;
			}
			elseif ( file_exists( TEMPLATEPATH . $template_name ) ) {
				$file = TEMPLATEPATH . $template_name;
			}
			else {
				$file  = dirname( __FILE__ ) . '/' . $filename . '.php';
			}

			require_once $file;

			$class = __NAMESPACE__ . '\\' . $classname;
			register_widget( $class );
		}
	}
}

Custom_Widgets_Init::instance();