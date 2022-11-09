<?php
/**
 * Listing Form Contact
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;

Functions::print_notices();
?>

<div class="rtcl-listing-info-selecting classima-form">
    <?php if ( ! Functions::is_ad_type_disabled() ): ?>
        <div id="rtcl-ad-type-selection">
            <div class="classified-listing-form-title">
                <i class="fa fa-tags" aria-hidden="true"></i><h3><?php esc_html_e( 'Select Type', 'classima' ); ?></h3>
            </div>
            <div class="row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php esc_html_e( 'Ad Type', 'classima' ); ?><span> *</span></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <select class="rtcl-select2 form-control" id="rtcl-ad-type" name="type" required>
                            <option value="">--<?php esc_html_e("Select Type", 'classima'); ?>--</option>
                            <?php
                            $types = Functions::get_listing_types();
                            if ( ! empty( $types ) ):
                                foreach ( $types as $type_id => $type ):
                                    $tSlt = $type_id == $selected_type ? ' selected' : '';
                                    echo "<option value='{$type_id}'{$tSlt}>" . esc_html( $type ) . "</option>";
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div id="rtcl-ad-category-selection"
         style="display: <?php echo esc_attr( ( ( $selected_type && in_array( $selected_type, array_keys( Functions::get_listing_types() ) ) ) || Functions::is_ad_type_disabled() ) ? 'block' : 'none' ); ?>">
        <div class="classified-listing-form-title">
            <i class="fa fa-tags" aria-hidden="true"></i><h3><?php esc_html_e( 'Select Category', 'classima' ); ?></h3>
        </div>
        <div class="rtcl-post-category">

            <div class="row" id="cat-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php esc_html_e( 'Category', 'classima' ); ?><span> *</span></label>
                </div>
                <div class="col-sm-9 col-12">
                    <div class="form-group">
                        <select class="rtcl-select2 form-control" id="rtcl-category" name="category" required>
                            <option value=""><?php echo esc_html( Text::get_select_category_text() ); ?></option>
                            <?php
                            $cats          = Functions::get_one_level_categories( 0, $selected_type );
                            $parent_cat_id = isset( $parent_cat_id ) ? $parent_cat_id : 0;
                            if ( ! empty( $cats ) ) {
                                foreach ( $cats as $cat ) {
                                    $slt = $parent_cat_id == $cat->term_id ? ' selected' : '';
                                    echo "<option value='{$cat->term_id}'{$slt}>{$cat->name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <?php $child_cats = $parent_cat_id ? Functions::get_one_level_categories($parent_cat_id) : array() ?>

            <div class="row <?php echo empty($child_cats) ? ' rtcl-hide' : ''; ?>" id="sub-cat-row">
                <div class="col-sm-3 col-12">
                    <label class="control-label"><?php esc_html_e( 'Sub Category', 'classima' ); ?><span> *</span></label>
                </div>
                <div class="col-sm-9 col-12" id="rtcl-sub-category-wrap">
                    <div class="form-group">
                       
                        <?php if ( ! empty( $child_cats ) ) : ?>
                            <select class="form-control rtcl-select2" id="rtcl-sub-category" name="sub_category" required>
                                <?php
                                echo "<option value=''>" . esc_html( Text::get_select_category_text() ) . "</option>";
                                foreach ( $child_cats as $cat ) {
                                    echo "<option value='" . absint( $cat->term_id ) . "'>" . esc_html( $cat->name ) . "</option>";
                                }
                                ?>
                            </select>
                        <?php endif;?>
                       
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>