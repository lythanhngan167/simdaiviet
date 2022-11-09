<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$attr = '';
if ( !empty( $data['url']['url'] ) ) {
	$attr  = 'href="' . $data['url']['url'] . '"';
	$attr .= !empty( $data['url']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= !empty( $data['url']['nofollow'] ) ? ' rel="nofollow"' : '';
	$title = '<a ' . $attr . '>' . $data['title'] . '</a>';

	if ( $data['icontype'] == 'image' ) {
		$icon = wp_get_attachment_image( $data['image']['id'], 'full' );
		$icon = '<a ' . $attr . '>' . $icon . '</a>';
	}
	else {
		$icon = '<i class="'.$data['icon'].'" aria-hidden="true"></i>';
		$icon = '<a ' . $attr . '>' . $icon . '</a>';
	}
}
else {
	$title = $data['title'];

	if ( $data['icontype'] == 'image' ) {
		$icon = \radiustheme\Lib\WP_SVG::get_attachment_image( $data['image']['id'], 'full' );
	}
	else {
		$icon = '<i class="'.$data['icon'].'" aria-hidden="true"></i>';
	}
}
?>

<?php if ( $data['style'] == 2 ) { ?>

    <div class="rt-el-info-box-2">
        <?php if ( ! empty($data['block_no']) ): ?>
            <div class="rtin-number"><?php echo esc_html($data['block_no']); ?></div>
        <?php endif; ?>
        <div class="rtin-icon"><?php echo $icon;?></div>
        <div class="rtin-content">
            <h3 class="rtin-title"><?php echo wp_kses_post( $title );?></h3>
            <?php if ( $data['content'] ): ?>
                <p class="rtin-text"><?php echo wp_kses_post( $data['content'] );?></p>
            <?php endif; ?>
        </div>
    </div>

<?php } else { ?>

    <div class="rt-el-info-box">
        <div class="rtin-icon"><?php echo $icon;?></div>
        <div class="rtin-content">
            <h3 class="rtin-title"><?php echo wp_kses_post( $title );?></h3>
            <?php if ( $data['content'] ): ?>
                <p class="rtin-text"><?php echo wp_kses_post( $data['content'] );?></p>
            <?php endif; ?>
        </div>
    </div>

<?php } ?>