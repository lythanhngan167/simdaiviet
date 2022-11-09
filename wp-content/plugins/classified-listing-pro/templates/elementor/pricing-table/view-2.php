<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

?>
<div class="rtcl-el-pricing-box rtcl-el-pricing-box-<?php echo esc_html( $style ); ?> content-alignment-<?php echo esc_html( $content_alignment ); ?>">
	<?php if ( $pricing_label ) { ?>
		<span class="pricing-label"><?php echo esc_html( $pricing_label ); ?></span>
	<?php } ?>
	<div class="pricing-header">
		<?php if ( $settings['title'] ) : ?>
			<h3 class="rtcl-el-pricing-title"><?php echo esc_html( $settings['title'] ); ?></h3>
		<?php endif; ?>
		<div class="rtcl-el-pricing-price">
			<span class="rtcl-el-price <?php echo esc_html( $currency_position ); ?>">
				<span class="rtcl-el-pricing-currency"><?php echo esc_html( $settings['currency'] ); ?></span>
				<span class="rtcl-el-number"> <?php echo esc_html( $settings['price'] ); ?> </span>
			</span>	
			<span class="rtcl-el-pricing-duration"> <?php echo !empty( $settings['show_per_sign'] ) ? '/' : ''; ?> <?php echo esc_html( $settings['unit'] ); ?></span>
		</div>
	</div>
	<div class="pricing-body">
		<div class="rtcl-el-pricing-features">
			<?php echo wp_kses_post( $feature_html ); ?>
		</div>
	</div>
	<div class="pricing-footer">
		<?php if ( $btn ) : ?>
			<div class="rtcl-el-pricing-button"><?php echo wp_kses_post( $btn ); ?></div>
		<?php endif; ?>
	</div>
</div>
