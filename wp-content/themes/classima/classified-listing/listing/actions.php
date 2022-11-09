<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var boolean $can_add_favourites
 * @var boolean $can_report_abuse
 * @var boolean $social
 * @var integer $listing_id
 */
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use Rtcl\Models\Listing;
use radiustheme\Classima\RDTheme;

$listing = new Listing( $listing_id );

if ( !$can_add_favourites && !$can_report_abuse && !$social && !$listing->can_show_views() ) {
    return;
}
?>
<?php if ( RDTheme::$options['single_listing_style'] == '2' ): ?>
    <ul class='list-group list-group-flush rtcl-single-listing-action'>
        <?php if ( $listing->can_show_views() ): ?>
            <li class="list-group-item rtin-icon-common"><span class='rtcl-icon rtcl-icon-eye'></span><?php echo sprintf( esc_html__( '%s views', 'classima' ), number_format_i18n( $listing->get_view_counts() ) );?></li>
        <?php endif; ?>
        <?php if ( $can_add_favourites ): ?>
            <li class="list-group-item rtin-icon-common" id="rtcl-favourites"><?php echo Functions::get_favourites_link( $listing_id );?></li>
        <?php endif; ?>
        <?php if ( $can_report_abuse ): ?>
            <li class='list-group-item rtin-icon-common'>
                <?php if ( is_user_logged_in() ): ?>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#rtcl-report-abuse-modal"><span class='rtcl-icon rtcl-icon-trash-1'></span><?php esc_html_e( 'Report Abuse', 'classima' );?></a>
                <?php else: ?>
                    <a href="javascript:void(0)" class="rtcl-require-login"><span class='rtcl-icon rtcl-icon-trash-1'></span><?php esc_html_e( 'Report Abuse', 'classima' );?></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
        <?php if ( $social ): ?>
            <li class="list-group-item rtcl-sidebar-social">
                <div class="share-label rtin-icon-common"><i class="fa fa-fw fa-share-alt" aria-hidden="true"></i><?php esc_html_e( 'Share this Ad:', 'classima' );?></div>
                <?php echo wp_kses_post( $social );?>
            </li>
        <?php endif; ?>
    </ul>

<?php elseif ( RDTheme::$options['single_listing_style'] == '4' ): ?>
    <ul class='list-inline list-group-flush rtcl-single-listing-action'>
	    <?php if ( $social ): ?>
            <li class="list-inline-item rtcl-sidebar-social">
                <span class="rtin-share-title"><i class="fa fa-share-alt" aria-hidden="true"></i></span>
                <div class="rtin-details-social-action">
	                <?php echo wp_kses_post( $social );?>
                </div>
            </li>
	    <?php endif; ?>
		<?php if ( $can_add_favourites ):
            echo '<li>';
            if ( is_user_logged_in() ) {
	            $favourites = (array) get_user_meta( get_current_user_id(), 'rtcl_favourites', true );

	            if ( in_array( $listing_id, $favourites ) ) {
		            echo '<a href="javascript:void(0)" data-toggle="tooltip" data-original-title="' . Text::remove_from_favourite() . '" class="rtcl-favourites rtcl-active" data-id="' . $listing_id . '"><span class="rtcl-icon rtcl-icon-heart"></span></a>';
	            } else {
		            echo '<a href="javascript:void(0)" data-toggle="tooltip" data-original-title="' . Text::add_to_favourite() . '" class="rtcl-favourites" data-id="' . $listing_id . '"><span class="rtcl-icon rtcl-icon-heart-empty"></span></a>';
	            }
            } else {
	            echo '<a href="javascript:void(0)" data-toggle="tooltip" data-original-title="' . Text::add_to_favourite() . '" class="rtcl-require-login"><span class="rtcl-icon rtcl-icon-heart-empty"></span></a>';
            }
			echo '</li>';
            ?>
		<?php endif; ?>
		<?php if ( $can_report_abuse ): ?>
            <li class='list-inline-item'>
				<?php if ( is_user_logged_in() ): ?>
                    <span data-toggle="tooltip" data-original-title="<?php esc_html_e('Report Abuse', 'classima'); ?>">
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#rtcl-report-abuse-modal">
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        </a>
                    </span>
				<?php else: ?>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php esc_html_e('Report Abuse', 'classima'); ?>" class="rtcl-require-login"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>
				<?php endif; ?>
            </li>
		<?php endif; ?>
    </ul>
<?php else: ?>
    <ul class='list-inline list-group-flush rtcl-single-listing-action'>
        <?php if ( $can_add_favourites ): ?>
            <li class="list-inline-item" id="rtcl-favourites"><?php echo Functions::get_favourites_link( $listing_id );?></li>
        <?php endif; ?>
        <?php if ( $can_report_abuse ): ?>
            <li class='list-inline-item'>
                <?php if ( is_user_logged_in() ): ?>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#rtcl-report-abuse-modal"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php esc_html_e( 'Report Abuse', 'classima' );?></a>
                <?php else: ?>
                    <a href="javascript:void(0)" class="rtcl-require-login"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php esc_html_e( 'Report Abuse', 'classima' );?></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
        <?php if ( $social ): ?>
            <li class="list-inline-item rtcl-sidebar-social">
                <span class="rtin-share-title"><i class="fa fa-share-alt" aria-hidden="true"></i><?php esc_html_e( 'Share', 'classima' );?>:</span>
                <?php echo wp_kses_post( $social );?>
            </li>
        <?php endif; ?>
    </ul>   
<?php endif; ?>

<div class="modal fade" id="rtcl-report-abuse-modal" tabindex="-1" role="dialog" aria-labelledby="rtcl-report-abuse-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rtcl-report-abuse-form" class="form-vertical">
                <div class="modal-header">
                    <h5 class="modal-title" id="rtcl-report-abuse-modal-label"><?php esc_html_e( 'Report Abuse', 'classima' );?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rtcl-report-abuse-message"><?php esc_html_e( 'Your Complain', 'classima' );?><span class="rtcl-star">*</span></label>
                        <textarea class="form-control" name="message" id="rtcl-report-abuse-message" rows="3" placeholder="<?php esc_attr_e( 'Message... ', 'classima' );?>" required></textarea>
                    </div>
                    <div id="rtcl-report-abuse-g-recaptcha"></div>
                    <div id="rtcl-report-abuse-message-display"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e( 'Close', 'classima' );?></button>
                    <button type="submit" class="btn btn-primary"><?php esc_html_e( 'Submit', 'classima' );?></button>
                </div>
            </form>
        </div>
    </div>
</div>