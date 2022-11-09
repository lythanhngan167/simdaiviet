<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-title rtin-style-<?php echo esc_attr( $data['style'] );?>">
	<h2 class="rtin-title"><?php echo esc_html( $data['title'] );?></h2>
	<?php if ( $data['subtitle'] && $data['style'] != '2' && $data['style'] != '4' ): ?>
		<p class="rtin-subtitle"><?php echo wp_kses_post( $data['subtitle'] );?></p>
	<?php endif; ?>
</div>