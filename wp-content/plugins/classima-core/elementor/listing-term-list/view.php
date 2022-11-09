<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-listing-term-list">
	<div class="sidebar-widget-area">
		<div class="widget rtcl rtcl-widget-filter-class">
			<h3 class="widgettitle"><?php echo esc_html( $data['title'] );?></h3>
			<div class="panel-block">
				<form class="rtcl-filter-form" method="GET">
					<div class="ui-accordion">
						<?php echo $data['filter']; ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>