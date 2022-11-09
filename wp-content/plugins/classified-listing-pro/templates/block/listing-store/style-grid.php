<?php

$wrap_class = '';
if (isset($settings['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $settings['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($settings['className'])) {
	$wrap_class .= $settings['className'];
}

$col_class = "col-xl-{$settings['col_xl']} col-lg-{$settings['col_lg']} col-md-{$settings['col_md']} col-sm-{$settings['col_sm']} col-{$settings['col_mobile']}";


?>
<div class="<?php echo esc_attr($wrap_class); ?>">
	<div class="rtcl rtcl-gb-listing-store style-<?php echo esc_attr($settings['style']); ?>">
		<div class="row auto-clear">

			<?php foreach ($stores as $store) : ?>
				<div class="rtcl-col-wrap <?php echo esc_attr($col_class); ?>">
					<div class="rtcl-item">
						<?php if ($settings['show_logo'] && $store['logo']) : ?>
							<div class="rtcl-logo">
								<a href="<?php echo esc_url($store['permalink']); ?>"><?php echo wp_kses_post($store['logo']); ?></a>
							</div>
						<?php endif; ?>

						<?php if ($settings['show_title'] && $store['title']) : ?>
							<h3 class="rtcl-title">
								<a href="<?php echo esc_url($store['permalink']); ?>"><?php echo esc_html($store['title']); ?></a>
							</h3>
						<?php endif; ?>

						<?php if ($settings['show_count'] && $store['count']) :
							$count_html = sprintf(_nx('%s Ad', '%s Ads', $store['count'], 'Number of Ads', 'classified-listing'), number_format_i18n($store['count']));
						?>
							<div class="rtcl-count"><?php echo wp_kses_post($count_html); ?></div>
						<?php endif; ?>

						<?php if ($settings['show_desc'] && $store['description']) : ?>
							<p class="rtcl-description">
								<?php
								if ($settings['desc_limit']) {
									echo wp_trim_words($store['description'], $settings['desc_limit']);
								} else {
									echo wp_kses_post($store['description']);
								}
								?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>

		</div>
	</div>
</div>