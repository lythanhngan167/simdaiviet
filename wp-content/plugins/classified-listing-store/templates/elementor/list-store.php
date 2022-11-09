<?php

// $column = !empty($instance['rtcl_store_column']) ? $instance['rtcl_store_column'] : 4;

// $tab_column    = !empty($instance['rtcl_store_column_tablet']) ? $instance['rtcl_store_column_tablet'] : $column;
// $mobile_column = !empty($instance['rtcl_store_column_mobile']) ? $instance['rtcl_store_column_mobile'] : $tab_column;

// $col_class = 'column-lg-'.$column.' column-sm-'.$tab_column.' column-xs-'.$mobile_column;

?>
<div class="rtcl-elementor-widget rtcl-el-listing-store-list  <?php // echo esc_attr($col_class); ?> ">
	<?php foreach ($instance['stores'] as $store) { ?>
		<?php
		$count_text = $store['count'] > 1 ? sprintf(__('%s Ads', 'classified-listing-store'), $store['count']) : sprintf(__('%s Ad', 'classified-listing-store'), $store['count']);
		?>
		<div class="store-item">
			<?php if (!empty($instance['rtcl_show_image'])) { ?>
			<div class="store-left"><a href="<?php echo esc_url($store['permalink']); ?>"><?php echo $store['logo']; ?></a></div>
			<?php } ?>
			<div class="store-right">
				<?php if (!empty($instance['rtcl_show_title'])) { ?>
				<h3 class="store-title"><a href="<?php echo $store['permalink']; ?>"><?php echo $store['title']; ?></a></h3>
				<?php } ?>
				<?php if (!empty($instance['rtcl_show_time'])) { ?>
				<div class="store-time"><?php echo sprintf(esc_html__('Since %s', 'classified-listing-store'), $store['time']); ?></div>
				<?php } ?>
				<?php if (!empty($instance['rtcl_show_count'])) { ?>
				<div class="store-count"><?php echo esc_html($count_text); ?></div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>