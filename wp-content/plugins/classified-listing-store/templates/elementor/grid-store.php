<?php

$column = !empty($instance['rtcl_store_column']) ? $instance['rtcl_store_column'] : 4;

$tab_column    = !empty($instance['rtcl_store_column_tablet']) ? $instance['rtcl_store_column_tablet'] : $column;
$mobile_column = !empty($instance['rtcl_store_column_mobile']) ? $instance['rtcl_store_column_mobile'] : $tab_column;

$col_class = 'column-lg-'.$column.' column-sm-'.$tab_column.' column-xs-'.$mobile_column;

// error_log(print_r($instance['rtcl_store_column_mobile'], true), 3, __DIR__.'/log.txt');

?>
<div class="rtcl-elementor-widget rtcl-el-listing-store-grid <?php echo esc_attr($col_class); ?>">
	<?php foreach ($instance['stores'] as $store) { ?>
		<?php $count_html = sprintf(_nx('%s ad', '%s ads', $store['count'], 'Number of Ads', 'classified-listing-store'), number_format_i18n($store['count'])); ?>
		<!-- <div class="store-item-wrapper"> -->
			<div class="store-item" href="<?php echo esc_attr($store['permalink']); ?>">
				<?php if (!empty($instance['rtcl_show_image'])) { ?>
				<div class="store-logo"><a href="<?php echo $store['permalink']; ?>"><?php echo $store['logo']; ?></a></div>
				<?php } ?>
				<?php if (!empty($instance['rtcl_show_title'])) { ?>
				<h3 class="store-title"><a href="<?php echo $store['permalink']; ?>"><?php echo esc_html($store['title']); ?></a></h3>
				<?php } ?>
				<?php if (!empty($instance['rtcl_show_time'])) { ?>
				<div class="store-time"><?php echo sprintf(esc_html__('Since %s', 'classified-listing-store'), $store['time']); ?></div>
				<?php } ?>
				<?php if (!empty($instance['rtcl_show_count'])) { ?>
				<div class="store-count"><?php echo esc_html($count_html); ?></div>
				<?php } ?>

			</div>
		<!-- </div> -->
	<?php } ?>
</div>