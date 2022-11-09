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
<div class="rt-el-listing-cat-box-4">
	<div class="row auto-clear">
		<?php foreach ( $data['rt_results'] as $result ): ?>
			<div class="<?php echo esc_attr( $col_class )?>">
				<div class="rtin-item">
					<?php if ( $result['icon_html'] ): ?>
						<a class="rtin-icon" href="<?php echo esc_attr( $result['permalink'] );?>"><?php echo wp_kses_post( $result['icon_html'] );?></a>
					<?php endif; ?>
					<h3 class="rtin-title"><a href="<?php echo esc_attr( $result['permalink'] );?>"><?php echo esc_html( $result['name'] );?></a></h3>
					<?php if ( $data['count'] ): ?>
						<div class="rtin-count">(<?php echo esc_html( number_format_i18n( $result['count'] ) );?>)</div>
					<?php endif; ?>
					<div class="rtin-content"><?php echo wp_kses_post( wp_trim_words( $result['description'], $data['content_limit'] ) );?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>