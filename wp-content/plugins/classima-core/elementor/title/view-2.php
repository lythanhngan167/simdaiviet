<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-title rtin-style-<?php echo esc_attr( $data['style'] );?>">
	<?php if ( $data['subtitle'] ): ?>
		<p class="rtin-subtitle"><?php echo wp_kses_post( $data['subtitle'] );?></p>
	<?php endif; ?>
	<h2 class="rtin-title"><?php echo esc_html( $data['title'] );?></h2>
</div>