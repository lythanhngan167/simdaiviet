<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-title-animated">
	<h1 class="rtin-title"><span class="title-typejs" data-options="<?php echo esc_attr( $data['options'] );?>"></span>&nbsp;</h1>
	<?php if ( $data['subtitle'] ): ?>
		<p class="rtin-subtitle"><?php echo wp_kses_post( $data['subtitle'] );?></p>
	<?php endif; ?>
</div>