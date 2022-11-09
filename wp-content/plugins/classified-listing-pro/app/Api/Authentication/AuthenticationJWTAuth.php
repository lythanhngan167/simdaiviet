<?php

namespace RtclPro\Api\Authentication;

use RtclPro\Helpers\Api;

class AuthenticationJWTAuth
{

    static function is_valid_request($headers) {
        if ((isset($headers['AUTHORIZATION']) && $headers['AUTHORIZATION'] !== "") || (isset($headers['AUTHORISATION']) && $headers['AUTHORISATION'] !== "")) {
            if (isset($headers['AUTHORIZATION'])) {
                $authorization_header = explode(" ", $headers['AUTHORIZATION']);
            } elseif (isset($headers['AUTHORISATION'])) {
                $authorization_header = explode(" ", $headers['AUTHORISATION']);
            }

            if (isset($authorization_header[0]) && (strcasecmp($authorization_header[0], 'Bearer') == 0) && isset($authorization_header[1]) && $authorization_header[1] !== "") {
                $jwt_token = explode(".", $authorization_header[1]);
                $jwt = new AuthenticationJWTAuth();

                if ($jwt->jwt_token_segment_validation($jwt_token)) {
                    return $jwt->jwt_signature_validation($jwt_token);
                } else {
                    $response = array(
                        'status'        => "error",
                        'error'         => 'SEGMENT_FAULT',
                        'code'          => '401',
                        'error_message' => 'Incorrect JWT Format.'
                    );
                    wp_send_json($response, 401);
                }
                // return  ? $jwt->jwt_signature_validation($jwt_token) : false;
            } else {
                $response = array(
                    'status'        => "error",
                    'error'         => 'INVALID_AUTHORIZATION_HEADER_TOKEN_TYPE',
                    'code'          => '401',
                    'error_message' => 'Authorization header must be type of Bearer Token.'
                );
                wp_send_json($response, 401);
            }
        }
        $response = array(
            'status'        => "error",
            'error'         => 'MISSING_AUTHORIZATION_HEADER',
            'code'          => '401',
            'error_message' => 'Authorization header not received. Either authorization header was not sent or it was removed by your server due to security reasons.'
        );
        wp_send_json($response, 401);
    }

    static function check_is_valid_request($headers) {
        if ((isset($headers['AUTHORIZATION']) && $headers['AUTHORIZATION'] !== "") || (isset($headers['AUTHORISATION']) && $headers['AUTHORISATION'] !== "")) {
            if (isset($headers['AUTHORIZATION'])) {
                $authorization_header = explode(" ", $headers['AUTHORIZATION']);
            } elseif (isset($headers['AUTHORISATION'])) {
                $authorization_header = explode(" ", $headers['AUTHORISATION']);
            }

            if (isset($authorization_header[0]) && (strcasecmp($authorization_header[0], 'Bearer') == 0) && isset($authorization_header[1]) && $authorization_header[1] !== "") {
                $jwt_token = explode(".", $authorization_header[1]);
                $jwt = new AuthenticationJWTAuth();

                if ($jwt->jwt_token_segment_validation($jwt_token)) {
                    return $jwt->is_jwt_signature_validation($jwt_token);
                }
            }
        }
        return false;
    }

    function base64UrlDecode($text) {
        return base64_decode(str_pad(strtr($text, '-_', '+/'), strlen($text) % 4, '=', STR_PAD_RIGHT));
    }

    function isJson($string) {
        return !((json_decode($string) == null));
    }

    function jwt_token_segment_validation($jwt_token) {
        return $this->isJson($this->base64UrlDecode($jwt_token[0])) && $this->isJson($this->base64UrlDecode($jwt_token[1]));
    }

    function jwt_signature_validation($jwt_token) {
        $header_json = json_decode($this->base64UrlDecode($jwt_token[0]));
        $payload_json = json_decode($this->base64UrlDecode($jwt_token[1]));
        $signing_algo = $header_json->alg;
        $exp = $payload_json->exp;

        $signature = hash_hmac('sha256', $jwt_token[0] . "." . $jwt_token[1], rtcl()->getApiSecret(), true);
        $base64UrlSignature = Api::base64UrlEncode($signature);

        if (hash_equals($base64UrlSignature, $jwt_token[2])) {
            $user_data = json_decode($this->base64UrlDecode($jwt_token[1]));
            $user = get_user_by('login', $user_data->name);
            if ($user) {
                wp_set_current_user($user->ID);
                return true;
            } else {
                $response = array(
                    'status'        => "error",
                    'error'         => 'USER_NOT_FOUND',
                    'code'          => '404',
                    'error_message' => 'User not found.'
                );
                wp_send_json($response, 401);
            }
        } else {
            $response = array(
                'status'        => "error",
                'error'         => 'INVALID_SIGNATURE',
                'code'          => '401',
                'error_message' => 'JWT Signature is invalid.'
            );
            wp_send_json($response, 401);
        }
        return false;
    }

    function is_jwt_signature_validation($jwt_token) {
        $signature = hash_hmac('sha256', $jwt_token[0] . "." . $jwt_token[1], rtcl()->getApiSecret(), true);
        $base64UrlSignature = Api::base64UrlEncode($signature);

        if (hash_equals($base64UrlSignature, $jwt_token[2])) {
            $user_data = json_decode($this->base64UrlDecode($jwt_token[1]));
            $user = get_user_by('login', $user_data->name);
            if ($user) {
                wp_set_current_user($user->ID);
                return true;
            }
        }
        return false;
    }

    function jwt_signature_validation_old($jwt_token) {
        $header_json = json_decode($this->base64UrlDecode($jwt_token[0]));
        $payload_json = json_decode($this->base64UrlDecode($jwt_token[1]));
        $signing_algo = $header_json->alg;
        $exp = $payload_json->exp;

        if (get_option('mo_api_authentication_jwt_signing_algorithm') == $signing_algo && $exp > time()) {
            $signature = hash_hmac('sha256', $jwt_token[0] . "." . $jwt_token[1], rtcl()->getApiSecret(), true);
            $base64UrlSignature = Api::base64UrlEncode($signature);

            if (hash_equals($base64UrlSignature, $jwt_token[2])) {
                $user_data = json_decode($this->base64UrlDecode($jwt_token[1]));
                wp_send_json($user_data, 401);
                $user = get_user_by('login', $user_data->name);
                wp_set_current_user($user->ID);
                return false;
            } else {
                $response = array(
                    'status'        => "error",
                    'error'         => 'INVALID_SIGNATURE',
                    'code'          => '401',
                    'error_message' => 'JWT Signature is invalid.'
                );
                wp_send_json($response, 401);
            }
        }
        return false;
    }
}