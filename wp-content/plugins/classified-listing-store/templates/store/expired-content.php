<?php
/**
 * Store single expired content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.2.31
 */

?>
<?php do_action('rtcl_before_single_store_expired_content'); ?>

<div class="rtcl store-content-wrap">
    <p><?php _e('This store is unavailable due to membership is expired for this store owner.', 'classified-listing-store') ?></p>
</div>

<?php do_action('rtcl_after_single_store_expired_content'); ?>
