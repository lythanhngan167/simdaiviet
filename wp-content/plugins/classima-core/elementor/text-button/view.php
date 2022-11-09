<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$btn = $attr = '';

if ( !empty( $data['btnurl']['url'] ) ) {
	$attr  = 'href="' . $data['btnurl']['url'] . '"';
	$attr .= !empty( $data['btnurl']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= !empty( $data['btnurl']['nofollow'] ) ? ' rel="nofollow"' : '';
	
}
if ( !empty( $data['btntext'] ) ) {
	$btn = '<a class="btn rdtheme-button-3" ' . $attr . '>' . $data['btntext'] . '</a>';
}
?>
<div class="rt-el-text-btn">
	<div class="rtin-item">
		<div class="rtin-left">
			<div class="rtin-left-inner">
				<div class="rtin-content">
					<h3 class="rtin-title"><?php echo esc_html( $data['title'] );?></h3>
					<div class="rtin-content"><?php echo wp_kses_post( $data['content'] );?></div>
					<?php if ( $btn ): ?>
						<div class="rtin-btn"><?php echo wp_kses_post( $btn );?></div>
					<?php endif; ?>				
				</div>				
			</div>

		</div>
		<div class="rtin-right"> </div>
	</div>
</div>