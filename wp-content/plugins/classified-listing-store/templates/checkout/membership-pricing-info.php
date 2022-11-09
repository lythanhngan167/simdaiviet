<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var Payment $payment
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options;

?>
<div class="pricing-info membership-pricing-info">
    <table class="table table-bordered table-striped">
        <tr>
            <th colspan="2"><?php esc_html_e("Details", "classified-listing-store"); ?></th>
        </tr>
        <tr>
            <td class="text-right rtcl-vertical-middle"><?php esc_html_e('Membership Title', 'classified-listing-store'); ?></td>
            <td><?php echo esc_html($payment->pricing->getTitle()); ?></td>
        </tr>
        <tr>
            <td class="text-right"><?php esc_html_e('Features', 'classified-listing-store'); ?></td>
            <td class="features">
                <?php do_action('rtcl_membership_features', $payment->pricing->getId()) ?>
            </td>
        </tr>
        <?php do_action('rtcl_payment_receipt_details_before_total_amount', $payment ); ?>
        <tr>
            <td class="text-right rtcl-vertical-middle"><?php esc_html_e('Amount ', 'classified-listing-store'); ?></td>
            <td><?php echo Functions::get_payment_formatted_price_html( $payment->get_total() ); ?></td>
        </tr>
    </table>
</div>
