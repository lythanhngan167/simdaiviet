<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.2.31
 *
 * @var Store $store
 */

use RtclStore\Models\Store;


if (empty($store)) global $store;

if (empty($store)) return;

$social_media = $store->get_social_media();
if (empty($social_media)) {
    return;
}
foreach ($social_media as $key => $social_media_url) { ?>
    <a href="<?php echo esc_url($social_media_url); ?>" target="_blank"
       rel="nofollow"><i class="rtcl-icon rtcl-icon-<?php echo esc_attr($key); ?>"></i></a>
    <?php
}