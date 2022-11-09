<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\Classima;

/*-------------------------------------
INDEX
=======================================
#. Listing Search
#. Single Listing
#. Archive Listing
#. Grid View
#. List View
#. Listing Form
#. My Account
#. Checkout
#. WooCommerce
#. Store
-------------------------------------*/

$primary_color    = Helper::get_primary_color(); // #1aa78e
$secondary_color  = Helper::get_secondary_color(); // #fcaf01
$primary_rgb      = Helper::hex2rgb( $primary_color ); // 26, 167, 142
$secondary_rgb    = Helper::hex2rgb( $secondary_color ); // 252, 175, 1
?>

<?php
/*-------------------------------------
#. Listing Search
---------------------------------------*/
?>
.classima-listing-search-form .rtin-search-btn {
	background: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-search-form .rtin-search-btn:hover {
	background: <?php echo esc_html( $secondary_color ); ?>;
}
.rtcl-ui-modal .rtcl-modal-wrapper .rtcl-modal-content .rtcl-content-wrap .rtcl-ui-select-list-wrap .rtcl-ui-select-list ul li a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Single Listing
---------------------------------------*/
?>
.classima-listing-single .owl-carousel .owl-nav [class*=owl-] {
	border-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .owl-carousel .owl-nav [class*=owl-]:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details .rtin-slider-box #rtcl-slider-wrapper .rtcl-listing-gallery__trigger {
	background-color: <?php echo esc_html( $secondary_color ); ?>
}
.classima-listing-single .classima-single-details .rtin-slider-box .rtcl-price-amount {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.classima-listing-single .classima-single-details .single-listing-meta-price-mob .rtin-price {
	background: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details .rtin-specs .rtin-spec-items li:before {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details .rtcl-single-listing-action li a:hover {
	color: <?php echo esc_html( $secondary_color ); ?>;
}
.classima-listing-single .classima-single-details .nav-tabs a.active {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

.classima-listing-single .classima-single-details-2 .rtin-price {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details-2 .rtin-specs-title::after {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

#content .classima-listing-single .classima-single-related .owl-related-nav .owl-prev:hover,
#content .classima-listing-single .classima-single-related .owl-related-nav .owl-next:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single-sidebar .rtin-price {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classified-seller-info .rtin-box .rtin-phone .numbers:before {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classified-seller-info .rtin-box .rtin-phone .rtcl-contact-reveal-wrapper .numbers a:first-child::before {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classified-seller-info .rtin-box .rtin-email a i {
	color: <?php echo esc_html( $secondary_color ); ?>;
}
.classified-seller-info .rtin-box .rtin-chat a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classified-seller-info .rtin-box .rtin-chat a:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?>;
}

#classima-mail-to-seller .btn {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#classima-mail-to-seller .btn:hover,
#classima-mail-to-seller .btn:active {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.review-area .comment .comment-meta .comment-meta-left .comment-info .c-author {
	color: <?php echo esc_html( $primary_color ); ?>;
}

.classima-listing-single .classima-single-details-3 .rtin-price {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details-3 .rtin-specs-title:after {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details .rtin-slider-box #rtcl-slider-wrapper .swiper-button-prev,
.classima-listing-single .classima-single-details .rtin-slider-box #rtcl-slider-wrapper .swiper-button-next,
.rtrs-review-wrap .rtrs-review-box .rtrs-review-body .rtrs-reply-btn .rtrs-item-btn:hover,
.rtrs-review-wrap .rtrs-review-form .rtrs-form-group .rtrs-submit-btn {
    background-color: <?php echo esc_html( $primary_color ); ?> !important;
}
.classima-listing-single .classima-single-details .rtin-slider-box #rtcl-slider-wrapper .swiper-button-prev:hover,
.classima-listing-single .classima-single-details .rtin-slider-box #rtcl-slider-wrapper .swiper-button-next:hover,
.rtrs-review-wrap .rtrs-review-form .rtrs-form-group .rtrs-submit-btn:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?> !important;
}

<?php
/*-------------------------------------
#. Archive Listing
---------------------------------------*/
?>
a#classima-toggle-sidebar {
	background: <?php echo esc_html( $secondary_color ); ?>;
}
.sidebar-widget-area .widget .rtcl-widget-categories ul.rtcl-category-list li a:hover,
.sidebar-widget-area .widget .rtcl-widget-categories ul.rtcl-category-list li.rtcl-active > a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .rtcl-widget-filter-class .panel-block .ui-accordion-item .ui-accordion-content .filter-list li .sub-list li a:before {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-buttons .btn {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-buttons .btn:hover,
.sidebar-widget-area .rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-buttons .btn:focus {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}

.sidebar-widget-area .rtcl-widget-filter-class .panel-block .ui-accordion-item.is-open .ui-accordion-title .ui-accordion-icon {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.site-content .listing-archive-top .listing-sorting .rtcl-view-switcher > a.active i,
.site-content .listing-archive-top .listing-sorting .rtcl-view-switcher > a:hover i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-accordion-item .ui-accordion-title {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-accordion-item.rtcl-ad-type-filter a.filter-submit-trigger:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-accordion-item ul.filter-list li a:hover, .elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-accordion-item ul.filter-list li.rtcl-active>a {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-accordion-item ul.filter-list li.has-sub ul.sub-list li a:before {
color: <?php echo esc_html( $primary_color ); ?>;
}
.elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-buttons .btn {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.elementor-widget .widget.rtcl-widget-filter-class .panel-block .rtcl-filter-form .ui-buttons .btn:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.sidebar-widget-area .widget.rtcl-widget-filter-class .panel-block ul.filter-list li.active>a {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-7 .rtin-item .rtin-thumb .rtin-price {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-content .rtin-meta li i,
#content .listing-grid-each-7 .rtin-item .rtin-bottom .action-btn a:hover, #content .listing-grid-each-7 .rtin-item .rtin-bottom .action-btn a.rtcl-favourites:hover .rtcl-icon {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-right .rtin-price .rtcl-price-meta,
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-right .rtin-price .rtcl-price-amount,
.rtcl-quick-view-container .rtcl-qv-summary .rtcl-qv-price,
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-content .rtin-cat:hover,
#content .rtcl-list-view .rtin-title a:hover,
#content .listing-grid-each-7 .rtin-item .rtin-content .rtin-title a:hover,
.rtcl-quick-view-container .rtcl-qv-summary .rtcl-qv-title a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-list-each-4 .rtin-price .rtcl-price-range,
#content .listing-list-each-6 .rtin-price .rtcl-price-range,
#content .listing-grid-each-1 .rtin-price .rtcl-price-range,
#content .listing-grid-each-2 .rtin-price .rtcl-price-range,
#content .listing-grid-each-4 .rtin-price .rtcl-price-range {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-right .rtin-quick-view a:hover,
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-right .rtin-fav a:hover,
#content .rtcl-list-view .listing-list-each-6 .rtin-item .rtin-right .rtin-compare a:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.classima-listing-single .classima-single-details .single-listing-meta-wrap .single-listing-meta li i {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .rtcl-widget-filter-class .panel-block .ui-accordion-item .ui-accordion-title .ui-accordion-icon {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
#rtcl-compare-btn-wrap a.rtcl-compare-btn, #rtcl-compare-panel-btn {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
#rtcl-compare-btn-wrap a.rtcl-compare-btn:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.rtcl-compare-table .rtcl-compare-table-title h3 a:hover,
#rtcl-compare-wrap .rtcl-compare-item h4.rtcl-compare-item-title a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.single-rtcl_listing .classima-single-details.classima-single-details-4 .rtin-slider-box #rtcl-slider-wrapper .rtcl-slider-nav:hover .swiper-button-next:after,
.single-rtcl_listing .classima-single-details.classima-single-details-4 .rtin-slider-box #rtcl-slider-wrapper .rtcl-slider-nav:hover .swiper-button-prev:after {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-single-details-4 .rtcl-price-amount {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-listing-single .classima-single-details-4 .rtin-specs-title:after {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .rtin-details4-sidebar .classima-single-map h3.main-title::after,
.sidebar-widget-area .rtin-details4-sidebar .widget h3::after {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Grid View
---------------------------------------*/
?>
#content .listing-grid-each .rtin-item .rtin-content .rtin-cat:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each.featured-listing .rtin-thumb:after {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-1 .rtin-item .rtin-content .rtin-title a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-1 .rtin-item .rtin-content .rtin-price .rtcl-price-amount {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each.listing-grid-each-2 .rtin-item .rtin-content .rtin-title a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each.listing-grid-each-2 .rtin-item .rtin-content .rtin-price .rtcl-price-amount {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-3 .rtin-item .rtin-thumb .rtin-price {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-3 .rtin-item .rtin-content .rtin-title a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-3 .rtin-item .rtin-content .rtin-bottom .rtin-phn .classima-phone-reveal:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .listing-grid-each-3 .rtin-item .rtin-content .rtin-bottom .rtin-fav a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .listing-grid-each-4 .rtin-item .rtin-content .rtin-title a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-4 .rtin-item .rtin-content .rtin-price .rtcl-price-amount {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-4 .rtin-item .rtin-content .rtin-bottom .rtin-phn .classima-phone-reveal:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .listing-grid-each-4 .rtin-item .rtin-content .rtin-bottom .rtin-fav a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .listing-grid-each-6 .rtin-item .rtin-content .rtin-price .rtcl-price-amount {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-6 .rtin-item .rtin-content .rtin-title a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each .rtin-item .rtin-content .rtcl-price-meta {
    color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each .rtin-item .rtin-thumb .rtin-type {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each-8 .rtin-item .rtin-thumb .rtcl-meta-buttons .rtcl-btn:hover {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .listing-grid-each .rtin-item .rtin-content .rtin-meta li a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}
<?php
/*-------------------------------------
#. List View
---------------------------------------*/
?>
#content .rtcl-list-view .listing-list-each.featured-listing .rtin-thumb::after {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .rtin-title a a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-1 .rtin-item .rtin-content .rtin-cat-wrap .rtin-cat:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-1 .rtin-item .rtin-content .rtin-meta li i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-1 .rtin-item .rtin-right .rtin-details a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-1 .rtin-item .rtin-right .rtin-details a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-2 .rtin-item .rtin-content .rtin-cat:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-2 .rtin-item .rtin-right .rtin-details a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-2 .rtin-item .rtin-right .rtin-details a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-3 .rtin-item .rtin-content .rtin-price {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-3 .rtin-item .rtin-content .rtin-cat:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-4 .rtin-item .rtin-content .rtin-cat:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-4 .rtin-item .rtin-content .rtin-meta li i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-4 .rtin-item .rtin-right .rtin-price .rtcl-price-amount {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-4 .rtin-item .rtin-right .rtin-phn .classima-phone-reveal:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-4 .rtin-item .rtin-right .rtin-fav a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-5 .rtin-item .rtin-content .rtin-price .rtcl-price-amount {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-5 .rtin-item .rtin-content .rtin-meta li a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-4 .rtin-item .rtin-right .rtin-price .rtcl-price-meta {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-map-popup .rtcl-map-popup-content .rtcl-map-item-title a {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-map-popup .rtcl-map-popup-content .rtcl-map-item-title a:hover {
    color: <?php echo esc_html( $secondary_color ); ?>;
}
#content .rtcl-list-view .listing-list-each-2 .rtin-item .rtin-content .rtin-meta li.rtin-usermeta a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Listing Form
---------------------------------------*/
?>
.classima-form .classified-listing-form-title i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-form .rtcl-gallery-uploads .rtcl-gallery-upload-item a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-form .rtcl-gallery-uploads .rtcl-gallery-upload-item a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.classima-form .rtcl-submit-btn {
	background: linear-gradient(to bottom, rgba(<?php echo esc_html( $secondary_rgb ); ?>, 0.8), <?php echo esc_html( $secondary_color ); ?>);
}
.classima-form .rtcl-submit-btn:hover,
.classima-form .rtcl-submit-btn:active {
	background: <?php echo esc_html( $secondary_color ); ?>;
}
.rt-el-listing-location-box-2:hover .rtin-content {
    background: linear-gradient(to bottom, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.07) 0%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.11) 7%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.22) 14%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.4) 24%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.72) 37%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.83) 43%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.9) 50%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.95) 62%, rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.93) 100%);
}
.classima-form .rtcl-post-section-title i {
    color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. My Account
---------------------------------------*/
?>
.classima-myaccount .sidebar-widget-area .rtcl-MyAccount-navigation li.is-active,
.classima-myaccount .sidebar-widget-area .rtcl-MyAccount-navigation li:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-myaccount .sidebar-widget-area .rtcl-MyAccount-navigation li.rtcl-MyAccount-navigation-link--chat span.rtcl-unread-badge {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-myaccount .sidebar-widget-area .rtcl-MyAccount-navigation li.rtcl-MyAccount-navigation-link--chat:hover span.rtcl-unread-badge {
	color: <?php echo esc_html( $primary_color ); ?>;
}

#rtcl-user-login-wrapper .btn,
.rtcl .rtcl-login-form-wrap .btn,
#rtcl-lost-password-form .btn {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#rtcl-user-login-wrapper .btn:hover,
.rtcl .rtcl-login-form-wrap .btn:hover,
#rtcl-lost-password-form .btn:hover,
#rtcl-user-login-wrapper .btn:active,
.rtcl .rtcl-login-form-wrap .btn:active,
#rtcl-lost-password-form .btn:active {
	background: <?php echo esc_html( $secondary_color ); ?>;
}
.rtcl-account .rtcl-ui-modal .rtcl-modal-wrapper .rtcl-modal-content .rtcl-modal-body .btn-success {
    background-color: <?php echo esc_html( $primary_color ); ?>;
    border-color: <?php echo esc_html( $primary_color ); ?>;
}
#rtcl-store-managers-content .rtcl-store-manager-action .rtcl-store-invite-manager:hover,
#rtcl-store-managers-content .rtcl-store-manager-action .rtcl-store-invite-manager:active,
#rtcl-store-managers-content .rtcl-store-manager-action .rtcl-store-invite-manager:focus,
.rtcl-account .rtcl-ui-modal .rtcl-modal-wrapper .rtcl-modal-content .rtcl-modal-body .btn-success:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?>;
    border-color: <?php echo esc_html( $secondary_color ); ?>;
}
.rtcl-account .rtcl-ui-modal .rtcl-modal-wrapper .rtcl-modal-content .rtcl-modal-body .form-control:focus {
    border-color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-account-sub-menu ul li.active a,
.rtcl-account-sub-menu ul li:hover a {
    color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Checkout
---------------------------------------*/
?>
.rtcl-checkout-form-wrap .btn:hover,
.rtcl-checkout-form-wrap .btn:active,
.rtcl-checkout-form-wrap .btn:focus {
	background-color: <?php echo esc_html( $secondary_color ); ?> !important;
}
.rtcl-payment-history-wrap .page-item.active .page-link {
    background-color: <?php echo esc_html( $primary_color ); ?>;
	border-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. WooCommerce
---------------------------------------*/
?>
.woocommerce button.button {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.woocommerce button.button:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.woocommerce-info {
	border-color: <?php echo esc_html( $primary_color ); ?>;
}
.woocommerce-info:before {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.woocommerce-checkout .woocommerce .checkout #payment .place-order button#place_order,
.woocommerce form .woocommerce-address-fields #payment .place-order button#place_order {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.woocommerce-checkout .woocommerce .checkout #payment .place-order button#place_order:hover,
.woocommerce form .woocommerce-address-fields #payment .place-order button#place_order:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.woocommerce-account .woocommerce .woocommerce-MyAccount-navigation ul li.is-active a,
.woocommerce-account .woocommerce .woocommerce-MyAccount-navigation ul li.is-active a:hover,
.woocommerce-account .woocommerce .woocommerce-MyAccount-navigation ul li a:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Store
---------------------------------------*/
?>
.classima-store-single .rtin-banner-wrap .rtin-banner-content .rtin-store-title-area .rtin-title-meta li i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-store-single .classima-store-info .rtin-store-web i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-store-single .classima-store-info .rtin-oh-title i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-store-single .classima-store-info .rtin-phone .numbers:before {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-store-single .classima-store-info .rtin-email a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.classima-store-single .classima-store-info .rtin-email a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
<?php
/*-------------------------------------
#. Filter Range Slider
---------------------------------------*/
?>
.rtcl-range-slider-field input[type=range]::-webkit-slider-thumb {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-range-slider-field input[type=range]::-moz-range-thumb {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-range-slider-field input[type=range]::-ms-fill-lower {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-range-slider-field input[type=range]::-ms-thumb {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.rtcl-range-slider-field input[type=range]:focus::-ms-fill-lower {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .rtcl-widget-filter-wrapper.style2 .rtcl-widget-filter-class .panel-block .ui-accordion-item .ui-accordion-title::before {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}