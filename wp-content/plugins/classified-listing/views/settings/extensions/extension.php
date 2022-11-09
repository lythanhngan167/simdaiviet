<?php

use Rtcl\Resources\Options;

$addons = Options::addons();
?>
<div id="rtcl" class="wrap rtcl-extensions">
	<h1><?php esc_html_e("Get Extensions & Themes", 'classified-listing') ?></h1>
	<div id="rtcl-ext-wrap" class="rtcl">
		<div class="rtcl-product-list">
			<?php
			if (!empty($addons)) {
				foreach ($addons as $addon) {
					$addon = wp_parse_args($addon, [
						'title'    => '',
						'img_url'  => rtcl()->get_assets_uri('images/placeholder.jpg'),
						'demo_url' => '',
						'buy_url'  => '',
					])
					?>
					<div class="rtcl-product">
						<div class="type"><?php echo !empty($addon['type']) ? esc_html($addon['type']) : '' ?></div>
						<img alt="<?php echo esc_attr($addon['title']) ?>"
							 src="<?php echo esc_url($addon['img_url']) ?>">
						<div class="rtcl-product-info">
							<h3 class="rtcl-p-title">
								<a target="_blank" href="<?php echo esc_url($addon['buy_url']) ?>">
									<?php echo esc_attr($addon['title']) ?></a>
							</h3>
							<div class="rtcl-p-action">
								<a class="rtcl__btn btn__buy" target="_blank"
								   href="<?php echo esc_url($addon['buy_url']) ?>"><?php esc_html_e("Buy Now", "classified-listing"); ?></a>
								<a class="rtcl__btn btn__demo" target="_blank"
								   href="<?php echo esc_url($addon['demo_url']) ?>"><?php esc_html_e("Live Demo", "classified-listing"); ?></a>
							</div>
						</div>
					</div>
					<?php
				}
			}
			?>
            <div class="rtcl-product more-text">
                <h3 class="rtcl-p-title">
                    <a target="_blank" href="https://www.radiustheme.com/classified-listing-addons/">More Addons</a>
                </h3>
            </div>
		</div>
	</div>
</div>