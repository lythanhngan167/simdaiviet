<?php
include("order.php");
add_action( 'wp_enqueue_scripts', 'classima_child_styles', 18 );
function classima_child_styles() {
	wp_enqueue_style( 'classipost-style', get_stylesheet_uri() );
}

add_action( 'after_setup_theme', 'classima_child_theme_setup' );
function classima_child_theme_setup() {
    load_child_theme_textdomain( 'classima', get_stylesheet_directory() . '/languages' );
}
