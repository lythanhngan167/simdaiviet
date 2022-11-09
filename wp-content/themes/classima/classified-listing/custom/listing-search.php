<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.5.3
 */

namespace radiustheme\Classima;

if (!class_exists( 'RtclPro' ) || !class_exists( 'Rtcl' )) return;

use Rtcl\Helpers\Functions;

$loc_class      = 'rtin-loc-space';
$radius_class   = 'rtin-radius-space';
$typ_class      = 'rtin-type-space';
$cat_class      = 'rtin-cat-space';
$key_class      = 'rtin-key-space';
$btn_class      = 'rtin-btn-holder';

$loc_text = esc_attr__( 'Select Location', 'classima' );
$cat_text = esc_attr__( 'Select Category', 'classima' );
$typ_text = esc_attr__( 'Select Type', 'classima' );

$selected_location = $selected_category = false;

if ( get_query_var( 'rtcl_location' ) && $location = get_term_by( 'slug', get_query_var( 'rtcl_location' ), rtcl()->location ) ) {
    $selected_location = $location;
}

if ( get_query_var( 'rtcl_category' ) && $category = get_term_by( 'slug', get_query_var( 'rtcl_category' ), rtcl()->category ) ) {
    $selected_category = $category;
}

$orderby = strtolower(Functions::get_option_item('rtcl_general_settings', 'taxonomy_orderby', 'name'));
$order = strtoupper(Functions::get_option_item('rtcl_general_settings', 'taxonomy_order', 'ASC'));

$style = RDTheme::$options['listing_search_style'];
?>
<div class="rtcl rtcl-search rtcl-search-inline classima-listing-search">
    <form action="<?php echo esc_url( Functions::get_filter_form_url() ); ?>" class="form-vertical rtcl-widget-search-form rtcl-search-inline-form classima-listing-search-form rtin-style-<?php echo esc_attr( $style );?>">
        <?php if ( !empty( RDTheme::$options['listing_search_items']['location'] ) ): ?>
            <?php if ( method_exists('Rtcl\Helpers\Functions','location_type') && 'local' === Functions::location_type() ): ?>
                <div class="<?php echo esc_attr( $loc_class ); ?>">
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
                <div class="<?php echo esc_attr( $loc_class ); ?>">
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
                    <div class="<?php echo esc_attr( $radius_class ); ?>">
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

        <?php if ( !empty( RDTheme::$options['listing_search_items']['category'] ) ): ?>
            <div class="<?php echo esc_attr( $cat_class );?>">
                <div class="form-group">
                    <?php if ( $style == 'suggestion' || $style == 'standard' ): ?>
                        <div class="rtcl-search-input-button classima-search-style-2 rtin-category">
                            <?php
                            $cat_args = array(
                                'show_option_none'  => $cat_text,
                                'option_none_value' => '',
                                'taxonomy'          => rtcl()->category,
                                'name'              => 'rtcl_category',
                                'id'                => 'rtcl-category-search-' . wp_rand(),
                                'class'             => 'form-control rtcl-category-search',
                                'selected'          => get_query_var( 'rtcl_category' ),
                                'hierarchical'      => true,
                                'value_field'       => 'slug',
                                'depth'             => Functions::get_category_depth_limit(),
                                'orderby'           => $orderby,
                                'order'             => ('DESC' === $order) ? 'DESC' : 'ASC',
                                'show_count'        => false,
                                'hide_empty'        => false,
                            );
                            if ('_rtcl_order' === $orderby) {
                                $args['orderby'] = 'meta_value_num';
                                $args['meta_key'] = '_rtcl_order';
                            }
                            wp_dropdown_categories($cat_args);
                            ?>
                        </div>
                    <?php elseif ( $style == 'dependency' ): ?>
                        <div class="rtcl-search-input-button classima-search-style-2 classima-search-dependency rtin-category">
                            <?php
                            Functions::dropdown_terms( array(
                                'show_option_none'  => $cat_text,
                                'option_none_value' => - 1,
                                'taxonomy'          => rtcl()->category,
                                'name'              => 'c',
                                'class'             => 'form-control rtcl-category-search',
                                'selected'          => $selected_category ? $selected_category->term_id : 0
                            ) );
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="rtcl-search-input-button rtcl-search-input-category">
                            <span class="search-input-label category-name">
                                <?php echo $selected_category ? esc_html( $selected_category->name ) : esc_html( $cat_text ); ?>
                            </span>
                            <input type="hidden" name="rtcl_category" class="rtcl-term-field" value="<?php echo $selected_category ? esc_attr( $selected_category->slug ) : '' ?>">
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if ( !empty( RDTheme::$options['listing_search_items']['type'] ) ): ?>
            <div class="<?php echo esc_attr( $typ_class );?>">
                <div class="form-group">
                    <div class="rtcl-search-input-button rtcl-search-input-type">
                        <?php
                        $listing_types = Functions::get_listing_types();
                        $listing_types = empty( $listing_types ) ? array() : $listing_types;
                        ?>
                        <div class="dropdown classima-listing-search-dropdown">
                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php esc_html_e( 'Select Type', 'classima' ); ?></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-adtype=""><?php echo esc_html( $typ_text ); ?></a>
                                <?php foreach ( $listing_types as $key => $listing_type ): ?>
                                    <a class="dropdown-item" href="#" data-adtype="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $listing_type ); ?></a>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="filters[ad_type]">
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( !empty( RDTheme::$options['listing_search_items']['keyword'] ) ): ?>
            <div class="<?php echo esc_attr( $key_class );?>">
                <div class="form-group">
                    <div class="rtcl-search-input-button rtin-keyword">
                        <input type="text" data-type="listing" name="q" class="rtcl-autocomplete" placeholder="<?php esc_html_e('Enter Keyword here ...', 'classima'); ?>" value="<?php if (isset($_GET['q'])) {echo esc_attr(Functions::clean( wp_unslash(($_GET['q']))));} ?>" />
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="<?php echo esc_attr( $btn_class );?> rtin-btn-holder">
            <button type="submit" class="rtin-search-btn rdtheme-button-1"><i class="fas fa-search" aria-hidden="true"></i><?php esc_html_e( 'Search', 'classima' );?></button>
        </div>
    </form>
</div>