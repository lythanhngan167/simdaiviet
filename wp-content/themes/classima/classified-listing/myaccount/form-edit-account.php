<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var WP_User $user
 * @var string  $phone
 * @var string  $whatsapp_number
 * @var string  $website
 * @var string  $geo_address
 * @var string  $state_text
 * @var string  $city_text
 * @var array   $user_locations
 * @var int     $sub_location_id
 * @var int     $location_id
 * @var string  $town_text
 * @var string  $zipcode
 * @var float   $latitude
 * @var float   $longitude
 * @var int     $pp_id
 */

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

if (!defined('ABSPATH')) {
    exit;
}

do_action('rtcl_before_edit_account_form'); ?>
<?php
$user_id = $user->ID;
$address = get_user_meta($user_id, '_rtcl_address', true);
$latitude = get_user_meta($user_id, '_rtcl_latitude', true);
$longitude = get_user_meta($user_id, '_rtcl_longitude', true);
$geo_address = get_user_meta($user_id, '_rtcl_geo_address', true);
?>


<form class="rtcl-EditAccountForm form-horizontal classima-form" id="rtcl-user-account" method="post">

	<?php do_action( 'rtcl_edit_account_form_start' ); ?>

    <div class="classima-form-section">
        <div class="classified-listing-form-title">
            <i class="fa fa-user" aria-hidden="true"></i><h3><?php esc_html_e( 'Basic Information', 'classima' ); ?></h3>
        </div>

        <div class="row classima-acc-form-username-row">
            <div class="col-sm-3 col-6">
                <label class="control-label"><?php esc_html_e( 'Username', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-6">
                <div class="form-group">
                    <div class="rtin-textvalue"><?php echo esc_html( $user->user_login ); ?></div>
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-fname-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'First Name', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="text" class="form-control" value="<?php echo esc_attr( $user->first_name ); ?>" id="rtcl-first-name" name="first_name">
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-lname-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Last Name', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="text" name="last_name" id="rtcl-last-name" value="<?php echo esc_attr( $user->last_name ); ?>" class="form-control" />
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-email-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Email', 'classima' ); ?><span> *</span></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="email" name="email" id="rtcl-email" class="form-control" value="<?php echo esc_attr($user->user_email); ?>" required="required" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 col-12">
                <label for="rtcl-profile-picture" class="control-label">
                    <?php _e('Profile Picture', 'classima'); ?><span>*</span>
                </label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="rtcl-profile-picture-wrap form-group">
                    <?php if (!$pp_id): ?>
                        <div class="rtcl-gravatar-wrap">
                            <?php echo get_avatar($user->ID);
                            echo "<p>" . sprintf(
                                    __('<a href="%s">You can change your profile picture on Gravatar</a>.', 'classima'),
                                    __('https://en.gravatar.com/')
                                ) . "</p>";
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="rtcl-media-upload-wrap">
                        <div class="rtcl-media-upload rtcl-media-upload-pp<?php echo($pp_id ? ' has-media' : ' no-media') ?>">
                            <div class="rtcl-media-action">
                                <span class="rtcl-icon-plus add"><?php esc_html_e('Add Logo', 'classima'); ?></span>
                                <span class="rtcl-icon-trash remove"><?php esc_html_e('Delete Logo', 'classima'); ?></span>
                            </div>
                            <div class="rtcl-media-item">
                                <?php echo($pp_id ? wp_get_attachment_image($pp_id, [100, 100]) : '') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-cpass-row">
            <div class="col-sm-3 col-8">
                <label for="rtcl-change-password" class="control-label"><?php esc_html_e( 'Change Password', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-4">
                <div class="form-group">
                    <input type="checkbox" class="rtin-checkbox" name="change_password" id="rtcl-change-password" value="1">
                </div>
            </div>
        </div>

        <div class="row rtcl-password-fields" style="display: none">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'New Password', 'classima' ); ?><span> *</span></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="password" name="pass1" id="password" class="form-control rtcl-password" autocomplete="off" required="required" />
                </div>
            </div>
        </div>

        <div class="row rtcl-password-fields" style="display: none">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Confirm Password', 'classima' ); ?><span> *</span></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="password" name="pass2" id="password_confirm" class="form-control" autocomplete="off" data-rule-equalTo="#password" required />
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-phone-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Phone', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
	                <?php
	                $phone = esc_attr($phone);
	                $field = "<input type='text' name='phone' id='rtcl-phone' value='{$phone}' class='form-control'/>";
	                Functions::print_html(apply_filters('rtcl_edit_account_phone_field', $field, $phone), true);
	                ?>
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-whatsapp-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'WhatsApp Phone', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="text" name="whatsapp_number" id="rtcl-whatsapp-phone" value="<?php echo esc_attr( $whatsapp_number ); ?>" class="form-control" />
                </div>
            </div>
        </div>

        <div class="row classima-acc-form-website-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Website', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <input type="url" name="website" id="rtcl-website" value="<?php echo esc_attr( $website ); ?>" class="form-control" />
                </div>
            </div>
        </div>
    </div>

    <div class="classima-form-section">
        <div class="classified-listing-form-title">
            <i class="fa fa-map-marker" aria-hidden="true"></i><h3><?php esc_html_e( 'Location', 'classima' ); ?></h3>
        </div>
        <?php if ('local' === Functions::location_type()) : ?>
            <div class="row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php echo esc_html( $state_text ); ?><span> *</span></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <select id="rtcl-location" name="location" class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
                            <option value="">--<?php esc_html_e( 'Select Location', 'classima' ) ?>--</option>
                            <?php
                            $locations = Functions::get_one_level_locations();
                            if ( ! empty( $locations ) ) {
                                foreach ( $locations as $location ) {
                                    $slt = '';
                                    if ( in_array( $location->term_id, $user_locations ) ) {
                                        $location_id = $location->term_id;
                                        $slt         = " selected";
                                    }
                                    echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <?php
            $sub_locations = array();
            if ( $location_id ) {
                $sub_locations = Functions::get_one_level_locations( $location_id );
            }
            ?>

            <div class="row <?php echo empty( $sub_locations ) ? ' rtcl-hide' : ''; ?>" id="sub-location-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php echo esc_html( $city_text ); ?><span> *</span></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <select id="rtcl-sub-location" name="sub_location" class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
                            <option value="">--<?php esc_html_e( 'Select Location', 'classima' ) ?>--</option>
                            <?php
                            if ( ! empty( $sub_locations ) ) {
                                foreach ( $sub_locations as $location ) {
                                    $slt = '';
                                    if ( in_array( $location->term_id, $user_locations ) ) {
                                        $sub_location_id = $location->term_id;
                                        $slt             = " selected";
                                    }
                                    echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <?php
            $sub_sub_locations = array();
            if ( $sub_location_id ) {
                $sub_sub_locations = Functions::get_one_level_locations( $sub_location_id );
            }
            ?>

            <div class="row <?php echo empty( $sub_sub_locations ) ? ' rtcl-hide' : ''; ?>" id="sub-sub-location-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php echo esc_html( $town_text ); ?><span> *</span></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <select id="rtcl-sub-sub-location" name="sub_sub_location" class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
                            <option value="">--<?php esc_html_e( 'Select Location', 'classima' ) ?>--</option>
                            <?php
                            if ( ! empty( $sub_sub_locations ) ) {
                                foreach ( $sub_sub_locations as $location ) {
                                    $slt = '';
                                    if ( in_array( $location->term_id, $user_locations ) ) {
                                        $slt = " selected";
                                    }
                                    echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row classima-acc-form-zip-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php esc_html_e( 'Zip Code', 'classima' ); ?></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <input type="text" name="zipcode" value="<?php echo esc_attr( $zipcode ); ?>" class="rtcl-map-field form-control" id="rtcl-zipcode"/>
                    </div>
                </div>
            </div>

            <div class="row classima-acc-form-address-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php esc_html_e( 'Address', 'classima' ); ?></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <textarea name="address" rows="2" class="rtcl-map-field form-control" id="rtcl-address"><?php echo esc_textarea( $address ); ?></textarea>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-sm-3 col-12">
                    <label class="control-label" for="rtcl-geo-address"><?php esc_html_e("Location", "classima") ?></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="rtcl-geo-address-field form-group">
                        <input type="text" name="rtcl_geo_address" autocomplete="off"
                               value="<?php echo esc_attr($geo_address) ?>"
                               id="rtcl-geo-address"
                               placeholder="<?php esc_html_e("Select a location", "classima"); ?>"
                               class="form-control rtcl-geo-address-input rtcl_geo_address_input"/>
                        <i class="rtcl-get-location rtcl-icon rtcl-icon-target" id="rtcl-geo-loc-form"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (method_exists('Rtcl\Helpers\Functions','has_map') && Functions::has_map() && 'geo' === Functions::location_type()): ?>
            <div class="row classima-acc-form-map-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php esc_html_e( 'Map', 'classima' ); ?></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <div class="rtcl-map-wrap">
                            <div class="rtcl-map" data-type="input">
                                <div class="marker" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>" data-address="<?php echo esc_attr($address); ?>"><?php echo esc_html($address); ?></div>
                            </div>
                        </div>
                    </div>
                    <!-- Map Hidden field-->
                    <input type="hidden" name="latitude" value="<?php echo esc_attr($latitude); ?>" id="rtcl-latitude"/>
                    <input type="hidden" name="longitude" value="<?php echo esc_attr($longitude); ?>" id="rtcl-longitude"/>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php do_action( 'rtcl_edit_account_form' ); ?>

    <div class="row">
        <div class="col-sm-3 col-12"></div>
        <div class="col-sm-9 col-12">
            <div class="form-group">
                <input type="submit" name="submit" class="btn rtcl-submit-btn" value="<?php esc_html_e( 'Update Account', 'classima' ); ?>" />
            </div>
        </div>
    </div>

    <?php do_action( 'rtcl_edit_account_form_end' ); ?>
</form>

<?php do_action( 'rtcl_after_edit_account_form' ); ?>
