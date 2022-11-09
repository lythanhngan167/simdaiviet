<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates/compare
 * @version       1.0.0
 *
 * @var array $compare_ids
 */

use Rtcl\Helpers\Link;

if (empty($compare_ids) || !is_array($compare_ids)) return;
?>

<div id="rtcl-compare-panel">
    <div id="rtcl-compare-wrap">
        <div id="rtcl-compare-panel-btn">
            <span class="rtcl-compare-listing-count"><?php echo count($compare_ids) ?></span>
            <i class="rtcl-icon rtcl-icon-exchange"></i>
        </div>
        <h5 class="rtcl-compare-wrap-title"><?php esc_html_e("Compare", 'classified-listing-pro'); ?></h5>
        <div id="rtcl-compare-list">
            <?php
            foreach ($compare_ids as $compare_id) {
                $listing = rtcl()->factory->get_listing($compare_id);
                if ($listing) {
                    ?>
                    <div class="rtcl-compare-item">
                        <a class="rtcl-compare-item-image" href="<?php echo get_the_permalink($listing->get_id()) ?>">
                            <?php $listing->the_thumbnail('thumbnail') ?>
                        </a>
                        <h4 class="rtcl-compare-item-title"><a
                                    href="<?php echo get_the_permalink($listing->get_id()) ?>"><?php $listing->the_title(); ?></a>
                        </h4>
                        <div class="rtcl-compare-remove-wrap">
                            <a class="rtcl-compare-remove"
                               data-listing_id="<?php echo absint($listing->get_id()); ?>"><i
                                        class="rtcl-icon rtcl-icon-trash-empty"></i></a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <div id="rtcl-compare-no-item"><?php esc_html_e("No Items Selected", 'classified-listing-pro'); ?></div>
        <div id="rtcl-compare-btn-wrap">
            <a class="rtcl-compare-btn"
               href="<?php echo esc_url(Link::get_page_permalink('compare_page')); ?>"><?php esc_html_e("Compare", 'classified-listing-pro'); ?></a>
            <a class="rtcl-compare-btn-clear"
               href="javascript():;"><?php esc_html_e("Clear", 'classified-listing-pro'); ?></a>
        </div>
    </div>
</div>
