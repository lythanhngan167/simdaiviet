<?php 
/**
 * Check Radius Theme License
 *
 * @since 1.0
 * 
 */
namespace RTLC;

class Helper {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
 
    private $license_url = "https://envato.radiustheme.com/license-check";
    
    private $theme_name = "";
    private $theme_slug = "";

    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', [$this, 'theme_menu'] );
        add_action( 'admin_init', [$this, 'theme_option'] ); 
        add_action( 'wp_ajax_rtlc_verification', [$this, 'rtlc_verification'] );  

        $theme_info = wp_get_theme();
        $theme_info = ( $theme_info->parent() ) ? $theme_info->parent() : $theme_info;
        $theme_name = $theme_info->get('Name'); 

        // theme name
        $this->theme_name = $theme_name; 

        // theme slug
        $theme_name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $theme_name)));
        $this->theme_slug = $theme_name;  
    }

    /**
     * Add options page
     */
    public function theme_menu() { 
        add_theme_page( esc_html__( 'Theme License', 'classima' ), esc_html__( 'Theme License', 'classima' ), "manage_options", "rtlc", array( $this, 'create_admin_page' ), null, 99);
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        settings_errors();
        
        $this->options = get_option('rt_licenses');  
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Theme License', 'classima' ); ?></h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'rt_option_group' );
                do_settings_sections( 'classima-setting' );
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function theme_option() {        
        register_setting(
            'rt_option_group', // Option group
            'rt_license', // Option name
            array( $this, 'sanitize_text' ) // Sanitize
        );

        add_settings_section(
            'rt_license_section', // ID
            false, // Title
            false, // Callback
            'classima-setting' // Page
        );  

        add_settings_field(
            'rt_purchase_code',  
            esc_html__( 'Purchase Code', 'classima' ),
            array( $this, 'purchase_code_callback' ), 
            'classima-setting',
            'rt_license_section'
        );  

        add_settings_field(
            'rt_license_status', 
            esc_html__( 'License Status', 'classima' ),
            array( $this, 'license_status_callback' ), 
            'classima-setting',
            'rt_license_section'
        );  

        add_settings_field(
            'rt_license_note', 
            esc_html__( 'Note:', 'classima' ),
            array( $this, 'license_note_callback' ), 
            'classima-setting',
            'rt_license_section'
        ); 
        
        add_settings_field(
            'rtlc_license_check', // ID
            false, // Title 
            array( $this, 'license_check_callback' ), // Callback
            'classima-setting', // Page
            'rt_license_section' // Section
        ); 
    } 
     

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize_text( $input ) {
        $new_input = array(); 

        if( isset( $input['rt_purchase_code'] ) )
            $new_input['rt_purchase_code'] = sanitize_text_field( $input['rt_purchase_code'] );

        return $new_input;
    }   

    /** 
     * Get the settings option array and print one of its values
     */
    public function purchase_code_callback() { 
        $value = ''; 
        //this first line is for checking old codebase
        if ( isset( $this->options[$this->theme_slug.'_license_key'] ) ) {
            $value = esc_attr( $this->options[$this->theme_slug.'_license_key'] );
        } else if ( isset( $this->options[$this->theme_slug.'_license'] ) && isset( $this->options[$this->theme_slug.'_license']['key'] ) ) {
            $value = esc_attr( $this->options[$this->theme_slug.'_license']['key'] );
        }

        printf(
            '<input type="text" class="regular-text" id="rt_purchase_code" name="rt_license['.$this->theme_slug.'_license_key'.']" value="%s" />',
            $value
        );
    }

    /** 
     * Check license status
     */
    public function license_status_callback() {  
        $verify = false;

        $status_text = esc_html__( 'Not Activated', 'classima' );
         
        if ( rtlc_is_valid()['success'] ) {
            $verify = true;
            $status_text = esc_html__( 'Activated', 'classima' );
        } else if ( isset( rtlc_is_valid()['domain_match'] ) && !rtlc_is_valid()['domain_match'] ) {  
            $status_text = esc_html__( 'Domain Mismtach', 'classima' );
        }
 
        $class = ( $verify ) ? 'verified' : 'unverified';
        echo "<span class='rtlc-status-btn rtlc-{$class}'>{$status_text}</span>";
    } 

    /** 
     * User note
     */
    public function license_note_callback() { 
        $status = esc_html__( 'Please keep in mind, you can activate one license in one domain, if you face any problem in activation, please contact our', 'classima' ).' <a href="https://www.radiustheme.com/contact/">'.esc_html__( 'Support Center', 'classima' ).'</a>';
        echo "<span class='rtcl-note'>{$status}</span> <br><pre>";  
    }

    /** 
     * Active license button
     */
    public function license_check_callback() {
        printf(
            '<input type="button" id="rtlc_license_check" class="button button-primary rtcl-active-btn" value="%s" /> <span class="rtlc-loader"><i class="dashicons dashicons-update spin"></i></span>', esc_html__( 'Activate License', 'classima' )
        );
    }
 

    /** 
     * Ajax action function to verify license
     */
    function rtlc_verification() {   
        $purchase_code = ( !empty( $_REQUEST['purchase_code'] ) ) ? sanitize_text_field( $_REQUEST['purchase_code'] ) : '';

        if ( $purchase_code ) { 
            $rt_license_server = $this->license_url;
            if ( !$rt_license_server ) return;

            $theme_name = $this->theme_name;
            $domian_name = esc_url( home_url() );  

            $api_url = "{$rt_license_server}/?theme_name={$theme_name}&purchase_code=" . $purchase_code . "&domain_name=" . $domian_name;   
            $envato_data = wp_remote_get( $api_url );  
            if ( is_wp_error( $envato_data ) ) {
                return [];
            }

            $envato_data = wp_remote_retrieve_body( $envato_data );  
            
            if ( $envato_data ) { 
                if ( $envato_data == '"true"' ) {  
                    $arr_inputs = get_option('rt_licenses');   
                    $arr_inputs[$this->theme_slug.'_license'] = [
                        'key' => sanitize_text_field( $purchase_code ),
                        'domain' => esc_url( $domian_name ),
                    ]; 
                    
                    update_option('rt_licenses', $arr_inputs);
                }

                echo json_decode( $envato_data ); 
            }
        } 
         
        die();
    }  
}

if ( is_admin() ) {
    new Helper();
}
