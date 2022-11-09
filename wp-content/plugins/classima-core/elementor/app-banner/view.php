<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$btn1 = $attr1 = '';
$btn2 = $attr2 = '';

if ( !empty( $data['buttonurl1']['url'] ) ) {
	$attr1  = 'href="' . $data['buttonurl1']['url'] . '"';
	$attr1 .= !empty( $data['buttonurl1']['is_external'] ) ? ' target="_blank"' : '';
	$attr1 .= !empty( $data['buttonurl1']['nofollow'] ) ? ' rel="nofollow"' : '';
}
if ( !empty( $data['buttontext1'] ) ) {
	$btn1 = '<a ' . $attr1 . '><div class="item-text">'.esc_html__('Get it on', 'classima-core').'<span>' . $data['buttontext1'] . '</span></div><div class="item-icon"><i class="fab fa-android"></i></div></a>';
}
if ( !empty( $data['buttonurl2']['url'] ) ) {
    $attr2  = 'href="' . $data['buttonurl2']['url'] . '"';
    $attr2 .= !empty( $data['buttonurl2']['is_external'] ) ? ' target="_blank"' : '';
    $attr2 .= !empty( $data['buttonurl2']['nofollow'] ) ? ' rel="nofollow"' : '';
}
if ( !empty( $data['buttontext2'] ) ) {
    $btn2 = '<a ' . $attr2 . '><div class="item-text">'.esc_html__('Get it on', 'classima-core').'<span>' . $data['buttontext2'] . '</span></div><div class="item-icon"><i class="fab fa-apple"></i></div></a>';
}
$count = count( $data['shape'] );
$i = 0;
?>
<div class="app-banner">
	<div class="row">
        <div class="col-lg-6 col-sm-12 col-12">
            <div class="banner-content">
                <h2 class="item-title"><?php echo wp_kses_post( $data['title'] );?></h2>
                <p><?php echo wp_kses_post( $data['subtitle'] );?></p>
                <div class="download-btn">
                    <?php echo wp_kses_post( $btn1 ); ?>
                    <?php echo wp_kses_post( $btn2 ); ?>
                </div>
            </div>
        </div>
        <?php if ( $data['image'] ): ?>
            <div class="col-lg-6 d-none d-sm-block">
                <div class="banner-img">
                    <div class="item-img">
                        <?php echo wp_get_attachment_image( $data['image']['id'], 'full' ); ?>
                    </div>
                    <?php if ( $count > 0 ): ?>
                        <div class="bg-shape">
                            <?php
                            foreach ( $data['shape'] as $index => $attachment ) {
                                $i++;
                                echo wp_get_attachment_image($attachment['id'], 'full', '', array('class' => 'shape'.$i));
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
	</div>
</div>