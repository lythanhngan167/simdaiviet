<?php

namespace RtclPro\Gateways\Stripe\lib;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;

class StripeHelper
{


    /**
     * Gets the order by Stripe source ID.
     *
     * @since 4.0.0
     * @version 4.0.0
     * @param string $source_id
     */
    public static function get_order_by_source_id( $source_id ) {
        global $wpdb;

        $order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $source_id, '_stripe_source_id' ) );

        if ( ! empty( $order_id ) ) {
            return rtcl()->fatory->get( $order_id );
        }

        return false;
    }


    /**
     * Gets the order by Stripe charge ID.
     *
     * @since 4.0.0
     * @since 4.1.16 Return false if charge_id is empty.
     * @param string $charge_id
     */
    public static function get_order_by_charge_id( $charge_id ) {
        global $wpdb;

        if ( empty( $charge_id ) ) {
            return false;
        }

        $order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $charge_id, 'transaction_id' ) );

        if ( ! empty( $order_id ) ) {
            return rtcl()->factory->get_order( $order_id );
        }

        return false;
    }

    /**
     * Gets the order by Stripe PaymentIntent ID.
     *
     * @since 4.2
     * @param string $intent_id The ID of the intent.
     * @return Payment|bool Either an order or false when not found.
     */
    public static function get_order_by_intent_id( $intent_id ) {
        global $wpdb;

        $order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $intent_id, '_stripe_intent_id' ) );

        if ( ! empty( $order_id ) ) {
            return rtcl()->factory->get_order( $order_id );
        }

        return false;
    }

    /**
     * Gets the order by Stripe SetupIntent ID.
     *
     * @since 4.3
     * @param string $intent_id The ID of the intent.
     * @return Payment|bool Either an order or false when not found.
     */
    public static function get_order_by_setup_intent_id( $intent_id ) {
        global $wpdb;

        $order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $intent_id, '_stripe_setup_intent' ) );

        if ( ! empty( $order_id ) ) {
            return rtcl()->factory->get_order( $order_id );
        }

        return false;
    }

    /**
     * Checks Stripe minimum order value authorized per currency
     */
    public static function get_minimum_amount() {
        // Check order amount
        switch (Functions::get_order_currency()) {
            case 'USD':
            case 'CAD':
            case 'EUR':
            case 'CHF':
            case 'AUD':
            case 'SGD':
                $minimum_amount = 50;
                break;
            case 'GBP':
                $minimum_amount = 30;
                break;
            case 'DKK':
                $minimum_amount = 250;
                break;
            case 'NOK':
            case 'SEK':
                $minimum_amount = 300;
                break;
            case 'JPY':
                $minimum_amount = 5000;
                break;
            case 'MXN':
                $minimum_amount = 1000;
                break;
            case 'HKD':
                $minimum_amount = 400;
                break;
            default:
                $minimum_amount = 50;
                break;
        }

        return $minimum_amount;
    }


    /**
     * Get Stripe amount to pay
     *
     * @param float  $total    Amount due.
     * @param string $currency Accepted currency.
     *
     * @return float|int
     */
    public static function get_stripe_amount($total, $currency = '') {
        if (!$currency) {
            $currency = Functions::get_order_currency();
        }

        if (in_array(strtolower($currency), self::no_decimal_currencies())) {
            return absint($total);
        } else {
            return absint(Functions::format_decimal(((float)$total * 100), Functions::get_price_decimals())); // In cents.
        }
    }

    /**
     * List of currencies supported by Stripe that has no decimals
     * https://stripe.com/docs/currencies#zero-decimal from https://stripe.com/docs/currencies#presentment-currencies
     *
     * @return array $currencies
     */
    public static function no_decimal_currencies() {
        return [
            'bif', // Burundian Franc
            'clp', // Chilean Peso
            'djf', // Djiboutian Franc
            'gnf', // Guinean Franc
            'jpy', // Japanese Yen
            'kmf', // Comorian Franc
            'krw', // South Korean Won
            'mga', // Malagasy Ariary
            'pyg', // Paraguayan Guaraní
            'rwf', // Rwandan Franc
            'ugx', // Ugandan Shilling
            'vnd', // Vietnamese Đồng
            'vuv', // Vanuatu Vatu
            'xaf', // Central African Cfa Franc
            'xof', // West African Cfa Franc
            'xpf', // Cfp Franc
        ];
    }


    /**
     * Stripe uses the smallest denomination in currencies such as cents.
     * We need to format the returned currency from Stripe into human-readable form.
     * The amount is not used in any calculations so returning string is sufficient.
     *
     * @param object $balance_transaction
     * @param string $type Type of number to format
     *
     * @return string|void
     */
    public static function format_balance_fee($balance_transaction, $type = 'fee') {
        if (!is_object($balance_transaction)) {
            return;
        }

        if (in_array(strtolower($balance_transaction->currency), self::no_decimal_currencies())) {
            if ('fee' === $type) {
                return $balance_transaction->fee;
            }

            return $balance_transaction->net;
        }

        if ('fee' === $type) {
            return number_format($balance_transaction->fee / 100, 2, '.', '');
        }

        return number_format($balance_transaction->net / 100, 2, '.', '');
    }


    /**
     * Gets the Stripe fee for order. With legacy check.
     *
     * @param Payment $order
     *
     * @return string $amount
     * @since 4.1.0
     */
    public static function get_stripe_fee($order = null) {
        if (is_null($order)) {
            return false;
        }

        return get_post_meta($order->get_id(), '_stripe_fee', true);
    }

    /**
     * Updates the Stripe fee for order.
     *
     * @param object $order
     * @param float  $amount
     *
     * @since 4.1.0
     */
    public static function update_stripe_fee($order = null, $amount = 0.0) {
        if (is_null($order)) {
            return false;
        }
        update_post_meta($order->get_id(), '_stripe_fee', $amount);
    }

    /**
     * Gets the Stripe fee for order. With legacy check.
     *
     * @param Payment $order
     *
     * @return string $amount
     * @since 4.1.0
     */
    public static function get_stripe_net($order = null) {
        if (is_null($order)) {
            return false;
        }

        return get_post_meta($order->get_id(), '_stripe_net', true);
    }

    /**
     * Updates the Stripe net for order.
     *
     * @param object $order
     * @param float  $amount
     *
     * @since 4.1.0
     */
    public static function update_stripe_net($order = null, $amount = 0.0) {
        if (is_null($order)) {
            return false;
        }
        update_post_meta($order->get_id(), '_stripe_net', $amount);
    }

    /**
     * Updates the Stripe currency for order.
     *
     * @param object $order
     * @param string $currency
     *
     * @since 4.1.0
     */
    public static function update_stripe_currency($order, $currency) {
        if (is_null($order)) {
            return false;
        }

        update_post_meta($order->get_id(), '_stripe_currency', $currency);
    }


    /**
     * Sanitize statement descriptor text.
     *
     * Stripe requires max of 22 characters and no special characters.
     *
     * @param string $statement_descriptor
     *
     * @return string $statement_descriptor Sanitized statement descriptor
     * @since 4.0.0
     */
    public static function clean_statement_descriptor($statement_descriptor = '') {
        $disallowed_characters = ['<', '>', '\\', '*', '"', "'", '/', '(', ')', '{', '}'];

        // Strip any tags.
        $statement_descriptor = strip_tags($statement_descriptor);

        // Strip any HTML entities.
        // Props https://stackoverflow.com/questions/657643/how-to-remove-html-special-chars .
        $statement_descriptor = preg_replace('/&#?[a-z0-9]{2,8};/i', '', $statement_descriptor);

        // Next, remove any remaining disallowed characters.
        $statement_descriptor = str_replace($disallowed_characters, '', $statement_descriptor);

        // Trim any whitespace at the ends and limit to 22 characters.
        $statement_descriptor = substr(trim($statement_descriptor), 0, 22);

        return $statement_descriptor;
    }

    /**
     * Converts a WooCommerce locale to the closest supported by Stripe.js.
     *
     * Stripe.js supports only a subset of IETF language tags, if a country specific locale is not supported we use
     * the default for that language (https://stripe.com/docs/js/appendix/supported_locales).
     * If no match is found we return 'auto' so Stripe.js uses the browser locale.
     *
     * @param string $rtcl_locale The locale to convert.
     *
     * @return string Closest locale supported by Stripe ('auto' if NONE).
     */
    public static function convert_rtcl_locale_to_stripe_locale( $rtcl_locale ) {
        // List copied from: https://stripe.com/docs/js/appendix/supported_locales.
        $supported = [
            'ar',     // Arabic.
            'bg',     // Bulgarian (Bulgaria).
            'cs',     // Czech (Czech Republic).
            'da',     // Danish.
            'de',     // German (Germany).
            'el',     // Greek (Greece).
            'en',     // English.
            'en-GB',  // English (United Kingdom).
            'es',     // Spanish (Spain).
            'es-419', // Spanish (Latin America).
            'et',     // Estonian (Estonia).
            'fi',     // Finnish (Finland).
            'fr',     // French (France).
            'fr-CA',  // French (Canada).
            'he',     // Hebrew (Israel).
            'hu',     // Hungarian (Hungary).
            'id',     // Indonesian (Indonesia).
            'it',     // Italian (Italy).
            'ja',     // Japanese.
            'lt',     // Lithuanian (Lithuania).
            'lv',     // Latvian (Latvia).
            'ms',     // Malay (Malaysia).
            'mt',     // Maltese (Malta).
            'nb',     // Norwegian Bokmål.
            'nl',     // Dutch (Netherlands).
            'pl',     // Polish (Poland).
            'pt-BR',  // Portuguese (Brazil).
            'pt',     // Portuguese (Brazil).
            'ro',     // Romanian (Romania).
            'ru',     // Russian (Russia).
            'sk',     // Slovak (Slovakia).
            'sl',     // Slovenian (Slovenia).
            'sv',     // Swedish (Sweden).
            'th',     // Thai.
            'tr',     // Turkish (Turkey).
            'zh',     // Chinese Simplified (China).
            'zh-HK',  // Chinese Traditional (Hong Kong).
            'zh-TW',  // Chinese Traditional (Taiwan).
        ];

        // Stripe uses '-' instead of '_' (used in WordPress).
        $locale = str_replace( '_', '-', $rtcl_locale );

        if ( in_array( $locale, $supported, true ) ) {
            return $locale;
        }

        // The plugin has been fully translated for Spanish (Ecuador), Spanish (Mexico), and
        // Spanish(Venezuela), and partially (88% at 2021-05-14) for Spanish (Colombia).
        // We need to map these locales to Stripe's Spanish (Latin America) 'es-419' locale.
        // This list should be updated if more localized versions of Latin American Spanish are
        // made available.
        $lowercase_locale                  = strtolower( $rtcl_locale );
        $translated_latin_american_locales = [
            'es_co', // Spanish (Colombia).
            'es_ec', // Spanish (Ecuador).
            'es_mx', // Spanish (Mexico).
            'es_ve', // Spanish (Venezuela).
        ];
        if ( in_array( $lowercase_locale, $translated_latin_american_locales, true ) ) {
            return 'es-419';
        }

        // Finally, we check if the "base locale" is available.
        $base_locale = substr( $rtcl_locale, 0, 2 );
        if ( in_array( $base_locale, $supported, true ) ) {
            return $base_locale;
        }

        // Default to 'auto' so Stripe.js uses the browser locale.
        return 'auto';
    }

    /**
     * Localize Stripe messages based on code
     *
     * @return array
     * @version 3.0.6
     * @since   3.0.6
     */
    public static function get_localized_messages() {
        return apply_filters(
            'rtcl_stripe_localized_messages',
            [
                'invalid_number'           => __('The card number is not a valid credit card number.', 'classified-listing-pro'),
                'invalid_expiry_month'     => __('The card\'s expiration month is invalid.', 'classified-listing-pro'),
                'invalid_expiry_year'      => __('The card\'s expiration year is invalid.', 'classified-listing-pro'),
                'invalid_cvc'              => __('The card\'s security code is invalid.', 'classified-listing-pro'),
                'incorrect_number'         => __('The card number is incorrect.', 'classified-listing-pro'),
                'incomplete_number'        => __('The card number is incomplete.', 'classified-listing-pro'),
                'incomplete_cvc'           => __('The card\'s security code is incomplete.', 'classified-listing-pro'),
                'incomplete_expiry'        => __('The card\'s expiration date is incomplete.', 'classified-listing-pro'),
                'expired_card'             => __('The card has expired.', 'classified-listing-pro'),
                'incorrect_cvc'            => __('The card\'s security code is incorrect.', 'classified-listing-pro'),
                'incorrect_zip'            => __('The card\'s zip code failed validation.', 'classified-listing-pro'),
                'invalid_expiry_year_past' => __('The card\'s expiration year is in the past', 'classified-listing-pro'),
                'card_declined'            => __('The card was declined.', 'classified-listing-pro'),
                'missing'                  => __('There is no card on a customer that is being charged.', 'classified-listing-pro'),
                'processing_error'         => __('An error occurred while processing the card.', 'classified-listing-pro'),
                'invalid_sofort_country'   => __('The billing country is not accepted by SOFORT. Please try another country.', 'classified-listing-pro'),
                'email_invalid'            => __('Invalid email address, please correct and try again.', 'classified-listing-pro'),
                'invalid_request_error'    => __('Unable to process this payment, please try again or use alternative method.', 'classified-listing-pro')
            ]
        );
    }
}
