<?php

namespace RtclPro\Api\V1;

use RtclPro\Helpers\Fns;
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use RtclPro\Helpers\Api;
use Rtcl\Helpers\Functions;
use RtclPro\Models\Conversation;
use Rtcl\Controllers\Hooks\Filters;
use RtclPro\Controllers\ChatController;

class V1_MyApi
{
    public function register_routes() {
        register_rest_route('rtcl/v1', 'my', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'my_info_callback'],
                'permission_callback' => [Api::class, 'permission_check']
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'update_my_account_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => [
                    'first_name' => [
                        'description' => esc_html__('First name is required field.', 'classified-listing-pro'),
                        'type'        => 'string',
                        'required'    => true,
                    ],
                    'last_name'  => [
                        'description' => esc_html__('Last name is required field.', 'classified-listing-pro'),
                        'type'        => 'string',
                        'required'    => true,
                    ]
                ]
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/profile-image', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'upload_my_profile_image_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => [
                    'image' => [
                        'type'              => 'file',
                        'validate_callback' => function ($value, $request, $param) {
                            $files = $request->get_file_params();
                            if (empty($files['image'])) {
                                return new WP_Error('rest_invalid_param', esc_html__('parameter image file field is required.', 'classified-listing-pro'), array('status' => 400));
                            }

                            return true;
                        },
                        'description'       => 'Image file is required field.',
                    ]
                ]
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/listings', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_my_listings'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => [
                    'per_page' => [
                        'description'       => esc_html__('Maximum number of items to be returned in result set.', 'classified-listing-pro'),
                        'type'              => 'integer',
                        'default'           => 20,
                        'minimum'           => 1,
                        'maximum'           => 100,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                    ],
                    'page'     => [
                        'description'       => esc_html__('Current page of the collection.', 'classified-listing-pro'),
                        'type'              => 'integer',
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                        'minimum'           => 1,
                    ]
                ]
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_my_listing_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'listing_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Listing id is required', 'classified-listing-pro'),
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/favourites', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_my_favourite_listings'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'per_page' => array(
                        'description'       => esc_html__('Maximum number of items to be returned in result set.', 'classified-listing-pro'),
                        'type'              => 'integer',
                        'default'           => 20,
                        'minimum'           => 1,
                        'maximum'           => 100,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                    'page'     => array(
                        'description'       => esc_html__('Current page of the collection.', 'classified-listing-pro'),
                        'type'              => 'integer',
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                        'minimum'           => 1,
                    )
                )
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'toggle_my_favourite_listing'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'listing_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Listing is required', 'classified-listing-pro'),
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/mark-as-sold', [
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => [$this, 'toggle_mark_as_sold'],
            'permission_callback' => [Api::class, 'permission_check'],
            'args'                => array(
                'listing_id' => array(
                    'required'    => true,
                    'type'        => 'integer',
                    'description' => esc_html__('Listing id required', 'classified-listing-pro'),
                )
            )
        ]);
        register_rest_route('rtcl/v1', 'my/chat', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_my_chat_list'],
                'permission_callback' => [Api::class, 'permission_check']
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/chat/check', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'check_has_conversation'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'listing_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Listing is required', 'classified-listing-pro'),
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/chat/conversation', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_conversation_messages'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'con_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Conversation is required', 'classified-listing-pro'),
                    ),
                    'limit'  => array(
                        'required'    => false,
                        'type'        => 'integer',
                        'description' => esc_html__('Message limit', 'classified-listing-pro'),
                    )
                )
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'start_new_conversation'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'listing_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Listing is required', 'classified-listing-pro'),
                    ),
                    'text'       => array(
                        'required'    => true,
                        'type'        => 'string',
                        'description' => esc_html__('Message text is required', 'classified-listing-pro'),
                    )
                )
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_chat_conversation'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'con_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Conversation is required', 'classified-listing-pro'),
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/chat/message', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'send_message'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'con_id'     => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Conversation ID is required', 'classified-listing-pro'),
                    ),
                    'listing_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Listing ID is required', 'classified-listing-pro'),
                    ),
                    'text'       => array(
                        'required'    => true,
                        'type'        => 'string',
                        'description' => esc_html__('Message text is required', 'classified-listing-pro'),
                    )
                )
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [$this, 'set_message_read'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'con_id'     => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Conversation ID is required', 'classified-listing-pro'),
                    ),
                    'message_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => esc_html__('Message ID is required', 'classified-listing-pro'),
                    )
                )
            ]
        ]);
    }


    public function check_has_conversation(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $visitor_id = get_current_user_id();
        if (!$visitor_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => esc_html__("You are not logged in.", 'classified-listing-pro')
            );
            wp_send_json($response, 403);
        }

        $listing_id = $request->get_param('listing_id');
        if (!($listing = rtcl()->factory->get_listing($listing_id)) || !$listing->exists() || $visitor_id === $listing->get_author_id()) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => esc_html__("You are not allow to access this.", 'classified-listing-pro')
            );
            wp_send_json($response, 403);
        }
        $author_id = $listing->get_author_id();
        $response = false;
        if ($con_id = ChatController::has_conversation_started($visitor_id, $author_id, $listing_id)) {
            $response['con_id'] = $con_id;
            $conversation = new Conversation($con_id);
            $response = $conversation->getData();
            $response->messages = $conversation->messages();
        }

        return rest_ensure_response($response);
    }

    public function start_new_conversation(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $visitor_id = get_current_user_id();
        if (!$visitor_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        $listing_id = $request->get_param('listing_id');
        $text = $request->get_param('text');
        if (!($listing = rtcl()->factory->get_listing($listing_id)) || !$listing->exists() || $visitor_id === $listing->get_author_id()) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not allow to access this."
            );
            wp_send_json($response, 403);
        }
        $author_id = $listing->get_author_id();
        if ($con_id = ChatController::has_conversation_started($visitor_id, $author_id, $listing_id)) {
            $conversation = new Conversation($con_id);
            $response = $conversation->getData();
            $response->messages = $conversation->messages();
        } else {
            $response = ChatController::initiate_new_conversation_write_message([
                'listing_id'   => $listing->get_id(),
                'sender_id'    => $visitor_id,
                'recipient_id' => $author_id
            ], $text);
            if (empty($response)) {
                return rest_ensure_response(false);
            }
        }

        return rest_ensure_response($response);
    }

    public function delete_chat_conversation(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        $con_id = $request->get_param('con_id');
        if (!ChatController::_is_valid_conversation($con_id)) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not permitted to access this conversation."
            );
            wp_send_json($response, 403);
        }

        return rest_ensure_response(ChatController::_delete_conversation($con_id));
    }

    public function send_message(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => esc_html__("You are not logged in.", "classified-listing-pro")
            );
            wp_send_json($response, 403);
        }

        $con_id = $request->get_param('con_id');
        $text = $request->get_param('text');
        $listing_id = $request->get_param('listing_id');
        if (!ChatController::_is_valid_conversation($con_id)) {
            $response = [
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => esc_html__("You are not permitted to access this conversation.", "classified-listing-pro")
            ];
            wp_send_json($response, 403);
        }

        if (!$response = ChatController::_send_message($con_id, $listing_id, $text)) {
            $response = [
                'status'        => "error",
                'error'         => 'SERVERERROR',
                'code'          => '503',
                'error_message' => "Server error"
            ];
            wp_send_json($response, 503);
        }

        return rest_ensure_response($response);
    }

    public function set_message_read(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        $con_id = $request->get_param('con_id');
        $message_id = $request->get_param('message_id');
        if (!ChatController::_is_valid_conversation($con_id)) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not permitted to access this conversation."
            );
            wp_send_json($response, 403);
        }


        return rest_ensure_response(ChatController::_set_message_read($con_id, $message_id));
    }


    public function get_conversation_messages(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        $con_id = $request->get_param('con_id');
        $limit = absint($request->get_param('limit'));
        $limit = $limit ? $limit : 50;
        if (!ChatController::_is_valid_conversation($con_id)) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not permitted to access this conversation."
            );
            wp_send_json($response, 403);
        }
        $conversation = new Conversation($con_id);
        $response = $conversation->getData();
        $response->messages = $conversation->messages($limit);
        Fns::update_chat_conversation_status($con_id, $user_id);
        return rest_ensure_response($response);
    }

    public function get_my_chat_list(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        return rest_ensure_response(ChatController::_fetch_conversations($user_id));
    }

    public function delete_my_listing_callback(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }
        if (!$request->get_param('listing_id') || (!$listing = rtcl()->factory->get_listing($request->get_param('listing_id'))) || $listing->get_post_type() !== rtcl()->post_type) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Listing not found.'
            );
            wp_send_json($response, 400);
        }

        if ($user_id !== $listing->get_author_id()) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not permitted to delete."
            );
            wp_send_json($response, 403);
        }
        $children = get_children(apply_filters('rtcl_before_delete_listing_attachment_query_args', [
            'post_parent'    => $listing->get_id(),
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => 'inherit',
        ], $listing->get_id()));
        if (!empty($children)) {
            foreach ($children as $child) {
                wp_delete_attachment($child->ID, true);
            }
        }

        do_action('rtcl_before_delete_listing', $listing->get_id());
        $result = Functions::delete_post($listing->get_id());

        return rest_ensure_response($result ? $listing->get_id() : false);
    }

    public function toggle_my_favourite_listing(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        if (!$request->get_param('listing_id') || (!$listing = rtcl()->factory->get_listing($request->get_param('listing_id')))) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Listing not found.'
            );
            wp_send_json($response, 400);
        }

        $favourites = get_user_meta($user_id, 'rtcl_favourites', true);
        $favourites = !empty($favourites) && is_array($favourites) ? $favourites : [];

        if (in_array($listing->get_id(), $favourites)) {
            if (($key = array_search($listing->get_id(), $favourites)) !== false) {
                unset($favourites[$key]);
            }
        } else {
            $favourites[] = $listing->get_id();
        }

        $favourites = array_filter($favourites);
        $favourites = array_values($favourites);
        update_user_meta($user_id, 'rtcl_favourites', $favourites);

        return rest_ensure_response($favourites);
    }

    public function toggle_mark_as_sold(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        if (!$request->get_param('listing_id') || (!$listing = rtcl()->factory->get_listing($request->get_param('listing_id')))) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Listing not found.'
            );
            wp_send_json($response, 400);
        }
        $data = [
            'listing_id' => $listing->get_id()
        ];
        if (absint(get_post_meta($listing->get_id(), '_rtcl_mark_as_sold', true))) {
            delete_post_meta($listing->get_id(), '_rtcl_mark_as_sold');
            $data['action'] = 'unsold';
        } else {
            update_post_meta($listing->get_id(), '_rtcl_mark_as_sold', 1);
            $data['action'] = 'sold';
        }

        return rest_ensure_response($data);
    }

    public function get_my_listings(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        $per_page = (int)$request->get_param("per_page");
        $page = (int)$request->get_param("page");
        $args = [
            'post_type'      => rtcl()->post_type,
            'post_status'    => 'any',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'author'         => $user_id,
            'fields'         => 'ids',
        ];
        $response = Api::get_query_listing_data(apply_filters('rtcl_rest_response_my_listings_args', $args));

        return rest_ensure_response($response);
    }

    public function get_my_favourite_listings(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }
        $per_page = (int)$request->get_param("per_page");
        $page = (int)$request->get_param("page");
        $favourite_posts = get_user_meta($user_id, 'rtcl_favourites', true);
        $favourite_post_ids = !empty($favourite_posts) && is_array($favourite_posts) ? $favourite_posts : [0];
        $args = array(
            'post_type'      => rtcl()->post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'fields'         => 'ids',
            'post__in'       => $favourite_post_ids
        );
        $response = Api::get_query_listing_data(apply_filters('rtcl_rest_response_my_favourite_listings_args', $args));

        return rest_ensure_response($response);
    }

    public function update_my_account_callback(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }
        // Validate password
        $password = '';
        if ($request->get_param('change_password') === true) {
            $password = sanitize_text_field($request->get_param('pass1'));
            $error = null;
            if (empty($password)) {
                // Password is empty
                $error = esc_html__('The password field is empty.', 'classified-listing-pro');
            }
            if ($password !== $request->get_param('pass2')) {
                // Passwords don't match
                $error = esc_html__("The two passwords you entered don't match.", 'classified-listing-pro');
            }
            if ($error) {
                $response = array(
                    'status'        => "error",
                    'error'         => 'DADREQUEST',
                    'code'          => '40!',
                    'error_message' => $error
                );
                wp_send_json($response, 403);
            }
        }
        $first_name = sanitize_text_field($request->get_param('first_name'));
        $last_name = sanitize_text_field($request->get_param('last_name'));

        $user_data = [
            'ID'         => $user_id,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'nickname'   => $first_name
        ];

        if (!empty($password)) {
            $user_data['user_pass'] = $password;
        }
        $user_id = wp_update_user($user_data);
        $user_meta = [
            '_rtcl_phone'           => !empty($request->get_param('phone')) ? esc_attr($request->get_param('phone')) : null,
            '_rtcl_whatsapp_number' => !empty($request->get_param('whatsapp_number')) ? esc_attr($request->get_param('whatsapp_number')) : null,
            '_rtcl_website'         => !empty($request->get_param('website')) ? esc_url_raw($request->get_param('website')) : null,
            '_rtcl_zipcode'         => !empty($request->get_param('zipcode')) ? esc_attr($request->get_param('zipcode')) : null,
            '_rtcl_address'         => !empty($request->get_param('address')) ? esc_textarea($request->get_param('address')) : null,
        ];
        if ($request->has_param('latitude')) {
            $user_meta['_rtcl_latitude'] = !empty($request->get_param('latitude')) ? esc_attr($request->get_param('latitude')) : null;
        }
        if ($request->has_param('longitude')) {
            $user_meta['_rtcl_longitude'] = !empty($request->get_param('longitude')) ? esc_attr($request->get_param('longitude')) : null;
        }
        if ($request->has_param('locations')) {
            $locations = $request->get_param('locations');
            $user_meta['_rtcl_location'] = !empty($locations) && is_array($locations) ? array_filter(array_map('absint', $locations)) : [];
        }
        foreach ($user_meta as $metaKey => $metaValue) {
            update_user_meta($user_id, $metaKey, $metaValue);
        }

        $user = get_user_by("ID", $user_id);
        do_action('rtcl_rest_api_update_my_account_success', $user, $request);
        $user_data = Api::get_user_data($user);

        return rest_ensure_response($user_data);
    }

    public function my_info_callback(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => esc_html__("You are not logged in.", 'classified-listing-pro')
            );
            wp_send_json($response, 403);
        }
        $user = get_user_by('ID', $user_id);
        $my_data = Api::get_user_data($user);

        return rest_ensure_response($my_data);
    }

    public function upload_my_profile_image_callback(WP_REST_Request $request) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        if (!$user_id) {
            $response = array(
                'status'        => "error",
                'error'         => 'FORBIDDEN',
                'code'          => '403',
                'error_message' => "You are not logged in."
            );
            wp_send_json($response, 403);
        }

        $files = $request->get_file_params();
        if (empty($files['image'])) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => "Image file field is required."
            );
            wp_send_json($response, 400);
        }
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        Filters::beforeUpload();
        $status = wp_handle_upload($files['image'], ['test_form' => false]);
        Filters::afterUpload();
        if ($status && isset($status['error'])) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => $status['error']
            );
            wp_send_json($response, 400);
        }
        $filename = $status['file'];
        // Check the type of tile. We'll use this as the 'post_mime_type'.
        $fileType = wp_check_filetype(basename($filename));

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $fileType['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $filename);
        if (is_wp_error($attach_id)) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => $attach_id->get_error_message()
            );
            wp_send_json($response, 400);
        }

        if ($existing_pp = absint(get_user_meta($user_id, '_rtcl_pp_id', true))) {
            wp_delete_attachment($existing_pp, true);
        }
        update_user_meta($user_id, '_rtcl_pp_id', $attach_id);
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
        $src = wp_get_attachment_image_src($attach_id, [80, 80]);
        $data = [
            'thumbnail_id' => $attach_id,
            'src'          => $src[0],
            'user_id'      => $user_id
        ];
        do_action('rtcl_user_pp_updated', $data, $user_id, $attach_id, $request->get_file_params());

        return rest_ensure_response($data);
    }
}
