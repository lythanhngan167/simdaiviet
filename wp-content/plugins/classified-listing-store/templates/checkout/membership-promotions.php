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

use Rtcl\Resources\Options;
use RtclStore\Models\Membership;

?>
<table id="rtcl-membership-promotions-table"
       class="rtcl-responsive-table form-group table table-hover table-stripped table-bordered">
    <tr>
        <th><?php esc_html_e("Promotions", "classified-listing-store"); ?></th>
        <th><?php esc_html_e("Remaining ads", "classified-listing-store"); ?></th>
        <th class="promotion-validity"><?php _e("Validation Duration<small>(# Days)</small>", "classified-listing-store"); ?></th>
    </tr>
    <?php if (!empty($promotions)) :
        $all_promotions = Options::get_listing_promotions();
        foreach ($promotions as $promotion_key => $promotion) {
            ?>
            <tr>
                <td class="form-check rtcl-membership-promotion-item"
                    data-label="<?php esc_html_e("Promotions:", "classified-listing-store"); ?>">
                    <?php
                    printf('<label><input type="checkbox" name="%s" value="%s" class="rtcl-membership-promotion-input" required/> %s</label>',
                        '_rtcl_membership_promotions[]',
                        esc_attr($promotion_key),
                        !empty($all_promotions[$promotion_key]) ? esc_html($all_promotions[$promotion_key]) : esc_html($promotion_key)
                    );
                    ?>
                </td>
                <td class="rtcl-membership-promotion-ads"
                    data-label="<?php esc_html_e("Remaining ads:", "classified-listing-store"); ?>">
                    <?php !empty($promotion['ads']) ? esc_html_e(absint($promotion['ads'])) : esc_html_e(0); ?>
                </td>
                <td class="rtcl-membership-promotion-validate text-right"
                    data-label="<?php esc_html_e('Validation Duration:', 'classified-listing-store') ?>">
                    <?php printf(__("%d Days", "classified-listing-store"),
                        !empty($promotion['validate']) ? absint($promotion['validate']) : 0
                    ); ?>
                </td>
            </tr>
        <?php } ?>
    <?php endif; ?>
</table>