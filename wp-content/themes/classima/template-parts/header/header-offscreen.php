<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

$nav_menu_args   = Helper::nav_menu_args();

$logo = empty( RDTheme::$options['logo']['url'] ) ? Helper::get_img( 'logo-dark.png' ) : RDTheme::$options['logo'];

if (!empty($logo['url']) && !empty($logo['width']) && !empty($logo['height'])) {
    $rdtheme_logo = '<img class="logo-small" src="'. esc_url( $logo['url'] ).'" width="'. esc_attr( $logo['width'] ).'" height="'. esc_attr( $logo['height'] ).'" />';
} else {
    $rdtheme_logo = '<img class="logo-small" src="'. esc_url( $logo['url'] ).'" width="150" height="45" />';
}

$loc_text = esc_attr__( 'Select Location', 'classima' );
$style = RDTheme::$options['listing_search_style'];

$loc_text = esc_attr__( 'Select Location', 'classima' );

$selected_location = false;

if ( get_query_var( 'rtcl_location' ) && $location = get_term_by( 'slug', get_query_var( 'rtcl_location' ), rtcl()->location ) ) {
	$selected_location = $location;
}

$orderby = strtolower(Functions::get_option_item('rtcl_general_settings', 'taxonomy_orderby', 'name'));
$order = strtoupper(Functions::get_option_item('rtcl_general_settings', 'taxonomy_order', 'ASC'));

?>

<div class="rt-header-menu mean-container" id="meanmenu">
    <div class="mean-bar">
    	<a class="mean-logo-area" href="<?php echo esc_url(home_url('/'));?>" alt="<?php echo esc_attr( get_bloginfo( 'title' ) );?>"><?php echo wp_kses_post($rdtheme_logo);?></a>
        <?php
        $html = '';

        if ( RDTheme::$options['header_btn_txt_mob'] && RDTheme::$options['header_btn_url'] ) {
            $html .= '<a class="header-btn header-menu-btn header-menu-btn-mob" href="'.esc_url( RDTheme::$options['header_btn_url'] ).'"><i class="fas fa-plus" aria-hidden="true"></i><span>'.esc_html( RDTheme::$options['header_btn_txt_mob'] ).'</span></a>';
        }

        if ( Helper::is_chat_enabled() ) {
            $html .= '<a class="header-chat-icon header-chat-icon-mobile rtcl-chat-unread-count" href="'.esc_url( Link::get_my_account_page_link( 'chat' ) ).'"><i class="far fa-comments" aria-hidden="true"></i></a>';
        }

        if ( RDTheme::$options['header_icon'] && class_exists( 'RtclPro' ) && class_exists( 'Rtcl' ) ) {
            $html .= '<a class="header-login-icon header-login-icon-mobile" href="'.esc_url( Link::get_my_account_page_link() ).'"><i class="far fa-user" aria-hidden="true"></i></a>';
        }

        if ( $html ) {
            printf( '<div class="header-mobile-icons">%s</div>' , $html );
        }
        ?>
        <span class="sidebarBtn ">
            <span></span>
        </span>

    </div>
    <div class="rt-slide-nav">
        <div class="offscreen-navigation">
            <?php wp_nav_menu( $nav_menu_args ); ?>
        </div>
    </div>
