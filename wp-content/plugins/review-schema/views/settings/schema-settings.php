<?php

if (! defined('ABSPATH')) {
	exit;
}

use Rtrs\Helpers\Functions;

$post_type = Functions::getPostTypes(false, false);

/**
 * Schema Settings.
 */
$str = sprintf(
	__('<strong>Note:</strong> You can override all post type from <a href="%s">Review Schema Generator</a>', 'review-schema'),
	admin_url('edit.php?post_type=rtrs')
);
$arr = ['a' => ['href' => []], 'strong' => []];

$options = [
	'general_section' => [
		'title'       => esc_html__('General', 'review-schema'),
		'type'        => 'title',
	],
	'post_type' => [
		'title'          => esc_html__('Schema Support', 'review-schema'),
		'description'    => wp_kses($str, $arr),
		'type'           => 'auto_schema',
		'options'        => $post_type,
	],
	'site_section' => [
		'title'       => esc_html__('Site Info', 'review-schema'),
		'type'        => 'title',
	],
	'site_schema' => [
		'title'      => esc_html__('Enable site schema', 'review-schema'),
		'type'       => 'radio',
		'class'      => 'regular-text',
		'default'    => 'home_page',
		'options'    => [
			'home_page'  => esc_html__('Home page only', 'review-schema'),
			'all'	       => esc_html__('Sitewide (Apply General Settings schema sitewide)', 'review-schema'),
			'off'	       => esc_html__('Turn off (Turn off global schema)', 'review-schema'),
		],
	],
	'url' => [
		'type'        => 'url',
		'class'       => 'regular-text',
		'default'     => trailingslashit(get_home_url()),
		'title'       => esc_html__('Web URL', 'review-schema') . "<span class='rtrs-required'>*</span>",
		'recommended' => true,
	],
	'category' => [
		'title'       => esc_html__('Category', 'review-schema'),
		'type'        => 'schema_type',
		'class'       => 'regular-text rtrs-select2',
		'required'    => true,
		'options'     => Functions::getSiteTypes(),
		'empty'       => esc_html__('Select One', 'review-schema'),
		'description' => esc_html__('Use the most appropriate schema category for local business', 'review-schema'),
	],
	'name' => [
		'type'     => 'text',
		'class'    => 'regular-text',
		'title'    => esc_html__('Name', 'review-schema'),
		'required' => true,
	],
	'alternateName' => [
		'type'     => 'text',
		'class'    => 'regular-text',
		'title'    => esc_html__('Alternate Name', 'review-schema'),
	],
	'image' => [
		'type'     => 'image',
		'title'    => esc_html__('Image', 'review-schema') . "<span class='rtrs-required'>*</span>",
	],
	'logo' => [
		'type'        => 'image',
		'title'       => esc_html__('Business Logo', 'review-schema'),
		'description' => esc_html__('The image must be 112x112px, at minimum.', 'review-schema'),
	],
	'priceRange' => [
		'type'        => 'text',
		'class'       => 'regular-text',
		'title'       => esc_html__('Price Range', 'review-schema') . "<span class='rtrs-required'>*</span>",
		'recommended' => true,
		'description' => esc_html__('The price range of the business, for example $$$.', 'review-schema'),
	],
	'telephone' => [
		'type'        => 'text',
		'class'       => 'regular-text',
		'title'       => esc_html__('Site Telephone', 'review-schema') . "<span class='rtrs-required'>*</span>",
		'recommended' => true,
	],
	'sameAs' => [
		'type'        => 'textarea',
		'class'       => 'regular-text',
		'title'       => esc_html__('Same As', 'review-schema'),
		'placeholder' => esc_html__('http://example1.com&#10;http://example1.com', 'review-schema'),
		'description' => wp_kses(__('Add additional url per line which are same as this site', 'review-schema'), ['br' => []]),
	],
	'address_section' => [
		'title'       => esc_html__('Address', 'review-schema'),
		'type'        => 'title',
	],
	'addresses' => [
		'type'   => 'group',
		'is_pro' => true,
		'title'  => esc_html__('Address', 'review-schema'),
		'fields' => [
			'streetAddress' => [
				'type'  => 'text',
				'class' => 'regular-text',
				'title' => esc_html__('Street Address', 'review-schema'),
			],
			'addressLocality' => [
				'type'         => 'text',
				'class'        => 'regular-text',
				'title'        => esc_html__('Address Locality', 'review-schema'),
				'description'  => esc_html__('City (i.e Melbourne)', 'review-schema'),
			],
			'addressRegion' => [
				'type'        => 'text',
				'class'       => 'regular-text',
				'title'       => esc_html__('Address Region', 'review-schema'),
				'description' => esc_html__('State (i.e. Victoria)', 'review-schema'),
			],
			'postalCode' => [
				'type'  => 'text',
				'class' => 'regular-text',
				'title' => esc_html__('Postal Code', 'review-schema'),
			],
			'addressCountry' => [
				'title'    => esc_html__('Country', 'review-schema'),
				'type'     => 'select',
				'class'    => 'regular-text ',
				'options'  => Functions::getCountryList(),
				'empty'    => esc_html__('Select One', 'review-schema'),
			],
		],
	],
	'geo_coordinates_section' => [
		'title'  => esc_html__('Geo Coordinates', 'review-schema'),
		'type'   => 'title',
	],
	'latitude' => [
		'title' => esc_html__('Latitude', 'review-schema'),
		'type'  => 'text',
		'class' => 'regular-text',
	],
	'longitude' => [
		'title' => esc_html__('Longitude', 'review-schema'),
		'type'  => 'text',
		'class' => 'regular-text',
	],
	'radius' => [
		'title' => esc_html__('Radius', 'review-schema'),
		'type'  => 'number',
		'class' => 'regular-text',
	],
	'site_others_section' => [
		'title'       => esc_html__('Site Others Info', 'review-schema'),
		'type'        => 'title',
	],
	'description' => [
		'type'        => 'textarea',
		'class'       => 'regular-text',
		'title'       => esc_html__('Description', 'review-schema'),
	],
	'openingHours' => [
		'type'            => 'textarea',
		'class'           => 'regular-text',
		'title'           => esc_html__('Opening Hours', 'review-schema'),
		'placeholder'     => esc_html__('Monday 11:00-14:30&#10;Tuesday 17:00-21:30', 'review-schema'),
		'description'     => wp_kses(__('- Days are specified with the day name. Like: Monday, Tuesday</br>
        - Times are specified using 24:00 time. For example, 3PM is specified as 15:00. <br>
        - Add Opening Hours by separate line. Like: Monday 10:00-18:00', 'review-schema'), ['br' => []]),
	],
	'servesCuisine' => [
		'type'        => 'text',
		'class'       => 'regular-text',
		'title'       => esc_html__('Serves Cuisine', 'review-schema'),
	],
	'menu' => [
		'type'        => 'url',
		'class'       => 'regular-text',
		'title'       => esc_html__('Restaurant Menu URL', 'review-schema'),
	],
	'acceptsReservations'  => [
		'title'   => esc_html__('Accepts Reservations', 'review-schema'),
		'type'    => 'checkbox',
		'label'   => esc_html__('Accept', 'review-schema'),
	],
];

return apply_filters('rtrs_schema_settings_options', $options);
