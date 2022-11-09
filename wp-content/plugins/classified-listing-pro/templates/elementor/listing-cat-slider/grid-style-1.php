<?php
/**
 * @author        RadiusTheme
 *
 * @version       1.0.0
 */

use Rtcl\Helpers\Functions;

?>

<?php

$data = [
    'settings' => $settings,
    'style'    => $style,
];
Functions::get_template('elementor/listing-cat-slider/slider-header', $data, '', $default_template_path);

foreach ($terms as $trm) {
    $count = 0;
    if (!empty($settings['rtcl_hide_empty']) || !empty($settings['rtcl_show_count'])) {
        $count = Functions::get_listings_count_by_taxonomy(
            $trm->term_id,
            rtcl()->category,
            !empty($settings['rtcl_pad_counts']) ? 1 : 0
        );

        if (!empty($settings['rtcl_hide_empty']) && 0 == $count) {
            continue;
        }
    }
    // swiper-slide Start.
    echo '<div class="swiper-slide">';
    $content_alignemnt = !empty($settings['rtcl_content_alignment']) ? $settings['rtcl_content_alignment'] : null;
    echo '<div class="cat-item-wrap equal-item">';
    echo '<div class="cat-details text-' . esc_attr($settings['rtcl_cat_box_alignment']) . ' ' . esc_attr($content_alignemnt) . '">';
    echo '<div class="cat-details-inner">';

    $view_post = sprintf(
    /* translators: %s: Category term */
        __('View all posts in %s', 'classified-listing-pro'),
        $trm->name
    );
    if ($settings['rtcl_show_image']) {
        $icon_image_html = '';
        if ('image' === $settings['rtcl_icon_type']) {
            $image_size = isset($settings['rtcl_icon_image_size_size']) ? $settings['rtcl_icon_image_size_size'] : 'medium';
            if ('custom' === $image_size) {
                $image_size = isset($settings['rtcl_icon_image_size_custom_dimension']) ? $settings['rtcl_icon_image_size_custom_dimension'] : 'medium';
            }
            $image_id = get_term_meta($trm->term_id, '_rtcl_image', true);
            $image_attributes = wp_get_attachment_image_src((int)$image_id, $image_size);
            $image = isset($image_attributes[0]) && !empty($image_attributes[0]) ? $image_attributes[0] : '';
            if ('' !== $image) {
                echo "<div class='image'>";
                $icon_image_html .= '<a href="' . esc_url(get_term_link($trm)) . '" class="rtcl-responsive-container" title="' . esc_attr($view_post) . '">';
                $icon_image_html .= '<img src="' . esc_url($image) . '" class="rtcl-responsive-img" />';
                $icon_image_html .= '</a>';
                echo $icon_image_html;
                echo '</div>';
            }
        }

        if ('icon' === $settings['rtcl_icon_type']) {
            $icon_id = get_term_meta($trm->term_id, '_rtcl_icon', true);
            if ($icon_id) {
                echo "<div class='icon'>";
                printf(
                    '<a href="%s" title="%s"><span class="rtcl-icon rtcl-icon-%s"></span></a>',
                    esc_url(get_term_link($trm)),
                    esc_attr($view_post),
                    esc_attr($icon_id)
                );
                echo '</div>';
            }
        }
    }

    if ($settings['rtcl_show_category_title']) {
        printf(
            "<h3 class='rtcl-category-title'><a href='%s' title='%s'>%s</a></h3>",
            esc_url(get_term_link($trm)),
            esc_attr($view_post),
            esc_html($trm->name)
        );
    }

    if (!empty($settings['rtcl_show_count'])) {
        printf("<div class='views'>%d <span class='ads-count'>%s</span></div>", absint($count), esc_html__('Ads', 'classified-listing-pro'));
    }
    if ($settings['rtcl_description'] && $trm->description) {
        $word_limit = wp_trim_words($trm->description, $settings['rtcl_content_limit']);
        printf('<p>%s</p>', esc_html($word_limit));
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>'; // swiper-slide end.
}

Functions::get_template('elementor/listing-cat-slider/slider-footer', $data, '', $default_template_path);
