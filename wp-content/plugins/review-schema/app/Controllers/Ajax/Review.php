<?php

namespace Rtrs\Controllers\Ajax;

use Rtrs\Helpers\Functions;
use Rtrs\Models\Review as ReviewList;

class Review {
	public function __construct() {
		add_action('wp_ajax_rtrs_review_edit_form', [$this, 'rtrs_review_edit_form']);
		add_action('wp_ajax_nopriv_rtrs_review_edit_form', [$this, 'rtrs_review_edit_form']);

		add_action('wp_ajax_rtrs_self_video_popup', [$this, 'rtrs_self_video_popup']);
		add_action('wp_ajax_nopriv_rtrs_self_video_popup', [$this, 'rtrs_self_video_popup']);

		add_action('wp_ajax_rtrs_review_edit', [$this, 'rtrs_review_edit']);

		add_action('wp_ajax_rtrs_review_filter', [$this, 'rtrs_review_filter']);
		add_action('wp_ajax_nopriv_rtrs_review_filter', [$this, 'rtrs_review_filter']);

		add_action('wp_ajax_rtrs_pagination', [$this, 'rtrs_pagination']);
		add_action('wp_ajax_nopriv_rtrs_pagination', [$this, 'rtrs_pagination']);

		add_action('wp_ajax_rtrs_image_upload', [$this, 'rtrs_image_upload']);
		add_action('wp_ajax_nopriv_rtrs_image_upload', [$this, 'rtrs_image_upload']);

		add_action('wp_ajax_rtrs_video_upload', [$this, 'rtrs_video_upload']);
		add_action('wp_ajax_nopriv_rtrs_video_upload', [$this, 'rtrs_video_upload']);

		add_action('wp_ajax_rtrs_remove_file', [$this, 'rtrs_remove_file']);
		add_action('wp_ajax_nopriv_rtrs_remove_file', [$this, 'rtrs_remove_file']);

		add_action('wp_ajax_rtrs_review_hightlight', [$this, 'rtrs_review_hightlight']);
		add_action('wp_ajax_rtrs_review_helpful', [$this, 'rtrs_review_helpful']);

		add_action('wp_ajax_rtrs_auto_fill_schema', [$this, 'rtrs_auto_fill_schema']);
	}

