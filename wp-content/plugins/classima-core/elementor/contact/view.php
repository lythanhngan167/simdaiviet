<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Metro_Core;
?>
<div class="rt-el-contact">
	<ul>
		<?php if ( $data['address'] ): ?>
			<li><i class="fas fa-paper-plane" aria-hidden="true"></i> <?php echo esc_html( $data['address'] ); ?></li>
		<?php endif; ?>

		<?php if ( $data['phone'] ): ?>
			<li><i class="fas fa-phone" aria-hidden="true"></i> <a href="tel:<?php echo esc_attr( str_replace( array( ' ', '-' ) , '', $data['phone'] ) ); ?>"><?php echo esc_html( $data['phone'] );?></a></li>
		<?php endif; ?>

		<?php if ( $data['email'] ): ?>
			<li><i class="far fa-envelope" aria-hidden="true"></i> <a href="mailto:<?php echo esc_attr( $data['email'] );?>"><?php echo esc_html( $data['email'] );?></a></li>
		<?php endif; ?>

		<?php if ( $data['fax'] ): ?>
			<li><i class="fas fa-fax" aria-hidden="true"></i> <?php echo esc_html( $data['fax'] );?></li>
		<?php endif; ?>
	</ul>
</div>