<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class StoreMetaHook
{

    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'store_meta_box']);
        add_filter('postbox_classes_store_rtcl_store_information', [__CLASS__, 'add_metabox_classes']);

        add_action('save_post', [__CLASS__, 'save_store_meta_data'], 10, 2);

        add_filter('manage_edit-' . rtcl()->post_type_pricing . '_columns', [__CLASS__, 'pricing_get_columns'], 100);
        add_action('manage_' . rtcl()->post_type_pricing . '_posts_custom_column', [__CLASS__, 'pricing_column_content'], 10, 2);

        add_filter('manage_edit-store_columns', [__CLASS__, 'store_get_columns'], 100);
        add_action('manage_store_posts_custom_column', [__CLASS__, 'store_column_content'], 10, 2);

    }

    static function pricing_get_columns($columns) {

        $new_columns = array(
            'pricing_type' => esc_html__('Pricing Type', 'classified-listing-store')
        );
        $target_column = 'title';

        return Functions::array_insert_after($target_column, $columns, $new_columns);
    }

    static function pricing_column_content($column, $post_id) {
        switch ($column) {
            case 'pricing_type' :
                $pTypes = Options::get_pricing_types();
                $type = get_post_meta($post_id, 'pricing_type', true);
                $type = in_array($type, array_keys($pTypes)) ? $type : 'regular';
                echo !empty($pTypes[$type]) ? $pTypes[$type] : '--';
                break;
        }

    }

    static function store_get_columns($columns) {

        $new_columns = array(
            'owner' => esc_html__('Owner', 'classified-listing-store')
        );
        $target_column = 'title';

        return Functions::array_insert_after($target_column, $columns, $new_columns);
    }

    static function store_column_content($column, $post_id) {
        switch ($column) {
            case 'owner' :
                $user = get_user_by('id', get_post_meta($post_id, 'store_owner_id', true));
                if ($user) {
                    echo esc_html($user->data->display_name);
                }
                break;
        }

    }

    static function add_metabox_classes($classes = array()) {
        array_push($classes, sanitize_html_class('rtcl'));

        return $classes;
    }

    static function store_meta_box() {
        add_meta_box(
            'rtcl_store_information',
            esc_html__('Store Information', 'classified-listing-store'),
            [__CLASS__, 'store_information'],
            rtclStore()->post_type,
            'normal',
            'high'
        );
    }

    static function store_information($post) {
        $name = $post->post_title;
        $banner_id = get_post_meta($post->ID, 'banner_id', true);
        $logo_id = get_post_meta($post->ID, 'logo_id', true);
        $store_owner_id = get_post_meta($post->ID, 'store_owner_id', true);
        $user = get_user_by('id', $store_owner_id);
        $user_string = "";
        if ($user) {
            $user_string = sprintf(
            /* translators: 1: user display name 2: user ID 3: user email */
                esc_html__('%1$s (#%2$s &ndash; %3$s)', 'classified-listing-store'),
                $user->display_name,
                absint($user->ID),
                $user->user_email
            );
        }
        $slogan = get_post_meta($post->ID, 'slogan', true);
        $email = get_post_meta($post->ID, 'email', true);
        $phone = get_post_meta($post->ID, 'phone', true);
        $website = get_post_meta($post->ID, 'website', true);
        $address = get_post_meta($post->ID, 'address', true);
        $oh_type = get_post_meta($post->ID, 'oh_type', true);
        $oh_type = $oh_type ? $oh_type : "always";
        $oh_hours = (array)get_post_meta($post->ID, 'oh_hours', true);
        $max_image_size = Functions::formatBytes(Functions::get_max_upload(), 0);
        $allowed_image_type = implode(', ', (array)Functions::get_option_item('rtcl_misc_settings', 'image_allowed_type', array(
            'png',
            'jpeg',
            'jpg'
        )));
        ?>
        <div class="rtcl-store-settings">

            <div id="rtcl-store-media">
                <div class="form-group">
                    <label><?php esc_html_e("Store Banner", 'classified-listing-store'); ?></label>
                    <div class="rtcl-store-media-item rtcl-store-banner-wrap">
                        <div class="rtcl-store-banner<?php echo esc_attr($banner_id ? '' : ' no-banner'); ?>">
                            <div class="rtcl-media-action">
                                <span class="rtcl-icon-plus add"><?php esc_html_e("Add Banner", "classified-listing-store") ?></span>
                                <span class="rtcl-icon-trash remove"><?php esc_html_e("Delete Banner", "classified-listing-store") ?></span>
                            </div>
                            <div class="banner"><?php echo wp_get_attachment_image($banner_id, 'rtcl-store-banner', '', ["class" => "rtcl-thumbnail", "alt" => $name]); ?>
                            </div>
                        </div>
                        <div class="alert alert-danger mt-2">
                            <?php
                            $banner_size = (array)Functions::get_option_item('rtcl_misc_settings', 'store_banner_size', array(
                                'width'  => 992,
                                'height' => 300,
                                'crop'   => 'yes'
                            ));
                            printf(
                                esc_html__("Recommended image size to (%dx%d)px, Maximum file size %s, Allowed image type (%s)", "classified-listing-store"),
                                absint($banner_size['width']),
                                absint($banner_size['height']),
                                $max_image_size,
                                $allowed_image_type
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><?php esc_html_e("Store Logo", 'classified-listing-store'); ?></label>
                    <div class="rtcl-store-media-item rtcl-store-logo-wrap">
                        <div class="rtcl-store-logo<?php echo esc_attr($logo_id ? '' : ' no-logo'); ?>">
                            <div class="rtcl-media-action">
                                <span class="rtcl-icon-plus add"><?php esc_html_e("Add Logo", "classified-listing-store") ?></span>
                                <span class="rtcl-icon-trash remove"><?php esc_html_e("Delete Logo", "classified-listing-store") ?></span>
                            </div>
                            <div class="logo"><?php echo wp_get_attachment_image($logo_id, 'rtcl-store-logo', '', ["class" => "rtcl-thumbnail", "alt" => $name]); ?></div>
                        </div>
                        <div class="alert alert-danger mt-2">
                            <?php
                            $logo_size = Functions::get_option_item('rtcl_misc_settings', 'store_logo_size', array(
                                'width'  => 200,
                                'height' => 150,
                                'crop'   => 'yes'
                            ));
                            printf(
                                esc_html__("Recommended image size to (%dx%d)px, Maximum file size %s, Allowed image types %s", "classified-listing-store"),
                                absint($logo_size['width']),
                                absint($logo_size['height']),
                                $max_image_size,
                                $allowed_image_type
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="rtcl-store-hours">
                <div class="form-group">
                    <label><?php esc_html_e("Opening hours", "classified-listing-store") ?></label>
                    <div class="oh-list-wrap">
                        <div class="form-group">
                            <div id="oh-type-wrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="oh-type"
                                           id="oh-type-open-on-selected"
                                           value="selected" <?php checked("selected", $oh_type) ?>>
                                    <label class="form-check-label"
                                           for="oh-type-open-on-selected"><?php _e("Open on selected hours", "classified-listing-store") ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="oh-type" id="oh-type-always-open"
                                           value="always" <?php checked("always", $oh_type) ?>>
                                    <label class="form-check-label"
                                           for="oh-type-always-open"><?php _e("Always open", "classified-listing-store") ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group"
                             id="oh-list" <?php if ($oh_type !== 'selected') { ?> style="display:none" <?php } ?>>
                            <?php
                            $days = array(
                                'sunday'    => esc_html__("Sunday", "classified-listing-store"),
                                'monday'    => esc_html__("Monday", "classified-listing-store"),
                                'tuesday'   => esc_html__("Tuesday", "classified-listing-store"),
                                'wednesday' => esc_html__("Wednesday", "classified-listing-store"),
                                'thursday'  => esc_html__("Thursday", "classified-listing-store"),
                                'friday'    => esc_html__("Friday", "classified-listing-store"),
                                'saturday'  => esc_html__("Saturday", "classified-listing-store"),
                            );
                            foreach ($days as $dayKey => $day) {
                                $idDay = "oh-" . $dayKey . "-active";
                                ?>
                                <div class="oh-item">
                                    <table>
                                        <tr>
                                            <td class="oh-time-active"><input
                                                        id="<?php echo esc_attr($idDay); ?>"
                                                        name="oh[<?php echo esc_attr($dayKey); ?>][active]"
                                                        value="1" <?php checked(1, isset($oh_hours[$dayKey]['active']) ? 1 : 0) ?>
                                                        autocomplete="off"
                                                        type="checkbox"></td>
                                            <td class="oh-time-day"><?php echo esc_html($day) ?></td>
                                            <td class="oh-time-hour">
                                                <div class="oh-time"><input type="text"
                                                                            value="<?php echo isset($oh_hours[$dayKey]['open']) ? esc_attr($oh_hours[$dayKey]['open']) : null; ?>"
                                                                            autocomplete="off"
                                                                            name="oh[<?php echo esc_attr($dayKey); ?>][open]"
                                                                            class="form-control open-hour"> - <input
                                                            value="<?php echo isset($oh_hours[$dayKey]['open']) ? esc_attr($oh_hours[$dayKey]['close']) : null; ?>"
                                                            type="text"
                                                            name="oh[<?php echo esc_attr($dayKey); ?>][close]"
                                                            autocomplete="off"
                                                            class="form-control close-hour"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="rtcl-email"
                       class="col-sm-3 control-label"><?php _e('Store Owner', 'classified-listing-store'); ?></label>
                <div class="col-sm-9">
                    <select class="form-control rtcl-ajax-select" data-action="rtcl_json_search_users"
                            name="store_owner_id"
                            data-placeholder="<?php esc_attr_e('Store Owner', 'classified-listing-store'); ?>">
                        <option value="<?php echo esc_attr($store_owner_id); ?>"
                                selected="selected"><?php echo htmlspecialchars(wp_kses_post($user_string)); // htmlspecialchars to prevent XSS when rendered by selectWoo. ?>
                        <option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="rtcl-last-name"
                       class="col-sm-3 control-label"><?php _e('Slogan', 'classified-listing-store'); ?></label>
                <div class="col-sm-9">
                    <input type="text" name="slogan" id="rtcl-last-name" value="<?php echo esc_attr($slogan); ?>"
                           class="form-control"/>
                </div>
            </div>

            <div class="form-group row">
                <label for="rtcl-email"
                       class="col-sm-3 control-label"><?php _e('Store E-mail Address', 'classified-listing-store'); ?></label>
                <div class="col-sm-9">
                    <input type="text" name="email" id="rtcl-email" class="form-control"
                           value="<?php echo esc_attr($email); ?>"/>
                </div>
            </div>

            <div class="form-group row">
                <label for="rtcl-last-name"
                       class="col-sm-3 control-label"><?php _e('Store Phone', 'classified-listing-store'); ?></label>
                <div class="col-sm-9">
                    <input type="text" name="phone" id="rtcl-phone" value="<?php echo esc_attr($phone) ?>"
                           class="form-control"/>
                </div>
            </div>
            <div class="form-group row">
                <label for="rtcl-last-name"
                       class="col-sm-3 control-label"><?php _e('Store Website', 'classified-listing-store'); ?></label>
                <div class="col-sm-9">
                    <input type="url" name="website" id="rtcl-website" value="<?php echo esc_url($website); ?>"
                           class="form-control"/>
                </div>
            </div>
            <div class="form-group row">
                <label for="rtcl-last-name"
                       class="col-sm-3 control-label"><?php _e('Store Address', 'classified-listing-store'); ?></label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="address"><?php echo esc_textarea($address) ?></textarea>
                </div>
            </div>
        </div>
        <?php
    }

    static function save_store_meta_data($post_id, $post) {

        if (!isset($_POST['post_type'])) {
            return $post_id;
        }

        if (rtclStore()->post_type != $post->post_type) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the logged in user has permission to edit this post
        if (!current_user_can('manage_rtcl_options')) {
            return $post_id;
        }

        // Open hours type
        if (isset($_POST['oh-type'])) {
            $oh_type = (in_array($_POST['oh-type'], array(
                'selected',
                'always'
            ))) ? sanitize_text_field($_POST['oh-type']) : 'always';
            update_post_meta($post_id, 'oh_type', $oh_type);
        }

        // open hours
        if (isset($_POST['oh'])) {
            if ($oh_type === 'selected' && !empty($_POST['oh'])) {
                update_post_meta($post_id, 'oh_hours', $_POST['oh']);
            } else {
                delete_post_meta($post_id, 'oh_hours');
            }
        }

        // store_owner_id
        $store_owner_id = get_post_meta($post_id, 'store_owner_id', true);
        if (isset($_POST['store_owner_id'])) {
            $store_owner_id = absint($_POST['store_owner_id']);
            update_post_meta($post_id, 'store_owner_id', $store_owner_id);
        }

        // Slogan
        if (isset($_POST['slogan'])) {
            $slogan = sanitize_text_field($_POST['slogan']);
            update_post_meta($post_id, 'slogan', $slogan);
        }

        // Email
        if (isset($_POST['email'])) {
            $email = sanitize_email($_POST['email']);
            update_post_meta($post_id, 'email', $email);
        }

        // Phone
        if (isset($_POST['phone'])) {
            $phone = sanitize_text_field($_POST['phone']);
            update_post_meta($post_id, 'phone', $phone);
        }

        // Website
        if (isset($_POST['website'])) {
            $website = esc_url_raw($_POST['website']);
            update_post_meta($post_id, 'website', $website);
        }

        // Address
        if (isset($_POST['address'])) {
            $address = sanitize_textarea_field($_POST['address']);
            update_post_meta($post_id, 'address', $address);
        }

        do_action('rtcl_store_meta_data_saved', $store_owner_id, $post, $_REQUEST);

    }

}