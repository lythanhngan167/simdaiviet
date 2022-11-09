<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$col_class = "col-xl-{$data['col_xl']} col-lg-{$data['col_lg']} col-md-{$data['col_md']} col-sm-{$data['col_sm']} col-{$data['col_mobile']}" ;
?>
<div class="rt-el-listing-store-grid">
	<div class="row auto-clear">
		<?php foreach ( $data['stores'] as $store ): ?>
			<?php $count_html = sprintf( _nx( '%s ad', '%s ads', $store['count'], 'Number of Ads', 'classima-core' ), number_format_i18n( $store['count'] ) );?>
			<div class="<?php echo esc_attr( $col_class )?>">
				<a class="rtin-item" href="<?php echo esc_attr( $store['permalink'] );?>">
					<div class="rtin-logo"><?php echo $store['logo']; ?></div>
					<h3 class="rtin-title"><?php echo esc_html( $store['title'] );?></h3>
					<div class="rtin-count"><?php echo esc_html( $count_html );?></div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>