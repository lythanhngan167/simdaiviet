<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

if ( $has_map ):?>
	<div class="content-block-gap"></div>
	<div class="site-content-block classima-single-map">
		<div class="main-title-block"><h3 class="main-title"><?php esc_html_e( 'Location', 'classima' );?></h3></div>
		<div class="main-content">
			<div class="embed-responsive embed-responsive-16by9">
				<div class="rtcl-map embed-responsive-item">
					<div class="marker" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>" data-address="<?php echo esc_attr($address); ?>"><?php echo esc_html($address); ?></div>
				</div>
			</div>
		</div>
	</div>
<?php endif;