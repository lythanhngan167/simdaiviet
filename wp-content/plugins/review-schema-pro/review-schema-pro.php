<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Review Schema Pro
 * Plugin URI:        https://www.radiustheme.com/downloads/wordpress-review-structure-data-schema-plugin/?utm_source=WordPress&utm_medium=reviewschema&utm_campaign=pro_click
 * Description:       This is a pro version of Review Schema Plugin
 * Version:           1.0.4
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       review-schema-pro
 * Domain Path:       /languages
 */

if ( !defined('ABSPATH') ) {
    exit;
}

// Define PLUGIN_FILE.
if ( !defined('RTRSP_PLUGIN_FILE') ) {
    define('RTRSP_PLUGIN_FILE', __FILE__);
}

// Define VERSION.
if ( !defined('RTRSP_VERSION') ) {
    define('RTRSP_VERSION', '1.0.4');
}  

if ( !in_array('review-schema/review-schema.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {  
    add_action( 'admin_notices', function() { 

        $class = 'notice notice-error'; 
        $text = esc_html__('Review Schema', 'review-schema-pro');
        $link = esc_url(
            add_query_arg(
                array(
                    'tab' => 'plugin-information',
                    'plugin' => 'review-schema',
                    'TB_iframe' => 'true',
                    'width' => '640',
                    'height' => '500',
                ), admin_url('plugin-install.php')
            )
        );
        $link_pro = 'https://www.radiustheme.com/downloads/review-schema';

        printf('<div class="%1$s"><p><a target="_blank" href="%3$s"><strong>Review Schema Pro</strong></a> is not working because you also need to install and activate <a class="thickbox open-plugin-details-modal" href="%2$s"><strong>%4$s</strong></a> plugin to get pro features.</p></div>', $class, $link, $link_pro, $text);
        
    } );
} 

add_action('rtrs_loaded', function() {  
    if ( !class_exists('Rtrsp') ) {
        require_once("app/Rtrsp.php");
    } 
} );  

//redirect to licensing page after activation
class RtrspActivation { 
    public function __construct() {  
        register_activation_hook(RTRSP_PLUGIN_FILE, array($this, 'plugin_activate'));
        
        add_action('admin_init', array($this, 'plugin_redirect') );
    }  
    
    function plugin_activate() {  
        add_option('rtrsp_activation_redirect', true); 
    } 

    function plugin_redirect() {
        if ( get_option('rtrsp_activation_redirect', false) ) {
            
            delete_option('rtrsp_activation_redirect'); 
            wp_redirect( admin_url('admin.php?page=rtrs-settings&tab=tools') );
        }
    } 
} 
new RtrspActivation();

