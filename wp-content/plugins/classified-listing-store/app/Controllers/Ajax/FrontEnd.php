<?php

namespace RtclStore\Controllers\Ajax;

use Rtcl\Controllers\Hooks\Filters;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Text;
use Rtcl\Resources\Options as RtclOptions;
use RtclStore\Helpers\Functions as RtclFunctions;
use RtclStore\Models\Store;
use WP_Error;

class FrontEnd
{

    public static function init() {
        add_action('wp_ajax_rtcl_update_store_data', [__CLASS__, 'rtcl_update_store_data']);
        add_action('wp_ajax_rtcl_ajax_store_banner_upload', [__CLASS__, 'rtcl_ajax_store_banner_upload']);
        add_action('wp_ajax_rtcl_ajax_store_banner_delete', [__CLASS__, 'rtcl_ajax_store_banner_delete']);
        add_action('wp_ajax_rtcl_ajax_store_logo_upload', [__CLASS__, 'rtcl_ajax_store_logo_upload']);
        add_action('wp_ajax_rtcl_ajax_store_logo_delete', [__CLASS__, 'rtcl_ajax_store_logo_delete']);
        add_action('wp_ajax_rtcl_ajax_store_send_manager_invitation_by_email', [__CLASS__, 'rtcl_ajax_store_send_manager_invitation_by_email']);
        add_action('wp_ajax_rtcl_ajax_store_remove_manager_by_user_id', [__CLASS__, 'rtcl_ajax_store_remove_manager_by_user_id']);
        add_action('wp_ajax_rtcl_ajax_store_self_rm_store_manager', [__CLASS__, 'rtcl_ajax_store_self_rm_store_manager']);

        add_action('wp_ajax_rtcl_send_mail_to_store_owner', [__CLASS__, 'rtcl_send_mail_to_store_owner']);
        add_action('wp_ajax_nopriv_rtcl_send_mail_to_store_owner', [__CLASS__, 'rtcl_send_mail_to_store_owner']);

        add_action('wp_ajax_rtcl_store_ajax_membership_promotion', [__CLASS__, 'membership_promotion_action']);

        // Store Category
        add_action('wp_ajax_rtcl_store_get_child_category', [__CLASS__, 'get_store_child_category']);
    }

    public static function membership_promotion_action() {
        if (!Functions::verify_nonce()) {
            wp_send_json_error(esc_html__("Authentication error!!", "classified-listing-store"));
        }
        $membership = rtclStore()->factory->get_membership();

        $promotion_data = apply_filters('rtcl_membership_promotion_process_data', [
            'promotions' => Functions::clean($_POST['_rtcl_membership_promotions']),
            'listing_id' => Functions::clean($_POST['listing_id'])
        ], $membership);
        $errors = new \WP_Error();
        do_action('rtcl_membership_promotion_process_data', $promotion_data, $membership, $_REQUEST, $errors);
        $errors = apply_filters('rtcl_membership_promotion_validation_errors', $errors, $promotion_data, $membership, $_REQUEST);
        $response = [];
        if ($membership) {
            $response = $membership->apply_promotion($promotion_data, $errors);
        } else {
            $errors->add('rtcl_membership_promotion_no_membership', __("You have no membership.", "classified-listing-store"));
        }
        if (is_wp_error($errors) && $errors->has_errors()) {
            wp_send_json_error(apply_filters('rtcl_membership_promotion_error_data', $errors->get_error_message(), $errors));
        } else {
            if (!empty($response['success'])) {
                wp_send_json_success(apply_filters('rtcl_membership_promotion_success_data', [
                    'redirect_url' => Link::get_my_account_page_link('listings'),
                    'message'      => esc_html__("Your promotion Successful applied.", "classified-listing-store")
                ]));
            }
        }
    }


