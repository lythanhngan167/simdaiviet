<?php

namespace RtclPro\Api\V1;

use Rtcl\Controllers\Hooks\Comments;
use Rtcl\Helpers\Functions;
use RtclPro\Api\RestController;
use RtclPro\Helpers\Api;
use WP_Comment;
use WP_Comment_Query;
use WP_Error;
use WP_HTTP_Response;
use WP_Post;
use WP_REST_Posts_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User;

class V1_ReviewApi extends RestController
{
    /**
     * Constructor.
     *
     */
    public function __construct() {
        $this->namespace = 'rtcl/v1';
        $this->rest_base = 'reviews';
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [Api::class, 'permission_check'],
                    'args'                => $this->get_collection_params(),
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_permission_check'],
                    'args'                => [
                        'listing'      => [
                            'required'    => true,
                            'description' => esc_html__('Listing  ID', "classified-listing-pro"),
                            'type'        => 'integer'
                        ],
                        'rating'       => [
                            'required'    => Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox'),
                            'description' => esc_html__('Review rating between 1-5', "classified-listing-pro"),
                            'type'        => 'integer',
                            'minimum'     => 1,
                            'maximum'     => 5
                        ],
                        'title'        => [
                            'required'    => true,
                            'description' => esc_html__('Review title', "classified-listing-pro"),
                            'type'        => 'string'
                        ],
                        'content'      => [
                            'required'    => true,
                            'description' => esc_html__('Review content', "classified-listing-pro"),
                            'type'        => 'string'
                        ],
                        'author'       => [
                            'description' => esc_html__('Author ID', "classified-listing-pro"),
                            'type'        => 'integer'
                        ],
                        'author_name'  => [
                            'description' => esc_html__('Author name', "classified-listing-pro"),
                            'type'        => 'string'
                        ],
                        'author_email' => [
                            'type'        => 'string',
                            'description' => esc_html__('Author\'s email address', "classified-listing-pro"),
                            'format'      => 'email'
                        ],
                        'parent'       => [
                            'type'        => 'integer',
                            'description' => esc_html__('Specific parent comment ID', "classified-listing-pro"),
                        ]
                    ],
                ]
            ]
        );

    }

    public function create_permission_check() {
        Api::permission_check();
        if (!Functions::get_option_item('rtcl_moderation_settings', 'has_comment_form', false, 'checkbox')) {
            return new WP_Error(
                'rest_review_is_disabled',
                esc_html__('Sorry, review is disabled at this site.', 'classified-listing-pro'),
                array('status' => 401)
            );
        }
        return true;
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array Comments collection parameters.
     * @since 4.7.0
     *
     */
    public function get_collection_params() {
        $query_params = parent::get_collection_params();

        $query_params['after'] = [
            'description' => esc_html__('Limit response to comments published after a given ISO8601 compliant date.', 'classified-listing-pro'),
            'type'        => 'string',
            'format'      => 'date-time',
        ];

        $query_params['author'] = [
            'description' => esc_html__('Limit result set to comments assigned to specific user IDs. Requires authorization.', 'classified-listing-pro'),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ],
        ];

        $query_params['author_exclude'] = [
            'description' => esc_html__('Ensure result set excludes comments assigned to specific user IDs. Requires authorization.', 'classified-listing-pro'),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ],
        ];

        $query_params['author_email'] = [
            'default'     => null,
            'description' => esc_html__('Limit result set to that from a specific author email. Requires authorization.', 'classified-listing-pro'),
            'format'      => 'email',
            'type'        => 'string',
        ];

        $query_params['before'] = [
            'description' => esc_html__('Limit response to comments published before a given ISO8601 compliant date.', 'classified-listing-pro'),
            'type'        => 'string',
            'format'      => 'date-time',
        ];

        $query_params['exclude'] = [
            'description' => esc_html__('Ensure result set excludes specific IDs.', 'classified-listing-pro'),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ],
            'default'     => [],
        ];

        $query_params['include'] = [
            'description' => esc_html__('Limit result set to specific IDs.', 'classified-listing-pro'),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ],
            'default'     => [],
        ];

        $query_params['parent'] = [
            'default'     => [],
            'description' => esc_html__('Limit result set to comments of specific parent IDs.', "classified-listing-pro"),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ],
        ];

        $query_params['parent_exclude'] = [
            'default'     => [],
            'description' => esc_html__('Ensure result set excludes specific parent IDs.', "classified-listing-pro"),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ],
        ];

        $query_params['listing'] = [
            'description' => esc_html__('Limit result set to listing IDS', "classified-listing-pro"),
            'type'        => 'array',
            'items'       => [
                'type' => 'integer',
            ]
        ];

        $query_params['status'] = [
            'default'           => 'approve',
            'description'       => esc_html__('Limit result set to comments assigned a specific status. Requires authorization.', 'classified-listing-pro'),
            'sanitize_callback' => 'sanitize_key',
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $query_params['type'] = [
            'default'           => 'comment',
            'description'       => esc_html__('Limit result set to comments assigned a specific type. Requires authorization.', 'classified-listing-pro'),
            'sanitize_callback' => 'sanitize_key',
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $query_params['password'] = [
            'description' => esc_html__('The password for the post if it is password protected.', 'classified-listing-pro'),
            'type'        => 'string',
        ];

        /**
         * Filters REST API collection parameters for the comments controller.
         *
         * This filter registers the collection parameter, but does not map the
         * collection parameter to an internal WP_Comment_Query parameter. Use the
         * `rest_comment_query` filter to set WP_Comment_Query parameters.
         *
         *
         * @param array $query_params JSON Schema-formatted collection parameters.
         */
        return apply_filters('rtcl_rest_review_collection_params', $query_params);
    }

    /**
     * Retrieves a list of comment items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response|WP_HTTP_Response
     *
     */
    public function get_items($request) {


        // Retrieve the list of registered collection query parameters.
        $registered = $this->get_collection_params();

        /*
         * This array defines mappings between public API query parameters whose
         * values are accepted as-passed, and their internal WP_Query parameter
         * name equivalents (some are the same). Only values which are also
         * present in $registered will be set.
         */
        $parameter_mappings = [
            'author'         => 'author__in',
            'author_email'   => 'author_email',
            'author_exclude' => 'author__not_in',
            'exclude'        => 'comment__not_in',
            'include'        => 'comment__in',
            'offset'         => 'offset',
            'order'          => 'order',
            'parent'         => 'parent__in',
            'parent_exclude' => 'parent__not_in',
            'per_page'       => 'number',
            'listing'        => 'post__in',
            'search'         => 'search',
            'status'         => 'status',
            'type'           => 'type',
        ];

        $prepared_args = [
            'post_type' => rtcl()->post_type
        ];

        /*
         * For each known parameter which is both registered and present in the request,
         * set the parameter's value on the query $prepared_args.
         */
        foreach ($parameter_mappings as $api_param => $wp_param) {
            if (isset($registered[$api_param], $request[$api_param])) {
                $prepared_args[$wp_param] = $request[$api_param];
            }
        }

        // Ensure certain parameter values default to empty strings.
        foreach (array('author_email', 'search') as $param) {
            if (!isset($prepared_args[$param])) {
                $prepared_args[$param] = '';
            }
        }

        if (isset($registered['orderby'])) {
            $prepared_args['orderby'] = $this->normalize_query_param($request['orderby']);
        }

        $prepared_args['no_found_rows'] = false;

        $prepared_args['date_query'] = [];

        // Set before into date query. Date query must be specified as an array of an array.
        if (isset($registered['before'], $request['before'])) {
            $prepared_args['date_query'][0]['before'] = $request['before'];
        }

        // Set after into date query. Date query must be specified as an array of an array.
        if (isset($registered['after'], $request['after'])) {
            $prepared_args['date_query'][0]['after'] = $request['after'];
        }

        if (isset($registered['page']) && empty($request['offset'])) {
            $prepared_args['offset'] = $prepared_args['number'] * (absint($request['page']) - 1);
        }

        /**
         * Filters arguments, before passing to WP_Comment_Query, when querying comments via the REST API.
         *
         * @param array           $prepared_args Array of arguments for WP_Comment_Query.
         * @param WP_REST_Request $request       The current request.
         *
         *
         * @link  https://developer.wordpress.org/reference/classes/wp_comment_query/
         *
         */
        $prepared_args = apply_filters('rtcl_rest_review_query', $prepared_args, $request);

        $query = new WP_Comment_Query;
        $query_result = $query->query($prepared_args);

        $reviews = [];

        foreach ($query_result as $comment) {
//            if (!$this->check_read_permission($comment, $request)) {
//                continue;
//            }
            $data = Api::get_single_review_data($comment);
            $reviews[] = $this->prepare_response_for_collection($data);
        }

        $total_comments = $query->found_comments;
        $max_pages = $query->max_num_pages;
        $pagination = [
            'total'        => $query->found_comments,
            'per_page'     => $query->query_vars['number'],
            'current_page' => $query->query_vars['paged'],
            'total_pages'  => $query->max_num_pages
        ];
        if ($total_comments < 1) {
            $pagination = null;
            // Out-of-bounds, run the query again without LIMIT for total count.
            unset($prepared_args['number'], $prepared_args['offset']);

            $query = new WP_Comment_Query;
            $prepared_args['count'] = true;

            $total_comments = $query->query($prepared_args);
            $max_pages = ceil($total_comments / $request['per_page']);
        }
        $data = [
            'data'       => $reviews,
            'pagination' => $pagination
        ];
        $response = rest_ensure_response($data);
        $response->header('X-WP-Total', $total_comments);
        $response->header('X-WP-TotalPages', $max_pages);

        $base = add_query_arg(urlencode_deep($request->get_query_params()), rest_url(sprintf('%s/%s', $this->namespace, $this->rest_base)));

        if ($request['page'] > 1) {
            $prev_page = $request['page'] - 1;

            if ($prev_page > $max_pages) {
                $prev_page = $max_pages;
            }

            $prev_link = add_query_arg('page', $prev_page, $base);
            $response->link_header('prev', $prev_link);
        }

        if ($max_pages > $request['page']) {
            $next_page = $request['page'] + 1;
            $next_link = add_query_arg('page', $next_page, $base);

            $response->link_header('next', $next_link);
        }

        return $response;
    }


    /**
     * Creates a comment.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or error object on failure.
     * @since 4.7.0
     *
     */
    public function create_item($request) {
        Api::check_is_auth_user_request();
        if (get_option('comment_registration') && !is_user_logged_in()) {
            return new WP_Error(
                'rtcl_rest_review_login_required',
                esc_html__('Sorry, you must be logged in to comment.', 'classified-listing-pro'),
                array('status' => 401)
            );
        }

        // Limit who can set comment `author`, `author_ip` or `status` to anything other than the default.
        if (isset($request['author']) && get_current_user_id() !== $request['author'] && !current_user_can('moderate_comments')) {
            return new WP_Error(
                'rtcl_rest_review_invalid_author',
                /* translators: %s: Request parameter. */
                sprintf(esc_html__("Sorry, you are not allowed to edit '%s' for comments.", "classified-listing-pro"), 'author'),
                array('status' => rest_authorization_required_code())
            );
        }

        if (isset($request['author_ip']) && !current_user_can('moderate_comments')) {
            if (empty($_SERVER['REMOTE_ADDR']) || $request['author_ip'] !== $_SERVER['REMOTE_ADDR']) {
                return new WP_Error(
                    'rtcl_rest_review_invalid_author_ip',
                    /* translators: %s: Request parameter. */
                    sprintf(esc_html__("Sorry, you are not allowed to edit '%s' for comments.", "classified-listing-pro"), 'author_ip'),
                    array('status' => rest_authorization_required_code())
                );
            }
        }

        if (isset($request['status']) && !current_user_can('moderate_comments')) {
            return new WP_Error(
                'rtcl_rest_review_invalid_status',
                /* translators: %s: Request parameter. */
                sprintf(esc_html__("Sorry, you are not allowed to edit '%s' for comments.", "classified-listing-pro"), 'status'),
                array('status' => rest_authorization_required_code())
            );
        }

        if (empty($request['listing'])) {
            return new WP_Error(
                'rtcl_rest_review_invalid_listing_id',
                esc_html__('Sorry, you are not allowed to create this review without a listing.', "classified-listing-pro"),
                array('status' => 403)
            );
        }

        $listing = rtcl()->factory->get_listing((int)$request['listing']);

        if (!$listing) {
            return new WP_Error(
                'rtcl_rest_review_invalid_listing_id',
                esc_html__('Sorry, you are not allowed to create this comment without a post.', "classified-listing-pro"),
                array('status' => 403)
            );
        }

        if (is_user_logged_in() && $listing->get_author_id() === get_current_user_id()) {
            return new WP_Error(
                'rtcl_rest_review_permission',
                esc_html__('Sorry, you are not allowed to review at your own listing.', "classified-listing-pro"),
                array('status' => 403)
            );
        }

        if ('draft' === $listing->get_status()) {
            return new WP_Error(
                'rtcl_rest_review_draft_post',
                esc_html__('Sorry, you are not allowed to create a review on this listing.', "classified-listing-pro"),
                array('status' => 403)
            );
        }

        if ('trash' === $listing->get_status()) {
            return new WP_Error(
                'rtcl_rest_review_trash_post',
                esc_html__('Sorry, you are not allowed to create a review on this post.', "classified-listing-pro"),
                array('status' => 403)
            );
        }

        if (!$this->check_read_post_permission($listing->get_post_object(), $request)) {
            return new WP_Error(
                'rtcl_rest_cannot_read_listing',
                esc_html__('Sorry, you are not allowed to read the listing for this review.', "classified-listing-pro"),
                array('status' => rest_authorization_required_code())
            );
        }

        if (!comments_open($listing->get_id())) {
            return new WP_Error(
                'rtcl_rest_review_closed',
                esc_html__('Sorry, reviews are closed for this item.', "classified-listing-pro"),
                array('status' => 403)
            );
        }

        $prepared_comment = $this->prepare_item_for_database($request);
        if (is_wp_error($prepared_comment)) {
            return $prepared_comment;
        }

        $prepared_comment['comment_type'] = 'comment';

        // Setting remaining values before wp_insert_comment so we can use wp_allow_comment().
        if (!isset($prepared_comment['comment_date_gmt'])) {
            $prepared_comment['comment_date_gmt'] = current_time('mysql', true);
        }

        // Set author data if the user's logged in.
        $missing_author = empty($prepared_comment['user_id'])
            && empty($prepared_comment['comment_author'])
            && empty($prepared_comment['comment_author_email'])
            && empty($prepared_comment['comment_author_url']);

        if (is_user_logged_in() && $missing_author) {
            $user = wp_get_current_user();

            $prepared_comment['user_id'] = $user->ID;
            $prepared_comment['comment_author'] = $user->display_name;
            $prepared_comment['comment_author_email'] = $user->user_email;
            $prepared_comment['comment_author_url'] = $user->user_url;
        }

        // Honor the discussion setting that requires a name and email address of the comment author.
        if (get_option('require_name_email')) {
            if (empty($prepared_comment['comment_author']) || empty($prepared_comment['comment_author_email'])) {
                return new WP_Error(
                    'rtcl_rest_review_author_data_required',
                    esc_html__('Creating a comment requires valid author name and email values.', 'classified-listing-pro'),
                    array('status' => 400)
                );
            }
        }

        if (!isset($prepared_comment['comment_author_email'])) {
            $prepared_comment['comment_author_email'] = '';
        }

        if (!isset($prepared_comment['comment_author_url'])) {
            $prepared_comment['comment_author_url'] = '';
        }

        if (!isset($prepared_comment['comment_agent'])) {
            $prepared_comment['comment_agent'] = '';
        }

        $check_comment_lengths = wp_check_comment_data_max_lengths($prepared_comment);

        if (is_wp_error($check_comment_lengths)) {
            $error_code = $check_comment_lengths->get_error_code();
            return new WP_Error(
                $error_code,
                esc_html__('Comment field exceeds maximum length allowed.', 'classified-listing-pro'),
                array('status' => 400)
            );
        }

        $prepared_comment['comment_approved'] = wp_allow_comment($prepared_comment, true);
        if (is_wp_error($prepared_comment['comment_approved'])) {
            $error_code = $prepared_comment['comment_approved']->get_error_code();
            $error_message = $prepared_comment['comment_approved']->get_error_message();

            if ('comment_duplicate' === $error_code) {
                return new WP_Error(
                    $error_code,
                    $error_message,
                    array('status' => 409)
                );
            }

            if ('comment_flood' === $error_code) {
                return new WP_Error(
                    $error_code,
                    $error_message,
                    array('status' => 400)
                );
            }

            return $prepared_comment['comment_approved'];
        }


        /**
         * Filters a comment before it is inserted via the REST API.
         *
         * Allows modification of the comment right before it is inserted via wp_insert_comment().
         * Returning a WP_Error value from the filter will short-circuit insertion and allow
         * skipping further processing.
         *
         * @param array|WP_Error  $prepared_comment The prepared comment data for wp_insert_comment().
         * @param WP_REST_Request $request          Request used to insert the comment.
         *
         * @since 4.7.0
         * @since 4.8.0 `$prepared_comment` can now be a WP_Error to short-circuit insertion.
         *
         */
        $prepared_comment = apply_filters('rest_pre_insert_comment', $prepared_comment, $request);
        if (is_wp_error($prepared_comment)) {
            return $prepared_comment;
        }

        $prepared_comment = apply_filters('rtcl_rest_pre_insert_comment', $prepared_comment, $request);
        if (is_wp_error($prepared_comment)) {
            return $prepared_comment;
        }
        $exist_args = array(
            'post_type' => rtcl()->post_type,
            'post_id'   => $listing->get_id(),
            'number'    => 1,
            'parent'    => 0,
        );
        if (isset($prepared_comment['user_id'])) {
            $exist_args['user_id'] = $prepared_comment['user_id'];
        } else {
            $exist_args['author_email'] = $prepared_comment['comment_author_email'];
        }
        $comment_exist = get_comments($exist_args);


        if (count($comment_exist)) {
            if (Functions::get_option_item('rtcl_moderation_settings', 'enable_update_rating', '', 'checkbox')) {
                $tempReview = [];
                $tempReview['comment_ID'] = $comment_exist[0]->comment_ID;
                $tempReview['comment_content'] = $prepared_comment['comment_content'];
                wp_update_comment($tempReview);
                if (Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox')) {
                    update_comment_meta($comment_exist[0]->comment_ID, 'rating', $request->get_param('rating'));
                }
                update_comment_meta($comment_exist[0]->comment_ID, 'title', $request->get_param('title'));
                $review = get_comment($comment_exist[0]->comment_ID);

                /**
                 * Fires completely after a comment is created or updated via the REST API.
                 *
                 * @param WP_Comment      $comment  Inserted or updated comment object.
                 * @param WP_REST_Request $request  Request object.
                 * @param bool            $creating True when creating a comment, false
                 *                                  when updating.
                 *
                 */
                do_action('rtcl_rest_after_update_comment', $review, $request, true);

                $reviewData = Api::get_single_review_data($review);
                return rest_ensure_response($reviewData);
            } else {
                return new WP_Error(
                    "rtcl_review_already_exist",
                    esc_html__('You have already a review.', 'classified-listing-pro'),
                    array('status' => 400)
                );
            }
        }


        $comment_id = wp_insert_comment(wp_filter_comment(wp_slash((array)$prepared_comment)));

        if (!$comment_id) {
            return new WP_Error(
                'rtcl_rest_review_failed_create',
                esc_html__('Creating review failed.', 'classified-listing-pro'),
                array('status' => 500)
            );
        }

        Comments::clear_transients($comment_id);
        $comment = get_comment($comment_id);

        /**
         * Fires after a comment is created or updated via the REST API.
         *
         * @param WP_Comment      $comment  Inserted or updated comment object.
         * @param WP_REST_Request $request  Request object.
         * @param bool            $creating True when creating a comment, false
         *                                  when updating.
         *
         */
        do_action('rest_insert_comment', $comment, $request, true);
        do_action('rtcl_rest_insert_comment', $comment, $request, true);

        if (Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox')) {
            update_comment_meta($comment_id, 'rating', $request->get_param('rating'));
        }
        update_comment_meta($comment_id, 'title', $request->get_param('title'));

        /**
         * Fires completely after a comment is created or updated via the REST API.
         *
         * @param WP_Comment      $comment  Inserted or updated comment object.
         * @param WP_REST_Request $request  Request object.
         * @param bool            $creating True when creating a comment, false
         *                                  when updating.
         *
         */
        do_action('rtcl_rest_after_insert_comment', $comment, $request, true);

        $response = Api::get_single_review_data($comment);
        return rest_ensure_response($response);
    }


    /**
     * Prepares a single comment to be inserted into the database.
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return array|WP_Error Prepared comment, otherwise WP_Error object.
     * @since 4.7.0
     *
     */
    protected function prepare_item_for_database($request) {
        $prepared_comment = [];

        if (is_string($request['content'])) {
            $prepared_comment['comment_content'] = trim($request['content']);
        }
        $prepared_comment['comment_post_ID'] = (int)$request['listing'];

        if (isset($request['parent'])) {
            $prepared_comment['comment_parent'] = $request['parent'];
        }

        if (isset($request['author'])) {
            $user = new WP_User($request['author']);

            if ($user->exists()) {
                $prepared_comment['user_id'] = $user->ID;
                $prepared_comment['comment_author'] = $user->display_name;
                $prepared_comment['comment_author_email'] = $user->user_email;
                $prepared_comment['comment_author_url'] = $user->user_url;
            } else {
                return new WP_Error(
                    'rtcl_rest_review_author_invalid',
                    esc_html__('Invalid comment author ID.', 'classified-listing-pro'),
                    array('status' => 400)
                );
            }
        }

        if (isset($request['author_name'])) {
            $prepared_comment['comment_author'] = $request['author_name'];
        }

        if (isset($request['author_email'])) {
            $prepared_comment['comment_author_email'] = $request['author_email'];
        }

        if (isset($request['author_url'])) {
            $prepared_comment['comment_author_url'] = $request['author_url'];
        }

        if (isset($request['author_ip']) && current_user_can('moderate_comments')) {
            $prepared_comment['comment_author_IP'] = $request['author_ip'];
        } elseif (!empty($_SERVER['REMOTE_ADDR']) && rest_is_ip_address($_SERVER['REMOTE_ADDR'])) {
            $prepared_comment['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
        } else {
            $prepared_comment['comment_author_IP'] = '127.0.0.1';
        }

        if (!empty($request['author_user_agent'])) {
            $prepared_comment['comment_agent'] = $request['author_user_agent'];
        } elseif ($request->get_header('user_agent')) {
            $prepared_comment['comment_agent'] = $request->get_header('user_agent');
        }

        if (!empty($request['date'])) {
            $date_data = rest_get_date_with_gmt($request['date']);

            if (!empty($date_data)) {
                list($prepared_comment['comment_date'], $prepared_comment['comment_date_gmt']) = $date_data;
            }
        } elseif (!empty($request['date_gmt'])) {
            $date_data = rest_get_date_with_gmt($request['date_gmt'], true);

            if (!empty($date_data)) {
                list($prepared_comment['comment_date'], $prepared_comment['comment_date_gmt']) = $date_data;
            }
        }

        /**
         * Filters a comment added via the REST API after it is prepared for insertion into the database.
         *
         * Allows modification of the comment right after it is prepared for the database.
         *
         * @param array           $prepared_comment The prepared comment data for `wp_insert_comment`.
         * @param WP_REST_Request $request          The current request.
         *
         * @since 4.7.0
         *
         */
        return apply_filters('rtcl_rest_preprocess_comment', $prepared_comment, $request);
    }

    /**
     * Prepends internal property prefix to query parameters to match our response fields.
     *
     * @param string $query_param Query parameter.
     *
     * @return string The normalized query parameter.
     *
     */
    protected function normalize_query_param($query_param) {
        $prefix = 'comment_';

        switch ($query_param) {
            case 'id':
                $normalized = $prefix . 'ID';
                break;
            case 'post':
                $normalized = $prefix . 'post_ID';
                break;
            case 'parent':
                $normalized = $prefix . 'parent';
                break;
            case 'include':
                $normalized = 'comment__in';
                break;
            default:
                $normalized = $prefix . $query_param;
                break;
        }

        return $normalized;
    }

    /**
     * Checks if the comment can be read.
     *
     * @param WP_Comment      $comment Comment object.
     * @param WP_REST_Request $request Request data to check.
     *
     * @return bool Whether the comment can be read.
     *
     */
    protected function check_read_permission($comment, $request) {
        if (!empty($comment->comment_post_ID)) {
            if ($post = get_post($comment->comment_post_ID)) {
                if ($this->check_read_post_permission($post, $request) && 1 === (int)$comment->comment_approved) {
                    return true;
                }
            }
        }

        if (0 === get_current_user_id()) {
            return false;
        }

        if (empty($comment->comment_post_ID) && !current_user_can('moderate_comments')) {
            return false;
        }

        if (!empty($comment->user_id) && get_current_user_id() === (int)$comment->user_id) {
            return true;
        }

        return current_user_can('edit_comment', $comment->comment_ID);
    }

    /**
     * Checks if the post can be read.
     *
     * Correctly handles posts with the inherit status.
     *
     * @param WP_Post         $post    Post object.
     * @param WP_REST_Request $request Request data to check.
     *
     * @return bool Whether post can be read.
     *
     */
    protected function check_read_post_permission($post, $request) {
        $post_type = get_post_type_object($post->post_type);

        // Return false if custom post type doesn't exist
        if (!$post_type) {
            return false;
        }


        $posts_controller = $post_type->get_rest_controller();

        // Ensure the posts controller is specifically a WP_REST_Posts_Controller instance
        // before using methods specific to that controller.
        if (!$posts_controller instanceof WP_REST_Posts_Controller) {
            $posts_controller = new WP_REST_Posts_Controller($post->post_type);
        }

        $has_password_filter = false;

        // Only check password if a specific post was queried for or a single comment
        $requested_post = !empty($request['post']) && (!is_array($request['post']) || 1 === count($request['post']));
        $requested_comment = !empty($request['id']);
        if (($requested_post || $requested_comment) && $posts_controller->can_access_password_content($post, $request)) {
            add_filter('post_password_required', '__return_false');

            $has_password_filter = true;
        }

        if (post_password_required($post)) {
            $result = current_user_can('edit_post', $post->ID);
        } else {
            $result = $posts_controller->check_read_permission($post);
        }

        if ($has_password_filter) {
            remove_filter('post_password_required', '__return_false');
        }

        return $result;
    }

}