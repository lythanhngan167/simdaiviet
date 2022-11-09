<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

namespace radiustheme\Classima_Core;

use Rtcl\Helpers\Link;

$btn = $attr = '';

if ( $data['btntype'] == 'page' ) {
    $url = '#';
    if ( ! empty($data['page']) ) {
        $pricing = rtcl()->factory->get_pricing($data['page']);
        $url = add_query_arg('option', $pricing->getId(), Link::get_checkout_endpoint_url('membership'));
    }
    $attr    = 'href="' . $url . '"';
}
else {
	if ( !empty( $data['buttonurl']['url'] ) ) {
		$attr  = 'href="' . $data['buttonurl']['url'] . '"';
		$attr .= !empty( $data['buttonurl']['is_external'] ) ? ' target="_blank"' : '';
		$attr .= !empty( $data['buttonurl']['nofollow'] ) ? ' rel="nofollow"' : '';
	}
}

if ( $data['btntext'] ) {
	$btn = '<a ' . $attr . '>' . $data['btntext'] . '</a>';
}

$features = preg_split( "/\R/", $data['features'] ); // string to array
$features = implode( '<br/>', $features );
?>
<div class="rt-el-pricing-box">
	<?php if ( $data['title'] ): ?>
		<h3 class="rtin-title"><?php echo esc_html( $data['title'] );?></h3>
	<?php endif; ?>
	<div class="rtin-price">
		<span class="rtin-currency"><?php echo esc_html( $data['currency'] );?></span>
		<span class="rtin-number"><?php echo esc_html( $data['price'] );?></span>
		<span class="rtin-duration">
            <?php if ($data['per_sign'] == 'yes'): ?>/
            <?php endif; ?>
            <?php echo esc_html( $data['unit'] ); ?>
        </span>
	</div>
	<p class="rtin-features"><?php echo wp_kses_post( $features );?></p>
	<?php if ( $btn ): ?>
		<div class="rtin-button"><?php echo wp_kses_post( $btn );?></div>
	<?php endif; ?>
</div>