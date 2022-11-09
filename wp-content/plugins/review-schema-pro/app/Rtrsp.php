<?php

require_once __DIR__ . './../vendor/autoload.php'; 
 
use Rtrsp\Traits\SingletonTrait;
 
use Rtrsp\Shortcodes\ShortcodeFilter;    
use Rtrsp\Controllers\Admin\AdminController; 
use Rtrsp\Controllers\Frontend\FrontendHook;

/**
 * Class Rtrsp
 */
final class Rtrsp {

    use SingletonTrait;  

    /**
     * Review Schema Constructor.
     */
    public function __construct() { 
        $this->define_constants();   

        $this->init_hooks();  
    } 

    private function init_hooks() {
 
        add_action('plugins_loaded', [$this, 'on_plugins_loaded'], -1);
 
        add_action('init', [$this, 'init'], 1); 
    }

    public function init() {
        do_action('rtrsp_before_init');
        $this->load_plugin_textdomain();
        // Load your all dependency hooks
        AdminController::getInstance(); 
        FrontendHook::getInstance(); 
        ShortcodeFilter::getInstance(); 
        do_action('rtrsp_init');
    }

    public function on_plugins_loaded() {
        do_action('rtrsp_loaded');
    }

    /**
     * Load Localisation files
     */
    public function load_plugin_textdomain() {
         
        $locale = determine_locale();
        $locale = apply_filters('rtrsp_plugin_locale', $locale, 'review-schema-pro');
        unload_textdomain('review-schema-pro');
        load_textdomain('review-schema-pro', WP_LANG_DIR . '/review-schema-pro/review-schema-pro-' . $locale . '.mo');
        load_plugin_textdomain('review-schema-pro', false, plugin_basename(dirname(RTRSP_PLUGIN_FILE)) . '/languages');
    } 
 
    private function define_constants() {
        $this->define('RTRSP_PATH', plugin_dir_path(RTRSP_PLUGIN_FILE));
        $this->define('RTRSP_URL', plugins_url('', RTRSP_PLUGIN_FILE));
        $this->define('RTRSP_SLUG', basename(dirname(RTRSP_PLUGIN_FILE)));
        $this->define('RTRSP_TEMPLATE_DEBUG_MODE', false);
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    public function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit(plugin_dir_path(RTRSP_PLUGIN_FILE));
    }

    /**
     * @return mixed
     */
    public function version() {
        return RTRSP_VERSION;
    }  

    /**
     * Get the template path.
     *
     * @return string
     */
    public function get_template_path() {
        return apply_filters('rtrsp_template_path', 'review-schema-pro/');
    } 

    /**
     * @param $file
     *
     * @return string
     */
    public function get_assets_uri($file) {
        $file = ltrim($file, '/');

        return trailingslashit(RTRSP_URL . '/assets') . $file;
    } 
     
}

/**
 * @return bool|SingletonTrait|Rtrsp
 */
function rtrsp() { 
    return Rtrsp::getInstance();
} 
rtrsp(); // Run Rtrsp Plugin   
 