<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;

$listing_post = $listing->get_listing();

if ( get_post_meta( $listing_post->ID, 'never_expires', true ) ) {
	$expiry_date = esc_html__( 'Never Expires', 'classima' );
}
else {
	$expiry_date = get_post_meta( $listing_post->ID, 'expiry_date', true );
	$expiry_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $expiry_date ) );
}
?>
<div class="myaccount-listing-bottom-contents">
	<div class="rtcl-listable">
		<div class="rtcl-listable-item">
			<span class="listable-label"><?php esc_html_e( 'Status', 'classima' ); ?></span>
			<span class="listable-value"><?php echo Functions::get_status_i18n( $listing_post->post_status ); ?></span>
		</div>
        <?php if ($listing->get_status() !== 'pending') { ?>
            <div class="rtcl-listable-item">
                <span class="listable-label"><?php esc_html_e( 'Expires on', 'classima' ); ?></span>
                <span class="listable-value"><?php echo esc_html( $expiry_date ); ?></span>
            </div>
        <?php } ?>
	</div>
	<div class="btn-group btn-group-justified rtin-action-btn">
		<?php if ( !Functions::is_payment_disabled() ): ?>
			<a href="<?php echo esc_url( Link::get_checkout_endpoint_url( 'submission', $listing_post->ID ) ); ?>" class="btn btn-primary btn-sm btn-block"><?php esc_html_e( 'Promote', 'classima' ); ?></a>
		<?php endif; ?>

		<?php if (Functions::current_user_can('edit_' . rtcl()->post_type, $listing_post->ID )): ?>
			<a href="<?php echo esc_url( Link::get_listing_edit_page_link( $listing_post->ID ) ); ?>" class="btn btn-default btn-sm rtcl-edit-listing" data-id="<?php echo esc_attr( $listing_post->ID ); ?>"><?php esc_html_e( 'Edit', 'classima' ); ?></a>
		<?php endif; ?>

		<?php if (Functions::current_user_can('delete_' . rtcl()->post_type, $listing_post->ID )): ?>
			<a href="#" class="btn btn-danger btn-sm rtcl-delete-listing" data-id="<?php echo esc_attr( $listing_post->ID ); ?>"><?php esc_html_e( 'Delete', 'classima' ); ?></a>
		<?php endif; ?>

        <?php if (method_exists('RtclPro\Helpers\Fns', 'is_enable_mark_as_sold') && Fns::is_enable_mark_as_sold()): ?>
            <a data-id="<?php echo absint($listing->get_id()) ?>"
               class="btn btn-default btn-sm mark-as-sold"><?php method_exists('RtclPro\Helpers\Fns', 'is_mark_as_sold') && Fns::is_mark_as_sold($listing->get_id()) ? esc_html_e("Mark as unsold", "classima") : esc_html_e("Mark as sold", "classima"); ?></a>
        <?php endif; ?>
	</div>
</div>