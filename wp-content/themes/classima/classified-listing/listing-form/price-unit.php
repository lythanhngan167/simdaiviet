<?php
/**
 * Listing Price unite field
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.0
 *
 * @var array $price_unit_list
 * @var array $price_unit
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (empty($price_units)) {
    return;
}
$labelColumn = is_admin() ? 'col-sm-12' : 'col-sm-3';
$inputColumn = is_admin() ? 'col-sm-12' : 'col-sm-9';
?>
<div class="row" id="rtcl-price-unit-wrap">
    <div class="col-12 <?php echo esc_attr($labelColumn); ?>">
        <label class="control-label"><?php esc_html_e( 'Price Unit', 'classima' ); ?></label>
    </div>
    <div class="col-12 <?php echo esc_attr($inputColumn); ?>">
        <div class="form-group">
            <select class="form-control rtcl-select2" id="rtcl-price-unit" name="_rtcl_price_unit">
                <option value=""><?php esc_html_e("No unit", "classima"); ?></option>
                <?php
                foreach ($price_unit_list as $unit_key => $unit) {
                    if (in_array($unit_key, $price_units)) {
                        echo sprintf('<option value="%s"%s>%s (%s)</label>',
                            esc_attr($unit_key),
                            $price_unit == $unit_key ? " selected" : null,
                            esc_html($unit['title']),
                            esc_html($unit['short'])
                        );
                    }
                }
                ?>
            </select>
        </div>
    </div>
</div>