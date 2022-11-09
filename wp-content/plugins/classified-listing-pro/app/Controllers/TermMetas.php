<?php

namespace RtclPro\Controllers;

class TermMetas
{
    public static function init() {
        add_action(rtcl()->category . '_add_form_fields', [__CLASS__, 'category_add_map_icon_field']);
        add_action(rtcl()->category . '_edit_form_fields', [__CLASS__, 'category_edit_map_icon_field']);
        add_action('edited_' . rtcl()->category, [__CLASS__, 'save_category_meta']);
        add_action('create_' . rtcl()->category, [__CLASS__, 'save_category_meta']);
    }


    /**
     * @param $term_id
     */
    static function save_category_meta($term_id) {
        if (isset($_POST['_rtcl_map_icon'])) {
            update_term_meta($term_id, '_rtcl_map_icon', absint($_POST['_rtcl_map_icon']));
        }
    }

    static function category_add_map_icon_field() {
        ?>
        <div class="form-field rtcl-term-group-wrap">
            <label for="rtcl-category-map-icon-id"><?php esc_html_e('Map Icon', 'classified-listing-pro'); ?></label>
            <input type="hidden" class="rtcl-category-image-id" id="rtcl-category-image-id" name="_rtcl_map_icon"/>
            <div class="rtcl-categories-image-wrapper"></div>
            <p>
                <input type="button" class="button button-secondary rtcl-categories-upload-image"
                       value="<?php esc_html_e('Add Map Icon', 'classified-listing-pro'); ?>"/>
                <input type="button" class="button button-secondary rtcl-categories-remove-image"
                       id="rtcl-categories-remove-map-icon"
                       value="<?php esc_html_e('Remove Icon', 'classified-listing-pro'); ?>"/>
            </p>
        </div>
        <?php
    }

    /**
     * @param \WP_Term $term
     */
    static function category_edit_map_icon_field($term) {
        $t_id = $term->term_id;
        $map_icon_id = absint(get_term_meta($t_id, "_rtcl_map_icon", true));
        $map_icon_src = $map_icon_id ? wp_get_attachment_thumb_url($map_icon_id) : '';
        ?>
        <tr class="form-field rtcl-term-group-wrap">
            <th scope="row">
                <label for="rtcl-category-image-id"><?php esc_html_e('Map Icon', 'classified-listing-pro'); ?></label>
            </th>
            <td>
                <input type="hidden" class="rtcl-category-image-id" id="rtcl-category-image-id" name="_rtcl_map_icon"
                       value="<?php echo $map_icon_id; ?>"/>
                <div class="rtcl-categories-image-wrapper">
                    <?php if ($map_icon_src) : ?>
                        <img src="<?php echo $map_icon_src; ?>" alt="<?php echo esc_attr($term->name) ?>"/>
                    <?php endif; ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary rtcl-categories-upload-image"
                           id="rtcl-categories-upload-map-icon"
                           value="<?php esc_html_e('Add Map Icon', 'classified-listing-pro'); ?>"/>
                    <input type="button" class="button button-secondary rtcl-categories-remove-image"
                           id="rtcl-categories-remove-map-icon"
                           value="<?php esc_html_e('Remove Map Icon', 'classified-listing-pro'); ?>"/>
                </p>
            </td>
        </tr>
        <?php
    }
}