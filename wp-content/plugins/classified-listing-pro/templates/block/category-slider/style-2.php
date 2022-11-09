<?php

/**
 * @author    RadiusTheme
 * @version       1.0.0
 */

use Rtcl\Helpers\Functions;
?>

<?php
$data = array(
	'settings' => $settings,
	'style' => $style,
);
Functions::get_template('block/category-slider/slider-header', $data, '', $default_template_path);

$icon_type = ($settings['icon_type'] == 'icon') ? 'item-icon' : 'item-image';
$item_class = 'listing-item rtcl-gb-cat-box';
$item_class .= ' rtcl-gb-cat-box-' . $settings['col_style']['style'];
$item_class .= ' ' . $settings['content_visibility']['contentAlign'];

?>

<?php if (!empty($terms)) { ?>
	<?php foreach ($terms as $term) {

		$count_html = null;
		if ($settings['content_visibility']['counter'] && !empty($term['count'])) {
			ob_start();
			$count_data = sprintf(_n('%s Ad', '%s Ads', $term["count"], 'classified-listing'), $term['count']); ?>
			<span class="rtcl-counter">
				<span><?php echo esc_html($count_data); ?></span>
			</span>
		<?php
			$count_html = ob_get_clean();
		} ?>
		<div class="swiper-slide">
			<div class="<?php echo esc_attr($item_class); ?>">
				<div class="rtcl-box-head">
					<?php if ($settings['content_visibility']['icon'] && !empty($term['icon_html'])) : ?>
						<div class="<?php echo esc_attr($icon_type); ?>">
							<a href="<?php echo esc_url($term['permalink']); ?>"><?php echo wp_kses_post($term['icon_html']); ?> </a>
						</div>
					<?php endif; ?>

					<div class="item-content">
						<h3 class="title"><a href="<?php echo esc_url($term['permalink']); ?>"><?php echo esc_html($term['name']); ?></a></h3>

						<?php if (!empty($settings['count_after_text']) && !empty($term['count'])) { ?>
							<div class="counter">
								<span><?php echo esc_html($term['count']); ?></span>
								<span><?php echo esc_html($settings['count_after_text']); ?></span>
							</div>
						<?php } else { ?>
							<div class="counter"> <?php echo wp_kses_post($count_html); ?> </div>
						<?php } ?>

					</div>
				</div>

				<div class="rtcl-box-body">
					<?php if ($settings['content_visibility']['subCat'] && !empty($term['child_html'])) : ?>
						<ul class="rtcl-sub-cats"><?php echo wp_kses_post($term['child_html']); ?></ul>
					<?php endif; ?>
					<?php if ($settings['content_visibility']['catDesc'] && !empty($term['description'])) : ?>
						<p class="content">
							<?php
							if ($settings['content_limit']) {
								echo wp_trim_words($term['description'], $settings['content_limit']);
							} else {
								echo wp_kses_post($term['description']);
							}
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php Functions::get_template('block/category-slider/slider-footer', $data, '', $default_template_path); ?>