	/**
	 * Get reviews by meta.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_reviews($sort_by, $filter_by) {
		// $per_page = get_option('comments_per_page') == 0 ? 5 : get_option('comments_per_page');
		$per_page = get_option('comments_per_page');
		$cur_page = isset($_REQUEST['current_page']) ? absint($_REQUEST['current_page']) : 1;
		$post_id  = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : null;
		$cur_page = isset($_REQUEST['pagi_num']) ? ($cur_page - 1) : $cur_page;
		$offset   = $cur_page * $per_page;

		$args = [
			'number'  => $per_page,
			'post_id' => $post_id,
		];

		if (isset($_REQUEST['pagi_num'])) {
			//TODO: check it later
			//if ( !$sort_by ) {
			$args['offset'] = $offset;
		}

		switch ($sort_by) {
			case 'top_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';

				break;

			case 'low_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';

				break;

			case 'recommended':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'rt_recommended',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'highlighted':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'rt_highlight',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'oldest_first':

				$args['order'] = 'ASC';

				break;
		}

		if ($filter_by) {
			$filter_by_value = [1, 5];
			switch ($filter_by) {
				case '5': $filter_by_value = [4.01, 5]; break;
				case '4': $filter_by_value = [3.01, 4]; break;
				case '3': $filter_by_value = [2.01, 3]; break;
				case '2': $filter_by_value = [1.01, 2]; break;
				case '1': $filter_by_value = [1, 1.99]; break;
			}

			$args['meta_query'] = [
				[
					'key'     => 'rating',
					'value'   => $filter_by_value,
					'compare' => 'BETWEEN',
				],
			];
		}

		$comments = get_comments($args);
		//pass post id in comment list
		global $rt_post_id;
		$rt_post_id = $post_id;

		wp_list_comments([
			'style'      => 'li',
			'short_ping' => true,
			'callback'   => [ReviewList::class, 'comment_list'],
		], $comments);
	}

	/**
	 * Get review pagination.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function paginate_comments_links($cur_page, $max_page, $args = []) {
		if (get_option('comments_per_page') >= $max_page) {
			return;
		}

		global $wp_rewrite;
		$args    = ['prev_text' => '<i class="rtrs-angle-left"></i>', 'next_text' => '<i class="rtrs-angle-right"></i>'];
		$post_id = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : null;

		$defaults = [
			'base'         => add_query_arg('cpage', '%#%'),
			'format'       => '',
			'total'        => $max_page,
			'current'      => $cur_page,
			'echo'         => true,
			'type'         => 'plain',
			'add_fragment' => '#comments',
		];

		if ($wp_rewrite->using_permalinks()) {
			$defaults['base'] = user_trailingslashit(trailingslashit(get_permalink($post_id)) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged');
		}

		$args       = wp_parse_args($args, $defaults);
		$page_links = paginate_links($args);

		return $page_links;
	}

	/**
	 * Get reviews by meta.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_total_reviews($sort_by, $filter_by) {
		$post_id = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : null;

		$args = [
			'post_id' => $post_id,
			'count'   => true,
		];

		switch ($sort_by) {
			case 'top_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';

				break;

			case 'low_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';

				break;

			case 'recommended':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'rt_recommended',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'highlighted':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'rt_highlight',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'oldest_first':

				$args['order'] = 'ASC';

				break;
		}

		if ($filter_by) {
			$filter_by_value = [1, 5];
			switch ($filter_by) {
				case '5': $filter_by_value = [4.01, 5]; break;
				case '4': $filter_by_value = [3.01, 4]; break;
				case '3': $filter_by_value = [2.01, 3]; break;
				case '2': $filter_by_value = [1.01, 2]; break;
				case '1': $filter_by_value = [1, 1.99]; break;
			}

			$args['meta_query'] = [
				[
					'key'     => 'rating',
					'value'   => $filter_by_value,
					'compare' => 'BETWEEN',
				],
			];
		}

		return get_comments($args);
	}

	/**
	 * Review filter ajax function.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_review_filter() {
		$sort_by   = isset($_REQUEST['sort_by']) ? sanitize_text_field($_REQUEST['sort_by']) : '';
		$filter_by = isset($_REQUEST['filter_by']) ? sanitize_text_field($_REQUEST['filter_by']) : '';
		$cur_page  = isset($_REQUEST['current_page']) ? absint($_REQUEST['current_page']) : 1;
		// $max_page = isset( $_REQUEST['max_page'] ) ? absint( $_REQUEST['max_page'] ) : 3;
		$max_page = $this->get_total_reviews($sort_by, $filter_by);

		ob_start();
		$this->get_reviews($sort_by, $filter_by);
		$review = ob_get_clean();

		$pagination = $this->paginate_comments_links($cur_page, $max_page);
		wp_send_json_success(['review' => $review, 'pagination' => $pagination, 'sort_by' => $sort_by]);
	}

	/**
	 * Review paginaiton ajax.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_pagination() {
		$sort_by   = isset($_REQUEST['sort_by']) ? sanitize_text_field($_REQUEST['sort_by']) : '';
		$filter_by = isset($_REQUEST['filter_by']) ? sanitize_text_field($_REQUEST['filter_by']) : '';
		$cur_page  = isset($_REQUEST['current_page']) ? absint($_REQUEST['current_page']) : 1;
		if ($sort_by) {
			$max_page = $this->get_total_reviews($sort_by, $filter_by);
		} else {
			$max_page = isset($_REQUEST['max_page']) ? absint($_REQUEST['max_page']) : 1;
		}

		ob_start();
		$this->get_reviews($sort_by, $filter_by);
		$review = ob_get_clean();

		$pagination = $this->paginate_comments_links($cur_page, $max_page);

		wp_send_json_success(['review' => $review, 'pagination' => $pagination]);
	}

	/**
	 * Review form upload image.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_image_upload() {
		if (! wp_verify_nonce($_POST['nonce'], rtrs()->getNonceId())) {
			die('Not allowed!');
		}

		$img_max_size = rtrs()->get_options('rtrs_media_settings', ['img_max_size', 1024]);

		$file               = $_FILES['rtrs-image'];
		$allowed_file_types = rtrs()->get_options('rtrs_media_settings', ['img_type', ['image/jpg', 'image/jpeg', 'image/png']]);
		// Allowed file size -> 2MB
		$allowed_file_size = $img_max_size * 1024;

		if (! empty($file['name'])) {
			// Check file type
			if (! in_array($file['type'], $allowed_file_types)) {
				$valid_file_type = str_replace('image/', '', implode(', ', $allowed_file_types));
				$error_file_type = str_replace('image/', '', $file['type']);

				wp_send_json_error(['msg' => sprintf(esc_html__('Invalid file type: %s. Supported file types: %s', 'review-schema'), $error_file_type, $valid_file_type)]);
			}

			// Check file size
			if ($file['size'] > $allowed_file_size) {
				wp_send_json_error(['msg' => sprintf(esc_html__('File is too large. Max. upload file size is %s', 'review-schema'), Functions::format_bytes($allowed_file_size))]);
			}

			if (! function_exists('wp_handle_upload')) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$upload_overrides = ['test_form' => false];
			$uploaded         = wp_handle_upload($file, $upload_overrides);

			if ($uploaded && ! isset($uploaded['error'])) {
				$filename = $uploaded['file'];
				$filetype = wp_check_filetype(basename($filename), null);

				$attach_id = wp_insert_attachment(
					[
						'guid'            => $uploaded['url'],
						'post_title'      => sanitize_text_field(preg_replace('/\.[^.]+$/', '', basename($filename))),
						'post_excerpt'    => '',
						'post_content'    => '',
						'post_mime_type'  => sanitize_text_field($filetype['type']),
						'post_status'     => 'reivew-inherit',
						'comments_status' => 'closed',
					],
					$uploaded['file'],
					0
				);

				$file_info = [];
				if (! is_wp_error($attach_id)) {
					wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
					update_post_meta($attach_id, 'attach_type', 'review');

					$file_info = [
						'id'  => $attach_id,
						'url' => wp_get_attachment_image_url($attach_id, 'thumbnail'),
					];
				}

				wp_send_json_success(['file_info' => $file_info]);
			} else {
				/*
				 * Error generated by _wp_handle_upload()
				 * @see _wp_handle_upload() in wp-admin/includes/file.php
				 */
				wp_send_json_error(['msg' => $uploaded['error']]);
			}
		}
	}

	/**
	 * Review form upload video.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_video_upload() {
		if (! wp_verify_nonce($_POST['nonce'], rtrs()->getNonceId())) {
			die('Not allowed!');
		}

		$file               = $_FILES['rtrs-video'];
		$allowed_file_types = rtrs()->get_options('rtrs_media_settings', ['video_type', ['video/mp4', 'video/mov', 'video/avi']]);

		$video_max_size    = rtrs()->get_options('rtrs_media_settings', ['video_max_size', 2048]);
		$allowed_file_size = $video_max_size * 1024;

		if (! empty($file['name'])) {
			// Check file type
			if (! in_array($file['type'], $allowed_file_types)) {
				$valid_file_type = str_replace('video/', '', implode(', ', $allowed_file_types));
				$error_file_type = str_replace('video/', '', $file['type']);

				wp_send_json_error(['msg' => sprintf(esc_html__('Invalid file type: %s. Supported file types: %s', 'review-schema'), $error_file_type, $valid_file_type)]);
			}

			// Check file size
			if ($file['size'] > $allowed_file_size) {
				wp_send_json_error(['msg' => sprintf(esc_html__('File is too large. Max. upload file size is %s', 'review-schema'), Functions::format_bytes($allowed_file_size))]);
			}

			if (! function_exists('wp_handle_upload')) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$upload_overrides = ['test_form' => false];
			$uploaded         = wp_handle_upload($file, $upload_overrides);

			if ($uploaded && ! isset($uploaded['error'])) {
				$filename = $uploaded['file'];
				$filetype = wp_check_filetype(basename($filename), null);

				//Todo: think about sanitization here
				$attach_id = wp_insert_attachment(
					[
						'guid'           => $uploaded['url'],
						'post_title'     => sanitize_text_field(preg_replace('/\.[^.]+$/', '', basename($filename))),
						'post_excerpt'   => '',
						'post_content'   => '',
						'post_mime_type' => sanitize_text_field($filetype['type']),
						'post_status'    => 'inherit',
					],
					$uploaded['file'],
					0
				);

				$file_info = [];
				if (! is_wp_error($attach_id)) {
					$file_info = [
						'id'   => $attach_id,
						'name' => preg_replace('/\.[^.]+$/', '', basename($filename)),
					];
				}

				wp_send_json_success(['file_info' => $file_info]);
			} else {
				/*
				 * Error generated by _wp_handle_upload()
				 * @see _wp_handle_upload() in wp-admin/includes/file.php
				 */
				wp_send_json_error(['msg' => $uploaded['error']]);
			}
		}
	}

	/**
	 * Review form remove file (image, video).
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_remove_file() {
		$attachment_id = isset($_REQUEST['attachment_id']) ? absint($_REQUEST['attachment_id']) : '';

		if (! current_user_can('delete_post', $attachment_id)) {
			return;
		}

		$deleted = wp_delete_attachment($attachment_id);
		if ($deleted) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Review highlight.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_review_hightlight() {
		if (current_user_can('administrator')) {
			$comment_id = isset($_REQUEST['comment_id']) ? absint($_REQUEST['comment_id']) : null;

			$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] == 'highlight') ? 1 : 0;
			if ($comment_id) {
				update_comment_meta($comment_id, 'rt_highlight', $highlight);
			}
		}
		wp_send_json_success();
	}

	/**
	 * Review like dislike.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_review_helpful() {
		if (! wp_verify_nonce($_POST['nonce'], rtrs()->getNonceId())) {
			die('Not allowed!');
		}

		if (is_user_logged_in()) {
			$comment_id   = isset($_REQUEST['comment_id']) ? absint($_REQUEST['comment_id']) : null;
			$helpful      = (isset($_REQUEST['helpful']) && $_REQUEST['helpful'] == 'helpful') ? 1 : 0;
			$helpful_type = (isset($_REQUEST['type']) && $_REQUEST['type'] == 'like') ? 'like' : 'dislike';

			if ($comment_id) {
				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;

				$old_helpful = get_comment_meta($comment_id, 'rt_helpful_' . $helpful_type, true);
				$old_helpful = isset($old_helpful) ? $old_helpful : '';
				if ($old_helpful) {
					if (! in_array($user_id, $old_helpful)) {
						$old_helpful[] = $user_id;
						update_comment_meta($comment_id, 'rt_helpful_' . $helpful_type, $old_helpful);
					} else {
						// remove like
						if (! $helpful) {
							if (($key = array_search($user_id, $old_helpful)) !== false) {
								unset($old_helpful[$key]);
							}
						}
						update_comment_meta($comment_id, 'rt_helpful_' . $helpful_type, $old_helpful);
					}
				} else {
					$new_helpful   = [];
					$new_helpful[] = $user_id;
					update_comment_meta($comment_id, 'rt_helpful_' . $helpful_type, $new_helpful);
				}

				//decrement
				$decrement_type = ($helpful_type == 'like') ? 'dislike' : 'like';
				$decrement      = get_comment_meta($comment_id, 'rt_helpful_' . $decrement_type, true);
				$decrement      = isset($decrement) ? $decrement : '';
				if ($decrement) {
					if (in_array($user_id, $decrement)) {
						if (($key = array_search($user_id, $decrement)) !== false) {
							unset($decrement[$key]);
						}
						update_comment_meta($comment_id, 'rt_helpful_' . $decrement_type, $decrement);
					}
				}
			}
		}
		wp_send_json_success();
	}

	/**
	 * Review edit form.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_review_edit_form() {
		$comment_post_id = isset($_REQUEST['comment_post_id']) ? absint($_REQUEST['comment_post_id']) : null;
		$comment_id      = isset($_REQUEST['comment_id']) ? absint($_REQUEST['comment_id']) : null;
		$comment_data = get_comment($comment_id);
		$review_edit = rtrs()->get_options('rtrs_review_settings', array( 'review_edit', 'yes' ));
		if ( $review_edit != 'yes' || $comment_data->user_id != get_current_user_id() ) {
			wp_send_json_error(esc_html__('Sorry! You do not have permission.', 'review-schema'));
		}
		// review edit global settings
		$review_edit_field = rtrs()->get_options('rtrs_review_settings', ['review_edit_field', ['rating', 'desc']]);

		ob_start();
		$post_type = get_post_type($comment_post_id);
		if (! Functions::isEnableByPostType($post_type)) {
			return;
		}

		$p_meta = Functions::getMetaByPostType($post_type);
		if (! $p_meta) {
			return;
		} // get back if not added

		$criteria       = (isset($p_meta['criteria']) && $p_meta['criteria'][0] == 'multi');
		$multi_criteria = isset($p_meta['multi_criteria']) ? unserialize($p_meta['multi_criteria'][0]) : null;

		do_action('rtrs_before_review_edit_form');

		echo '<div class="rtrs-modal">';
		echo '<div class="rtrs-review-form rtrs-review-popup">';
		echo '<h2 id="reply-title" class="rtrs-form-title">' . esc_html__('Edit your review', 'review-schema') . '</h2>';
		echo '<form action="#" method="post" class="rtrs-form-box">';
		$criteria_style = null;
		if ($criteria && $multi_criteria) {
			// add css for odd
			if (count($multi_criteria) % 2 != 0) {
				$criteria_style = 'grid-template-columns: repeat(1, 270px);';
			}
		}

		if (! $comment_data->comment_parent) {
			echo '<div class="rtrs-form-group rtrs-hide-reply"><ul class="rtrs-rating-category" style="' . esc_attr($criteria_style) . '">';

			if ($criteria && $multi_criteria) {
				$criteria_count = 1;
				foreach ($multi_criteria as $key => $value) :
					$slug = 'rt_rating_' . Functions::slugify($value); ?> 
                <li>
                    <div class="rtrs-category-text"><?php echo esc_html($value); ?></div> 
                    <div class="rtrs-rating-container">
                        <?php
							$rt_rating_criteria = get_comment_meta($comment_id, 'rt_rating_criteria', true);
				//if enable non criteria to criteria
				if (! $rt_rating_criteria) {
					$rating             = get_comment_meta($comment_id, 'rating', true);
					$rating_only[]      = $rating;
					$rt_rating_criteria = $rating_only;
				}
				for ($i = 5; $i >= 1; $i--) :

							$checked = isset($rt_rating_criteria[$criteria_count - 1]) && ($rt_rating_criteria[$criteria_count - 1] == $i) ? 'checked' : ''; ?>
                            <input <?php echo esc_attr($checked); ?> type="radio" id="<?php echo esc_attr($criteria_count); ?>-rating-<?php echo esc_attr($i); ?>" name="<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($i); ?>" /><label for="<?php echo esc_attr($criteria_count); ?>-rating-<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></label>
                        <?php endfor; ?> 
                    </div> 
                </li> 
                <?php $criteria_count++;
				endforeach;
			} else { ?> 
                <li>
                    <div class="rtrs-category-text"><?php esc_html_e('Rating', 'review-schema'); ?></div> 
                    <div class="rtrs-rating-container">
                        <?php
						$rt_rating = get_comment_meta($comment_id, 'rating', true);
						for ($i = 5; $i >= 1; $i--) :
						$checked = isset($rt_rating) && ($rt_rating == $i) ? 'checked' : '';
						?>
                            <input <?php echo esc_attr($checked); ?> type="radio" id="rt-rating-<?php echo esc_attr($i); ?>" name="rt_rating" value="<?php echo esc_attr($i); ?>" /><label for="rt-rating-<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></label>
                        <?php endfor; ?> 
                    </div> 
                </li> 
                <?php
			}
			echo '</ul></div>';
		}

		$pros_cons = (isset($p_meta['pros_cons']) && $p_meta['pros_cons'][0] == '1');
		if ($pros_cons && in_array('pros_cons', $review_edit_field)) { ?>
        <div class="rtrs-form-group rtrs-hide-reply">
            <div class="rtrs-feedback-input"> 
                <div class="rtrs-input-item rtrs-pros">
                    <h3 class="rtrs-input-title">
                        <span class="item-icon"><i class="rtrs-thumbs-up"></i></span>
                        <span class="item-text"><?php esc_html_e('PROS', 'review-schema'); ?></span>
                    </h3>
                    <?php
						$pros_cons = get_comment_meta($comment_id, 'rt_pros_cons', true);
						if (isset($pros_cons['pros'])) {
							foreach ($pros_cons['pros'] as $key => $value) { ?>
                                <div class="rtrs-input-filed">
                                    <span class="rtrs-remove-btn">+</span>
                                    <input type="text" value="<?php echo esc_attr($value); ?>" class="form-control" name="rt_pros[]" placeholder="<?php esc_attr_e('Write here!', 'review-schema'); ?>">
                                </div>
                            <?php
							}
						} else { ?>
                            <div class="rtrs-input-filed">
                                <span class="rtrs-remove-btn">+</span>
                                <input type="text" class="form-control" name="rt_pros[]" placeholder="<?php esc_attr_e('Write here!', 'review-schema'); ?>">
                            </div>
                    <?php } ?> 
                    <div class="rtrs-field-add"><i class="rtrs-plus"></i><?php esc_html_e('Add Field', 'review-schema'); ?></div>
                </div>

                <div class="rtrs-input-item rtrs-cons">
                    <h3 class="rtrs-input-title">
                        <span class="item-icon unlike-icon"><i class="rtrs-thumbs-down"></i></span>
                        <span class="item-text"><?php esc_html_e('CONS', 'review-schema'); ?></span>
                    </h3>
                    <?php
						$pros_cons = get_comment_meta($comment_id, 'rt_pros_cons', true);
						if (isset($pros_cons['cons'])) {
							foreach ($pros_cons['cons'] as $key => $value) { ?>
                                <div class="rtrs-input-filed">
                                    <span class="rtrs-remove-btn">+</span>
                                    <input type="text" value="<?php echo esc_attr($value); ?>" class="form-control" name="rt_cons[]" placeholder="<?php esc_attr_e('Write here!', 'review-schema'); ?>">
                                </div>
                            <?php
							}
						} else { ?>
                            <div class="rtrs-input-filed">
                                <span class="rtrs-remove-btn">+</span>
                                <input type="text" class="form-control" name="rt_cons[]" placeholder="<?php esc_attr_e('Write here!', 'review-schema'); ?>">
                            </div>
                    <?php } ?> 
                    
                    <div class="rtrs-field-add"><i class="rtrs-plus"></i><?php esc_html_e('Add Field', 'review-schema'); ?></div>
                </div>
            </div>
        </div> 
        <?php }

		$image_review = (isset($p_meta['image_review']) && $p_meta['image_review'][0] == '1');
		if ($image_review && in_array('image', $review_edit_field)) { ?>
        <div class="rtrs-form-group rtrs-hide-reply">
            <div class="rtrs-preview-imgs"></div>
        </div> 
        
        <div class="rtrs-form-group rtrs-media-form-group rtrs-hide-reply">             

            <div>
                <label class="rtrs-input-image-label"><?php esc_html_e('Upload Image', 'review-schema'); ?></label> 
            </div>

            <div>
                <div class="rtrs-multimedia-upload" >
                    <div class="rtrs-upload-box" id="rtrs-upload-box-image"> 
                        <span><?php esc_html_e('Upload Image', 'review-schema'); ?></span>
                    </div> 
                </div>
                <input type="file" id="rtrs-image" accept="image/*" style="display:none">
                <div class="rtrs-image-error"></div>
            </div>
        </div> 
        <?php }

		$video_review = (isset($p_meta['video_review']) && $p_meta['video_review'][0] == '1');
		if ($video_review && in_array('video', $review_edit_field)) { ?>
        <div class="rtrs-form-group rtrs-hide-reply">
            <div class="rtrs-preview-videos"></div>
        </div> 

        <div class="rtrs-form-group rtrs-media-form-group rtrs-hide-reply">
            <div>
                <label class="rtrs-input-video-label"><?php esc_html_e('Upload Video', 'review-schema'); ?></label> 
            </div>

            <div>
                <select name="rt_video_source" id="rtrs-video-source" class="rtrs-form-control">
                    <option value="self"><?php esc_html_e('Hosted Video', 'review-schema'); ?></option>
                    <option value="external"><?php esc_html_e('External Video', 'review-schema'); ?></option>
                </select> 
            </div>

            <div class="rtrs-source-video"> 
                <div class="rtrs-multimedia-upload">
                    <div class="rtrs-upload-box" id="rtrs-upload-box-video"> 
                        <span><?php esc_html_e('Upload a Video', 'review-schema'); ?></span>
                    </div>
                </div>
                <input type="file" id="rtrs-video" accept="video/*" style="display:none">
                <div class="rtrs-video-error"></div>
            </div>
        </div>  

        <div class="rtrs-form-group rtrs-source-external rtrs-hide-reply">
            <label class="rtrs-input-label" for="rt_external_video"><?php esc_html_e('External Video Link', 'review-schema'); ?></label> 
            <input id="rt_external_video" class="rtrs-form-control" placeholder="<?php esc_attr_e('https://www.youtube.com/watch?v=668nUCeBHyY', 'review-schema'); ?>" name="rt_external_video" type="text">
        </div> 
        <?php } //video_review

		$recommendation = (isset($p_meta['recommendation']) && $p_meta['recommendation'][0] == '1');
		if ($recommendation && in_array('recommendation', $review_edit_field)) { ?>
        <div class="rtrs-form-group rtrs-hide-reply">
            <label class="rtrs-input-label"><?php esc_html_e('Recommendation', 'review-schema'); ?></label>
            <div class="rtrs-recomnd-check">
                <div class="rtrs-form-check rtrs-tooltip">
                    <input type="radio" <?php if (get_comment_meta($comment_id, 'rt_recommended', true)) {
			echo 'checked';
		} ?> class="rtrs-form-checkbox" name="rt_recommended" value="1">
                    <label class="rtrs-checkbox-label check-excelent"></label>
                    <span class="rtrs-tooltiptext"><?php esc_html_e('Happy', 'review-schema'); ?></span>
                </div>

                <div class="rtrs-form-check rtrs-tooltip">
                    <input type="radio" <?php if (get_comment_meta($comment_id, 'rt_recommended', true) == -1) {
			echo 'checked';
		} ?> class="rtrs-form-checkbox" name="rt_recommended" value="-1">
                    <label class="rtrs-checkbox-label check-good"></label>
                    <span class="rtrs-tooltiptext"><?php esc_html_e('Sad', 'review-schema'); ?></span>
                </div>
                <div class="rtrs-form-check rtrs-tooltip">
                    <input type="radio" <?php if (get_comment_meta($comment_id, 'rt_recommended', true) == 0) {
			echo 'checked';
		} ?> class="rtrs-form-checkbox" name="rt_recommended" value="0">
                    <label class="rtrs-checkbox-label check-bad"></label>
                    <span class="rtrs-tooltiptext"><?php esc_html_e('Nothing', 'review-schema'); ?></span>
                </div>
            </div>
        </div> 
        <?php }

		$anonymous_review = (isset($p_meta['anonymous_review']) && $p_meta['anonymous_review'][0] == '1');
		if ($anonymous_review && in_array('anonymous', $review_edit_field)) { ?> 
        <div class="rtrs-form-group rtrs-hide-reply">
            <div class="rtrs-form-check">
                
                <input type="checkbox" <?php if (get_comment_meta($comment_id, 'rt_anonymous', true)) {
			echo esc_attr('checked');
		} ?> class="rtrs-form-checkbox" name="rt_anonymous" id="rtrs-anonymous">
                <label for="rtrs-anonymous" class="rtrs-checkbox-label"><?php esc_html_e('Review anonymously', 'review-schema'); ?></label>
            </div>
        </div> 
        <?php } ?>
        
        <?php if (in_array('title', $review_edit_field)) { ?>
        <div class="rtrs-form-group rtrs-hide-reply">
            <input id="rt_title" class="rtrs-form-control" placeholder="<?php esc_attr_e('Title', 'review-schema'); ?>" name="rt_title" value="<?php echo esc_attr(get_comment_meta($comment_id, 'rt_title', true)); ?>" type="text" value="" size="30" aria-required="true">
        </div>
        <?php } ?>
        
        <?php if (in_array('desc', $review_edit_field)) { ?>
        <div class="rtrs-form-group">
            <textarea id="message" class="rtrs-form-control" placeholder="<?php esc_attr_e('Write your review', 'review-schema'); ?>" name="comment" aria-required="true" rows="6" cols="45"><?php
			$comment = get_comment(intval($comment_id));
			echo wp_kses_post($comment->comment_content); ?></textarea>
        </div>
        <?php } ?>

        <div class="rtrs-form-group">
            <input name="submit" type="submit" id="submit" class="rtrs-submit-btn rtrs-review-edit-submit" value="<?php esc_attr_e('Submit Review', 'review-schema'); ?>"> 
            <input type="hidden" name="action" value="rtrs_review_edit">
            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($comment_post_id); ?>" id="comment_post_ID">
            <input type="hidden" name="comment_ID" value="<?php echo esc_attr($comment_id); ?>" id="comment_ID">
            <input type="hidden" name="comment_parent" id="comment_parent" value="0">
        </div>

        <?php
		echo '</form>';
		echo '</div>';
		echo '</div>'; //modal
		do_action('rtrs_after_review_edit_form');

		$edit_form = ob_get_clean();
		wp_send_json_success($edit_form);
	}

	/**
	 * Review edit.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_review_edit() {
		$comment_id = absint($_POST['comment_ID']);

		// if ( !current_user_can( 'edit_comment', $comment_id ) ) return;

		//not isset means not enable criteria
		if (! isset($_POST['rt_rating'])) {
			$post_id = absint($_POST['comment_post_ID']);

			$p_meta         = Functions::getMetaByPostType(get_post_type($post_id));
			$multi_criteria = isset($p_meta['multi_criteria']) ? unserialize($p_meta['multi_criteria'][0]) : null;

			if ($multi_criteria) {
				$i               = $total               = $avg_rating               = 0;
				$criteria_rating = [];
				foreach ($multi_criteria as $key => $value) {
					$slug = 'rt_rating_' . Functions::slugify($value);
					if (isset($_POST[$slug]) && ('' !== $_POST[$slug])) {
						$rating = absint($_POST[$slug]);
						$i++;
						$total += $rating;
						$criteria_rating[] = $rating;
					}
				}
				update_comment_meta($comment_id, 'rt_rating_criteria', array_map('absint', $criteria_rating));

				// add avg rating
				if (0 === $i) {
					$avg_rating = 0;
				} else {
					$avg_rating = round($total / $i, 1);
				}

				if ($avg_rating) {
					update_comment_meta($comment_id, 'rating', abs($avg_rating));
				}
			}
		} else {
			update_comment_meta($comment_id, 'rating', absint($_POST['rt_rating']));
		}

		// add title
		if (isset($_POST['rt_title']) && ('' !== $_POST['rt_title'])) {
			update_comment_meta($comment_id, 'rt_title', sanitize_text_field($_POST['rt_title']));
		}

		// add highlight
		if (current_user_can('administrator')) {
			$highlight = isset($_POST['rt_highlight']) ? 1 : 0;
			update_comment_meta($comment_id, 'rt_highlight', $highlight);
		}

		// add recommendation
		if (isset($_POST['rt_recommended']) && ('' !== $_POST['rt_recommended'])) {
			update_comment_meta($comment_id, 'rt_recommended', intval($_POST['rt_recommended']));
		}

		// add pros & cons
		$rt_pros_cons = [];
		if (isset($_POST['rt_pros']) && ('' !== $_POST['rt_pros'])) {
			$rt_pros_cons['pros'] = array_map('sanitize_text_field', array_filter($_POST['rt_pros']));
		}
		if (isset($_POST['rt_cons']) && ('' !== $_POST['rt_cons'])) {
			$rt_pros_cons['cons'] = array_map('sanitize_text_field', array_filter($_POST['rt_cons']));
		}
		if (isset($_POST['rt_pros']) || isset($_POST['rt_cons'])) {
			update_comment_meta($comment_id, 'rt_pros_cons', $rt_pros_cons);
		}

		// add image & video
		$attachments = [];
		if (isset($_POST['rt_attachment']['imgs']) && ('' !== $_POST['rt_attachment']['imgs'])) {
			$attachments['imgs'] = array_map('absint', $_POST['rt_attachment']['imgs']);
		}

		if (isset($_POST['rt_video_source']) && $_POST['rt_video_source'] == 'self') {
			if (isset($_POST['rt_attachment']['videos']) && ('' !== $_POST['rt_attachment']['videos'])) {
				$attachments['videos']       = array_map('absint', $_POST['rt_attachment']['videos']);
				$attachments['video_source'] = 'self';
			}
		} elseif (isset($_POST['rt_video_source']) && $_POST['rt_video_source'] == 'external') {
			if (isset($_POST['rt_external_video']) && ('' !== $_POST['rt_external_video'])) {
				$attachments['videos']       = [esc_url($_POST['rt_external_video'])];
				$attachments['video_source'] = 'external';
			}
		}

		if (isset($attachments['imgs']) || isset($attachments['videos'])) {
			update_comment_meta($comment_id, 'rt_attachment', $attachments);
		}

		// add anonymous
		if (isset($_POST['rt_anonymous'])) {
			update_comment_meta($comment_id, 'rt_anonymous', 1);
		}

		if (isset($_POST['comment'])) {
			// comment data
			$commentarr = [
				'comment_ID'      => $comment_id,
				'comment_content' => $_POST['comment'],
			];
			// update data in the database
			wp_update_comment( $commentarr );
		}
		wp_send_json_success();
	}

	/**
	 * auto fill schema.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_auto_fill_schema() {
		$html       = null;
		$metaData   = [];
		$post_id    = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : null;
		$schema_cat = isset($_REQUEST['snippet_cat']) ? sanitize_text_field($_REQUEST['snippet_cat']) : null;

		$rich_snippet     = false;
		$rich_snippet_cat = null;

		if ($schema_cat == 'post') {
			$p_meta           = Functions::getMetaByPostType('post');
			$rich_snippet     = (isset($p_meta['rich_snippet']) && $p_meta['rich_snippet'][0] == '1');
			$rich_snippet_cat = isset($p_meta['rich_snippet_cat']) ? $p_meta['rich_snippet_cat'][0] : null;
			
			if ($rich_snippet && $rich_snippet_cat) {
				$schema_cat = $rich_snippet_cat;
			} 
			// else {
			// 	$schema_cat = 'article';
			// }
		}
		if ($schema_cat) {
			$helper = new Functions();
			$post   = get_post($post_id);

			$publisher = rtrs()->get_options('rtrs_schema_publisher_settings', 'publisher_name');
			if ($publisher) {
				$metaData['publisher'] = $publisher;
			}
			$publisher_logo = rtrs()->get_options('rtrs_schema_publisher_settings', 'publisher_logo');
			if ($publisher_logo) {
				$metaData['publisherImage'] = $publisher_logo;
			}

			switch ($schema_cat) {
				case 'article':
				case 'news_article':
				case 'blog_posting':
					$article = [];
					if (! empty($post->post_title)) {
						$article['headline'] = $helper->sanitizeOutPut($post->post_title);
					}
					if (! empty($post_url = get_the_permalink($post_id))) {
						$article['mainEntityOfPage'] = $helper->sanitizeOutPut($post_url);
					}
					if (! empty($author = get_the_author_meta('display_name', $post->post_author))) {
						$article['author'] = $helper->sanitizeOutPut($author);

						if (! empty($url = get_the_author_meta('url', $post->post_author))) {
							$article['author_url'] = $helper->sanitizeOutPut($url, 'url');
						}
					}
					if (! empty($metaData['publisher'])) {
						$article['publisher'] = $helper->sanitizeOutPut($metaData['publisher']);
					}
					if (! empty($metaData['publisherImage'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'id'     => $img['id'],
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							];
						} else {
							$plA = [];
						}
						$article['publisherImage'] = $plA;
					}
					if (! empty($image_id = get_post_thumbnail_id($post->ID))) {
						$img              = $helper->imageInfo(absint($image_id));
						$article['image'] = [
							'id'     => $img['id'],
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
						];
					}
					if (! empty($post->post_date)) {
						$article['datePublished'] = $helper->sanitizeOutPut($post->post_date);
					}
					if (! empty($post->post_modified)) {
						$article['dateModified'] = $helper->sanitizeOutPut($post->post_modified);
					}
					if (! empty($post->post_excerpt)) {
						$article['description'] = sanitize_text_field($helper->sanitizeOutPut(
							$post->post_excerpt,
							'textarea'
						));
					}

					if (! empty($post->post_content)) {
						$article['articleBody'] = Functions::filter_content($post->post_content, 500);
					}

					$html = $article;
					break;

				case 'product':
					$markup = [];

					//if edd
					$post_type = get_post_type($post_id);
					if ($post_type == 'download' && function_exists('EDD')) {
						$post      = get_post($post_id);
						$permalink = get_the_permalink($post->ID);
						$markup    = [
							'@context'    => 'https://schema.org',
							'@type'       => 'Product',
							'@id'         => $permalink . '#comments', // Append '#comments' to differentiate between this @id and the @id generated for the Breadcrumblist.
							'name'        => wp_kses_post($post->post_title),
							'url'         => $permalink,
							'description' => wp_strip_all_tags(do_shortcode($post->post_excerpt ? $post->post_excerpt : $post->post_content)),
						];

						$image = get_the_post_thumbnail_url($post->ID);
						if ($image) {
							$markup['image'] = [
								'id'  => get_post_thumbnail_id($post->ID),
								'url' => $image,
							];
						}

						$markup['sku'] = $post->ID;
						$brand_name    = rtrs()->get_options('rtrs_woocommerce_settings', 'brand_name');
						if ($brand_name) {
							$markup['brand'] = esc_html($brand_name);
						}

						$identifier_type = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier_type');
						$identifier      = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier');
						if ($identifier_type && $identifier) {
							$markup['identifier_type'] = esc_html($identifier_type);
							$markup['identifier']      = esc_html($identifier);
						}

						$price             = edd_get_download_price($post->ID, false);
						$price_valid_until = gmdate('Y-12-31', time() + YEAR_IN_SECONDS);
						$markup['offers']  = [
							'@type'             => 'Offer',
							'url'               => $permalink,
							'price'             => $price,
							'priceValidUntil'   => $price_valid_until,
							'priceCurrency'     => 'USD',
							'itemCondition'     => 'https://schema.org/NewCondition',
							'availability'      => 'https://schema.org/InStock',
						];

						$download_rating = true;
						if ($download_rating) {
							$total_rating = ReviewList::getTotalRatings($post->ID);
							if ($total_rating) {
								$markup['aggregateRating'] = [
									'@type'       => 'AggregateRating',
									'ratingValue' => ReviewList::getAvgRatings($post->ID),
									'reviewCount' => $total_rating,
								];
							}

							// Markup 5 most recent rating/review.
							$comments = get_comments(
								[
									'number'      => 5,
									'post_id'     => $post->ID,
									'status'      => 'approve',
									'post_status' => 'publish',
									'post_type'   => 'download',
									'parent'      => 0,
									'meta_query'  => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
										[
											'key'     => 'rating',
											'type'    => 'NUMERIC',
											'compare' => '>',
											'value'   => 0,
										],
									],
								]
							);

							if ($comments) {
								$markup['review'] = [];
								foreach ($comments as $comment) {
									$markup['review'][] = [
										'@type'         => 'Review',
										'reviewRating'  => [
											'@type'       => 'Rating',
											'bestRating'  => '5',
											'ratingValue' => get_comment_meta($comment->comment_ID, 'rating', true),
											'worstRating' => '1',
										],
										'author'        => [
											'@type' => 'Person',
											'name'  => get_comment_author($comment),
										],
										'reviewBody'    => get_comment_text($comment),
										'datePublished' => get_comment_date('c', $comment),
									];
								}
							}
						}
					}

					//if wc
					if ($post_type == 'product') {
						$the_query = new \WP_Query([
							'post_type'      => 'product',
							'posts_per_page' => 1,
							'p'              => $post_id,
						]);
						while ($the_query->have_posts()) {
							$the_query->the_post();
							global $product;
							if (is_a($product, 'WC_Product')) {
								$shop_name = get_bloginfo('name');
								$shop_url  = home_url();
								$currency  = get_woocommerce_currency();
								$permalink = get_permalink($product->get_id());

								$markup = [
									'@id'         => $permalink . '#product',
									'name'        => wp_kses_post($product->get_name()),
									'url'         => $permalink,
									'description' => sanitize_text_field($product->get_short_description() ? $product->get_short_description() : $product->get_description()),
								];

								if (! empty($image_id = $product->get_image_id())) {
									$img             = $helper->imageInfo(absint($image_id));
									$markup['image'] = [
										'id'  => $img['id'],
										'url' => $helper->sanitizeOutPut($img['url'], 'url'),
									];
								}

								// Declare SKU or fallback to ID.
								if ($product->get_sku()) {
									$markup['sku'] = $product->get_sku();
								} else {
									$markup['sku'] = $product->get_id();
								}

								$brand_name = rtrs()->get_options('rtrs_woocommerce_settings', 'brand_name');
								if ($brand_name) {
									$markup['brand'] = esc_html($brand_name);
								}

								$identifier_type = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier_type');
								$identifier      = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier');
								if ($identifier_type && $identifier) {
									$markup['identifier_type'] = esc_html($identifier_type);
									$markup['identifier']      = esc_html($identifier);
								}

								if ('' !== $product->get_price()) {
									// Assume prices will be valid until the end of next year, unless on sale and there is an end date.
									$price_valid_until = gmdate('Y-12-31', time() + YEAR_IN_SECONDS);

									if ($product->is_type('variable')) {
										$lowest  = $product->get_variation_price('min', false);
										$highest = $product->get_variation_price('max', false);

										if ($lowest === $highest) {
											$markup_offer = [
												'@type'              => 'Offer',
												'price'              => wc_format_decimal($lowest, wc_get_price_decimals()),
												'priceValidUntil'    => $price_valid_until,
												'priceSpecification' => [
													'price'                 => wc_format_decimal($lowest, wc_get_price_decimals()),
													'priceCurrency'         => $currency,
													'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
												],
											];
										} else {
											$markup_offer = [
												'@type'      => 'AggregateOffer',
												'lowPrice'   => wc_format_decimal($lowest, wc_get_price_decimals()),
												'highPrice'  => wc_format_decimal($highest, wc_get_price_decimals()),
												'offerCount' => count($product->get_children()),
											];
										}
									} else {
										if ($product->is_on_sale() && $product->get_date_on_sale_to()) {
											$price_valid_until = gmdate('Y-m-d', $product->get_date_on_sale_to()->getTimestamp());
										}
										$markup_offer = [
											'@type'              => 'Offer',
											'price'              => wc_format_decimal($product->get_price(), wc_get_price_decimals()),
											'priceValidUntil'    => $price_valid_until,
											'priceSpecification' => [
												'price'                 => wc_format_decimal($product->get_price(), wc_get_price_decimals()),
												'priceCurrency'         => $currency,
												'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
											],
										];
									}

									$markup_offer += [
										'priceCurrency' => $currency,
										'availability'  => 'https://schema.org/' . ($product->is_in_stock() ? 'InStock' : 'OutOfStock'),
										'url'           => $permalink,
										'seller'        => [
											'@type' => 'Organization',
											'name'  => $shop_name,
											'url'   => $shop_url,
										],
									];

									$markup['offers'] = [apply_filters('woocommerce_structured_data_product_offer', $markup_offer, $product)];
								}

								if ($product->get_rating_count() && wc_review_ratings_enabled()) {
									$markup['aggregateRating'] = [
										'@type'       => 'AggregateRating',
										'ratingValue' => $product->get_average_rating(),
										'reviewCount' => $product->get_review_count(),
									];

									// Markup 5 most recent rating/review.
									$comments = get_comments(
										[
											'number'      => 5,
											'post_id'     => $product->get_id(),
											'status'      => 'approve',
											'post_status' => 'publish',
											'post_type'   => 'product',
											'parent'      => 0,
											'meta_query'  => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
												[
													'key'     => 'rating',
													'type'    => 'NUMERIC',
													'compare' => '>',
													'value'   => 0,
												],
											],
										]
									);

									if ($comments) {
										$markup['review'] = [];
										foreach ($comments as $comment) {
											$markup['review'][] = [
												'@type'         => 'Review',
												'reviewRating'  => [
													'@type'       => 'Rating',
													'bestRating'  => '5',
													'ratingValue' => get_comment_meta($comment->comment_ID, 'rating', true),
													'worstRating' => '1',
												],
												'author'        => [
													'@type' => 'Person',
													'name'  => get_comment_author($comment),
												],
												'reviewBody'    => get_comment_text($comment),
												'datePublished' => get_comment_date('c', $comment),
											];
										}
									}
								}
							}
						}
						wp_reset_postdata();
					}

					$html = $markup;
					break;

				default:
				//TODO: try to merge with article, news_article etc

					$article = [];
					if (! empty($post->post_title)) {
						$article['headline'] = $helper->sanitizeOutPut($post->post_title);
					}
					if (! empty($post_url = get_the_permalink($post_id))) {
						$article['mainEntityOfPage'] = $helper->sanitizeOutPut($post_url);
					}
					if (! empty($author = get_the_author_meta('display_name', $post->post_author))) {
						$article['author'] = $helper->sanitizeOutPut($author);

						if (! empty($url = get_the_author_meta('url', $post->post_author))) {
							$article['author_url'] = $helper->sanitizeOutPut($url, 'url');
						}
					}
					if (! empty($metaData['publisher'])) {
						$article['publisher'] = $helper->sanitizeOutPut($metaData['publisher']);
					}
					if (! empty($metaData['publisherImage'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'id'     => $img['id'],
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							];
						} else {
							$plA = [];
						}
						$article['publisherImage'] = $plA;
					}
					if (! empty($image_id = get_post_thumbnail_id($post->ID))) {
						$img              = $helper->imageInfo(absint($image_id));
						$article['image'] = [
							'id'     => $img['id'],
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
						];
					}
					if (! empty($post->post_date)) {
						$article['datePublished'] = $helper->sanitizeOutPut($post->post_date);
					}
					if (! empty($post->post_modified)) {
						$article['dateModified'] = $helper->sanitizeOutPut($post->post_modified);
					}
					if (! empty($post->post_excerpt)) {
						$article['description'] = sanitize_text_field($helper->sanitizeOutPut(
							$post->post_excerpt,
							'textarea'
						));
					}

					if (! empty($post->post_content)) {
						$article['articleBody'] = Functions::filter_content($post->post_content, 500);
					}

					$html = $article;
					break;
			}
		}

		if ($rich_snippet && $rich_snippet_cat) {
			$html['category'] = $rich_snippet_cat;
		} else {
			if ($schema_cat == 'article') {
				$html['category'] = 'article';
			}

			$rtrs_schema_settings  = get_option('rtrs_schema_settings'); 
			$post_types  = isset($rtrs_schema_settings['post_type']) && is_array($rtrs_schema_settings['post_type']) ? $rtrs_schema_settings['post_type'] : Functions::default_setting_schema();
			foreach ($post_types as $key => $value) {
				if (isset($value['post_type']) && $value['post_type'] == $schema_cat) {
					if (isset($value['schema_type']) && $value['schema_type']) {
						$html['category'] = $value['schema_type'];
						break;
					}
				}
			}
		}
		wp_send_json_success($html);
	}

	/**
	 * Self Hosted Video Popup.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function rtrs_self_video_popup() {
		$video_url = isset($_REQUEST['video_url']) ? esc_url($_REQUEST['video_url']) : null;
		ob_start();

		do_action('rtrs_before_self_hosted_popup');

		echo '<div class="rtrs-modal">';
		echo '<div class="rtrs-review-form rtrs-review-popup">';
		echo '<div class="rtrs-self-video"><video src="' . $video_url . '"  autoplay controls /></div>';

		echo '</div>';
		echo '</div>'; //modal
		do_action('rtrs_after_self_hosted_popup');

		$edit_form = ob_get_clean();
		wp_send_json_success($edit_form);
	}
}
