<?php
/**
 * @author  RadiusTheme
 * @since   1.3.4
 * @version 1.3.4
 */

namespace radiustheme\Classima;

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) {
	return;
}

$footer_columns = 0;

foreach ( range( 1, 4 ) as $i ) {
	if ( is_active_sidebar( 'footer-'. $i ) ){
		$footer_columns++;
	}
}

switch ( $footer_columns ) {
	case '1':
	$footer_class = 'col-sm-12 col-12';
	break;
	case '2':
	$footer_class = 'col-sm-6 col-12';
	break;
	case '3':
	$footer_class = 'col-md-4 col-sm-12 col-12';
	break;
	default:
	$footer_class = 'col-lg-3 col-sm-6 col-12';
	break;
}
$copyright_class = RDTheme::$options['payment_icons'] ? 'col-md-8 col-sm-12 col-12' : 'col-sm-12 col-12 text-center';
?>
<footer>
	<?php if ( $footer_columns ): ?>
		<div class="footer-top-area">
			<div class="container">
				<div class="row">
					<?php
					foreach ( range( 1, 4 ) as $i ) {
						if ( !is_active_sidebar( 'footer-'. $i ) ) continue;
						echo '<div class="' . esc_attr( $footer_class ) . '">';
						dynamic_sidebar( 'footer-'. $i );
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( RDTheme::$options['copyright_area'] ): ?>
		<div class="footer-bottom-area">
			<div class="container">
				<div class="row">
					<div class="<?php echo esc_attr( $copyright_class );?>"><?php echo wp_kses_post( RDTheme::$options['copyright_text'] );?></div>
					<?php if ( RDTheme::$options['payment_icons'] ): ?>
						<div class="col-md-4 col-sm-12 col-12">
							<ul class="payment-icons">
								<?php if ( RDTheme::$options['payment_img'] ) : ?>
									<?php
									$rdtheme_cards = explode( ',', RDTheme::$options['payment_img'] );
									?>
									<?php foreach ( $rdtheme_cards as $rdtheme_card ): ?>
										<li><?php echo wp_get_attachment_image( $rdtheme_card );?></li>
									<?php endforeach; ?>
								<?php else: ?>
									<li><img alt="<?php esc_html_e( 'payment', 'classima' ); ?>" src="<?php echo Helper::get_img( 'payment-method1.jpg' ); ?>" width="34" height="21"></li>
									<li><img alt="<?php esc_html_e( 'payment', 'classima' ); ?>" src="<?php echo Helper::get_img( 'payment-method2.jpg' ); ?>" width="34" height="21"></li>
									<li><img alt="<?php esc_html_e( 'payment', 'classima' ); ?>" src="<?php echo Helper::get_img( 'payment-method3.jpg' ); ?>" width="34" height="22"></li>
									<li><img alt="<?php esc_html_e( 'payment', 'classima' ); ?>" src="<?php echo Helper::get_img( 'payment-method4.jpg' ); ?>" width="33" height="22"></li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</footer>