<?php
/**
 * @author  RadiusTheme
 * @since   2.1.6.1
 * @version 2.1.6.1
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;
use radiustheme\Classima\RDTheme;
use Rtcl\Helpers\Functions;

$loc_text = apply_filters( 'rt_classima_all_location_text', __( 'All Locations', 'classima-core' ) );

$selected_location = false;

if ( get_query_var( 'rtcl_location' ) && $location = get_term_by( 'slug', get_query_var( 'rtcl_location' ), rtcl()->location ) ) {
	$selected_location = $location;
}

?>
<div class="rtcl rtcl-search rtcl-search-inline classima-listing-search-3">
    <form action="<?php echo esc_url( Functions::get_filter_form_url() ); ?>"
          class="form-vertical rtcl-widget-search-form rtcl-search-inline-form classima-listing-search-form">
		<?php if ( ! empty( RDTheme::$options['listing_search_items']['location'] ) ): ?>
			<?php if ( method_exists( 'Rtcl\Helpers\Functions', 'location_type' ) && 'local' === Functions::location_type() ): ?>
                <div class="rtcl-search-input-button rtcl-search-input-location">
                    <div class="find-form__header">
                        <h4 class="find-form__heading">
                            <span class=""><?php esc_html_e( 'Find anything in', 'classima-core' ); ?></span>
                            <span class="find-form__dir">
                                <img src="<?php echo Helper::get_img( 'dir3.svg' ); ?>" alt="icon">
                            </span>
                        </h4>
                        <div class="find-form__button">
                            <span class="search-input-label location-name">
                                <?php echo $selected_location ? esc_html( $selected_location->name ) : esc_html( $loc_text ) ?>
                            </span>
                            <input type="hidden" class="rtcl-term-field" name="rtcl_location"
                                   value="<?php echo $selected_location ? esc_attr( $selected_location->slug ) : '' ?>">
                        </div>
                    </div>
                </div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( ! empty( RDTheme::$options['listing_search_items']['keyword'] ) ): ?>
            <div class="find-form__input">
                <input type="text" data-type="listing" name="q" class="rtcl-autocomplete find-form__field"
                       placeholder="<?php esc_html_e( 'Buy, Sell, Rent & Exchange in one Click', 'classima-core' ); ?>"
                       value="<?php if ( isset( $_GET['q'] ) ) {
                           echo esc_attr(Functions::clean( wp_unslash( ( $_GET['q'] ) ) ));
                       } ?>"/>
                <button type="submit" class="find-form__search-button"><i class="fas fa-search"></i></button>
            </div>
		<?php endif; ?>
    </form>
</div>