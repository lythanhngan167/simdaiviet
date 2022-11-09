<?php

/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

use Rtcl\Helpers\BlockFns;

$wrap_class = '';
if (isset($settings['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $settings['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($settings['className'])) {
	$wrap_class .= $settings['className'];
}
?>
<div class="<?php echo esc_attr($wrap_class); ?>">
	<div class="rtcl-gb-pricing-box rtcl-gb-pricing-box-view-<?php echo esc_html($style); ?> content-alignment-<?php echo esc_html($content_alignment); ?>">
		<?php if ($pricing_label) { ?>
			<span class="pricing-label"><?php echo esc_html($pricing_label); ?></span>
		<?php } ?>
		<div class="pricing-header">
			<?php if ($settings['title']) : ?>
				<h3 class="rtcl-gb-pricing-title"><?php echo esc_html($settings['title']); ?></h3>
			<?php endif; ?>
			<div class="rtcl-gb-pricing-price">
				<span class="rtcl-gb-price <?php echo esc_html($currency_position); ?>">
					<span class="rtcl-gb-pricing-currency"><?php echo esc_html($settings['currency']); ?></span>
					<span class="rtcl-gb-number"> <?php echo esc_html($settings['price']); ?> </span>
				</span>
				<span class="rtcl-gb-pricing-duration">/<?php echo esc_html($settings['unit']); ?></span>
			</div>
		</div>
		<div class="pricing-body">
			<div class="rtcl-gb-pricing-features">
				<?php echo wp_kses($feature_html, BlockFns::kses_allowed_svg()); ?>
			</div>
		</div>
		<div class="pricing-footer">
			<?php if ($btn) : ?>
				<div class="rtcl-gb-pricing-button"><?php echo wp_kses($btn, BlockFns::kses_allowed_svg()); ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>