    public static function rtcl_send_mail_to_store_owner() {
        $store_id = (int)$_POST["store_id"];
        $name = sanitize_text_field($_POST["name"]);
        $email = sanitize_email($_POST["email"]);
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $message = stripslashes(wp_kses(nl2br($_POST["message"]), [
            'a'      => [
                'href'  => true,
                'title' => true,
            ],
            'br'     => [],
            'ul'     => [],
            'ol'     => [],
            'li'     => [],
            'strong' => []
        ]));
        $data = wp_parse_args([
            'store_id' => $store_id,
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'message'  => $message
        ], $_POST);

        $error = new WP_Error();

        if (!Functions::verify_nonce()) {
            $error->add('rtcl_session_error', __("Your session have been expired.", "classified-listing-store"));
        }
        if (!Functions::is_human('store_contact')) {
            $error->add('rtcl_recaptcha_error', esc_html__('Invalid Captcha: Please try again.', 'classified-listing-store'));
        }
	    $store = rtclStore()->factory->get_store($store_id);
	    if (!$store) {
		    $error->add('rtcl_notfound_error', __("Store is not selected.", "classified-listing-store"));
	    }
        $data['store'] = $store;
        do_action('rtcl_store_contact_form_validation', $error, $data);

        if (is_wp_error($error) && !empty($error->errors)) {
            wp_send_json_error([
                'error' => apply_filters('rtcl_store_contact_form_error', $error->get_error_message(), $error)
            ]);
        }

        if (!rtcl()->mailer()->emails['Store_Contact_Email_To_Owner']->trigger($store_id, $data)) {
            wp_send_json_error(['error' => __("An error to send mail!", "classified-listing-store")]);
        }

        wp_send_json_success(['message' => __("Your e-mail has been sent!", "classified-listing-store")]);

    }

    public static function rtcl_ajax_store_remove_manager_by_user_id() {
        if (!Functions::verify_nonce()) {
            wp_send_json_error(__("Session not valid", "classified-listing-store"));
        }
        if (!$store = RtclFunctions::get_current_user_store()) {
            wp_send_json_error(__("No store found.", "classified-listing-store"));
        }
        if (empty($_POST['manager_user_id']) || !($manager_user_id = absint($_POST['manager_user_id']))) {
            wp_send_json_error(__("Manager id not found.", "classified-listing-store"));
        }
        if (!$user = get_user_by('id', $manager_user_id)) {
            wp_send_json_error(__("Manager not found to remove.", "classified-listing-store"));
        }
        if (!in_array($user->ID, $store->get_manager_ids(), true) && !in_array($user->ID, array_keys($store->get_manager_invitation_list()))) {
            wp_send_json_error(__("Manager not exist.", "classified-listing-store"));
        }
        wp_send_json_success([
            'manager_user_id' => $store->remove_manager($user),
            'message'         => __("Successfully removed.", "classified-listing-store")
        ]);
    }

    public static function rtcl_ajax_store_self_rm_store_manager() {
        if (!Functions::verify_nonce()) {
            wp_send_json_error(__("Session not valid", "classified-listing-store"));
        }
        $current_user_id = get_current_user_id();
        if (!$store = RtclFunctions::get_manager_store($current_user_id)) {
            wp_send_json_error(__("No store found.", "classified-listing-store"));
        }

        if (!in_array($current_user_id, $store->get_manager_ids(), true)) {
            wp_send_json_error(__("Manager not exist.", "classified-listing-store"));
        }
        wp_send_json_success([
            'manager_user_id' => $store->remove_manager($current_user_id),
            'message'         => __("Successfully removed.", "classified-listing-store")
        ]);
    }

    public static function rtcl_ajax_store_send_manager_invitation_by_email() {
        if (!Functions::verify_nonce()) {
            wp_send_json_error(__("Session not valid", "classified-listing-store"));
        }
        if (!$store = RtclFunctions::get_current_user_store()) {
            wp_send_json_error(__("No store found.", "classified-listing-store"));
        }
        if (empty($_POST['email']) || !is_email($_POST['email'])) {
            wp_send_json_error(__("Email is not a valid email", "classified-listing-store"));
        }
        $email = trim($_POST['email']);
        if (!$user = get_user_by('email', $email)) {
            wp_send_json_error(sprintf(__("User not found using this (%s) email", "classified-listing-store"), $email));
        }
        if ($user->ID === $store->owner_id()) {
            wp_send_json_error(__("You are the owner of this store!!", "classified-listing-store"));
        }
        if (absint(get_user_meta($user->ID, '_rtcl_store_id', true))) {
            wp_send_json_error(sprintf(__("This (%s) user already is a manager of a store.", "classified-listing-store"), $email));
        }
        if (in_array($user->ID, array_keys($store->get_manager_invitation_list()), true)) {
            wp_send_json_error(sprintf(__("Invitation already send to this (%s) user.", "classified-listing-store"), $email));
        }
        if (!$store->add_to_manager_invitation_list($user)) {
            wp_send_json_error(__("Internal server error", "classified-listing-store"));
        }
        $name = trim(implode(' ', [$user->first_name, $user->last_name]));
        $name = $name ? $name : $user->display_name;
        $pp_id = absint(get_user_meta($user->ID, '_rtcl_pp_id', true));
        wp_send_json_success([
            'html'    => sprintf('<div class="rtcl-store-manager">
                    <div class="rtcl-store-m-avatar">%s</div>
                    <div class="rtcl-store-m-info">
                        <div class="rtcl-m-info">%s</div>
                        <div class="rtcl-m-info">%s</div>
                        <div class="rtcl-m-info pending"><span>%s</span></div>
                    </div>
                    <span class="rtcl-store-manager-remove rtcl-icon rtcl-icon-trash" data-menager_user_id="%d"></span>
                </div>',
                $pp_id ? wp_get_attachment_image($pp_id, [100, 100]) : get_avatar($user->ID),
                esc_html($name),
                esc_html($email),
                esc_html__("Pending", 'classified-listing-store'),
                $user->ID
            ),
            'message' => sprintf(__("Manager Invitation is sent to this (%s) user.", "classified-listing-store"), $email)
        ]);
    }

