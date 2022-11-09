<?php

require_once __DIR__ . './../vendor/autoload.php';

use Rtrs\Hooks\Backend;
use Rtrs\Hooks\Frontend;
use Rtrs\Hooks\SeoHooks;
use Rtrs\Widgets\Widget;
use Rtrs\Helpers\Functions;
use Rtrs\Traits\SingletonTrait;
use Rtrs\Controllers\Shortcodes;
use Rtrs\Controllers\Marketing\Offer;
use Rtrs\Controllers\Admin\Activation;
use Rtrs\Controllers\Marketing\Review;
use Rtrs\Controllers\Ajax\AjaxController;
use Rtrs\Controllers\Admin\AdminController;

/**
 * Class Rtrs.
 */
final class Rtrs {
	use SingletonTrait;

	private $post_type = 'rtrs';

	private $post_type_affiliate = 'rtrs_affiliate';

	private $nonceId = '__rtrs_wpnonce';

	private $nonceText = 'rtrs_nonce_kx2T6dYRXSxD';

	/**
	 * Review Schema Constructor.
	 */
	public function __construct() {
		$this->define_constants();

		new Widget();

		$this->init_hooks();
		new Activation();
	}

	private function init_hooks() {
		add_action('plugins_loaded', [$this, 'on_plugins_loaded'], -1);

		add_action('init', [$this, 'init'], 1);
		add_action('init', [Shortcodes::class, 'init_short_code']); // Init ShortCode
	}

	public function init() {
		do_action('rtrs_before_init');

		$this->load_plugin_textdomain();

		new AdminController();
		new AjaxController();
		Review::init();
		new Offer();
		new Backend();
		new Frontend();
		do_action('rtrs_init');
	}

	public function on_plugins_loaded() {
		new SeoHooks();
		do_action('rtrs_loaded');
	}

	/**
	 * Load Localisation files.
	 */
	public function load_plugin_textdomain() {
		if( ! function_exists('determine_locale') ){
			require_once ABSPATH.WPINC.'/l10n.php';
		}
		$locale = function_exists('determine_locale') ? determine_locale() : 'en_US'; // Forums Support: Undefine Function fixed. 
		$locale = apply_filters('rtrs_plugin_locale', $locale);
		unload_textdomain('review-schema');
		load_textdomain('review-schema', WP_LANG_DIR . '/review-schema/review-schema-' . $locale . '.mo');
		load_plugin_textdomain('review-schema', false, plugin_basename(dirname(RTRS_PLUGIN_FILE)) . '/languages');
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	public function is_request($type) {
		switch ($type) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined('DOING_AJAX');
			case 'cron':
				return defined('DOING_CRON');
			case 'frontend':
				return (! is_admin() || defined('DOING_AJAX')) && ! defined('DOING_CRON');
		}
	}

	private function define_constants() {
		$this->define('RTRS_PATH', plugin_dir_path(RTRS_PLUGIN_FILE));
		$this->define('RTRS_URL', plugins_url('', RTRS_PLUGIN_FILE));
		$this->define('RTRS_SLUG', basename(dirname(RTRS_PLUGIN_FILE)));
		$this->define('RTRS_TEMPLATE_DEBUG_MODE', false);
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	public function define($name, $value) {
		if (! defined($name)) {
			define($name, $value);
		}
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit(plugin_dir_path(RTRS_PLUGIN_FILE));
	}

	/**
	 * @return mixed
	 */
	public function version() {
		return RTRS_VERSION;
	}

	/**
	 * @return string
	 */
	public function getPostType() {
		return $this->post_type;
	}

	/**
	 * @return string
	 */
	public function getPostTypeAffiliate() {
		return $this->post_type_affiliate;
	}

	/**
	 * @return string
	 */
	public function getNonceId() {
		return $this->nonceId;
	}

	/**
	 * @return string
	 */
	public function getNonceText() {
		return $this->nonceText;
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function get_template_path() {
		return apply_filters('rtrs_template_path', 'review-schema/');
	}

	/**
	 * Get the template partial path.
	 *
	 * @return string
	 */
	public function get_partial_path($path = null, $args = []) {
		Functions::get_template_part('partials/' . $path, $args);
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function get_assets_uri($file) {
		$file = ltrim($file, '/');

		return trailingslashit(RTRS_URL . '/assets') . $file;
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function render($viewName, $args = [], $return = false) {
		$path     = str_replace('.', '/', $viewName);
		$viewPath = RTRS_PATH . '/views/' . $path . '.php';
		if (! file_exists($viewPath)) {
			return;
		}

		if ($args) {
			extract($args);
		}

		if ($return) {
			ob_start();
			include $viewPath;

			return ob_get_clean();
		}
		include $viewPath;
	}

	/**
	 * @param $file
	 * Get all optoins field value
	 *
	 * @return mixed
	 */
	public function get_options() {
		$option_field = func_get_args()[0];
		$result       = get_option($option_field);
		$func_args    = func_get_args();
		array_shift($func_args);

		foreach ($func_args as $arg) {
			if (is_array($arg)) {
				if (! empty($result[$arg[0]])) {
					$result = $result[$arg[0]];
				} else {
					$result = $arg[1];
				}
			} else {
				if (! empty($result[$arg])) {
					$result = $result[$arg];
				} else {
					$result = null;
				}
			}
		}

		return $result;
	}
}

/**
 * Rivew Schema
 *
 * @return bool|SingletonTrait|Rtrs
 */
function rtrs() {
	return Rtrs::getInstance();
}
rtrs(); // Run Rtrs Plugin.
