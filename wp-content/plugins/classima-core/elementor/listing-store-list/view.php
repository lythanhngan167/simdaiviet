<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;
?>
<div class="rt-el-listing-store-list">
	<?php foreach ( $data['stores'] as $store ): ?>
		<?php
		$count_text = $store['count'] > 1 ? sprintf( __( '%s Ads', 'classima-core' ) , $store['count'] ) : sprintf( __( '%s Ad', 'classima-core' ) , $store['count'] );
		?>
		<div class="rtin-item">
			<div class="rtin-left"><a href="<?php echo esc_url( $store['permalink'] ); ?>"><?php echo $store['logo']; ?></a></div>
			<div class="rtin-right">
				<h3 class="rtin-title"><a href="<?php echo $store['permalink']; ?>"><?php echo $store['title']; ?></a></h3>
				<div class="rtin-time"><?php echo sprintf( esc_html__( 'Since %s', 'classima-core' ) , $store['time'] ); ?></div>
				<div class="rtin-count"><?php echo esc_html( $count_text ); ?></div>
			</div>
		</div>
	<?php endforeach; ?>
</div>