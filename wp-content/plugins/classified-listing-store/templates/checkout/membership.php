<?php
/**
 * Membership checkout
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */


use Rtcl\Helpers\Functions;
$currency = Functions::get_order_currency();
$currency_symbol = Functions::get_currency_symbol( $currency );
?>

<table id="rtcl-checkout-pricing-option"
       class="rtcl-responsive-table rtcl-pricing-options form-group table table-hover table-stripped table-bordered rtcl-membership-pricing-options">
    <tr>
        <th><?php esc_html_e( "Membership", "classified-listing-store" ); ?></th>
        <th><?php esc_html_e( "Features", "classified-listing-store" ); ?></th>
        <th><?php printf( esc_html__( 'Price [%s %s]', 'classified-listing-store' ),
                $currency,
                $currency_symbol ); ?></th>
    </tr>
	<?php if ( ! empty( $pricing_options ) ) :
		foreach ( $pricing_options as $option ) :
			$price = get_post_meta( $option->ID, 'price', true );
			?>
            <tr>
                <td class="form-check rtcl-pricing-option"
                    data-label="<?php esc_html_e( "Membership:", "classified-listing-store" ); ?>">
					<?php
					printf( '<label><input type="radio" name="%s" value="%s" class="rtcl-checkout-pricing" required data-price="%s"/> %s</label>',
						'pricing_id', esc_attr( $option->ID ), esc_attr( $price ), esc_html( $option->post_title ) );
					?>
                </td>
                <td class="rtcl-pricing-features"
                    data-label="<?php esc_html_e( "Features:", "classified-listing-store" ); ?>">
					<?php do_action( 'rtcl_membership_features', $option->ID ) ?>
                </td>
                <td class="rtcl-pricing-price text-right"
                    data-label="<?php printf( esc_html__( 'Price [%s %s]:', 'classified-listing-store' ),
                        $currency,
                        $currency_symbol ); ?>"><?php echo Functions::get_payment_formatted_price( $price ); ?> </td>
            </tr>
		<?php endforeach;
	else: ?>
        <tr>
            <th colspan="3"><?php esc_html_e( "No plan found.", "classified-listing-store" ); ?></th>
        </tr>
	<?php endif; ?>
</table>
