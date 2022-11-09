<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$btn = $attr = '';

if ( !empty( $data['buttonurl']['url'] ) ) {
	$attr  = 'href="' . $data['buttonurl']['url'] . '"';
	$attr .= !empty( $data['buttonurl']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= !empty( $data['buttonurl']['nofollow'] ) ? ' rel="nofollow"' : '';
	
}
if ( !empty( $data['buttontext'] ) ) {
	$btn = '<a ' . $attr . '>' . $data['buttontext'] . '</a>';
}
?>
<div class="rt-el-cta-1">
	<div class="container">
		<div class="rtin-item">
			<div class="rtin-left">
				<h3 class="rtin-title"><?php echo wp_kses_post( $data['title1'] );?></h3>
				<p class="rtin-subtitle"><?php echo wp_kses_post( $data['subtitle'] );?></p>				
			</div>
			<div class="rtin-right">
				<?php echo wp_kses_post( $btn );?>
			</div>
		</div>		
	</div>
</div>