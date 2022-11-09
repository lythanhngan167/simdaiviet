<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$btn = $attr = $icon = '';

if (!empty($data['icon'])) {
	$icon = '<i class="'.$data['icon'].'" aria-hidden="true"></i>';
}

if ( !empty( $data['btnurl']['url'] ) ) {
	$attr  = 'href="' . $data['btnurl']['url'] . '"';
	$attr .= !empty( $data['btnurl']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= !empty( $data['btnurl']['nofollow'] ) ? ' rel="nofollow"' : '';
	
}
if ( !empty( $data['btntext'] ) ) {
	$btn = '<a class="rt-btn--style2" ' . $attr . '>' . $data['btntext'] . $icon . '</a>';
}
?>

<div class="rt-btn-animated-icon">
	<?php if ( $btn ): ?>
		<?php echo wp_kses_post( $btn ); ?>
	<?php endif; ?>
</div>