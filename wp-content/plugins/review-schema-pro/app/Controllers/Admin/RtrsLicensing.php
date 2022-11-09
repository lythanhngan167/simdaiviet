<?php
namespace Rtrsp\Controllers\Admin;

use Rtrs\Helpers\Functions;
use Rtrsp\Helpers\Fns;
use Rtrsp\Models\RtrsLicense;

use Rtrs\Models\SettingsAPI;

class RtrsLicensing {

    // Licensing variable
    static private $store_url = 'https://www.radiustheme.com';
    static private $product_id = 152929;
    static private $author = "RadiusTheme";

    static function init() {
        add_action('admin_init', [__CLASS__, 'license']);
        add_action('rtrs_admin_settings_saved', [__CLASS__, 'update_licencing_status']);
        add_action('wp_ajax_rtrs_manage_licensing', [__CLASS__, 'manage_licensing']);
        add_filter('rtrs_tools_settings_options', [__CLASS__, 'add_tools_licensing_options'], 15);
    }

    public static function license() {
        if (Fns::check_license()) {
            $settings = Functions::get_option('rtrs_tools_settings');
            $license_key = !empty($settings['license_key']) ? trim($settings['license_key']) : null;
            $status = !empty($settings['license_status']) && $settings['license_status'] === 'valid';
            new RtrsLicense(static::$store_url, RTRSP_PLUGIN_FILE, array(
                'version' => RTRSP_VERSION,
                'license' => $license_key,
                'item_id' => self::$product_id,
                'author'  => self::$author,
                'url'     => home_url(),
                'beta'    => false,
                'status'  => $status
            ));
        }
    }