    public static function rtcl_ajax_store_logo_delete() {
        $error = true;
        $message = null;
        if ($store = RtclFunctions::get_current_user_store()) {
            $logo_id = absint(get_post_meta($store->get_id(), 'logo_id', true));
            if ($logo_id && wp_delete_attachment($logo_id)) {
                delete_post_meta($store->get_id(), 'logo_id');
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

    public static function rtcl_ajax_store_banner_delete() {
        $error = true;
        $message = null;
        if ($store = RtclFunctions::get_current_user_store()) {
            $banner_id = absint(get_post_meta($store->get_id(), 'banner_id', true));
            if ($banner_id && wp_delete_attachment($banner_id)) {
                delete_post_meta($store->get_id(), 'banner_id');
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

    public static function rtcl_ajax_store_banner_upload() {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $msg = $data = null;
        $error = true;
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
                $store_id = 0;
                if ($store = RtclFunctions::get_current_user_store()) {
                    $store_id = $store->get_id();
                }
                // Check the type of tile. We'll use this as the 'post_mime_type'.
                $filetype = wp_check_filetype(basename($filename));

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
                $store_owner_id = wp_get_current_user()->ID;
                // Create post if does not exist
                if ($store_id < 1) {

                    add_filter("post_type_link", "__return_empty_string");

                    $store_id = wp_insert_post(apply_filters("rtcl_insert_post", array(
                        'post_title'      => '',
                        'post_content'    => '',
                        'post_status'     => 'publish',
                        'post_author'     => 1,
                        'post_type'       => rtclStore()->post_type,
                        'comments_status' => 'closed',
                        'meta_input'      => array(
                            'store_owner_id' => $store_owner_id
                        )
                    )));

                    remove_filter("post_type_link", "__return_empty_string");
                }

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
                    do_action('rtcl_store_meta_data_saved', $store_owner_id, get_post($store_id), $_REQUEST);
                }
            } else {
                $msg = $status['error'];
            }
        } else {
            $msg = esc_html__("Banner image should be selected", "classified-listing-store");
        }

        wp_send_json(array(
            'message' => $msg,
            'error'   => $error,
            'data'    => $data
        ));

    }

    public static function get_store_child_category() {
        Functions::clear_notices();
        $success = false;
        $message = array();
        $cat_id = isset($_POST['term_id']) ? absint($_POST['term_id']) : 0;
        $child_cats = null;
        if ($cat_id) {
            $success = true;
            $childCats = RtclFunctions::get_store_category($cat_id);
            if (!empty($childCats)) {
                $child_cats .= sprintf("<option value=''>%s</option>", esc_html(Text::get_select_category_text()));
                foreach ($childCats as $child_cat) {
                    $child_cats .= "<option value='{$child_cat->term_id}'>{$child_cat->name}</option>";
                }
            }
        } else {
            Functions::add_notice(__("Category not selected.", "classified-listing-store"), 'error');
        }
        if (Functions::notice_count('error')) {
            $message = Functions::get_notices('error');
        }
        Functions::clear_notices();
        $response = array(
            'message'    => $message,
            'success'    => $success,
            'child_cats' => $child_cats,
            'cat_id'     => $cat_id
        );
        wp_send_json($response);
    }

    public static function rtcl_ajax_store_logo_upload() {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $msg = $data = null;
        $error = true;
        if (isset($_FILES['logo'])) {
            Filters::beforeUpload();
            $status = wp_handle_upload($_FILES['logo'], array(
                'test_form' => false
            ));
            Filters::afterUpload();
            if ($status && !isset($status['error'])) {
                // $filename should be the path to a file in the upload directory.
                $filename = $status['file'];

                // The ID of the post this attachment is for.
                $store_id = 0;
                if ($store = RtclFunctions::get_current_user_store()) {
                    $store_id = $store->get_id();
                }
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

                $store_owner_id = wp_get_current_user()->ID;
                // Create post if does not exist
                if ($store_id < 1) {

                    add_filter("post_type_link", "__return_empty_string");

                    $store_id = wp_insert_post(apply_filters("rtcl_insert_post", array(
                        'post_title'      => '',
                        'post_content'    => '',
                        'post_status'     => 'publish',
                        'post_author'     => 1,
                        'post_type'       => rtclStore()->post_type,
                        'comments_status' => 'closed',
                        'meta_input'      => array(
                            'store_owner_id' => wp_get_current_user()->ID
                        )
                    )));

                    remove_filter("post_type_link", "__return_empty_string");
                }

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
                    do_action('rtcl_store_meta_data_saved', $store_owner_id, get_post($store_id), $_REQUEST);
                }
            } else {
                $msg = $status['error'];
            }
        } else {
            $msg = esc_html__("Banner image should be selected", "classified-listing-store");
        }

        wp_send_json(array(
            'message' => $msg,
            'error'   => $error,
            'data'    => $data
        ));

    }

    public static function rtcl_update_store_data() {
        $error = true;
        $data = [];
        $msg = $getStore = null;
        if (Functions::verify_nonce()) {
            $data = $_POST;
            $title = isset($data['name']) ? esc_html($data['name']) : null;
            $slug = isset($data['id']) ? esc_attr($data['id']) : null;
            $content = isset($data['details']) ? esc_textarea($data['details']) : " ";
            $category = isset($data['store-category']) ? absint($data['store-category']) : 0;

            if ($title && ((isset($data['id']) && $slug) || (!isset($data['id']) && !$slug))) {
                $store_arg = array(
                    'post_title'   => $title,
                    'post_name'    => $slug,
                    'post_content' => $content
                );

                $meta = array();
                if (isset($data['meta']) && !empty($data['meta'])) {
                    foreach ($data['meta'] as $mKey => $mValue) {
                        if ('address' == $mKey) {
                            $meta[$mKey] = sanitize_textarea_field($mValue);
                        } else if ('website' == $mKey) {
                            $meta[$mKey] = esc_url_raw($mValue);
                        } else if ('email' == $mKey) {
                            $meta[$mKey] = sanitize_email($mValue);
                        } else if ('social_media' == $mKey) {
                            $mValue = array_filter($mValue);
                            $meta[$mKey] = !empty($mValue) ? array_map('esc_url_raw', $mValue) : '';
                        } else if ('oh_type' == $mKey) {
                            $meta[$mKey] = in_array($mValue, array(
                                'selected',
                                'always'
                            )) ? esc_attr($mValue) : 'selected';
                        } else {
                            $meta[$mKey] = Functions::clean($mValue);
                        }
                    }
                }

                $meta = apply_filters('rtcl_store_mata_data_before_update', $meta, $data, $_REQUEST);
                $store_owner_id = get_current_user_id();
                if ($store = RtclFunctions::get_current_user_store()) {
                    $store_id = $store->get_id();
                    $error = false;
                    $store_arg['ID'] = $store_id;
                    wp_update_post($store_arg);
                    wp_set_post_terms($store_id, $category, rtclStore()->category);
                    if (!empty($meta)) {
                        foreach ($meta as $mKey => $mValue) {
                            update_post_meta($store_id, sanitize_key($mKey), $mValue);
                        }
                    }
                    $store_owner_id = absint(get_post_meta($store->get_id(), 'store_owner_id', true));
                    $msg = esc_html__("Your store is successfully updated.", 'classified-listing-store');
                    do_action('rtcl_store_meta_data_saved', $store_owner_id, $store, $_REQUEST);
                } else {
                    $meta['store_owner_id'] = get_current_user_id();
                    $store_arg['meta_input'] = $meta;
                    $store_arg['post_status'] = 'publish';
                    $store_arg['post_type'] = rtclStore()->post_type;
                    $store_arg['post_author'] = 1;
                    $store_id = wp_insert_post($store_arg);
                    if (!is_wp_error($store_id)) {
                        wp_set_post_terms($store_id, $category, rtclStore()->category);
                        $error = false;
                        $msg = esc_html__("Your store is successfully updated.", 'classified-listing-store');
                        do_action('rtcl_store_meta_data_saved', $store_owner_id, get_post($store_id), $_REQUEST);
                    } else {
                        $msg = $store_id->get_error_message();
                    }

                }

            } else {
                $msg = esc_html__("Please Select required field.", 'classified-listing-store');
            }

        } else {
            $msg = "error";
        }

        wp_send_json(array(
            'error'    => $error,
            'message'  => $msg,
            'response' => $getStore,
            'data'     => $data,
        ));
    }

}