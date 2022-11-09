<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.3
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;

global $store;
$store_oh_type = get_post_meta($store->get_id(), 'oh_type', true);
$store_oh_hours = get_post_meta($store->get_id(), 'oh_hours', true);
$store_oh_hours = is_array($store_oh_hours) ? $store_oh_hours : ($store_oh_hours ? (array)$store_oh_hours : array());

$today = strtolower(date('l'));

$days = array(
	'sunday'    => esc_html__( 'Sunday', 'classima' ),
	'monday'    => esc_html__( 'Monday', 'classima' ),
	'tuesday'   => esc_html__( 'Tuesday', 'classima' ),
	'wednesday' => esc_html__( 'Wednesday', 'classima' ),
	'thursday'  => esc_html__( 'Thursday', 'classima' ),
	'friday'    => esc_html__( 'Friday', 'classima' ),
	'saturday'  => esc_html__( 'Saturday', 'classima' ),
);
?>

<div class="classima-store-details widget">
	<h3 class="widgettitle"><?php esc_html_e( 'Details', 'classima' );?></h3>
	<div>
		<p><?php echo $store->get_the_description(); ?></p>
		<?php if ( $store->get_social_media() ): ?>
	        <div class="classima-store-socials"><?php echo wp_kses_post( $store->get_social_media_html() ); ?></div>
	    <?php endif; ?>
	</div>
</div>

<div class="classima-store-info widget">
	<h3 class="widgettitle"><?php esc_html_e( 'Store Information', 'classima' );?></h3>

	<div>
        <?php if (Fns::registered_user_only('store_contact') && !is_user_logged_in()) { ?>
            <p class="login-message">
                <?php echo wp_kses(sprintf(__("Please <a href='%s'>login</a> to view the store contact.", "classima"), esc_url(Link::get_my_account_page_link())), ['a' => ['href' => []]]); ?>
            </p>
        <?php } else { ?>
            <?php if ( $store_website = $store->get_website() ): ?>
                <div class="rtin-store-web"><i class="fa fa-globe" aria-hidden="true"></i><a target="_blank" href="<?php echo esc_url_raw( $store_website ); ?>" rel="nofollow"><?php esc_html_e( 'Visit Website' , 'classima') ?></a></div>
            <?php endif; ?>
		
		    <div class="rtin-oh-title"><i class="far fa-clock" aria-hidden="true"></i><?php esc_html_e( 'Opening Hours', 'classima' );?></div>
            <div class="rtin-store-hours-list">
                <?php if ( $store_oh_type == "selected" ): ?>
                    <?php if ( !empty( $store_oh_hours ) && is_array( $store_oh_hours ) ): ?>
                        <?php foreach ( $store_oh_hours as $hKey => $oh_hour ): ?>
                            <div class="row<?php echo esc_attr( ( $hKey == $today ) ? ' current-store-hour' : '' ); ?>">
                                <div class="col-4">
                                    <span class="hour-day"><?php echo esc_html( $days[$hKey] ); ?></span>
                                </div>
                                <div class="col-8">
                                    <?php if ( isset( $oh_hour['active'] ) ): ?>
                                        <div class="oh-hours">
                                            <span><?php echo isset( $oh_hour['open'] ) ? esc_html( $oh_hour['open'] ) : ''; ?></span>
                                            <span>-</span>
                                            <span><?php echo isset($oh_hour['close'] ) ? esc_html( $oh_hour['close'] ) : ''; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="oh-hours"><?php esc_html_e( 'Closed' , 'classima') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="oh-always always-close"><?php esc_html_e( 'Permanently Closed', 'classima' ); ?></div>
                    <?php endif; ?>
                <?php elseif ( $store_oh_type == 'always' ): ?>
                    <div class="oh-always always-open"><?php esc_html_e( 'Always Open' , 'classima' ); ?></div>
                <?php endif; ?>
            </div>

            <?php if ( $store_phone = $store->get_phone() ): ?>
                <div class="rtin-phone"><?php Listing_Functions::the_phone( $store_phone );?></div>
            <?php endif; ?>

            <?php if ( $store_email = $store->get_email() ) : ?>
                <div class="media rtin-email">
                    <a data-toggle="modal" data-target="#classima-mail-to-seller" href="#"><i class="fas fa-envelope" aria-hidden="true"></i><?php esc_html_e( 'Message Store Owner', 'classima' );?></a>

                    <div class="modal fade" id="classima-mail-to-seller" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" data-hide="0"><?php Functions::get_template( 'store/contact-form', null, '', rtclStore()->get_plugin_template_path() ); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php } ?>

	</div>

</div>