<?php

namespace RtclStore\Api\V1;

use Rtcl\Controllers\Hooks\Filters;
use RtclPro\Helpers\Api;
use RtclStore\Helpers\Functions as StoreFunctions;
use RtclStore\Helpers\StoreApi;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class V1_StoreApi
{
    public function __construct() {
        register_rest_route('rtcl/v1', 'stores', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_stores_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'search'     => array(
                        'description'       => 'Limit results to those matching a string.',
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                    'categories' => array(
                        'type'        => 'array',
                        'items'       => array(
                            'type' => 'integer'
                        ),
                        'description' => esc_html__('Category ids as array of integer or only single integer.', 'classified-listing-store'),
                    ),
                    'per_page'   => array(
                        'description'       => esc_html__('Maximum number of items to be returned in result set.', 'classified-listing-store'),
                        'type'              => 'integer',
                        'default'           => 20,
                        'minimum'           => 1,
                        'maximum'           => 100,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                    'page'       => array(
                        'description'       => esc_html__('Current page of the collection.', 'classified-listing-store'),
                        'type'              => 'integer',
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                        'minimum'           => 1,
                    ),
                    'order_by'   => array(
                        'description' => esc_html__('Order by.', 'classified-listing-store'),
                        'type'        => 'string'
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', '/stores/(?P<store_id>[\d]+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_single_store_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'store_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => 'Store id is required',
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'store/listings', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_store_listings_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'store_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => 'Store id is required',
                    ),
                    'per_page' => array(
                        'description'       => 'Maximum number of items to be returned in result set.',
                        'type'              => 'integer',
                        'default'           => 20,
                        'minimum'           => 1,
                        'maximum'           => 100,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                    'page'     => array(
                        'description'       => 'Current page of the collection.',
                        'type'              => 'integer',
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => 'rest_validate_request_arg',
                        'minimum'           => 1,
                    ),
                    'order_by' => array(
                        'description' => 'Order by.',
                        'type'        => 'string'
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'store/email-owner', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'email_to_store_owner_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => array(
                    'store_id' => array(
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => 'Store id is required',
                    ),
                    'name'     => array(
                        'required'    => false,
                        'type'        => 'string',
                        'description' => 'name field is the sender name.',
                    ),
                    'email'    => array(
                        'required'    => false,
                        'type'        => 'string',
                        'description' => 'email field is the sender email.',
                    ),
                    'phone'    => array(
                        'required'    => false,
                        'type'        => 'string',
                        'description' => 'Phone field is the sender email.',
                    ),
                    'message'  => array(
                        'required'    => true,
                        'type'        => 'string',
                        'description' => 'Message is the message details',
                    )
                )
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/store', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'my_store_callback'],
                'permission_callback' => [Api::class, 'permission_check']
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'update_my_store_callback'],
                'permission_callback' => [Api::class, 'permission_check'],
                'args'                => [
                    'title'        => [
                        'description' => 'Title is required field.',
                        'type'        => 'string',
                        'required'    => true,
                    ],
                    'email'        => [
                        'description' => 'email is required field.',
                        'type'        => 'string',
                        'required'    => true,
                    ],
                    'slug'         => [
                        'description' => 'Slug field.',
                        'type'        => 'string',
                        'required'    => false,
                    ],
                    'phone'        => [
                        'description' => 'phone field.',
                        'type'        => 'string',
                        'required'    => false,
                    ],
                    'address'      => [
                        'description' => 'Store address field.',
                        'type'        => 'string',
                        'required'    => false,
                    ],
                    'website'      => [
                        'description' => 'Store website field.',
                        'type'        => 'string',
                        'required'    => false,
                    ],
                    'description'  => [
                        'description' => 'Store description field.',
                        'type'        => 'string',
                        'required'    => false,
                    ],
                    'social_media' => [
                        'description' => 'Store social_media field.',
                        'type'        => 'object',
                        'required'    => false,
                    ],
                    'oh_type'      => [
                        'description' => 'oh_type open hour type.',
                        'type'        => 'string',
                        'required'    => false,
                    ],
                    'oh_hours'     => [
                        'description' => 'oh_hours open hours.',
                        'type'        => 'object',
                        'required'    => false,
                    ],
                    'slogan'       => [
                        'description' => 'slogan is required field.',
                        'type'        => 'string',
                        'required'    => false,
                    ]
                ]
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/store/logo', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'upload_my_store_logo_callback'],
            'permission_callback' => [Api::class, 'permission_check'],
            'args'                => [
                'logo' => [
                    'type'              => 'file',
                    'validate_callback' => function ($value, $request, $param) {
                        $files = $request->get_file_params();
                        if (empty($files['image'])) {
                            return new WP_Error('rest_invalid_param', 'parameter logo file field is required.', ['status' => 400]);
                        }
                        return true;
                    },
                    'description'       => 'Logo image file is required field.',
                ]
            ]
        ]);
        register_rest_route('rtcl/v1', 'my/store/banner', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'upload_my_store_banner_callback'],
            'permission_callback' => [Api::class, 'permission_check'],
            'args'                => [
                'banner' => [
                    'type'              => 'file',
                    'validate_callback' => function ($value, $request, $param) {
                        $files = $request->get_file_params();
                        if (empty($files['image'])) {
                            return new WP_Error('rest_invalid_param', 'parameter banner file field is required.', ['status' => 400]);
                        }
                        return true;
                    },
                    'description'       => 'Banner image file is required field.',
                ]
            ]
        ]);
    }

    public function upload_my_store_logo_callback(WP_REST_Request $request) {
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

        $store = StoreFunctions::get_user_store($user_id);
        if (!$store) {

            add_filter("post_type_link", "__return_empty_string");

            $store_id = wp_insert_post(apply_filters("rtcl_insert_post", array(
                'post_title'      => '',
                'post_content'    => '',
                'post_status'     => 'publish',
                'post_author'     => 1,
                'post_type'       => rtclStore()->post_type,
                'comments_status' => 'closed',
                'meta_input'      => array(
                    'store_owner_id' => $user_id
                )
            )));
            $store = rtclStore()->factory->get_store($store_id);

            remove_filter("post_type_link", "__return_empty_string");
        }

        $files = $request->get_file_params();
        if (empty($files['logo'])) {
            $response = [
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => "Logo file field is required."
            ];
            wp_send_json($response, 400);
        }
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        Filters::beforeUpload();
        $status = wp_handle_upload($files['logo'], ['test_form' => false]);
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

        if ($existing_logo = get_post_meta($store->get_id(), 'logo_id', true)) {
            wp_delete_attachment($existing_logo);
        }
        update_post_meta($store->get_id(), 'logo_id', $attach_id);
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
        $src = wp_get_attachment_image_src($attach_id, 'rtcl-store-logo');
        return rest_ensure_response($src[0]);
    }

    public function upload_my_store_banner_callback(WP_REST_Request $request) {
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

        $store = StoreFunctions::get_user_store($user_id);
        if (!$store) {

            add_filter("post_type_link", "__return_empty_string");

            $store_id = wp_insert_post(apply_filters("rtcl_insert_post", array(
                'post_title'      => '',
                'post_content'    => '',
                'post_status'     => 'publish',
                'post_author'     => 1,
                'post_type'       => rtclStore()->post_type,
                'comments_status' => 'closed',
                'meta_input'      => array(
                    'store_owner_id' => $user_id
                )
            )));
            $store = rtclStore()->factory->get_store($store_id);

            remove_filter("post_type_link", "__return_empty_string");
        }

        $files = $request->get_file_params();
        if (empty($files['banner'])) {
            $response = [
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => "Banner file field is required."
            ];
            wp_send_json($response, 400);
        }
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        Filters::beforeUpload();
        $status = wp_handle_upload($files['banner'], ['test_form' => false]);
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

        if ($existing_banner = get_post_meta($store->get_id(), 'banner_id', true)) {
            wp_delete_attachment($existing_banner);
        }
        update_post_meta($store->get_id(), 'banner_id', $attach_id);
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
        $src = wp_get_attachment_image_src($attach_id, 'rtcl-store-banner');
        return rest_ensure_response($src[0]);
    }

    public function my_store_callback(WP_REST_Request $request) {
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
        $store = StoreFunctions::get_user_store($user_id);
        if (!$store) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Store not found.'
            );
            wp_send_json($response, 400);
        }

        $my_data = StoreApi::get_single_store_data($store);
        return rest_ensure_response($my_data);
    }

    public static function update_my_store_callback(WP_REST_Request $request) {
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
        if ($social_media = $request->get_param('social_media')) {
            $social_media = array_filter($social_media);
            $social_media = !empty($social_media) ? array_map('esc_url_raw', $social_media) : '';
        }
        $oh_hours = $request->get_param('oh_hours');
        $oh_hours = !empty($oh_hours) ? array_filter($oh_hours) : [];
        $store = StoreFunctions::get_user_store($user_id);
        $store_arg = array(
            'post_title'   => $request->get_param('title'),
            'post_content' => sanitize_textarea_field($request->get_param('description')),
            'meta_input'   => apply_filters('rtcl_store_rest_api_mata_data_before_update', [
                'address'      => sanitize_textarea_field($request->get_param('address')),
                'website'      => esc_url_raw($request->get_param('website')),
                'email'        => sanitize_email($request->get_param('email')),
                'slogan'       => sanitize_text_field($request->get_param('slogan')),
                'phone'        => sanitize_text_field($request->get_param('phone')),
                'social_media' => $social_media,
                'oh_type'      => in_array($request->get_param('oh_type'), ['selected', 'always']) ? $request->get_param('oh_type') : 'selected',
                'oh_hours'     => $oh_hours
            ])
        );
        if ($store) {
            $store_arg['ID'] = $store->get_id();
            wp_update_post($store_arg);
            $store = rtclStore()->factory->get_store($store->get_id());
        } else {
            $slug = sanitize_title($request->get_param('slug'));
            if (!$slug) {
                $response = array(
                    'status'        => "error",
                    'error'         => 'FORBIDDEN',
                    'code'          => '403',
                    'error_message' => 'store slug is required'
                );
                wp_send_json($response, 403);
            }
            global $wpdb;
            $check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s LIMIT 1";
            $slug_check = $wpdb->get_var($wpdb->prepare($check_sql, $slug));
            if ($slug_check) {
                $response = array(
                    'status'        => "error",
                    'error'         => 'FORBIDDEN',
                    'code'          => '403',
                    'error_message' => 'store slug is already exist!'
                );
                wp_send_json($response, 403);
            }
            $store_arg['post_name'] = $slug;
            $store_arg['meta_input']['store_owner_id'] = $user_id;
            $store_arg['post_status'] = 'publish';
            $store_arg['post_type'] = rtclStore()->post_type;
            $store_arg['post_author'] = 1;
            $store_id = wp_insert_post($store_arg);
            if (is_wp_error($store_id)) {
                $response = array(
                    'status'        => "error",
                    'error'         => 'FORBIDDEN',
                    'code'          => '403',
                    'error_message' => $store_id->get_error_message()
                );
                wp_send_json($response, 403);
            }
            $store = rtclStore()->factory->get_store($store_id);
        }

        return rest_ensure_response(StoreApi::get_my_store_data($store));
    }


    public function get_stores_callback(WP_REST_Request $request) {

        $per_page = (int)$request->get_param("per_page");
        $page = (int)$request->get_param("page");
        $categories = $request->get_param("categories");
        $search = $request->get_param("search");
        $order_by = $request->get_param("order_by");
        // Prepare variables
        $args = array(
            'post_type'      => rtclStore()->post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'fields'         => 'ids'
        );
        if ($search) {
            $args['s'] = $search;
        }

        $response = StoreApi::get_query_store_data(apply_filters('rtcl_store_rest_api_stores_args', $args));
        return rest_ensure_response($response);
    }

    public function get_store_listings_callback(WP_REST_Request $request) {
        if (!$request->get_param('store_id') || (!$store = rtclStore()->factory->get_store($request->get_param('store_id'))) || $store->get_post_type() !== rtclStore()->post_type) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Store not found.'
            );
            wp_send_json($response, 400);
        }

        $per_page = (int)$request->get_param("per_page");
        $page = (int)$request->get_param("page");

        $args = [
            'post_type'      => rtcl()->post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'author'         => $store->owner_id(),
            'paged'          => $page,
            'fields'         => 'ids'
        ];
        $response = Api::get_query_listing_data(apply_filters('rtcl_store_rest_api_store_listings_args', $args));
        return rest_ensure_response($response);
    }

    public function get_single_store_callback(WP_REST_Request $request) {
        $store = null;
        if (!$request->get_param('store_id') || (!$store = rtclStore()->factory->get_store($request->get_param('store_id'))) || $store->get_post_type() !== rtclStore()->post_type) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Store not found.'
            );
            wp_send_json($response, 400);
        }
        $store_data = StoreFunctions::is_store_expired($store) ? null : StoreApi::get_single_store_data($store);
        return rest_ensure_response($store_data);
    }

    public function email_to_store_owner_callback(WP_REST_Request $request) {
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
        if (!$request->get_param('store_id') || (!$store = rtclStore()->factory->get_store($request->get_param('store_id')))) {
            $response = array(
                'status'        => "error",
                'error'         => 'BADREQUEST',
                'code'          => '400',
                'error_message' => 'Store not found.'
            );
            wp_send_json($response, 400);
        }
        $user = get_userdata($user_id);
        $name = $request->get_param("name");
        $email = $request->get_param("email");
        $data = [
            'name'    => $name ? $name : $user->display_name,
            'email'   => $email ? $email : $user->user_email,
            'phone'   => $request->get_param('phone'),
            'message' => stripslashes(esc_textarea($request->get_param("message"))),
            'store'   => $store
        ];
        $email = rtcl()->mailer()->emails['Store_Contact_Email_To_Owner']->trigger($store->get_id(), $data);
        return rest_ensure_response($email);
    }

}
