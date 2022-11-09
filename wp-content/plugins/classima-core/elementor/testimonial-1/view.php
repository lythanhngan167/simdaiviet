<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-testimonial-1">
	<p class="rtin-content"><?php echo esc_html( $data['content'] );?></p>
	<?php if ( $data['image'] ): ?>
		<div class="rtin-thumb"><?php echo wp_get_attachment_image( $data['image']['id'], 'thumbnail' );?></div>
	<?php endif; ?>
	<h3 class="rtin-name"><?php echo esc_html( $data['name'] );?></h3>
	<?php if ( $data['designation'] ): ?>
		<div class="rtin-designation"><?php echo esc_html( $data['designation'] );?></div>
	<?php endif; ?>
</div>