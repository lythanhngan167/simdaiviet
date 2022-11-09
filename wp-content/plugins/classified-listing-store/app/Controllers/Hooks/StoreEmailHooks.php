<?php


namespace RtclStore\Controllers\Hooks;


use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Models\RtclEmail;
use Rtcl\Resources\Options as RtclOptions;
use RtclStore\Emails\StoreContactEmailToOwner;
use RtclStore\Emails\StoreManagerInvitation;
use RtclStore\Emails\StoreUpdateEmailToAdmin;

class StoreEmailHooks {

	public static function init() {
		add_filter( 'rtcl_email_services', [ __CLASS__, 'add_store_email_services' ], 10 );
		add_filter( 'rtcl_email_order_item_details_fields', [
			__CLASS__,
			'rtcl_email_order_item_details_fields'
		], 10, 4 );
	}

	static function add_store_email_services( $services ) {
		$services['Store_Update_Email_To_Admin']  = new StoreUpdateEmailToAdmin();
		$services['Store_Contact_Email_To_Owner'] = new StoreContactEmailToOwner();
		$services['StoreManagerInvitation']       = new StoreManagerInvitation();

		return $services;
	}

	/**
	 * @param $fields
	 * @param $order Payment
	 * @param $sent_to_admin
	 * @param $email RtclEmail
	 *
	 * @return array
	 */
	static function rtcl_email_order_item_details_fields( $fields, $order, $sent_to_admin, $email ) {

		if ( $order->is_membership() ) {

			$fields['item_title']['label'] = apply_filters( 'rtcl_email_order_item_details_title', __( "Membership Order", 'classified-listing-store' ), $order );
			$pricing                       = $order->pricing;
			$description                   = $pricing->getDescription();
			$promotions                    = get_post_meta( $pricing->getId(), '_rtcl_membership_promotions', true );
			$promotion_list                = RtclOptions::get_listing_promotions();
			ob_start();
			?>

            <div style="display: flex;font-weight: bold;">
                <div style="flex: 0 0 90px;"></div>
                <div style="flex: 0 0 50px;display: flex;justify-content: center;align-items: center;"><?php esc_html_e( 'Ads', "classified-listing-store" ) ?></div>
                <div style="flex: 0 0 50px;display: flex;justify-content: center;align-items: center;"><?php esc_html_e( 'Days', "classified-listing-store" ) ?></div>
            </div>
            <div style="display: flex;border-top: 1px solid #eee;margin-top: 5px;padding-top: 5px;">
                <div style="flex: 0 0 90px;"><?php esc_html_e( 'Regular', "classified-listing-store" ) ?></div>
                <div style="flex: 0 0 50px;display: flex;justify-content: center;align-items: center;"><?php echo absint( get_post_meta( $pricing->getId(), 'regular_ads', true ) ) ?></div>
                <div style="flex: 0 0 50px;display: flex;justify-content: center;align-items: center;"><?php echo absint( $pricing->getVisible() ) ?></div>
            </div>
			<?php
			if ( is_array( $promotions ) && ! empty( $promotions ) ) {
				foreach ( $promotions as $promotion_key => $promotion ) {
					?>
                    <div style="display: flex;border-top: 1px solid #eee;margin-top: 5px;padding-top: 5px;">
                        <div style="flex: 0 0 90px;"><?php esc_html_e( $promotion_list[ $promotion_key ] ) ?></div>
                        <div style="flex: 0 0 50px;display: flex;justify-content: center;align-items: center;"><?php echo absint( $promotion['ads'] ) ?></div>
                        <div style="flex: 0 0 50px;display: flex;justify-content: center;align-items: center;"><?php echo absint( $promotion['validate'] ) ?></div>
                    </div>
					<?php
				}
			}
			if ( $description ): ?>
                <p><?php Functions::print_html( $description, true ); ?></p>
			<?php endif;
			$features                    = ob_get_clean();
			$fields['features']['value'] = $features;
		}

		return $fields;
	}
}
