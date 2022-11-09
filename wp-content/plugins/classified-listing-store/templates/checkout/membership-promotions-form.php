<?php
/**
 * Membership checkout
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 * @var array      $promotions
 * @var Membership $membership
 * @var int        $listing_id
 */

use RtclStore\Models\Membership;

?>
<div class="rtcl-membership-promotions-form-wrap" style="display: block">
    <?php do_action('rtcl_before_membership_promotion_form', $listing_id, $promotions, $membership); ?>
    <form id="rtcl-membership-promotions-form" method="post" novalidate="novalidate">
        <?php
        do_action('rtcl_membership_promotion_form_start', $listing_id, $promotions, $membership);

        do_action('rtcl_membership_promotion_form', $listing_id, $promotions, $membership);

        do_action('rtcl_membership_promotion_form_submit_button', $listing_id, $promotions, $membership);

        do_action('rtcl_membership_promotion_form_end', $listing_id, $promotions, $membership);
        ?>
    </form>
    <?php do_action('rtcl_after_membership_promotion_form', $listing_id, $promotions, $membership); ?>
</div>