</div>
<?php if ( ! empty( RDTheme::$options['listing_search_items']['keyword'] ) || ! empty( RDTheme::$options['listing_search_items']['location'] ) ): ?>
    <div class="main-header-inner mobile-header-search">
        <div class="main-navigation-area">
            <form action="<?php echo esc_url( Functions::get_filter_form_url() ); ?>" class="form-inline rtcl-widget-search-form rtcl-search-inline-form classima-listing-search-form rtin-style-<?php echo esc_attr( $style ); ?>">
	            <?php if ( ! empty( RDTheme::$options['listing_search_items']['location'] ) ): ?>
                    <?php if ( method_exists('Rtcl\Helpers\Functions','location_type') && 'local' === Functions::location_type() ): ?>
                        <div class="rtin-loc-space">
                            <div class="form-group">
                                <?php if ( $style == 'suggestion' ): ?>
                                    <div class="rtcl-search-input-button classima-search-style-2 rtin-location">
                                        <input type="text" data-type="location" class="rtcl-autocomplete rtcl-location" placeholder="<?php echo esc_attr( $loc_text ); ?>" value="<?php echo $selected_location ? $selected_location->name : '' ?>">
                                        <input type="hidden" name="rtcl_location" value="<?php echo $selected_location ? $selected_location->slug : '' ?>">
                                    </div>
                                <?php elseif ( $style == 'standard' ): ?>
                                    <div class="rtcl-search-input-button classima-search-style-2 rtin-location">
                                        <?php
                                        $loc_args = array(
                                            'show_option_none'  => $loc_text,
                                            'option_none_value' => '',
                                            'taxonomy'          => rtcl()->location,
                                            'name'              => 'rtcl_location',
                                            'id'                => 'rtcl-location-search-' . wp_rand(),
                                            'class'             => 'form-control rtcl-location-search',
                                            'selected'          => get_query_var( 'rtcl_location' ),
                                            'hierarchical'      => true,
                                            'value_field'       => 'slug',
                                            'depth'             => Functions::get_location_depth_limit(),
                                            'orderby'           => $orderby,
                                            'order'             => ('DESC' === $order) ? 'DESC' : 'ASC',
                                            'show_count'        => false,
                                            'hide_empty'        => false,
                                        );
                                        if ('_rtcl_order' === $orderby) {
                                            $args['orderby'] = 'meta_value_num';
                                            $args['meta_key'] = '_rtcl_order';
                                        }
                                        wp_dropdown_categories($loc_args);
                                        ?>
                                    </div>
                                <?php elseif ( $style == 'dependency' ): ?>
                                    <div class="rtcl-search-input-button classima-search-style-2 rtin-location">
                                        <?php
                                        Functions::dropdown_terms( array(
                                            'show_option_none' => $loc_text,
                                            'taxonomy'         => rtcl()->location,
                                            'name'             => 'l',
                                            'class'            => 'form-control',
                                            'selected'         => $selected_location ? $selected_location->term_id : 0
                                        ) );
                                        ?>
                                    </div>
                                <?php else: ?>
                                    <div class="rtcl-search-input-button rtcl-search-input-location">
                                            <span class="search-input-label location-name">
                                                <?php echo $selected_location ? esc_html( $selected_location->name ) : esc_html( $loc_text ) ?>
                                            </span>
                                        <input type="hidden" class="rtcl-term-field" name="rtcl_location" value="<?php echo $selected_location ? esc_attr( $selected_location->slug ) : '' ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="rtin-loc-space">
                            <div class="form-group">
                                <div class="rtcl-search-input-button classima-search-style-2 rtin-location">
                                    <input type="text" name="geo_address" autocomplete="off"
                                           value="<?php echo !empty($_GET['geo_address']) ? esc_attr($_GET['geo_address']) : '' ?>"
                                           placeholder="<?php esc_html_e("Select a location", "classima"); ?>"
                                           class="form-control rtcl-geo-address-input"/>
                                    <i class="rtcl-get-location rtcl-icon rtcl-icon-target"></i>
                                    <input type="hidden" class="latitude" name="center_lat"
                                           value="<?php echo !empty($_GET['center_lat']) ? esc_attr($_GET['center_lat']) : '' ?>">
                                    <input type="hidden" class="longitude" name="center_lng"
                                           value="<?php echo !empty($_GET['center_lng']) ? esc_attr($_GET['center_lng']) : '' ?>">
                                </div>
                            </div>
                        </div>
                        <?php if ( !empty( RDTheme::$options['listing_search_items']['radius'] ) ): ?>
                            <div class="rtin-radius-space">
                                <div class="form-group">
                                    <div class="rtcl-search-input-button classima-search-style-2 rtin-radius">
                                        <i class=""></i>
                                        <input type="number" class="form-control" name="distance"
                                               value="<?php echo !empty($_GET['distance']) ? absint($_GET['distance']) : 30 ?>"
                                               placeholder="<?php esc_html_e("Radius", "classima"); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <input type="hidden" class="distance" name="distance" value="30">
                        <?php endif; ?>
                    <?php endif; ?>
	            <?php endif; ?>

                <?php if ( ! empty( RDTheme::$options['listing_search_items']['keyword'] ) ): ?>
                    <div class="rtin-key-space">
                        <div class="form-group">
                            <div class="rtcl-search-input-button rtin-keyword">
                                <input type="text" data-type="listing" name="q" class="rtcl-autocomplete" placeholder="<?php esc_html_e('Enter Keyword here ...', 'classima'); ?>" value="<?php if (isset($_GET['q'])) {echo esc_attr(Functions::clean( wp_unslash(($_GET['q']))));} ?>" />
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="rtin-btn-holder">
                    <button type="submit" class="rtin-search-btn rdtheme-button-1"><i class="fas fa-search" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>