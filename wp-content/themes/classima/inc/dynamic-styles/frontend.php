<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4.1
 */

namespace radiustheme\Classima;

Helper::requires( 'dynamic-styles/common.php' );

/*-------------------------------------
INDEX
=======================================
#. Defaults
#. Typography
#. Header
#. Breadcrumb
#. Footer
#. Theme Defaults
#. Widgets
#. Contents Area
-------------------------------------*/

$primary_color    = Helper::get_primary_color(); // #1aa78e
$secondary_color  = Helper::get_secondary_color(); // #fcaf01
$primary_rgb      = Helper::hex2rgb( $primary_color ); // 26, 167, 142
$secondary_rgb    = Helper::hex2rgb( $secondary_color ); // 252, 175, 1

$menu_typo     = RDTheme::$options['menu_typo'];
$submenu_typo  = RDTheme::$options['submenu_typo'];
$resmenu_typo  = RDTheme::$options['resmenu_typo'];


$top_bar_bgcolor          = RDTheme::$options['top_bar_bgcolor'];
$menu_color               = RDTheme::$options['menu_color'];
$menu_hover_color         = RDTheme::$options['sitewide_color'] == 'custom' ? RDTheme::$options['menu_hover_color'] : $primary_color;
$submenu_color            = RDTheme::$options['submenu_color'];
$submenu_hover_color      = RDTheme::$options['submenu_hover_color'];
$submenu_hover_bgcolor    = RDTheme::$options['sitewide_color'] == 'custom' ? RDTheme::$options['submenu_hover_bgcolor'] : $primary_color;


$breadcrumb_link_color       = RDTheme::$options['breadcrumb_link_color'];
$breadcrumb_link_hover_color = RDTheme::$options['sitewide_color'] == 'custom' ? RDTheme::$options['breadcrumb_link_hover_color'] : $primary_color;
$breadcrumb_active_color     = RDTheme::$options['breadcrumb_active_color'];
$breadcrumb_seperator_color  = RDTheme::$options['breadcrumb_seperator_color'];


$footer_bgcolor          = RDTheme::$options['footer_bgcolor'];
$footer_title_color      = RDTheme::$options['footer_title_color'];
$footer_color            = RDTheme::$options['footer_color'];
$footer_link_color       = RDTheme::$options['footer_link_color'];
$footer_link_hover_color = RDTheme::$options['sitewide_color'] == 'custom' ? RDTheme::$options['footer_link_hover_color'] : $primary_color;
$copyright_bgcolor       = RDTheme::$options['copyright_bgcolor'];
$copyright_color         = RDTheme::$options['copyright_color'];
?>

