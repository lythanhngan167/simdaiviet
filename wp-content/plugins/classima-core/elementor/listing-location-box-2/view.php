<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$count_html = sprintf( _nx( '%s Ad Posted', '%s Ads Posted', $data['count'], 'Number of Ads', 'classima-core' ), number_format_i18n( $data['count'] ) );

$link_start = $data['enable_link'] ? '<a href="'.$data['permalink'].'">' : '';
$link_end   = $data['enable_link'] ? '</a>' : '';

$class = $data['display_count'] ? 'rtin-has-count' : '';
?>
<div class="rt-el-listing-location-box-2 <?php echo esc_attr( $class );?>">

	<?php echo wp_kses_post( $link_start );?>

	<div class="rtin-img"></div>
	<div class="rtin-content">
		<h3 class="rtin-title"><?php echo esc_html( $data['title'] );?></h3>
		<?php if ( $data['display_count'] ): ?>
			<div class="rtin-counter"><?php echo esc_html( $count_html );?></div>
		<?php endif; ?>
	</div>

	<?php echo wp_kses_post( $link_end );?>
</div>