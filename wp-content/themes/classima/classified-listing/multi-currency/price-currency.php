<?php
/**
 * Listing Price unit field
 *
 * @author     RadiusTheme
 * @package    rtcl-multi-currency/templates
 * @version    1.1.0
 *
 * @var string $rtcl_price_currency
 * @var string $default_currency
 * @var array $currencies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$labelColumn = is_admin() ? 'col-sm-2' : 'col-sm-3';
$inputColumn = is_admin() ? 'col-sm-10' : 'col-sm-9';
?>
<div class="row rtcl-pricing-item rtcl-pricing-currency-field rtcl-form-group form-group">
    <div class="<?php echo esc_attr( $labelColumn ); ?> col-12">
        <label class="control-label" for="rtcl-price-currency">
			<?php esc_html_e( 'Currency', 'classima' ); ?>
            <span class="require-star">*</span>
        </label>
    </div>
    <div class="<?php echo esc_attr( $inputColumn ); ?> col-12">
        <select required class="form-control rtcl-select2" id="rtcl-price-currency" name="rtcl_price_currency">
			<?php
			if ( ! empty( $currencies ) ) {
				foreach ( $currencies as $currencyCode => $currencyLabel ) {
					$selected = $rtcl_price_currency ? ( $rtcl_price_currency === $currencyCode ? ' selected' : '' ) : ( $default_currency === $currencyCode ? ' selected' : '' );
					?>
                    <option value="<?php echo esc_attr( $currencyCode ) ?>" <?php echo $selected; ?>><?php echo esc_html( $currencyLabel ); ?></option>
					<?php
				}
			}
			?>
        </select>
    </div>
</div>