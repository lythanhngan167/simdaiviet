<?php

require_once __DIR__ . './../vendor/autoload.php';

use RtclStore\Api\RestApi;
use Rtcl\Helpers\Functions;
use RtclStore\Models\Factory;
use RtclStore\Helpers\Install;
use RtclStore\Models\Dependencies;
use RtclStore\Controllers\Shortcodes;
use RtclStore\Controllers\StoreQuery;
use RtclStore\Controllers\Controllers;
use RtclStore\Controllers\Hooks\MenuHooks;
use RtclStore\Controllers\Hooks\StoreMetaHook;
use RtclStore\Controllers\Hooks\TemplateHooks;
use RtclStore\Controllers\Hooks\TemplateLoader;
use RtclStore\Controllers\Hooks\AfterSetupTheme;
use RtclStore\Widgets\Widget;
use RtclStore\Controllers\ELStoreController;

if (!class_exists(RtclStore::class)):
    final class RtclStore
    {

        protected static $instance = null;

        public $post_type = "store";
        public $category = "store_category";

        /**
         * Query instance.
         *
         * @var StoreQuery
         */
        public $query = null;

        /**
         * @var Factory
         */
        public $factory = null;

        public static function getInstance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Cloning is forbidden.
         *
         * @since 1.0
         */
        public function __clone() {
            Functions::doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'classified-listing-store'), '1.0');
        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0
         */
        public function __wakeup() {
            Functions::doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'classified-listing-store'), '1.0');
        }

        /**
         * Auto-load in-accessible properties on demand.
         *
         * @param mixed $key Key name.
         *
         * @return mixed
         */
        public function __get($key) {
            if (in_array($key, array('plugins_loaded'), true)) {
                return $this->$key();
            }
        }

        public function __construct() {
            $this->define_constants();
            $this->load_plugin_textdomain();
            $this->hooks();
        }

        private function hooks() {
            $dependence = Dependencies::getInstance();
            if ($dependence->check()) {
                if (rtcl()->is_request('frontend')) {
                    $this->frontend_hook();
                }
                if (rtcl()->is_request('admin')) {
                    $this->backend_hook();
                }
                new Controllers();
                new RestApi();
                $this->query = new StoreQuery();
                add_action('after_setup_theme', [AfterSetupTheme::class, 'template_functions'], 11);
                add_action('init', [$this, 'init'], 0);
                add_action('init', [Shortcodes::class, 'init_short_code']);
                do_action('rtcl_store_loaded', $this);
            }
        }

        private function backend_hook() {
            StoreMetaHook::init();
            MenuHooks::init();
        }

        private function frontend_hook() {
            TemplateHooks::init();
            add_action('init', [TemplateLoader::class, 'init']);
        }

        public function init() {
            do_action('rtcl_store_before_init');

            Widget::init();
            $this->factory = new Factory();
			if (did_action('elementor/loaded')) {
				ELStoreController::init();
			}
            do_action('rtcl_store_init');
        }

        private function define_constants() {
            $this->define('RTCL_STORE_PATH', plugin_dir_path(RTCL_STORE_PLUGIN_FILE));
            $this->define('RTCL_STORE_URL', plugins_url('', RTCL_STORE_PLUGIN_FILE));
            $this->define('RTCL_STORE_TEMPLATE_DEBUG_MODE', false);
        }

        /**
         * Get the plugin path.
         *
         * @return string
         */
        public function plugin_path() {
            return untrailingslashit(plugin_dir_path(RTCL_STORE_PLUGIN_FILE));
        }


	    /**
	     * @return string
	     */
	    public function get_plugin_template_path() {
		    return $this->plugin_path() . '/templates/';
	    }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present.
         *
         * Locales found in:
         *      - WP_LANG_DIR/classified-listing-store/classified-listing-store-LOCALE.mo
         *      - WP_LANG_DIR/plugins/classified-listing-store-LOCALE.mo
         */
        private function load_plugin_textdomain() {
            $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
            $locale = apply_filters('plugin_locale', $locale, 'classified-listing-store');
            unload_textdomain('classified-listing-store');
            load_textdomain('classified-listing-store', WP_LANG_DIR . '/classified-listing-store/classified-listing-store-' . $locale . '.mo');
            load_plugin_textdomain('classified-listing-store', false, plugin_basename(dirname(RTCL_STORE_PLUGIN_FILE)) . '/languages');
        }

        /**
         * Define constant if not already set.
         *
         * @param string      $name  Constant name.
         * @param string|bool $value Constant value.
         */
        private function define($name, $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }

    }

    function rtclStore() {
        return RtclStore::getInstance();
    }

    add_action('plugins_loaded', 'rtclStore', 30);
    register_activation_hook(RTCL_STORE_PLUGIN_FILE, [Install::class, 'activate']);
    register_deactivation_hook(RTCL_STORE_PLUGIN_FILE, [Install::class, 'deactivate']);
endif;
