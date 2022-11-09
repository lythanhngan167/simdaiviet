<?php

namespace RtclPro\Gateways\Stripe\lib;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use stdClass;
use WP_Error;

class StripeAPI
{

    /**
     * Stripe API Endpoint
     */
    const ENDPOINT = 'https://api.stripe.com/v1/';
    const STRIPE_API_VERSION = '2019-09-09';

    /**
     * Secret API Key.
     *
     * @var string
     */
    private static $secret_key = '';

    /**
     * Set secret API Key.
     *
     * @param $secret_key
     */
    public static function set_secret_key($secret_key) {
        self::$secret_key = $secret_key;
    }

    /**
     * Get secret key.
     *
     * @return string
     */
    public static function get_secret_key() {
        if (!self::$secret_key) {
            $options = Functions::get_option('rtcl_payment_stripe');
            if (isset($options['testmode'], $options['stripe_livesecretkey'], $options['stripe_testsecretkey'])) {
                self::set_secret_key('yes' === $options['testmode'] ? $options['stripe_testsecretkey'] : $options['stripe_livesecretkey']);
            } elseif (isset($options['stripe_sandbox'], $options['stripe_livesecretkey'], $options['stripe_testsecretkey'])) {
                self::set_secret_key('yes' === $options['stripe_sandbox'] ? $options['stripe_testsecretkey'] : $options['stripe_livesecretkey']);
            }
        }
        return self::$secret_key;
    }

    /**
     * Generates the user agent we use to pass to API request so
     * Stripe can identify our application.
     *
     * @since   4.0.0
     * @version 4.0.0
     */
    public static function get_user_agent() {
        $app_info = [
            'name'       => 'Classified Listing Stripe Gateway',
            'version'    => RTCL_PRO_VERSION,
            'url'        => 'https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/',
            'partner_id' => 'pp_partner_EYuSt9peR0WTMg',
        ];

        return [
            'lang'         => 'php',
            'lang_version' => phpversion(),
            'publisher'    => 'rtcl',
            'uname'        => php_uname(),
            'application'  => $app_info,
        ];
    }

    /**
     * Generates the headers to pass to API request.
     *
     * @since   4.0.0
     * @version 4.0.0
     */
    public static function get_headers() {
        $user_agent = self::get_user_agent();
        $app_info = $user_agent['application'];

        $headers = apply_filters(
            'rtcl_stripe_request_headers',
            [
                'Authorization'  => 'Basic ' . base64_encode(self::get_secret_key() . ':'),
                'Stripe-Version' => self::STRIPE_API_VERSION,
            ]
        );

        // These headers should not be overridden for this gateway.
        $headers['User-Agent'] = $app_info['name'] . '/' . $app_info['version'] . ' (' . $app_info['url'] . ')';
        $headers['X-Stripe-Client-User-Agent'] = wp_json_encode($user_agent);

        return $headers;
    }

    /**
     * Send the request to Stripe's API
     *
     * @param array  $request
     * @param string $api
     * @param string $method
     * @param bool   $with_headers To get the response with headers.
     *
     * @return stdClass|array
     * @throws StripeException
     * @since   3.1.0
     * @version 4.0.6
     */
    public static function request($request, $api = 'charges', $method = 'POST', $with_headers = false) {
        StripeLogger::log("{$api} request: " . print_r($request, true));

        $headers = self::get_headers();
        $idempotency_key = '';

        if ('charges' === $api && 'POST' === $method) {
            $customer = !empty($request['customer']) ? $request['customer'] : '';
            $source = !empty($request['source']) ? $request['source'] : $customer;
            $idempotency_key = apply_filters('rtcl_stripe_idempotency_key', $request['metadata']['order_id'] . '-' . $source, $request);

            $headers['Idempotency-Key'] = $idempotency_key;
        }

        $response = wp_safe_remote_post(
            self::ENDPOINT . $api,
            [
                'method'  => $method,
                'headers' => $headers,
                'body'    => apply_filters('rtcl_stripe_request_body', $request, $api),
                'timeout' => 70,
            ]
        );

        if (is_wp_error($response) || empty($response['body'])) {
            StripeLogger::log(
                'Error Response: ' . print_r($response, true) . PHP_EOL . PHP_EOL . 'Failed request: ' . print_r(
                    [
                        'api'             => $api,
                        'request'         => $request,
                        'idempotency_key' => $idempotency_key,
                    ],
                    true
                )
            );

            throw new StripeException(print_r($response, true), __('There was a problem connecting to the Stripe API endpoint.', 'classified-listing-pro'));
        }

        if ($with_headers) {
            return [
                'headers' => wp_remote_retrieve_headers($response),
                'body'    => json_decode($response['body']),
            ];
        }

        return json_decode($response['body']);
    }

