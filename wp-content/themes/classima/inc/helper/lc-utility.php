<?php 
/**
 * Check Radius Theme License
 *
 * @since 1.0
 * 
 */
namespace RTLC;

class Utility { 

    /**
     * Start up
     */
    public function __construct() { 
        add_action( 'admin_notices', [$this, 'register_notice'], 1 ); 
        add_action( 'admin_head', [$this, 'style'] );
        add_action( 'admin_footer', [$this, 'script'] );  
    }  

    function register_notice() {
        //if licensed activated and license page return
        if ( rtlc_is_valid()['success'] || ( isset( $_GET['page'] ) && $_GET['page'] == 'rtlc') ) return;

        $class = 'notice notice-error'; 

        if ( isset( rtlc_is_valid()['domain_match'] ) && !rtlc_is_valid()['domain_match'] ) { 
            $message = sprintf(
                wp_kses( 
                    __( '<b>Your license key doesn\'t match your current domain. Please activate the license again for your current domain. <a href="%s">Activate License</a></b>', 'classima' ),
                    [
                        'a' => [ 'href' => [], 'target' => [] ],
                        'b' => []
                    ]
                ),  
                menu_page_url( 'rtlc', false )
            ); 
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );  
        } else {
            $message = sprintf(
                wp_kses( 
                    __( '<b>Please activate your theme using Envato <a href="%s" target="_blank">purchase code</a>, to get full theme functionality and customer support. <a href="%s">Activate License</a></b>', 'classima' ),
                    [
                        'a' => [ 'href' => [], 'target' => [] ],
                        'b' => []
                    ]
                ), 
                esc_url('https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-'),
                menu_page_url( 'rtlc', false )
            ); 
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );  
        } 
    } 

    /** 
     * Added some css style for rt license page
     */
    function style( $path ) { 
        if ( !isset( $_GET['page'] ) || $_GET['page'] != 'rtlc') return; ?> 
        <style>
            .rtlc-status-btn {
                color: white; padding: 5px 15px; border-radius: 5px;
            }
            .rtlc-unverified {
                background: #b32121;
            }
            .rtlc-verified {
                background: #498414;
            }
            .rtcl-note {
                color: red;
            }
            .dashicons.spin {
                animation: dashicons-spin 1s infinite;
                animation-timing-function: linear;
            } 
            @keyframes dashicons-spin {
                0% {
                    transform: rotate( 0deg );
                }
                100% {
                    transform: rotate( 360deg );
                }
            }
            .rtlc-loader {  
                margin-top: 10px;
                display: inline-block; 
                visibility: hidden;
            } 
            #rtlc_license_check:focus {
                box-shadow: none;
            }
            .rtcl-active-btn {
                padding: 5px 20px !important;
                font-size: 14px !important;
            }
            #setting-error-tgmpa {
                display: none;
            }
        </style>
        <?php 
    } 

    /** 
     * Check license by ajax
     */
    function script() { 
        
        if( wp_script_is( 'jquery', 'done' ) && ( isset( $_GET['page'] ) && $_GET['page'] == 'rtlc' ) ) { ?>
        <script type="text/javascript"> 
            jQuery("#rtlc_license_check").on("click", function() {
                let purchase_code = jQuery('#rt_purchase_code').val(); 
                if ( purchase_code ) {
                    jQuery.ajax({
                        type: "post",
                        dataType: "json",
                        url: '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>',
                        data: {
                            action: "rtlc_verification", 
                            purchase_code,   
                        },
                        beforeSend: function() {     
                            jQuery('.rtlc-loader').css("visibility", "inherit");  
                        },
                        success: function( resp ) {   
                            if ( resp === 555 ) {
                                alert('<?php esc_html_e('Purchase code already activated for one domain!!!', 'classima'); ?>');
                            } else {
                                if ( resp ) {
                                    jQuery('.rtlc-status-btn').html('<?php esc_html_e('Activated', 'classima'); ?>');
                                    jQuery('.rtlc-status-btn').removeClass('rtlc-unverified').addClass('rtlc-verified');
                                } else {
                                    alert('<?php esc_html_e('Sorry!!! Purchase code does not match', 'classima'); ?>');
                                }
                            } 
                            jQuery('.rtlc-loader').css("visibility", "hidden");
                        }, 
                    }); 
                } else {
                    alert('<?php esc_html_e('Purchase code is required!!!', 'classima'); ?>');
                } 
            });
        </script> 
        <?php
        }

        if ( wp_script_is( 'jquery', 'done' ) && !rtlc_is_valid()['success'] ) { 
            if ( isset( $_GET['page'] ) && $_GET['page'] == 'fw-backups-demo-content' ) { ?>
                <script type="text/javascript"> 
                    jQuery("#fw-ext-backups-demo-list .theme-actions a").on("click", function(e) { 
                        e.preventDefault();
                        alert('<?php esc_html_e('Please activate your theme using Envato purchase code, to install demo data.', 'classima'); ?>');
                        return false;
                    });
                </script> 
            <?php
            }

            if ( isset( $_GET['page'] ) && $_GET['page'] == 'classima-install-plugins' ) { ?>
                <script type="text/javascript">  
                    jQuery(".row-actions a").on("click", function(e) {  
                        if ( jQuery(this).closest('td').next().has('span').length > 0 ) { //find pre packaged
                            e.preventDefault();
                            alert('<?php esc_html_e('Please activate your theme using Envato purchase code, to use this plugin.', 'classima'); ?>');
                            return false;
                        } 
                    });

                    jQuery(".check-column input").on("change", function(e) {  
                        
                        if ( !jQuery(e.target).is(':checked') ) return;

                        if ( jQuery(this).parent().hasClass('column-cb') ) { //all checked or not 
                            jQuery('table.wp-list-table > tbody  > tr').each(function(index, tr) {   
                                if ( jQuery(tr).find('.column-source').has('span').length > 0 ) { 
                                    jQuery(tr).find('.check-column input').prop('checked', false);
                                }  
                            });
                        } else {
                            if ( jQuery(this).closest('th').next().next().has('span').length > 0 ) { //find pre packaged 
                                jQuery(this).prop('checked', false); 
                                alert('<?php esc_html_e('Please activate your theme using Envato purchase code, to use this plugin.', 'classima'); ?>');
                            } 
                        } 
                    });
                </script> 
            <?php
            }
        }
    }  
     
}

if ( is_admin() ) {
    new Utility();
} 
 
if ( !function_exists('rtlc_is_valid') ) {
    function rtlc_is_valid() {
        $theme_info = wp_get_theme();
        $theme_info = ( $theme_info->parent() ) ? $theme_info->parent() : $theme_info;
        $theme_name = $theme_info->get('Name');  

        //replace space
        $theme_name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $theme_name))); 
        $get_option = get_option('rt_licenses');

        //this first line is for checking old codebase
        if ( isset( $get_option[$theme_name.'_license_key'] ) ) {
            return [
                'success' => true,
                'domain_match' => true
            ];
        } else if ( isset( $get_option[$theme_name.'_license'] ) && isset( $get_option[$theme_name.'_license']['key'] ) ) {
            $domian_name = esc_url( home_url() ); 
            $domain = $get_option[$theme_name.'_license']['domain']; 

            $domian_name = str_replace(['https', 'http'], ['', ''], $domian_name);
            $domain = str_replace(['https', 'http'], ['', ''], $domain); 
            if ( $domian_name == $domain ) {
                return [
                    'success' => true,
                    'domain_match' => true
                ];
            } else {
                return [
                    'success' => false,
                    'domain_match' => false
                ];
            } 
        } else {
            return [
                'success' => false
            ];
        }
    }
}   