<?php
/*-------------------------------------
#. Defaults
---------------------------------------*/
?>
.primary-color {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.secondary-color {
	color: <?php echo esc_html( $secondary_color ); ?>;
}
.primary-bgcolor {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.secondary-bgcolor {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}

.post-nav-links > span, .post-nav-links > a:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Typography
---------------------------------------*/
?>
.main-header .main-navigation-area .main-navigation ul li a {
	font-family: <?php echo esc_html( $menu_typo['font-family'] ); ?>, sans-serif;
	font-size : <?php echo esc_html( $menu_typo['font-size'] ); ?>;
	font-weight : <?php echo esc_html( $menu_typo['font-weight'] ); ?>;
	line-height : <?php echo esc_html( $menu_typo['line-height'] ); ?>;
	text-transform : <?php echo esc_html( $menu_typo['text-transform'] ); ?>;
	font-style: <?php echo empty( $menu_typo['font-style'] ) ? 'normal' : $menu_typo['font-style']; ?>;
}
.main-header .main-navigation-area .main-navigation ul li ul li a {
	font-family: <?php echo esc_html( $submenu_typo['font-family'] ); ?>, sans-serif;
	font-size : <?php echo esc_html( $submenu_typo['font-size'] ); ?>;
	font-weight : <?php echo esc_html( $submenu_typo['font-weight'] ); ?>;
	line-height : <?php echo esc_html( $submenu_typo['line-height'] ); ?>;
	text-transform : <?php echo esc_html( $submenu_typo['text-transform'] ); ?>;
	font-style: <?php echo empty( $submenu_typo['font-style'] ) ? 'normal' : $submenu_typo['font-style']; ?>;
}
.mean-container .mean-nav ul li a {
	font-family: <?php echo esc_html( $resmenu_typo['font-family'] ); ?>, sans-serif;
	font-size : <?php echo esc_html( $resmenu_typo['font-size'] ); ?>;
	font-weight : <?php echo esc_html( $resmenu_typo['font-weight'] ); ?>;
	line-height : <?php echo esc_html( $resmenu_typo['line-height'] ); ?>;
	text-transform : <?php echo esc_html( $resmenu_typo['text-transform'] ); ?>;
	font-style: <?php echo empty( $resmenu_typo['font-style'] ) ? 'normal' : $resmenu_typo['font-style']; ?>;
}

.header-btn {
	font-family: <?php echo esc_html( $menu_typo['font-family'] ); ?>, sans-serif;
	font-size : <?php echo esc_html( $menu_typo['font-size'] ); ?>;
	font-weight : <?php echo esc_html( $menu_typo['font-weight'] ); ?>;
	line-height : <?php echo esc_html( $menu_typo['line-height'] ); ?>;
}

<?php
/*-------------------------------------
#. Header
---------------------------------------*/
?>
<?php // Top Bar ?>
.top-header {
    background-color: <?php echo esc_html( $top_bar_bgcolor ); ?>;
}
.top-header .top-header-inner .tophead-info li .fa {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.top-header .top-header-inner .tophead-social li a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}

<?php // Main Menu ?>
.main-header .main-navigation-area .main-navigation ul li a {
	color: <?php echo esc_html( $menu_color ); ?>;
}
.main-header .main-navigation-area .main-navigation ul.menu > li > a:hover {
    color: <?php echo esc_html( $menu_hover_color ); ?>;
}
.main-header .main-navigation-area .main-navigation ul.menu > li.current-menu-item > a::after,
.main-header .main-navigation-area .main-navigation ul.menu > li.current > a::after {
    background-color: <?php echo esc_html( $menu_hover_color ); ?>;
}

<?php // Sub Menu ?>
.main-header .main-navigation-area .main-navigation ul li ul {
	border-color: <?php echo esc_html( $primary_color ); ?>;
}
.main-header .main-navigation-area .main-navigation ul li ul li a {
	color: <?php echo esc_html( $submenu_color ); ?>;
}
.main-header .main-navigation-area .main-navigation ul li ul li:hover > a {
	color: <?php echo esc_html( $submenu_hover_color ); ?>;
    background-color: <?php echo esc_html( $submenu_hover_bgcolor ); ?>;
}

<?php // Multi Column Menu ?>
.main-header .main-navigation-area .main-navigation ul li.mega-menu > ul.sub-menu > li > a {
    background-color: <?php echo esc_html( $submenu_hover_bgcolor ); ?>;
}

<?php // Mean Menu ?>
.mean-container .mean-bar {
	border-color: <?php echo esc_html( $primary_color ); ?>;
}
.mean-container a.meanmenu-reveal,
.mean-container .mean-nav ul li a:hover,
.mean-container .mean-nav > ul > li.current-menu-item > a,
.mean-container .mean-nav ul li a.mean-expand {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.mean-container a.meanmenu-reveal span {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.mean-container a.meanmenu-reveal span:before {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.mean-container a.meanmenu-reveal span:after {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.mean-bar span.sidebarBtn span:before,
.mean-bar span.sidebarBtn span:after,
.mean-bar span.sidebarBtn span {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
.offscreen-navigation li.menu-item-has-children> a:after {
    color: <?php echo esc_html( $primary_color ); ?>;
}

<?php // Header icon and button ?>
a.header-chat-icon .rtcl-unread-badge {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.header-btn {
    background: <?php echo esc_html( $primary_color ); ?>;
}
.header-btn:hover {
    background: <?php echo esc_html( $secondary_color ); ?>;
}

@media all and (min-width: 992px) {
    .header-style-6 .main-header {
        background-color: <?php echo esc_html( $primary_color ); ?>;
    }
    .header-style-6 .header-btn i {
        background-color: <?php echo esc_html( $primary_color ); ?>;
    }
    .header-style-6 .header-btn-area .header-btn:hover {
        background-color: <?php echo esc_html( $secondary_color ); ?>;
    }
}

<?php
/*-------------------------------------
#. Breadcrumb
---------------------------------------*/
?>
.main-breadcrumb {
	color: <?php echo esc_html( $breadcrumb_seperator_color ); ?>;
}
.main-breadcrumb a span {
	color: <?php echo esc_html( $breadcrumb_link_color ); ?>;
}
.main-breadcrumb span {
	color: <?php echo esc_html( $breadcrumb_active_color ); ?>;
}
.main-breadcrumb a span:hover {
	color: <?php echo esc_html( $breadcrumb_link_hover_color ); ?>;
}

<?php
/*-------------------------------------
#. Footer
---------------------------------------*/
?>
.footer-top-area {
	background-color: <?php echo esc_html( $footer_bgcolor ); ?>;
}
.footer-top-area .widget > h3 {
	color: <?php echo esc_html( $footer_title_color ); ?>;
}
.footer-top-area .widget {
	color: <?php echo esc_html( $footer_color ); ?>;
}
.footer-top-area a:link,
.footer-top-area a:visited {
	color: <?php echo esc_html( $footer_link_color ); ?>;
}
.footer-top-area .widget a:hover,
.footer-top-area .widget a:active {
	color: <?php echo esc_html( $footer_link_hover_color ); ?>;
}
.footer-bottom-area {
	background-color: <?php echo esc_html( $copyright_bgcolor ); ?>;
	color: <?php echo esc_html( $copyright_color ); ?>;
}
.footer-style-2 .footer-top-area .widget .menu li a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}
.footer-style-2 .footer-top-area .widget .menu li a:hover:after {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Theme Defaults
---------------------------------------*/
?>
a.scrollToTop {
    background-color: rgba(<?php echo esc_html( $primary_rgb ); ?>, 0.3);
    color: <?php echo esc_html( $primary_color ); ?>;
    border-color: <?php echo esc_html( $primary_color ); ?>;
}
a.scrollToTop:hover,
a.scrollToTop:focus {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
a.rdtheme-button-1,
.rdtheme-button-1 {
	background: linear-gradient(to bottom, rgba(<?php echo esc_html( $secondary_rgb ); ?>, 0.8), <?php echo esc_html( $secondary_color ); ?>);
}
a.rdtheme-button-1:hover,
.rdtheme-button-1:hover {
    background: <?php echo esc_html( $secondary_color ); ?>;
}
a.rdtheme-button-3,
.rdtheme-button-3 {
    background-color: <?php echo esc_html( $primary_color ); ?>;
}
a.rdtheme-button-3:hover,
.rdtheme-button-3:hover {
    background-color: <?php echo esc_html( $secondary_color ); ?>;
}

<?php
/*-------------------------------------
#. Widgets
---------------------------------------*/
?>
.widget a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.widget h3:after {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.widget.widget_tag_cloud a:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
	border-color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .widget a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .widget ul li:before {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.sidebar-widget-area .widget.rtcl-widget-filter-class h3 {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.widget.widget_classima_about ul li a:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php
/*-------------------------------------
#. Contents Area
---------------------------------------*/
?>
.pagination-area ul li:not(:first-child):not(:last-child) a:hover,
.pagination-area ul li:not(:first-child):not(:last-child).active a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.pagination-area ul li.pagi-previous a:hover,
.pagination-area ul li.pagi-next a:hover,
.pagination-area ul li.pagi-previous span:hover,
.pagination-area ul li.pagi-next span:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.pagination-area ul li.pagi-previous i,
.pagination-area ul li.pagi-next i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.search-form .custom-search-input button.btn {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.post-each .post-title a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.post-each .post-meta li i {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.post-each.post-each-single .post-footer .post-tags a:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
	border-color: <?php echo esc_html( $primary_color ); ?>;
}
.post-author-block .rtin-right .author-name a:hover {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.post-title-block:after,
.comment-reply-title:after {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.comments-area .main-comments .comment-meta .reply-area a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.comments-area .main-comments .comment-meta .reply-area a:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
#respond form .btn-send {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
#respond form .btn-send:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.post-password-form input[type="submit"] {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.post-password-form input[type="submit"]:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.error-page .error-btn {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.error-page .error-btn:hover {
	background-color: <?php echo esc_html( $secondary_color ); ?>;
}
.wpcf7-form .wpcf7-submit {
	background: <?php echo esc_html( $primary_color ); ?>;
}
.wpcf7-form .wpcf7-submit:hover,
.wpcf7-form .wpcf7-submit:active {
	background: <?php echo esc_html( $secondary_color ); ?>;
}
.post-each .rtin-button.post-btn a {
    background: <?php echo esc_html( $primary_color ); ?>;
    border-color: <?php echo esc_html( $primary_color ); ?>;
}
.post-each .rtin-button.post-btn a:hover {
    background: <?php echo esc_html( $secondary_color ); ?>;
    border-color: <?php echo esc_html( $secondary_color ); ?>;
}
.classima-related-post .entry-categories a:hover {
    color: <?php echo esc_html( $primary_color ); ?>;
}