    public static function update_licencing_status($action) {
        if ("tools_settings" == $action) {
            $settings = Functions::get_option('rtrs_tools_settings');
            $license_key = !empty($settings['license_key']) ? trim($settings['license_key']) : null;
            $status = !empty($settings['license_status']) && $settings['license_status'] === 'valid';
            if ($license_key && !$status) {
                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license'    => $license_key,
                    'item_id'    => self::$product_id,
                    'url'        => home_url()
                );
                $response = wp_remote_post(self::$store_url,
                    array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                    $err = $response->get_error_message();
                    $message = (is_wp_error($response) && !empty($err)) ? $err : esc_html__('An error occurred, please try again.', 'review-schema-pro');
                } else {
                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if (false === $license_data->success) {
                        switch ($license_data->error) {
                            case 'expired' :
                                $message = sprintf(
                                    esc_html__('Your license key expired on %s.', 'review-schema-pro'),
                                    date_i18n(get_option('date_format'),
                                        strtotime($license_data->expires, current_time('timestamp')))
                                );
                                break;
                            case 'revoked' :
                                $message = esc_html__('Your license key has been disabled.', 'review-schema-pro');
                                break;
                            case 'missing' :
                                $message = esc_html__('Invalid license.', 'review-schema-pro');
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = esc_html__('Your license is not active for this URL.', 'review-schema-pro');
                                break;
                            case 'item_name_mismatch' :
                                $message = esc_html__('This appears to be an invalid license key for Review Schema Pro.', 'review-schema-pro');
                                break;
                            case 'no_activations_left':
                                $message = esc_html__('Your license key has reached its activation limit.', 'review-schema-pro');
                                break;
                            default :
                                $message = esc_html__('An error occurred, please try again.', 'review-schema-pro');
                                break;
                        }
                    }
                    // Check if anything passed on a message constituting a failure
                    if (empty($message) && $license_data->license === 'valid') {
                        $settings['license_status'] = $license_data->license;
                        update_option('rtrs_tools_settings', $settings); 
                        SettingsAPI::add_message(__('Review Schema licence successfully activated', 'review-schema-pro'), 'success');
                    } else { 
                        SettingsAPI::add_error($message ? $message : esc_html__('Error to activation review schema license', 'review-schema-pro'), 'error');
                    }
                }

            } else if (!$license_key && !$status) {
                unset($settings['license_status']);
                update_option('rtrs_tools_settings', $settings);
            }
        }
    }

    public static function manage_licensing() {
        $error = true;
        $type = $value = $data = $message = null;
        $settings = Functions::get_option('rtrs_tools_settings');
        $license_key = !empty($settings['license_key']) ? trim($settings['license_key']) : null;
        if (!empty($_REQUEST['type']) && $_REQUEST['type'] == "license_activate") {
            $api_params = array(
                'edd_action' => 'activate_license',
                'license'    => $license_key,
                'item_id'    => self::$product_id,
                'url'        => home_url()
            );
            $response = wp_remote_post(self::$store_url,
                array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                $err = $response->get_error_message();
                $message = (is_wp_error($response) && !empty($err)) ? $err : esc_html__('An error occurred, please try again.', 'review-schema-pro');
            } else {
                $license_data = json_decode(wp_remote_retrieve_body($response));
                if (false === $license_data->success) {
                    switch ($license_data->error) {
                        case 'expired':
                            $message = sprintf(
                                esc_html__('Your license key expired on %s.', 'review-schema-pro'),
                                date_i18n(get_option('date_format'),
                                    strtotime($license_data->expires, current_time('timestamp')))
                            );
                            break;
                        case 'revoked':
                            $message = esc_html__('Your license key has been disabled.', 'review-schema-pro');
                            break;
                        case 'missing':
                            $message = esc_html__('Invalid license.', 'review-schema-pro');
                            break;
                        case 'invalid':
                        case 'site_inactive':
                            $message = esc_html__('Your license is not active for this URL.', 'review-schema-pro');
                            break;
                        case 'item_name_mismatch':
                            $message = esc_html__('This appears to be an invalid license key for Review Schema Pro.', 'review-schema-pro');
                            break;
                        case 'no_activations_left':
                            $message = esc_html__('Your license key has reached its activation limit.', 'review-schema-pro');
                            break;
                        default:
                            $message = esc_html__('An error occurred, please try again.', 'review-schema-pro');
                            break;
                    }
                }
                // Check if anything passed on a message constituting a failure
                if (empty($message)) {
                    $settings['license_status'] = $license_data->license;
                    update_option('rtrs_tools_settings', $settings);
                    $error = false;
                    $type = 'license_deactivate';
                    $message = esc_html__("License successfully activated", 'review-schema-pro');
                    $value = esc_html__('Deactivate License', 'review-schema-pro');
                }
            }
        }
        if (!empty($_REQUEST['type']) && $_REQUEST['type'] == "license_deactivate") {
            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license'    => $license_key,
                'item_id'    => self::$product_id,
                'url'        => home_url()
            );
            $response = wp_remote_post(self::$store_url, ['timeout' => 15, 'sslverify' => false, 'body' => $api_params]);

            // Make sure there are no errors
            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                $err = $response->get_error_message();
                $message = (is_wp_error($response) && !empty($err)) ? $err : esc_html__('An error occurred, please try again.', 'review-schema-pro');
            } else {
                unset($settings['license_status']);
                update_option('rtrs_tools_settings', $settings);
                $error = false;
                $type = 'license_activate';
                $message = esc_html__("License successfully deactivated", 'review-schema-pro');
                $value = esc_html__('Activate License', 'review-schema-pro');
            }
        }
        $response = array(
            'error' => $error,
            'msg'   => $message,
            'type'  => $type,
            'value' => $value,
            'data'  => $data
        );
        wp_send_json($response);
    }

    public static function add_tools_licensing_options($options) {
        $position = array_search('license_key', array_keys($options));
        if ($position > -1) {
            $settings = Functions::get_option('rtrs_tools_settings');
            $status = !empty($settings['license_status']) && $settings['license_status'] === 'valid';
            $license_status = !empty($settings['license_key']) ? sprintf("<span class='license-status'>%s</span>",
                $status ? "<span data-action='rtrs_manage_licensing' class='button-secondary rtrs-licensing-btn danger license_deactivate'>" . esc_html__("Deactivate License", 'review-schema-pro') . "</span>"
                    : "<span data-action='rtrs_manage_licensing' class='button-secondary rtrs-licensing-btn button-primary license_activate'>" . esc_html__("Activate License", 'review-schema-pro') . "</span>"
            ) : ' ';
            $option = array(
                'license_key' => array(
                    'title'         => esc_html__('License key', 'review-schema-pro'),
                    'type'          => 'text',
                    'class'          => 'regular-text',
                    'wrapper_class' => 'rtrs-license-wrapper',
                    'description'   => $license_status
                )
            );
            Functions::array_insert($options, $position, $option);
        }

        return $options;
    }
}