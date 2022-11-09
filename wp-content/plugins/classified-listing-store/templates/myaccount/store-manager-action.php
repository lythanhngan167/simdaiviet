<?php

use RtclStore\Models\Store;

/**
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 *
 * @var \WP_User $current_user
 * @var Store    $store
 */

if (!$store)
    return;
?>

<div class="rtcl-store-manager-action">
    <p><?php printf(esc_html__("You are a manager of the store : %s", "classified-listing-store"), sprintf('<a href="%s">%s</a>', esc_url($store->get_the_permalink()), $store->get_the_title())) ?></p>
    <button type="button"
            class="btn btn-danger rtcl-self-rm-store-manager"><?php esc_html_e("Remove from store manager", "classified-listing-store"); ?></button>
</div>
