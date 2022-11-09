<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

namespace radiustheme\Classima;

use Rtcl\Models\Listing;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;
use RtclStore\Helpers\Functions as StoreFunctions;

$id           = get_the_id();
$listing      = new Listing( $id );
$listing_post = $listing->get_listing();
$email        = get_post_meta( $id, 'email', true );
$website      = get_post_meta( $id, 'website', true );
$phone        = get_post_meta( $id, 'phone', true );
$whatsapp     = get_post_meta( $id, '_rtcl_whatsapp_number', true );

$has_contact_form        = Functions::get_option_item( 'rtcl_moderation_settings', 'has_contact_form', false, 'checkbox');
$alternate_contact_form  = Functions::get_option_item( 'rtcl_moderation_settings', 'alternate_contact_form_shortcode');

$store        = false;
if ( class_exists( 'RtclStore' ) ) {
	$store = StoreFunctions::get_user_store( $listing_post->post_author );
}
?>
<div class="classified-seller-info widget">
	<h3 class="widgettitle"><?php esc_html_e( 'Seller Information', 'classima' ); ?></h3>
	<div class="rtin-box">

        <?php if ( $listing->can_show_user() ): ?>
            <div class="rtin-author">
                <?php if ( $store ): ?>
                    <?php $store->the_logo();?>
                    <h4 class="rtin-name"><a href="<?php $store->the_permalink();?>"><?php $listing->the_author();?></a></h4>
                <?php else: ?>
                    <h4 class="rtin-name"><?php $listing->the_author(); ?></h4>
                <?php endif; ?>
	            <?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
            </div>
        <?php endif; ?>

        <?php if (Fns::registered_user_only('listing_seller_information') && !is_user_logged_in()) { ?>
            <p class="login-message"><?php echo wp_kses(sprintf(__("Please <a href='%s'>login</a> to view the seller information.", "classima"), esc_url(Link::get_my_account_page_link())), ['a' => ['href' => []]]); ?></p>
        <?php } else { ?>

            <?php if ( $address = Listing_Functions::get_single_contact_address( $listing ) ): ?>
                <div class="rtin-location rtin-box-item clearfix">
                    <i class="fa fa-fw fa-map-marker" aria-hidden="true"></i><div class="rtin-box-item-text"><?php echo wp_kses_post( $address );?></div>
                </div>
            <?php endif; ?>

            <?php if ( $website ): ?>
                <div class="rtin-web rtin-box-item clearfix">
                    <i class="fa fa-fw fa-globe" aria-hidden="true"></i><a class="rtin-box-item-text" href="<?php echo esc_url( $website );?>" rel="nofollow" target="_blank"><?php esc_html_e( 'Visit Website', 'classima' );?></a>
                </div>
            <?php endif; ?>

            <?php if ( $store ): ?>
                <div class="rtin-store rtin-box-item clearfix">
                    <i class="fa fa-fw fa-shopping-basket" aria-hidden="true"></i><a class="rtin-box-item-text" href="<?php $store->the_permalink(); ?>" rel="nofollow"><?php esc_html_e( 'View Store', 'classima' );?></a>
                </div>
            <?php endif; ?>

            <?php if ( $phone || $whatsapp): ?>
                <div class="rtin-phone"><?php Listing_Functions::the_phone( $phone, $whatsapp );?></div>
            <?php endif; ?>

            <?php
            if (Fns::is_enable_chat() && ((is_user_logged_in() && $listing->get_author_id() !== get_current_user_id()) || !is_user_logged_in())):
                $chat_btn_class = ['rtcl-chat-link'];
                $chat_url = Link::get_my_account_page_link();
                if (is_user_logged_in()) {
                    $chat_url = '#';
                    array_push($chat_btn_class, 'rtcl-contact-seller');
                } else {
                    array_push($chat_btn_class, 'rtcl-no-contact-seller');
                }
                ?>
                <div class="media rtin-chat">
                    <a class="<?php echo esc_attr(implode(' ', $chat_btn_class)); ?>" data-listing_id="<?php the_ID(); ?>" href="<?php echo esc_url($chat_url) ?>"><i class="fa fa-comments" aria-hidden="true"></i><?php esc_html_e( 'Chat', 'classima' );?></a>
                </div>
            <?php endif; ?>

            <?php if ( $has_contact_form && ( $email || $alternate_contact_form ) ) : ?>
                <div class="media rtin-email">
                    <a data-toggle="modal" data-target="#classima-mail-to-seller" href="#"><i class="fas fa-envelope" aria-hidden="true"></i><?php esc_html_e( 'Email to Seller', 'classima' );?></a>
                </div>

            <?php endif; ?>
        <?php } ?>

	</div>
</div>