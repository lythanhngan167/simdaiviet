<?php

namespace RtclPro\Controllers;

use Rtcl\Controllers\Hooks\Comments;
use Rtcl\Models\Payment;
use Rtcl\Models\Listing;
use Rtcl\Helpers\Functions;

class CommentController
{


    /**
     * Hook in methods.
     */
    public static function init() {
        // Rating posts.
        add_filter('comments_open', [__CLASS__, 'comments_open'], 10, 2);
        add_filter('preprocess_comment', [__CLASS__, 'check_comment_is_allowed'], 0);
        add_filter('preprocess_comment', [__CLASS__, 'check_comment_rating'], 1);
        add_filter('preprocess_comment', [__CLASS__, 'check_comment_title'], 2);
        add_action('comment_post', [__CLASS__, 'add_comment_rating'], 1);
        add_action('comment_post', [__CLASS__, 'add_comment_title'], 1);
        add_action('comment_moderation_recipients', [__CLASS__, 'comment_moderation_recipients'], 10, 2);

        // Clear transients.
        add_action('wp_update_comment_count', [__CLASS__, 'clear_transients']);

        // Count comments.
        add_filter('wp_count_comments', [__CLASS__, 'wp_count_comments'], 10, 2);

        // Delete comments count cache whenever there is a new comment or a comment status changes.
        add_action('wp_insert_comment', [__CLASS__, 'delete_comments_count_cache']);
        add_action('wp_set_comment_status', [__CLASS__, 'delete_comments_count_cache']);

        // Comment
        add_action('wp_ajax_rtcl_ajax_submit_comment', [__CLASS__, 'rtcl_ajax_submit_comment']);
        add_action('wp_ajax_nopriv_rtcl_ajax_submit_comment', [__CLASS__, 'rtcl_ajax_submit_comment']);

    }


    public static function rtcl_ajax_submit_comment() {
        $message = $comment_html = $comment_id = null;
        $error = $comment = false;
        if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
            $post = get_post(absint($_POST['comment_post_ID']));
            if ($post->post_author == $current_user_id) {
                $error = true;
                $message = esc_html__("Ad author can't post rating.", 'classified-listing-pro');
            } else {
                $args = array(
                    'post_type' => rtcl()->post_type,
                    'post_id'   => $_POST['comment_post_ID'],
                    'user_id'   => $current_user_id,
                    'number'    => 1,
                    'parent'    => 0,
                );
                $comment_exist = get_comments($args);

                if (count($comment_exist) > 0) {
                    if (Functions::get_option_item('rtcl_moderation_settings', 'enable_update_rating', '', 'checkbox')) {
                        $title = sanitize_text_field($_POST['title']);
                        $comment_text = esc_textarea($_POST['comment']);
                        $rating = (isset($_POST['rating']) && absint($_POST['rating']) <= 5 && absint($_POST['rating']) >= 1) ? absint($_POST['rating']) : 0;
                        if ($title && $comment_text && $rating) {
                            $comment = array();
                            $comment['comment_ID'] = $comment_exist[0]->comment_ID;
                            $comment['comment_content'] = $comment_text;
                            wp_update_comment($comment);

                            update_comment_meta($comment_exist[0]->comment_ID, 'rating', $rating);
                            update_comment_meta($comment_exist[0]->comment_ID, 'title', $title);
                            Comments::clear_transients($post->ID);
                            $comment = get_comment($comment_exist[0]->comment_ID);
                            $message = esc_html__("Your rating has been updated.", 'classified-listing-pro');
                        } else {
                            $error = true;
                            $message = esc_html__("Please add the required field.", 'classified-listing-pro');
                        }
                    } else {
                        $error = true;
                        $message = esc_html__("You have already a review.", 'classified-listing-pro');
                    }
                }
            }
        } else {
            if (isset($_POST['email']) && is_string($_POST['email'])) {
                $comment_author_email = trim($_POST['email']);
            }
            if ($comment_author_email) {
                $args = array(
                    'post_type'    => rtcl()->post_type,
                    'post_id'      => $_POST['comment_post_ID'],
                    'author_email' => $comment_author_email,
                    'number'       => 1,
                    'parent'       => 0,
                );
                $comment_exist = get_comments($args);
                if (count($comment_exist) > 0) {
                    if (Functions::get_option_item('rtcl_moderation_settings', 'enable_update_rating', '', 'checkbox')) {
                        $title = sanitize_text_field($_POST['title']);
                        $comment_text = esc_textarea($_POST['comment']);
                        $rating = (isset($_POST['rating']) && absint($_POST['rating']) <= 5 && absint($_POST['rating']) >= 1) ? absint($_POST['rating']) : 0;
                        if ($title && $comment_text && $rating) {
                            $comment = array();
                            $comment['comment_ID'] = $comment_exist[0]->comment_ID;
                            $comment['comment_content'] = $comment_text;
                            wp_update_comment($comment);

                            update_comment_meta($comment_exist[0]->comment_ID, 'rating', $rating);
                            update_comment_meta($comment_exist[0]->comment_ID, 'title', $title);
                            Comments::clear_transients($_POST['comment_post_ID']);
                            $comment = get_comment($comment_exist[0]->comment_ID);
                            $message = esc_html__("Your rating has been updated.", 'classified-listing-pro');
                        } else {
                            $error = true;
                            $message = esc_html__("Please add the required field.", 'classified-listing-pro');
                        }
                    } else {
                        $error = true;
                        $message = esc_html__("You have already a review.", 'classified-listing-pro');
                    }
                }
            } else {
                $error = true;
                $message = esc_html__("Please add the required field.", 'classified-listing-pro');
            }
        }

