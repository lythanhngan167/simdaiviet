<?php

namespace RtclPro\Api\V1;

use RtclPro\Helpers\Api;
use RtclPro\Models\PushNotification;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class V1_PushNotificationApi
{
    public function register_routes() {
        register_rest_route('rtcl/v1', 'push-notification/register', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'register_push_notification_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => [
                    'push_token' => [
                        'description' => esc_html__('An Expo push token or an array of Expo push tokens specifying the recipient(s) of this message.', 'classified-listing-pro'),
                        'type'        => 'string',
                        'required'    => true,
                    ],
                    'events'     => [
                        'description' => esc_html__('Push Notification event list of array.', 'classified-listing-pro'),
                        'type'        => 'array',
                        'items'       => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ]
        ]);
	    register_rest_route('rtcl/v1', 'push-notification/add-device', [
		    [
			    'methods'             => WP_REST_Server::CREATABLE,
			    'callback'            => [$this, 'register_push_notification_callback'],
			    'permission_callback' => [Api::class, 'permission_check'],
			    'args'                => [
				    'push_token' => [
					    'description' => esc_html__('An Expo push token or an array of Expo push tokens specifying the recipient(s) of this message.', 'classified-listing-pro'),
					    'type'        => 'string',
					    'required'    => true,
				    ],
				    'events'     => [
					    'description' => esc_html__('Push Notification event list of array.', 'classified-listing-pro'),
					    'type'        => 'array',
					    'items'       => [
						    'type' => 'string'
					    ]
				    ]
			    ]
		    ]
	    ]);
    }


    public function register_push_notification_callback(WP_REST_Request $request) {
        Api::check_is_auth_user_request();
        $user_id = get_current_user_id();
        $push_token = $request->get_param('push_token');
        $events = $request->get_param('events');
        $pn = new PushNotification();
        $pnObject = $pn->registerEvents($push_token, $events, $user_id);
        if (!$pnObject) {
            return new WP_Error(
                'rest_push_notification_register_failed',
                esc_html__("Push notification update error.", 'classified-listing-pro'),
                ['status' => 403]
            );
        }

        return rest_ensure_response($pnObject);
    }
}