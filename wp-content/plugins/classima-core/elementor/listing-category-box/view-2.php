<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

if ( !$data['rt_results'] ) {
	return;
}

$col_class = "col-xl-{$data['col_xl']} col-lg-{$data['col_lg']} col-md-{$data['col_md']} col-sm-{$data['col_sm']} col-{$data['col_mobile']}" ;
?>
<div class="rt-el-listing-cat-box-2">
	<div class="row auto-clear">
		<?php foreach ( $data['rt_results'] as $result ): ?>
			<div class="<?php echo esc_attr( $col_class )?>">
				<a class="rtin-item" href="<?php echo esc_attr( $result['permalink'] );?>">
					<?php if ( $result['icon_html'] ): ?>
						<div class="rtin-icon"><?php echo wp_kses_post( $result['icon_html'] );?></div>
					<?php endif; ?>
					<h3 class="rtin-title"><?php echo esc_html( $result['name'] );?></h3>
					<?php if ( $data['count'] ): ?>
						<div class="rtin-count"><?php echo sprintf('(%s)', $result['count'] );?></div>
					<?php endif; ?>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>