        if ($comment || $error) {
            if ($comment) {
                /*
                 * Set Cookies
                 */
                $user = wp_get_current_user();
                do_action('set_comment_cookies', $comment, $user);

                /*
                 * If you do not like this loop, pass the comment depth from JavaScript code
                 */
                $comment_depth = 1;
                $comment_parent = $comment->comment_parent;
                while ($comment_parent) {
                    $comment_depth++;
                    $parent_comment = get_comment($comment_parent);
                    $comment_parent = $parent_comment->comment_parent;
                }

                /*
                 * Set the globals, so our comment functions below will work correctly
                 */
                $GLOBALS['comment'] = $comment;
                $GLOBALS['comment_depth'] = $comment_depth;
                $comment_id = $comment->comment_ID;
                /*
                 * Here is the comment template, you can configure it for your website
                 * or you can try to find a ready function in your theme files
                 */
                $comment_html = Functions::get_template_html('listing/review', array('comment' => $comment), '', rtclPro()->get_plugin_template_path());
            }
            wp_send_json(array(
                'comment_html' => $comment_html,
                'message'      => $message,
                'error'        => $error,
                'comment_id'   => $comment_id,
            ));
        }

        $comment = wp_handle_comment_submission(wp_unslash($_POST));
        if (is_wp_error($comment)) {
            $error = true;
            $error_data = intval($comment->get_error_data());
            if (!empty($error_data)) {
                $message = $comment->get_error_message();
            } else {
                $message = esc_html__('Unknown error', 'classified-listing-pro');
            }
        } else {
            $message = esc_html__("Successfully posted", "classified-listing");
            /*
             * Set Cookies
             */
            $user = wp_get_current_user();
            do_action('set_comment_cookies', $comment, $user);

            /*
             * If you do not like this loop, pass the comment depth from JavaScript code
             */
            $comment_depth = 1;
            $comment_parent = $comment->comment_parent;
            while ($comment_parent) {
                $comment_depth++;
                $parent_comment = get_comment($comment_parent);
                $comment_parent = $parent_comment->comment_parent;
            }

            /*
             * Set the globals, so our comment functions below will work correctly
             */
            $GLOBALS['comment'] = $comment;
            $GLOBALS['comment_depth'] = $comment_depth;
            $comment_id = $comment->comment_ID;
            /*
             * Here is the comment template, you can configure it for your website
             * or you can try to find a ready function in your theme files
             */
            $comment_html = Functions::get_template_html('listing/review', array('comment' => $comment), '', rtclPro()->get_plugin_template_path());
        }


