<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var array   $data
 * @var Listing $listing
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Rtcl\Models\Listing;

if (!$listing) return;
?>
<div class="rtcl-quick-view-container">
    <div class="rtcl-qv-row">
        <div class="rtcl-qv-gallery">
            <?php do_action('rtcl_quick_view_gallery', $listing); ?>
        </div>
        <div class="rtcl-qv-summary">
            <?php do_action('rtcl_quick_view_summary', $listing); ?>
        </div>
    </div>
</div>

