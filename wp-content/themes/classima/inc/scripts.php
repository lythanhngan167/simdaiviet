<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4.1
 */

namespace radiustheme\Classima;

use Elementor\Plugin;

use Rtcl\Helpers\Link;

class Scripts {

	public $version;
	protected static $instance = null;

	public function __construct() {
		$this->version = Constants::$theme_version;

		add_action( 'wp_enqueue_scripts',          array( $this, 'register_scripts' ), 12 );
		add_action( 'wp_enqueue_scripts',          array( $this, 'enqueue_scripts' ), 15 );
		add_action( 'wp_enqueue_scripts',          array( $this, 'enqueue_high_priority_scripts' ), 1500 );
		add_action( 'admin_enqueue_scripts',       array( $this, 'register_admin_scripts' ), 12 );
		add_action( 'admin_enqueue_scripts',       array( $this, 'enqueue_admin_scripts' ), 15 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_gutenberg_scripts' ), 12 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_scripts' ), 15 );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function register_scripts(){
		/* Deregister */
		wp_deregister_style( 'font-awesome' );

		// Google fonts
		wp_register_style( 'classima-gfonts',    $this->fonts_url(), array(), $this->version );

		wp_register_style( 'bootstrap',          Helper::get_maybe_rtl_css( 'bootstrap.min' ), array(), $this->version );
		wp_register_style( 'font-awesome',       Helper::get_css( 'font-awesome.min' ), array(), $this->version );
		wp_register_style( 'classima-meanmenu',  Helper::get_maybe_rtl_css( 'meanmenu' ), array(), $this->version );
		wp_register_style( 'classima-style',     Helper::get_maybe_rtl_css( 'style' ), array(), $this->version );
		wp_register_style( 'classima-listing',   Helper::get_maybe_rtl_css( 'listing' ), array(), $this->version );
		wp_register_style( 'classima-elementor', Helper::get_maybe_rtl_css( 'elementor' ), array(), $this->version );
		wp_register_style( 'classima-rtl',       Helper::get_css( 'rtl' ), array(), $this->version );

		wp_register_script( 'bootstrap',         Helper::get_js( 'bootstrap.bundle.min' ), array( 'jquery' ), $this->version, true );
		wp_register_script( 'classima-main',     Helper::get_js( 'main' ), array( 'jquery' ), $this->version, true );
		wp_register_script( 'sticky-sidebar',    Helper::get_js( 'sticky-sidebar.min' ), array( 'jquery' ), $this->version, true );
		wp_register_script( 'rt-bg-parallax',    Helper::get_js( 'rt-parallax' ), [ 'jquery' ], $this->version, true );
		wp_register_script( 'waypoints',         Helper::get_js( 'waypoints.min' ), array( 'jquery' ), $this->version, true );
		wp_register_script( 'counterup',         Helper::get_js( 'jquery.counterup.min' ), array( 'jquery' ), $this->version, true );
		wp_register_script( 'isotope',           Helper::get_js( 'isotope.pkgd.min' ), array( 'jquery' ), $this->version, true );
		wp_register_script( 'typed',             Helper::get_js( 'typed.min' ), array( 'jquery' ), $this->version, true );
	}

	public function register_admin_scripts() {
		wp_register_style( 'classima-admin',     Helper::get_maybe_rtl_css( 'admin' ), array(), $this->version );
	}

	public function register_gutenberg_scripts() {
		wp_register_style( 'classima-gfonts',    $this->fonts_url(), array(), $this->version );
		wp_register_style( 'classima-gutenberg', Helper::get_maybe_rtl_css( 'gutenberg' ), array(), $this->version );
	}

	public function enqueue_scripts() {
		/*CSS*/
		wp_enqueue_style( 'classima-gfonts' );
		wp_enqueue_style( 'bootstrap' );
        wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'classima-meanmenu' );
		$this->elementor_scripts(); // Elementor Scripts in preview mode
		$this->conditional_scripts(); // Conditional Scripts
		wp_enqueue_script( 'rt-bg-parallax' );
		wp_enqueue_style( 'classima-style' );
		wp_add_inline_style( 'classima-style',   $this->template_style() );
		wp_enqueue_style( 'classima-listing' );
		wp_enqueue_style( 'classima-elementor' );
		$this->dynamic_style();// Dynamic style

