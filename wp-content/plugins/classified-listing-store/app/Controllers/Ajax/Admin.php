<?php

namespace RtclStore\Controllers\Ajax;

use Rtcl\Controllers\Hooks\Filters;

class Admin
{

    public static function init() {
        add_action('wp_ajax_rtcl_admin_ajax_store_banner_upload', [__CLASS__, 'rtcl_admin_ajax_store_banner_upload']);
        add_action('wp_ajax_rtcl_admin_ajax_store_banner_delete', [__CLASS__, 'rtcl_admin_ajax_store_banner_delete']);
        add_action('wp_ajax_rtcl_admin_ajax_store_logo_upload', [__CLASS__, 'rtcl_admin_ajax_store_logo_upload']);
        add_action('wp_ajax_rtcl_admin_ajax_store_logo_delete', [__CLASS__, 'rtcl_admin_ajax_store_logo_delete']);
    }

    public static function rtcl_admin_ajax_store_logo_delete() {
        $error = true;
        $message = null;
        $store = get_post(isset($_POST['store_id']) ? absint($_POST['store_id']) : 0);
        if (is_object($store) && rtclStore()->post_type == $store->post_type) {
            $logo_id = absint(get_post_meta($store->ID, 'logo_id', true));
            if ($logo_id && wp_delete_attachment($logo_id)) {
                delete_post_meta($store->ID, 'logo_id');
                $error = false;
                $message = esc_html__("Successfully deleted", "classified-listing-store");
            } else {
                $message = __("File could not be deleted.", "classified-listing-store");
            }
        } else {
            $message = __("No store found to remove logo", "classified-listing-store");
        }

        wp_send_json(array(
            'error'   => $error,
            'message' => $message
        ));
    }

    public static function rtcl_admin_ajax_store_banner_delete() {
        $error = false;
        $message = null;
        $store = get_post(isset($_POST['store_id']) ? absint($_POST['store_id']) : 0);
        if (is_object($store) && rtclStore()->post_type == $store->post_type) {
            $banner_id = absint(get_post_meta($store->ID, 'banner_id', true));
            if ($banner_id && wp_delete_attachment($banner_id)) {
                delete_post_meta($store->ID, 'banner_id');
                $error = false;
                $message = esc_html__("Successfully deleted", "classified-listing-store");
            } else {
                $message = __("File could not be deleted.", "classified-listing-store");
            }
        } else {
            $message = __("No store found to remove banner", "classified-listing-store");
        }

        wp_send_json(array(
            'error'   => $error,
            'message' => $message
        ));
    }

    public static function rtcl_admin_ajax_store_banner_upload() {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $msg = $data = null;
        $error = true;
        $store = get_post(isset($_POST['store_id']) ? absint($_POST['store_id']) : 0);
        if (is_object($store) && rtclStore()->post_type == $store->post_type) {
            if (isset($_FILES['banner'])) {
                Filters::beforeUpload();
                $status = wp_handle_upload($_FILES['banner'], array(
                    'test_form' => false
                ));
                Filters::afterUpload();
                if ($status && !isset($status['error'])) {
                    // $filename should be the path to a file in the upload directory.
                    $filename = $status['file'];

                    // The ID of the post this attachment is for.
                    $store_id = $store->ID;

                    // Check the type of tile. We'll use this as the 'post_mime_type'.
                    $filetype = wp_check_filetype(basename($filename), null);

                    // Get the path to the upload directory.
                    $wp_upload_dir = wp_upload_dir();

                    // Prepare an array of post data for the attachment.
                    $attachment = array(
                        'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );

                    // Insert the attachment.
                    $attach_id = wp_insert_attachment($attachment, $filename, $store_id);
                    if (!is_wp_error($attach_id)) {
                        if ($existing_banner = get_post_meta($store_id, 'banner_id', true)) {
                            wp_delete_attachment($existing_banner);
                        }
                        update_post_meta($store_id, 'banner_id', $attach_id);
                        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                        $src = wp_get_attachment_image_src($attach_id, 'rtcl-store-banner');
                        $data = array(
                            'banner_id' => $attach_id,
                            'src'       => $src[0]
                        );
                        $error = false;
                        $msg = esc_html__("Successfully updated.", "classified-listing-store");
                    }
                } else {
                    $msg = $status['error'];
                }
            } else {
                $msg = esc_html__("Banner image should be selected", "classified-listing-store");
            }
        } else {
            $msg = __("No store found to upload banner", "classified-listing-store");
        }


        wp_send_json(array(
            'message' => $msg,
            'error'   => $error,
            'data'    => $data
        ));

    }

    public static function rtcl_admin_ajax_store_logo_upload() {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $msg = $data = null;
        $error = true;
        $store = get_post(isset($_POST['store_id']) ? absint($_POST['store_id']) : 0);
        if (is_object($store) && rtclStore()->post_type == $store->post_type) {
            if (isset($_FILES['logo'])) {
                $status = wp_handle_upload($_FILES['logo'], array(
                    'test_form' => false
                ));
                if ($status && !isset($status['error'])) {
                    // $filename should be the path to a file in the upload directory.
                    $filename = $status['file'];

                    // The ID of the post this attachment is for.
                    $store_id = $store->ID;
                    // Check the type of tile. We'll use this as the 'post_mime_type'.
                    $filetype = wp_check_filetype(basename($filename), null);

                    // Get the path to the upload directory.
                    $wp_upload_dir = wp_upload_dir();

                    // Prepare an array of post data for the attachment.
                    $attachment = array(
                        'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );

                    // Insert the attachment.
                    $attach_id = wp_insert_attachment($attachment, $filename, $store_id);
                    if (!is_wp_error($attach_id)) {
                        if ($existing_logo = get_post_meta($store_id, 'logo_id', true)) {
                            wp_delete_attachment($existing_logo);
                        }
                        update_post_meta($store_id, 'logo_id', $attach_id);
                        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                        $src = wp_get_attachment_image_src($attach_id, 'rtcl-store-logo');
                        $data = array(
                            'logo_id' => $attach_id,
                            'src'     => $src[0]
                        );
                        $error = false;
                        $msg = esc_html__("Successfully updated.", "classified-listing-store");
                    }
                } else {
                    $msg = $status['error'];
                }
            } else {
                $msg = esc_html__("Logo image should be selected", "classified-listing-store");
            }
        } else {
            $msg = __("No store found to upload logo", "classified-listing-store");
        }


        wp_send_json(array(
            'message' => $msg,
            'error'   => $error,
            'data'    => $data
        ));

    }
}