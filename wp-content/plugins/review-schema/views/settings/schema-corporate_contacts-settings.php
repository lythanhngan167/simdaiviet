<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use Rtrs\Helpers\Functions; 

/**
 * Settings for corporate contacts
 */
$options = array( 
    'type' => array( 
        'title'    => esc_html__('Contact Type', "review-schema"),
        'type'     => 'select',
        'class'    => 'regular-text',
        'options'  => array(
            'customer_support'		=> esc_html__( 'Customer Support', 'review-schema' ),
            'technical_support'		=> esc_html__( 'Technical Support', 'review-schema' ),
            'billing_support'		=> esc_html__( 'Billing Support', 'review-schema' ),
            'bill_payment'			=> esc_html__( 'Bill Payment', 'review-schema' ),
            'sales'					=> esc_html__( 'Sales', 'review-schema' ),
            'reservations'			=> esc_html__( 'Reservations', 'review-schema' ),
            'credit_card_support'	=> esc_html__( 'Credit Card Support', 'review-schema' ),
            'emergency'				=> esc_html__( 'Emergency', 'review-schema' ),
            'baggage_tracking'		=> esc_html__( 'Baggage Tracking', 'review-schema' ),
            'roadside_assistance'	=> esc_html__( 'Roadside Assistance', 'review-schema' ),
            'package_tracking'		=> esc_html__( 'Package Tracking', 'review-schema' ),
        ),
        'empty'    => esc_html__('Select One', "review-schema"), 
    ),  
    'telephone' => array( 
        'type'        => 'text',
        'class'       => 'regular-text',
        'title'       => esc_html__('Telephone', "review-schema"),
        'description' => esc_html__('An internationalized version of the phone number, starting with the "+" symbol and country code (+1 in the US and Canada).', "review-schema"),
        'recommended' => true,
    ),
    'url' => array( 
        'title' => esc_html__( 'URL', 'review-schema' ),
        'description' => esc_html__( 'The URL of contact page.', 'review-schema' ),
        'type' => 'text',
        'placeholder' => 'https://',
        'class'   => 'regular-text'
    ),
    'email' => array( 
        'title' => esc_html__( 'Email', 'review-schema' ), 
        'type' => 'text', 
        'class'   => 'regular-text'
    ), 
    'contactOption' => array( 
        'title'    => esc_html__('Contact Option', "review-schema"),
        'type'     => 'select',
        'class'    => 'regular-text',
        'options'  => array(
            'TollFree'  => esc_html__( 'Toll Free', 'review-schema' ),
            'HearingImpairedSupported' => esc_html__( 'Hearing Impaired Supported', 'review-schema' ) 
        ),
        'empty'    => esc_html__('Select One', "review-schema"), 
    ), 
    'areaServed' => array( 
        'title'    => esc_html__('Area Served', "review-schema"),
        'type'     => 'multiselect',
        'class'    => 'regular-text rtrs-select2', 
        'options'  => Functions::getCountryList(),
        'empty'    => esc_html__('Select One', "review-schema"), 
    ),
    'availableLanguage' => array( 
        'title'    => esc_html__('Available Language', "review-schema"),
        'type'     => 'multiselect',
        'class'    => 'regular-text rtrs-select2', 
        'options'  => Functions::getLanguageList(),
        'empty'    => esc_html__('Select One', "review-schema"), 
    ),
);

return apply_filters( 'rtrs_schema_corporate_contacts_settings_options', $options );
