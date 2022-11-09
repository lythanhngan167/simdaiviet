<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates/compare
 * @version       1.0.0
 *
 * @var array $compare_ids
 */

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

$listings = [];
$custom_field_ids = [];
if (empty($compare_ids) || !is_array($compare_ids)) {
    ?>
    <p class="rtcl-no-item-found"><?php esc_html_e("No Items Selected", 'classified-listing-pro'); ?></p>
    <?php
} ?>

<div id="rtcl-compare-content" class="rtcl-compare-content rtcl">
    <table class="rtcl-compare-table">
        <thead>
        <tr>
            <th></th>
            <?php
            foreach ($compare_ids as $compare_id) {
                $listing = rtcl()->factory->get_listing($compare_id);
                $cf_ids = Functions::get_custom_field_ids($listing->get_last_child_category_id());
                $custom_field_ids = $custom_field_ids + $cf_ids;
                if ($listing) {
                    $listings[] = $listing;
                    ?>
                    <th class="rtcl-compare-table-head" data-listing_id="<?php echo $listing->get_id() ?>">
                        <div class="rtcl-compare-table-remove">
                            <div class="rtcl-compare-remove" data-listing_id="<?php echo $listing->get_id() ?>">
                                <i class="rtcl-icon rtcl-icon-trash"></i>
                            </div>
                        </div>
                        <div class="rtcl-compare-table-thumb">
                            <a href="<?php echo get_the_permalink($listing->get_id()) ?>">
                                <div class="rtcl-compare-table-image"
                                     style="background-image:url(<?php echo esc_url($listing->get_the_thumbnail_url('medium')) ?>);"></div>
                            </a>
                        </div>
                        <div class="rtcl-compare-table-title">
                            <h3>
                                <a href="<?php echo get_the_permalink($listing->get_id()) ?>"><?php $listing->the_title(); ?></a>
                            </h3>
                        </div>
                    </th>
                    <?php
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($listings)) {
            $labels = [
                'price'    => esc_html__("Price", 'classified-listing-pro'),
                'review'   => esc_html__("Review", 'classified-listing-pro'),
                'category' => esc_html__("Category", 'classified-listing-pro')
            ];
            foreach ($labels as $labelKey => $label) {
                ?>
                <tr class="rtcl-compare-table-item" data-item="<?php echo esc_attr($labelKey) ?>">
                    <th class="rtcl-compare-table-label"><?php echo esc_html($label); ?></th>
                    <?php foreach ($listings as $listing) { ?>
                        <td class="rtcl-compare-table-value" data-value-item="<?php echo esc_attr($labelKey) ?>"
                            data-listing_id="<?php echo $listing->get_id(); ?>">
                            <?php
                            if ("price" === $labelKey) {
                                echo $listing->get_price_html();
                            } else if ("review" === $labelKey) {
                                $average_rating = $listing->get_average_rating();
                                $rating_count = $listing->get_rating_count();


                                echo Fns::get_rating_html($average_rating, $rating_count);
                            } else if ("category" === $labelKey && $category = $listing->get_categories()) {
                                $category = end($category);
                                echo esc_html($category->name);
                            }
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php
            }
            if (!empty($custom_field_ids)) {
                $custom_field_ids = array_unique($custom_field_ids);
                foreach ($custom_field_ids as $custom_field_id) {
                    $cf_label = Functions::get_cf_label($custom_field_id);
                    ?>
                    <tr class="rtcl-compare-table-item" data-item="_cf_<?php echo absint($custom_field_id) ?>">
                        <th class="rtcl-compare-table-label"><?php echo esc_html($cf_label); ?></th>
                        <?php foreach ($listings as $listing) {
                            $cf_data = Functions::get_cf_data($custom_field_id, $listing->get_id(), ['formatted_value' => true]);
                            ?>
                            <td class="rtcl-compare-table-value"
                                data-value-item="_cf_<?php echo absint($custom_field_id) ?>"
                                data-listing_id="<?php echo $listing->get_id(); ?>">
                                <?php
                                echo isset($cf_data['formatted_value']) ? $cf_data['formatted_value'] : '--';
                                ?>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php
                }
            }

        }
        ?>
        </tbody>
    </table>
</div>
