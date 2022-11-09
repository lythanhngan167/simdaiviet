<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options as RtclOptions;

class CustomHooks
{

    public static function init() {
        RtclApplyHook::init();
	    add_action('rtcl_membership_features', [__CLASS__, 'membership_features']);
    }

    public static function membership_features($pricing_id) {
        $pricing = rtcl()->factory->get_pricing($pricing_id);
        if ($pricing) {
            $description = $pricing->getDescription();
            $promotions = get_post_meta($pricing->getId(), '_rtcl_membership_promotions', true);
            $promotion_list = RtclOptions::get_listing_promotions();
            ?>
            <div class="rtcl-membership-promotions">
                <div class="promotion-item label-item">
                    <div class="item-label"></div>
                    <div class="item-listings"><?php esc_html_e('Ads', "classified-listing-store") ?></div>
                    <div class="item-validate"><?php esc_html_e('Days', "classified-listing-store") ?></div>
                </div>
                <div class="promotion-item">
                    <div class="item-label"><?php esc_html_e('Regular', "classified-listing-store") ?></div>
                    <div class="item-listings"><?php echo absint(get_post_meta($pricing_id, 'regular_ads', true)) ?></div>
                    <div class="item-validate"><?php echo absint($pricing->getVisible()) ?></div>
                </div>
                <?php
                if (is_array($promotions) && !empty($promotions)) {
                    foreach ($promotions as $promotion_key => $promotion) {
                        ?>
                        <div class="promotion-item">
                            <div class="item-label"><?php esc_html_e($promotion_list[$promotion_key]) ?></div>
                            <div class="item-listings"><?php echo absint($promotion['ads']) ?></div>
                            <div class="item-validate"><?php echo absint($promotion['validate']) ?></div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php if ($description): ?>
                <div class="pricing-description"><?php Functions::print_html($description, true); ?></div>
            <?php endif;
        }
    }

}
