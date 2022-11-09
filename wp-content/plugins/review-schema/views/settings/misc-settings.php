<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$options = [
	'text_section'              => [
		'title' => esc_html__( 'Misc Settings', 'review-schema' ),
		'type'  => 'title',
	],
	'title_reply'               => [
		'title' => esc_html__( 'Reply title', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'cancel_reply_link'         => [
		'title' => esc_html__( 'Cancel Reply Link Text', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'label_submit'              => [
		'title' => esc_html__( 'Submit Button Label', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'name_field_placeholder'    => [
		'title' => esc_html__( 'Name field placeholder', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'title_field_placeholder'   => [
		'title' => esc_html__( 'Title field placeholder', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'email_field_placeholder'   => [
		'title' => esc_html__( 'Email field placeholder', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'website_field_placeholder' => [
		'title' => esc_html__( 'Website field placeholder', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],
	'comment_field_placeholder' => [
		'title' => esc_html__( 'Comment field placeholder', 'review-schema' ),
		'type'  => 'text',
		'class' => 'regular-text',
		'description'  => esc_html__( 'Leave Empty For default Text.', 'review-schema' ),
	],

];

return apply_filters( 'rtrs_misc_settings_options', $options );