        wp_send_json(array(
            'comment_html' => $comment_html,
            'message'      => $message,
            'error'        => $error,
            'comment_id'   => $comment_id,
        ));
    }


    public static function comments_open($open, $post_id) {
        if (rtcl()->post_type === get_post_type($post_id)) {
            $open = false;
            if (Functions::get_option_item('rtcl_moderation_settings', 'has_comment_form', false, 'checkbox')) {
                $open = true;
            }
        }

        return apply_filters('rtcl_has_comment_form', $open, $post_id);
    }

    public static function check_comment_is_allowed($comment_data) {
        if (!is_admin() && isset($_POST['comment_post_ID'], $comment_data['comment_type']) && rtcl()->post_type === get_post_type(absint($_POST['comment_post_ID']))) { // WPCS: input var ok, CSRF ok.
            $args = array(
                'post_type' => rtcl()->post_type,
                'post_id'   => $_POST['comment_post_ID'],
                'number'    => 1,
                'parent'    => 0,
            );
            if (is_user_logged_in()) {
                $current_user_id = get_current_user_id();
                $post = get_post(absint($_POST['comment_post_ID']));
                if ($post->post_author == $current_user_id) {
                    if (wp_doing_ajax()) {
                        die(esc_html__("Ad author can't post rating.", 'classified-listing-pro'));
                    }
                    wp_die(esc_html__("Ad author can't post rating.", 'classified-listing-pro'));
                    exit;
                } else {
                    $args['user_id'] = $current_user_id;
                }
            } else {
                if (isset($_POST['email']) && is_string($_POST['email'])) {
                    $comment_author_email = trim($_POST['email']);
                }
                $args['author_email'] = $comment_author_email;
            }

            $comment_exist = get_comments($args);

            if (count($comment_exist) > 0) {
                if (Functions::get_option_item('rtcl_moderation_settings', 'enable_update_rating', '', 'checkbox')) {
                    if (wp_doing_ajax()) {
                        die(__("Only ajax can update the comment.", 'classified-listing-pro'));
                    }
                    wp_die(__("Only ajax can update the comment.", 'classified-listing-pro'));
                    exit;
                } else {
                    if (wp_doing_ajax()) {
                        die(__("You have already a review.", 'classified-listing-pro'));
                    }
                    wp_die(__("You have already a review.", 'classified-listing-pro'));
                    exit;
                }
            }
        }

        return $comment_data;
    }

    /**
     * Validate the comment ratings.
     *
     * @param array $comment_data Comment data.
     *
     * @return array
     */
    public static function check_comment_rating($comment_data) {
        // If posting a comment (not trackback etc) and not logged in.
        if (!is_admin() && isset($_POST['comment_post_ID'], $_POST['rating'], $comment_data['comment_type']) && rtcl()->post_type === get_post_type(absint($_POST['comment_post_ID'])) && empty($_POST['rating']) && '' === $comment_data['comment_type'] && Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox')) { // WPCS: input var ok, CSRF ok.
            if (wp_doing_ajax()) {
                die(esc_html__('Please rate the listing.', 'classified-listing-pro'));
            }
            wp_die(esc_html__('Please rate the listing.', 'classified-listing-pro'));
            exit;
        }

        return $comment_data;
    }

    /**
     * Validate the comment Title.
     *
     * @param array $comment_data Comment data.
     *
     * @return array
     */
    public static function check_comment_title($comment_data) {
        // If posting a comment (not trackback etc) and not logged in.
        if (!is_admin() && isset($_POST['comment_post_ID'], $_POST['title'], $comment_data['comment_type']) && rtcl()->post_type === get_post_type(absint($_POST['comment_post_ID'])) && empty($_POST['title']) && '' === $comment_data['comment_type']) { // WPCS: input var ok, CSRF ok.
            if (wp_doing_ajax()) {
                die(esc_html__('Please add the review title.', 'classified-listing-pro'));
            }
            wp_die(esc_html__('Please add the review title.', 'classified-listing-pro'));
            exit;
        }

        return $comment_data;
    }


    /**
     * Rating field for comments.
     *
     * @param int $comment_id Comment ID.
     */
    public static function add_comment_rating($comment_id) {
        if (isset($_POST['rating'], $_POST['comment_post_ID']) && rtcl()->post_type === get_post_type(absint($_POST['comment_post_ID']))) { // WPCS: input var ok, CSRF ok.
            if (!$_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0) { // WPCS: input var ok, CSRF ok, sanitization ok.
                return;
            }
            add_comment_meta($comment_id, 'rating', intval($_POST['rating']), true); // WPCS: input var ok, CSRF ok.

            $post_id = isset($_POST['comment_post_ID']) ? absint($_POST['comment_post_ID']) : 0; // WPCS: input var ok, CSRF ok.
            if ($post_id) {
                self::clear_transients($post_id);
                do_action('rtcl_add_comment_rating', $comment_id, $post_id);
            }
        }
    }

    /**
     * Title field for comments.
     *
     * @param int $comment_id Comment ID.
     */
    public static function add_comment_title($comment_id) {
        if (isset($_POST['title'], $_POST['comment_post_ID']) && rtcl()->post_type === get_post_type(absint($_POST['comment_post_ID'])) && $title = sanitize_text_field($_POST['title'])) { // WPCS: input var ok, CSRF ok.
            add_comment_meta($comment_id, 'title', $title, true); // WPCS: input var ok, CSRF ok.
        }
    }


    /**
     * Modify recipient of review email.
     *
     * @param array $emails     Emails.
     * @param int   $comment_id Comment ID.
     *
     * @return array
     */
    public static function comment_moderation_recipients($emails, $comment_id) {
        $comment = get_comment($comment_id);

        if ($comment && rtcl()->post_type === get_post_type($comment->comment_post_ID)) {
            $emails = array(get_option('admin_email'));
        }

        return $emails;
    }


    /**
     * Ensure product average rating and review count is kept up to date.
     *
     * @param int $post_id Post ID.
     */
    public static function clear_transients($post_id) {
        if (rtcl()->post_type === get_post_type($post_id)) {
            $listing = rtcl()->factory->get_listing($post_id);
            self::get_rating_counts_for_listing($listing);
            self::get_average_rating_for_listing($listing);
            self::get_review_count_for_listing($listing);
        }
    }

    /**
     * Delete comments count cache whenever there is
     * new comment or the status of a comment changes. Cache
     * will be regenerated next time Comments::wp_count_comments()
     * is called.
     */
    public static function delete_comments_count_cache() {
        delete_transient('rtcl_count_comments');
    }

    /**
     * Remove order notes and webhook delivery logs from wp_count_comments().
     *
     * @param object $stats   Comment stats.
     * @param int    $post_id Post ID.
     *
     * @return object
     * @since  1.0.0
     *
     */
    public static function wp_count_comments($stats, $post_id) {
        global $wpdb;

        if (0 === $post_id) {
            $stats = get_transient('rtcl_count_comments');

            if (!$stats) {
                $stats = array(
                    'total_comments' => 0,
                    'all'            => 0,
                );

                $count = $wpdb->get_results(
                    "
					SELECT comment_approved, COUNT(*) AS num_comments
					FROM {$wpdb->comments}
					WHERE comment_type NOT IN ('rtcl_webhook_delivery')
					GROUP BY comment_approved
				", ARRAY_A
                );

                $approved = array(
                    '0'            => 'moderated',
                    '1'            => 'approved',
                    'spam'         => 'spam',
                    'trash'        => 'trash',
                    'post-trashed' => 'post-trashed',
                );

                foreach ((array)$count as $row) {
                    // Don't count post-trashed toward totals.
                    if (!in_array($row['comment_approved'], array('post-trashed', 'trash', 'spam'), true)) {
                        $stats['all'] += $row['num_comments'];
                        $stats['total_comments'] += $row['num_comments'];
                    } elseif (!in_array($row['comment_approved'], array('post-trashed', 'trash'), true)) {
                        $stats['total_comments'] += $row['num_comments'];
                    }
                    if (isset($approved[$row['comment_approved']])) {
                        $stats[$approved[$row['comment_approved']]] = $row['num_comments'];
                    }
                }

                foreach ($approved as $key) {
                    if (empty($stats[$key])) {
                        $stats[$key] = 0;
                    }
                }

                $stats = (object)$stats;
                set_transient('rtcl_count_comments', $stats);
            }
        }

        return $stats;
    }

    /**
     * Make sure WP displays avatars for comments with the `review` type.
     *
     * @param array $comment_types Comment types.
     *
     * @return array
     * @since  2.3
     *
     */
    public static function add_avatar_for_review_comment_type($comment_types) {
        return array_merge($comment_types, array('review'));
    }


    /**
     * Get listing rating count for a product. Please note this is not cached.
     *
     * @param Listing $listing Product instance.
     *
     * @return int[]
     * @since 1.0.0
     *
     */
    public static function get_rating_counts_for_listing(&$listing) {
        global $wpdb;

        $counts = array();
        $raw_counts = $wpdb->get_results(
            $wpdb->prepare(
                "
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
		", $listing->get_id()
            )
        );

        foreach ($raw_counts as $count) {
            $counts[$count->meta_value] = absint($count->meta_value_count); // WPCS: slow query ok.
        }

        $listing->set_rating_counts($counts);

        $data_store = $listing->get_data_store();
        $data_store->update_rating_counts($listing);

        return $counts;
    }


    /**
     * Get listing rating for a product. Please note this is not cached.
     *
     * @param Listing $listing Product instance.
     *
     * @return float
     * @since 1.0.0
     *
     */
    public static function get_average_rating_for_listing(&$listing) {
        global $wpdb;

        $count = $listing->get_rating_count();

        if ($count) {
            $ratings = $wpdb->get_var(
                $wpdb->prepare(
                    "
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
			", $listing->get_id()
                )
            );
            $average = number_format($ratings / $count, 2, '.', '');
        } else {
            $average = 0;
        }

        $listing->set_average_rating($average);

        $data_store = $listing->get_data_store();
        $data_store->update_average_rating($listing);

        return $average;
    }


    /**
     * Get listing review count for a liasting (not replies). Please note this is not cached.
     *
     * @param Listing $listing Listing instance.
     *
     * @return int
     * @since 1.0.0
     *
     */
    public static function get_review_count_for_listing(&$listing) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
			SELECT COUNT(*) FROM $wpdb->comments
			WHERE comment_parent = 0
			AND comment_post_ID = %d
			AND comment_approved = '1'
		", $listing->get_id()
            )
        );

        $listing->set_review_count($count);

        $data_store = $listing->get_data_store();
        $data_store->update_review_count($listing);

        return $count;
    }

}
