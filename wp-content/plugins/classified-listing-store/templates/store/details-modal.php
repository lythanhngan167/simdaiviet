<?php
/**
 * Modal
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 *
 * @var Store  $store
 * @var string $store_oh_type
 * @var array  $store_oh_hours
 * @var string $today
 */

use Rtcl\Helpers\Functions;
use RtclStore\Models\Store;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $store;
$store_oh_type = get_post_meta($store->get_id(), 'oh_type', true);
$store_oh_hours = get_post_meta($store->get_id(), 'oh_hours', true);
$store_oh_hours = is_array($store_oh_hours) ? $store_oh_hours : ($store_oh_hours ? (array)$store_oh_hours : []);
$today = strtolower(date('l'));
?>
<!-- Modal -->
<div class="modal fade rtcl-bs-modal" id="store-details-modal" tabindex="-1" role="dialog" aria-labelledby="store-details-modal-label"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="store-details-modal-label"><?php $store->the_title(); ?></h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="store-more-details">
                    <div class="more-item store-hours-list-wrap">
                        <h3><?php esc_html_e("Open Hours", "classified-listing-store") ?></h3>
                        <div class="store-hours-list">
                            <?php if ($store_oh_type == "selected"): ?>
                                <?php if (is_array($store_oh_hours) && !empty($store_oh_hours)): ?>
                                    <?php foreach ($store_oh_hours as $hKey => $oh_hour): ?>
                                        <div class="row store-hour<?php echo esc_attr(($hKey == $today) ? ' current-store-hour' : ''); ?>">
                                            <div class="col-4">
                                                <span class="hour-day"><?php echo esc_html($hKey); ?></span>
                                            </div>
                                            <div class="col-8 oh-hours-wrap">
                                                <?php if (isset($oh_hour['active'])): ?>
                                                    <div class="oh-hours">
                                                        <span class="open-hour"><?php echo isset($oh_hour['open']) ? esc_html($oh_hour['open']) : ''; ?></span>
                                                        <span class="close-hour"><?php echo isset($oh_hour['close']) ? esc_html($oh_hour['close']) : ''; ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="off-day"><?php esc_html_e("Closed", "classified-listing-store") ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="always-open"><?php esc_html_e("Permanently Close", "classified-listing-store") ?></div>
                                <?php endif; ?>
                            <?php elseif ($store_oh_type == 'always'): ?>
                                <div class="always-open"><?php esc_html_e("Always Open", "classified-listing-store") ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($store_address = $store->get_address()): ?>
                        <div class="more-item store-address">
                            <h3><?php esc_html_e("Address", "classified-listing-store") ?></h3>
                            <p><?php echo esc_html($store_address); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($store_description = $store->get_the_description()): ?>
                        <div class="more-item store-description">
                            <h3><?php esc_html_e("Description", "classified-listing-store") ?></h3>
                            <div class="store-description-content"><?php echo esc_html($store_description); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($store_website = $store->get_website()): ?>
                        <div class="more-item store-website">
                            <h3><?php esc_html_e("Visit our website", "classified-listing-store") ?></h3>
                            <a target="_blank"
                               href="<?php echo esc_url_raw($store_website) ?>"<?php echo Functions::is_external($store_website) ? ' rel="nofollow"' : ''; ?>><?php echo esc_url_raw($store_website) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>