<?php
/**
 * Custom Field
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var string $field
 * @var string $id
 * @var string $field_attr
 * @var string $label
 * @var string $required_label
 * @var string $description
 */

use Rtcl\Helpers\Functions;

$required_label = $required_label ? '<span> *</span>' : '';
$label .= $required_label;

$labelColumn = is_admin() ? 'col-sm-2' : 'col-sm-3';
$inputColumn = is_admin() ? 'col-sm-10' : 'col-sm-9';
?>
<div class="row rtcl-cf-wrap"<?php Functions::esc_attrs_e($field_attr) ?>>
	<div class="col-12 <?php echo esc_attr($labelColumn); ?>">
		<label for="<?php echo esc_attr($id) ?>" class="control-label rtcl-cf-label"><?php echo wp_kses_post( $label ); ?></label>
	</div>
	<div class="col-12 rtcl-cf-field-wrap <?php echo esc_attr($inputColumn); ?>">
		<div class="form-group">
            <?php Functions::print_html($field, true); ?>
			<div class='help-block with-errors'></div>
			<?php if ( $description ) : ?>
				<small class='help-block'><?php echo esc_html( $description ); ?></small>
			<?php endif; ?>
		</div>
	</div>
</div>