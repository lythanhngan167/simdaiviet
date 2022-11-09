<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

if ( $data['icontype'] == 'image' ) {
	$icon = \radiustheme\Lib\WP_SVG::get_attachment_image( $data['image']['id'], 'full', true );
}
else {
	$icon = '<i class="'.$data['icon'].'" aria-hidden="true"></i>';
}
?>
<div class="rt-el-counter rtin-<?php echo esc_attr( $data['theme'] );?>">
	<div class="rtin-item clearfix">
		<div class="rtin-left">
			<?php echo $icon;?>
		</div>
		<div class="rtin-right">
			<div class="rtin-counter"><span class="rt-counter-num" data-counterup-time="<?php echo esc_attr( $data['speed'] );?>" data-counterup-delay="<?php echo esc_attr( $data['steps'] );?>"><?php echo esc_html( $data['number'] );?></span><?php echo esc_html( $data['suffix'] );?></div>
			<div class="rtin-title"><?php echo esc_html( $data['title'] );?></div>
		</div>	
	</div>
</div>