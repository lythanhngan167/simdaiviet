<?php

namespace RtclPro\Api\Authentication;

class AuthenticationApiKey
{
    static function is_valid_request($headers) {
        if (isset($headers['X-API-KEY']) && $headers['X-API-KEY'] !== "") {
            $api_key = get_option('rtcl_rest_api_key', null);
            if ($headers['X-API-KEY'] === $api_key) {
                return true;
            } else {
                $response = array(
                    'status'  => "error",
                    'error'   => 'INVALID_API_KEY',
                    'code'    => '401',
                    'error_message' => 'Sorry, you are using invalid API Key.'
                );
                wp_send_json($response, 401);
            }
        } else {
            $response = array(
                'status'  => "error",
                'error'   => 'MISSING_X_API_KEY_HEADER',
                'code'    => '401',
                'error_message' => 'X_API_KEY header not received. Either authorization header was not sent or it was removed by your server due to security reasons.'
            );
            wp_send_json($response, 401);
        }
    }

}