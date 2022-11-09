<?php

/**
 * @author    RadiusTheme
 * @version       1.0.0
 */

use Rtcl\Helpers\Functions;
?>

<?php
$data = array(
	'instance' => $instance,
	'style' => $style,
);
Functions::get_template('block/listing-ads-slider/grid/slider-header', $data, '', $default_template_path);

?>

<?php if (!empty($the_loops['posts'])) { ?>
	<?php foreach ($the_loops['posts'] as $the_loop) { ?>
		<div class="swiper-slide">
			<div <?php Functions::listing_class($the_loop['classes']); ?>>

				<?php if ($instance['content_visibility']["thumbnail"] && !empty($the_loop['thumbnail'])) : ?>
					<div class="listing-thumb">
						<a href="<?php echo esc_url($the_loop['post_link']); ?>" class="rtcl-media"><?php echo wp_kses_post($the_loop['thumbnail']); ?></a>
						<?php if ($instance['content_visibility']["sold"] && !empty($the_loop['sold'])) : ?>
							<?php echo wp_kses_post($the_loop['sold']); ?>
						<?php endif; ?>

						<div class="rtcl-show-lg rtcl-gb-meta-buttons-wrap <?php echo esc_attr($instance['content_visibility']["actionLayout"]); ?> meta-button-count-3">
							<?php if ($instance['content_visibility']["favourit_btn"] && !empty($the_loop['favourite_link'])) : ?>
								<div class="rtcl-gb-button"><?php echo wp_kses_post($the_loop['favourite_link']); ?></div>
							<?php endif; ?>

							<?php if ($instance['content_visibility']["quick_btn"] && !empty($the_loop['quick_view'])) : ?>
								<div class="rtcl-gb-button"><?php echo wp_kses_post($the_loop['quick_view']); ?></div>
							<?php endif; ?>

							<?php if ($instance['content_visibility']["compare_btn"] && !empty($the_loop['compare'])) : ?>
								<div class="rtcl-gb-button"><?php echo wp_kses_post($the_loop['compare']); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>


				<div class="item-content">

					<?php if ($instance['content_visibility']['badge'] && !empty($the_loop['badges'])) : ?>
						<div class="listing-badge-wrap"><?php echo wp_kses_post($the_loop['badges']); ?></div>
					<?php endif; ?>

					<?php if ($instance['content_visibility']['price'] && !empty($the_loop['price'])) : ?>
						<div class="item-price listing-price"><?php echo wp_kses_post($the_loop['price']) ?></div>
					<?php endif; ?>

					<?php if ($instance['content_visibility']['category'] && !empty($the_loop['categories'])) : ?>
						<div class='listing-cat'><?php echo wp_kses_post($the_loop['categories']); ?></div>
					<?php endif; ?>

					<?php if ($instance['content_visibility']['title'] && !empty($the_loop['title'])) { ?>
						<h3 class="listing-title"><a href="<?php echo esc_url($the_loop['post_link']) ?>"><?php echo esc_html($the_loop['title']) ?></a></h3>
					<?php } ?>

					<ul class="rtcl-listing-meta-data">
						<?php if ($instance['content_visibility']['listing_type'] && !empty($the_loop['listing_type'])) : ?>
							<li class="listing-type"><i class="rtcl-icon rtcl-icon-tags"></i><?php echo esc_html($the_loop['listing_type']); ?></li>
						<?php endif; ?>

						<?php if ($instance['content_visibility']['date'] && !empty($the_loop['time'])) : ?>
							<li class="updated"><i class="rtcl-icon rtcl-icon-clock"></i><?php echo esc_html($the_loop['time']); ?></li>
						<?php endif; ?>

						<?php if ($instance['content_visibility']['author'] && !empty($the_loop['author'])) : ?>
							<li class="author"><i class="rtcl-icon rtcl-icon-user"></i><?php echo esc_html($the_loop['author']); ?></li>
						<?php endif; ?>

						<?php if ($instance['content_visibility']['location'] && !empty($the_loop['locations'])) : ?>
							<li class="rt-location"><i class="rtcl-icon rtcl-icon-location"></i><?php Functions::print_html($the_loop['locations']); ?></li>
						<?php endif; ?>

						<?php if ($instance['content_visibility']['view'] && !empty($the_loop['views'])) : ?>
							<li class="rt-view"><i class="rtcl-icon rtcl-icon-eye"></i><?php echo esc_html($the_loop['views']); ?></li>
						<?php endif; ?>
					</ul>

					<?php if ($instance['content_visibility']['grid_content'] && !empty($the_loop['excerpt'])) : ?>
						<?php if ($instance['content_limit'] && !empty($the_loop['excerpt'])) { ?>
							<p class="rtcl-excerpt"><?php echo wp_trim_words(wpautop($the_loop['excerpt']), $instance['content_limit'], ''); ?></p>
						<?php } ?>
					<?php endif; ?>


				</div>

			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php Functions::get_template('block/listing-ads-slider/grid/slider-footer', $data, '', $default_template_path); ?>