		/*JS*/
		wp_enqueue_script( 'bootstrap' );
		wp_enqueue_script( 'classima-main' );
		$this->localized_scripts(); // Localization
	}

	public function enqueue_high_priority_scripts() {
		if ( is_rtl() ) {
			wp_enqueue_style( 'classima-rtl' );
		}
	}

	private function localized_scripts(){

		$localize_data = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'hasAdminBar'   => is_admin_bar_showing() ? 1 : 0,
			'hasStickyMenu' => RDTheme::$options['sticky_menu'] ? 1 : 0,
			'headerStyle'   => RDTheme::$header_style,
			'meanWidth'     => RDTheme::$options['resmenu_width'],
			'primaryColor'  => Helper::get_primary_color(),
			'rtl'           => is_rtl() ? 'yes' : 'no',
            'sold_out_text' => esc_html__('Sold Out', 'classima'),
		);

		$localize_data = apply_filters( 'classima_localized_data', $localize_data );

		wp_localize_script( 'classima-main', 'ClassimaObj', $localize_data );
	}

	private function conditional_scripts(){
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		if ( ( is_home() || is_archive() ) && RDTheme::$options['blog_style'] == 'style2' ) {
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'isotope' );
		}
	}

	public function elementor_scripts() {
		if ( !did_action( 'elementor/loaded' ) ) {
			return;
		}
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_script( 'waypoints' );
			wp_enqueue_script( 'counterup' );
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'isotope' );
			wp_enqueue_script( 'typed' );
		}
	}

	public function fonts_url(){
		$fonts_url = '';
		if ( 'off' !== _x( 'on', 'Google fonts - Roboto and Nunito : on or off', 'classima' ) ) {
			$fonts_url = add_query_arg( 'family', urlencode( 'Roboto:400,500,700|Nunito:400,600,700,800' ), "//fonts.googleapis.com/css" );
		}
		return $fonts_url;
	}

	public function enqueue_admin_scripts(){
		wp_enqueue_style( 'classima-admin' );
	}

	public function enqueue_gutenberg_scripts() {
		wp_enqueue_style( 'classima-gfonts' );
		wp_enqueue_style( 'classima-gutenberg' );
		ob_start();
		Helper::requires( 'dynamic-styles/common.php' );
		$dynamic_css = ob_get_clean();
		$css  = $this->add_wrapper_to_css( $dynamic_css, '.wp-block.editor-block-list__block' );
		$css  = str_replace( 'gtnbg_root' , '', $css );
		wp_add_inline_style( 'classima-gutenberg', $css );
	}

	public function template_style(){

		$opacity = RDTheme::$options['bgopacity']/100;

		$css = '';

		$css .= '@media all and (min-width: 1200px) {.container{max-width: '.RDTheme::$options['container_width'].'px;}}';

		if ( RDTheme::$bgtype == 'bgcolor' ) {
			$bgcolor = RDTheme::$bgcolor;
			$banner_style = "background-color:{$bgcolor};";
		}
		else {
			$bgimg = RDTheme::$bgimg;
			$banner_style = "background:url({$bgimg}) no-repeat scroll top center / cover;";
		}

		$css .= ".theme-banner{{$banner_style}}";

		$css .= ".theme-banner:before{background-color: rgba(0,0,0,{$opacity})}";

		return $css;
	}

	private function dynamic_style(){
		$dynamic_css = '';
		ob_start();
		Helper::requires( 'dynamic-styles/frontend.php' );
		Helper::requires( 'dynamic-styles/listing.php' );
		Helper::requires( 'dynamic-styles/elementor.php' );
		$dynamic_css .= ob_get_clean();
		$dynamic_css  = $this->optimized_css( $dynamic_css );
		wp_register_style( 'classima-dynamic', false );
		wp_enqueue_style( 'classima-dynamic' );
		wp_add_inline_style( 'classima-dynamic', $dynamic_css );
	}

	private function optimized_css( $css ) {
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), ' ', $css );
		return $css;
	}

	private function add_wrapper_to_css( $css, $base ) {
		$parts = explode('}', $css);
		foreach ($parts as &$part) {
			if (empty($part)) {
				continue;
			}

			$firstPart = substr($part, 0, strpos($part, '{') + 1);
			$lastPart = substr($part, strpos($part, '{') + 2);
			$subParts = explode(',', $firstPart);
			foreach ($subParts as &$subPart) {
				$subPart = str_replace("\n", '', $subPart);
				$subPart = $base . ' ' . trim($subPart);
			}

			$part = implode(', ', $subParts) . $lastPart;
		}

		$resultCSS = implode("}\n", $parts);

		return $resultCSS;
	}
}

Scripts::instance();