    /**
     * Retrieve API endpoint.
     *
     * @param string $api
     *
     * @version 4.0.0
     * @since   4.0.0
     */
    public static function retrieve($api) {
        StripeLogger::log("{$api}");

        $response = wp_safe_remote_get(
            self::ENDPOINT . $api,
            [
                'method'  => 'GET',
                'headers' => self::get_headers(),
                'timeout' => 70,
            ]
        );

        if (is_wp_error($response) || empty($response['body'])) {
            StripeLogger::log('Error Response: ' . print_r($response, true));
            return new WP_Error('stripe_error', __('There was a problem connecting to the Stripe API endpoint.', 'classified-listing-pro'));
        }

        return json_decode($response['body']);
    }

    /**
     * Send the request to Stripe's API with level 3 data generated
     * from the order. If the request fails due to an error related
     * to level3 data, make the request again without it to allow
     * the payment to go through.
     *
     * @param array   $request     Array with request parameters.
     * @param string  $api         The API path for the request.
     * @param array   $level3_data The level 3 data for this request.
     * @param Payment $order       The order associated with the payment.
     *
     * @return stdClass|array The response
     * @throws StripeException
     * @since   4.3.2
     * @version 5.1.0
     *
     */
    public static function request_with_level3_data($request, $api, $level3_data, $order) {
        // 1. Do not add level3 data if the array is empty.
        // 2. Do not add level3 data if there's a transient indicating that level3 was
        // not accepted by Stripe in the past for this account.
        // 3. Do not try to add level3 data if merchant is not based in the US.
        // https://stripe.com/docs/level3#level-iii-usage-requirements
        // (Needs to be authenticated with a level3 gated account to see above docs).
        if (
            empty($level3_data) ||
            get_transient('rtcl_stripe_level3_not_allowed')
//            ||'US' !== WC()->countries->get_base_country()
        ) {
            return self::request(
                $request,
                $api
            );
        }

        // Add level 3 data to the request.
        $request['level3'] = $level3_data;

        $result = self::request(
            $request,
            $api
        );

        $is_level3_param_not_allowed = (
            isset($result->error)
            && isset($result->error->code)
            && 'parameter_unknown' === $result->error->code
            && isset($result->error->param)
            && 'level3' === $result->error->param
        );

        $is_level_3data_incorrect = (
            isset($result->error)
            && isset($result->error->type)
            && 'invalid_request_error' === $result->error->type
        );

        if ($is_level3_param_not_allowed) {
            // Set a transient so that future requests do not add level 3 data.
            // Transient is set to expire in 3 months, can be manually removed if needed.
            set_transient('rtcl_stripe_level3_not_allowed', true, 3 * MONTH_IN_SECONDS);
        } elseif ($is_level_3data_incorrect) {
            // Log the issue so we could debug it.
            StripeLogger::log(
                'Level3 data sum incorrect: ' . PHP_EOL
                . print_r($result->error->message, true) . PHP_EOL
            );
        }

        // Make the request again without level 3 data.
        if ($is_level3_param_not_allowed || $is_level_3data_incorrect) {
            unset($request['level3']);
            return self::request(
                $request,
                $api
            );
        }

        return $result;
    }
}
