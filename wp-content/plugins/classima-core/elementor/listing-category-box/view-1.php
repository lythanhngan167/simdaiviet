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
<div class="rt-el-listing-cat-box">
	<div class="row auto-clear">
		<?php foreach ( $data['rt_results'] as $result ): ?>
			<div class="<?php echo esc_attr( $col_class )?>">
				<div class="rtin-item-wrap">
					<div class="rtin-item">
						<a class="rtin-title-area" href="<?php echo esc_attr( $result['permalink'] );?>">
							<?php if ( $result['icon_html'] ): ?>
								<div class="rtin-icon"><?php echo wp_kses_post( $result['icon_html'] );?></div>
							<?php endif; ?>
							<h3 class="rtin-title"><?php echo esc_html( $result['name'] );?></h3>
							<?php if ( $data['count'] ): ?>
								<?php
								$count_text = _n( 'Ad', 'Ads', $result['count'], 'classima-core' );
								$count_html = number_format_i18n( $result['count'] ). ' ' . $count_text;
								?>
								<div class="rtin-count"><?php echo esc_html( $count_html );?></div>
							<?php endif; ?>
						</a>
						<ul class="rtin-sub-cats">
							<?php foreach ( $result['sub_cats'] as $sub_cat ): ?>
								<?php
								if ( $data['count'] ) {
									$sub_cat_html = sprintf( '%s (%s)' , $sub_cat['name'], number_format_i18n( $sub_cat['count'] ) );
								}
								else {
									$sub_cat_html = $sub_cat['name'];
								}
								?>
								<li><a href="<?php echo esc_attr( $sub_cat['permalink'] );?>"><?php echo esc_html( $sub_cat_html );?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>