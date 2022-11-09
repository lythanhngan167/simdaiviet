<?php
/**
 * Show map view
 *
 * @package    classified-listing/Templates
 * @version    1.0.0
 *
 * @var $instance array
 */

if (!defined('ABSPATH')) {
    exit;
} ?>

<div class="rtcl-map-view" data-map-data='<?php echo htmlspecialchars(wp_json_encode(($instance['items']))); ?>'></div>
