<?php

namespace Rtrs\Helpers;

use Rtrs\Models\Field;

class Functions {
	public static function verify_nonce() {
		$nonce     = self::get_nonce();
		$nonceText = rtrs()->getNonceText();
		if (wp_verify_nonce($nonce, $nonceText)) {
			return true;
		}

		return false;
	}

	public static function get_nonce() {
		return isset($_REQUEST[rtrs()->getNonceId()]) ? sanitize_text_field($_REQUEST[rtrs()->getNonceId()]) : null;
	}

	public static function locate_template($name) {
		// Look within passed path within the theme - this is priority.
		$template = [];

		$template[] = rtrs()->get_template_path() . $name . '.php';

		if (! $template_file = locate_template(apply_filters('rtrs_locate_template_names', $template))) {
			$template_file = RTRS_PATH . "templates/$name.php";
		}

		return apply_filters('rtrs_locate_template', $template_file, $name);
	}

	/**
	 * Get template part (for templates like the shop-loop).
	 *
	 * RTRS_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
	 *
	 * @param mixed  $slug Template slug.
	 * @param string $name Template name (default: '').
	 */
	public static function get_template_part($slug, $args = null, $include = true) {
		// load template from theme if exist
		$template = RTRS_TEMPLATE_DEBUG_MODE ? '' : locate_template(
			[
				"{$slug}.php",
				rtrs()->get_template_path() . "{$slug}.php",
			]
		);

		// load template from pro plugin if exist
		if (! $template && function_exists('rtrsp')) {
			$fallback = rtrs()->plugin_path() . '-pro' . "/templates/{$slug}.php";
			$template = file_exists($fallback) ? $fallback : '';
		}

		// load template from current plugin if exist
		if (! $template) {
			$fallback = rtrs()->plugin_path() . "/templates/{$slug}.php";
			$template = file_exists($fallback) ? $fallback : '';
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters('rtrs_get_template_part', $template, $slug);

		if ($template) {
			if (! empty($args) && is_array($args)) {
				extract($args); // @codingStandardsIgnoreLine
			}

			// load_template($template, false, $args);
			if ($include) {
				include $template;
			} else {
				return $template;
			}
		}
	}

	public static function doing_it_wrong($function, $message, $version) {
		// @codingStandardsIgnoreStart
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();
		_doing_it_wrong($function, $message, $version);
	}

	public static function is_plugin_active($plugin) {
		return in_array($plugin, apply_filters('active_plugins', get_option('active_plugins')));
	}

	public static function get_template($fileName, $args = null) {
		if (! empty($args) && is_array($args)) {
			extract($args); // @codingStandardsIgnoreLine
		}

		$located = self::locate_template($fileName);

		if (! file_exists($located)) {
			/* translators: %s template */
			self::doing_it_wrong(__FUNCTION__, sprintf(__('%s does not exist.', 'review-schema'), '<code>' . $located . '</code>'), '1.0');

			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters('rtrs_get_template', $located, $fileName, $args);

		do_action('rtrs_before_template_part', $fileName, $located, $args);

		include $located;

		do_action('rtrs_after_template_part', $fileName, $located, $args);
	}

	public static function touch_time($edit, $for_post, $tab_index, $multi, $comment_date) {
		global $wp_locale;
		$post = get_post();

		if ($for_post) {
			$edit = ! (in_array($post->post_status, ['draft', 'pending'], true) && (! $post->post_date_gmt || '0000-00-00 00:00:00' === $post->post_date_gmt));
		}

		$tab_index_attribute = '';
		if ((int) $tab_index > 0) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		$post_date = ($for_post) ? $post->post_date : $comment_date;
		$jj        = ($edit) ? mysql2date('d', $post_date, false) : current_time('d');
		$mm        = ($edit) ? mysql2date('m', $post_date, false) : current_time('m');
		$aa        = ($edit) ? mysql2date('Y', $post_date, false) : current_time('Y');
		$hh        = ($edit) ? mysql2date('H', $post_date, false) : current_time('H');
		$mn        = ($edit) ? mysql2date('i', $post_date, false) : current_time('i');
		$ss        = ($edit) ? mysql2date('s', $post_date, false) : current_time('s');

		$cur_jj = current_time('d');
		$cur_mm = current_time('m');
		$cur_aa = current_time('Y');
		$cur_hh = current_time('H');
		$cur_mn = current_time('i');
		//sanitize text
		$month = '<label><span class="screen-reader-text">' . esc_html__('Month', 'review-schema') . '</span><select class="form-required" ' . ($multi ? '' : 'id="mm" ') . 'name="mm"' . $tab_index_attribute . ">\n";
		for ($i = 1; $i < 13; $i = $i + 1) {
			$monthnum  = zeroise($i, 2);
			$monthtext = $wp_locale->get_month_abbrev($wp_locale->get_month($i));
			$month .= "\t\t\t" . '<option value="' . esc_attr($monthnum) . '" data-text="' . esc_attr($monthtext) . '" ' . selected($monthnum, $mm, false) . '>';
			/* translators: 1: Month number (01, 02, etc.), 2: Month abbreviation. */
			$month .= sprintf('%1$s-%2$s', $monthnum, $monthtext) . "</option>\n";
		}
		$month .= '</select></label>';

		$day    = '<label><span class="screen-reader-text">' . esc_html__('Day', 'review-schema') . '</span><input type="text" ' . ($multi ? '' : 'id="jj" ') . 'name="jj" value="' . esc_attr($jj) . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';
		$year   = '<label><span class="screen-reader-text">' . esc_html__('Year', 'review-schema') . '</span><input type="text" ' . ($multi ? '' : 'id="aa" ') . 'name="aa" value="' . esc_attr($aa) . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';
		$hour   = '<label><span class="screen-reader-text">' . esc_html__('Hour', 'review-schema') . '</span><input type="text" ' . ($multi ? '' : 'id="hh" ') . 'name="hh" value="' . esc_attr($hh) . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';
		$minute = '<label><span class="screen-reader-text">' . esc_html__('Minute', 'review-schema') . '</span><input type="text" ' . ($multi ? '' : 'id="mn" ') . 'name="mn" value="' . esc_attr($mn) . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';

		echo '<div class="timestamp-wrap">';
		/* translators: 1: Month, 2: Day, 3: Year, 4: Hour, 5: Minute. */
		printf('%1$s %2$s, %3$s at %4$s:%5$s', $month, $day, $year, $hour, $minute);

		echo '</div><input type="hidden" id="ss" name="ss" value="' . esc_attr($ss) . '" />';

		if ($multi) {
			return;
		}

		echo "\n\n";

		$map = [
			'mm' => [$mm, $cur_mm],
			'jj' => [$jj, $cur_jj],
			'aa' => [$aa, $cur_aa],
			'hh' => [$hh, $cur_hh],
			'mn' => [$mn, $cur_mn],
		];

		foreach ($map as $timeunit => $value) {
			list($unit, $curr) = $value;

			echo '<input type="hidden" id="hidden_' . esc_attr($timeunit) . '" name="hidden_' . esc_attr($timeunit) . '" value="' . esc_attr($unit) . '" />' . "\n";
			$cur_timeunit = 'cur_' . $timeunit;
			echo '<input type="hidden" id="' . esc_attr($cur_timeunit) . '" name="' . esc_attr($cur_timeunit) . '" value="' . esc_attr($curr) . '" />' . "\n";
		} ?>
        <p>
            <a href="#edit_timestamp" class="save-timestamp hide-if-no-js button"><?php esc_html_e('OK', 'review-schema'); ?></a>
            <a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js button-cancel"><?php esc_html_e('Cancel', 'review-schema'); ?></a>
        </p>
        <?php
	}

	/**
	 * Review time display with time formate
	 *
	 * @return string
	 */
	public static function comment_review_time( $comment ){
		$p_meta = self::getMetaByPostType( get_post_type() );
		$human_readable_time  = isset($p_meta['human-time-diff']) && $p_meta['human-time-diff'][0] == '1' ? false : true;
		
		if( $human_readable_time ){
			$time =  human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp') ) . ' ' . esc_html__('ago', 'review-schema'); 
		}else{
			$time =  date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , strtotime( $comment->comment_date )  );  ;
		}
		return $time ;
	}

	/**
	 * @param $id
	 *
	 * @return bool|mixed|void
	 */
	public static function get_default_placeholder_url() {
		$placeholder_url = RTRS_URL . '/assets/imgs/placeholder.jpg';

		return apply_filters('rtrs_default_placeholder_url', $placeholder_url);
	}

	/**
	 * is_edit_page
	 * function to check if the current page is a post edit page.
	 *
	 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
	 *
	 * @return bool
	 */
	public static function is_edit_page($new_edit = null) {
		global $pagenow;
		//make sure we are on the backend
		if (! is_admin()) {
			return false;
		}

		if ($new_edit == 'edit') {
			return in_array($pagenow, ['post.php']);
		} elseif ($new_edit == 'new') { //check for new post page
			return in_array($pagenow, ['post-new.php']);
		} else { //check for either new or edit
			return in_array($pagenow, ['post.php', 'post-new.php']);
		}
	}

	/**
	 * @param $id
	 *
	 * @return bool|mixed|void
	 */
	public static function get_option($id) {
		if (! $id) {
			return false;
		}
		$settings = get_option($id, []);

		return apply_filters($id, $settings);
	}

	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 *
	 * @param string|array $var Data to sanitize.
	 *
	 * @return string|array
	 */
	public static function clean($var) {
		if (is_array($var)) {
			return array_map([self::class, 'clean'], $var);
		} else {
			return is_scalar($var) ? sanitize_text_field($var) : $var;
		}
	}

	/**
	 * @param $id
	 *
	 * @return bool|mixed|void
	 */
	public function fieldGenerator($fields = [], $multi = false) {
		$html = null;
		if (is_array($fields) && ! empty($fields)) {
			$rtField = new Field();
			if ($multi) {
				foreach ($fields as $field) {
					$html .= $rtField->Field($field);
				}
			} else {
				$html .= $rtField->Field($fields);
			}
		}

		return $html;
	}

	/**
	 *  Check review enable.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function allReviewType() {
		$args = [
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'post_type'      => rtrs()->getPostType(),
		];
		$query              = new \WP_Query($args);
		$active_review_type = [];
		while ($query->have_posts()): $query->the_post();
		$active_review_type[] = get_post_meta(get_the_ID(), 'rtrs_post_type', true);
		endwhile;
		wp_reset_postdata();

		return $active_review_type;
	}

	/**
	 *  Check review enable.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	private static $enable_post_type_schema = null;

	public static function isEnableByPostTypeSchema($p_type = null) {
		if (self::$enable_post_type_schema != null) {
			return self::$enable_post_type_schema;
		}

		global $post;
		$originalpost = $post; // to fix override post data

		$args = [
			'posts_per_page' => -1,
			'post_type'      => rtrs()->getPostType(),
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => 'rtrs_post_type',
					'value'   => $p_type,
					'compare' => '=',
				],
				[
					'key'     => 'rtrs_support',
					'value'   => 'review',
					'compare' => '!=',
				],
			],
		];
		$query = new \WP_Query($args);

		$enable  = false;
		$post_id = $post->ID;

		while ($query->have_posts()): $query->the_post();

		if ($p_type == 'page') {
			$page_id = get_post_meta(get_the_ID(), 'rtrs_page_id', false);

			if ($page_id) {
				foreach ($page_id as $page) {
					if ($post_id == $page) {
						self::$enable_post_type_schema = true;
						$enable                        = true;
						break 2; // break parent loop also
					}
				}
			} else {
				self::$enable_post_type_schema = true;
				$enable                        = true;
				break;
			}
		} else {
			self::$enable_post_type_schema = true;
			$enable                        = true;
		}
		endwhile;
		wp_reset_postdata();
		$post = $originalpost; // to fix override post data

		//enable schema from settings
		if (! $enable) {
			$post_type            = self::getPostTypes(true, false);
			$rtrs_schema_settings = get_option('rtrs_schema_settings');

			$setting_post_type = isset($rtrs_schema_settings['post_type']) ? array_column($rtrs_schema_settings['post_type'], 'post_type') : [];

			if ( $setting_post_type ) {
				$post_type = $setting_post_type;
			}

			if (in_array($p_type, $post_type)) {
				self::$enable_post_type_schema = true;
				$enable                        = true;
			}
		}

		return $enable;
	}

	/**
	 *  Check review enable.
	 *
	 * @package Review Review
	 *
	 * @since 1.0
	 */
	private static $enable_post_type_review = null;

	public static function isEnableByPostTypeReview($p_type = null) {
		if (self::$enable_post_type_review != null) {
			return self::$enable_post_type_review;
		}

		global $post;
		$originalpost = $post; // to fix override post data

		$args = [
			'posts_per_page' => -1,
			'post_type'      => rtrs()->getPostType(),
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => 'rtrs_post_type',
					'value'   => $p_type,
					'compare' => '=',
				],
			],
		];
		$query = new \WP_Query($args);

		$enable  = false;
		$post_id = $post->ID;

		while ($query->have_posts()): $query->the_post();

		if ($p_type == 'page') {
			$page_id = get_post_meta(get_the_ID(), 'rtrs_page_id', false);

			if ($page_id) {
				foreach ($page_id as $page) {
					if ($post_id == $page) {
						self::$enable_post_type_review = true;
						$enable                        = true;
						break 2; // break parent loop also
					}
				}
			} else {
				self::$enable_post_type_review = true;
				$enable                        = true;
				break;
			}
		} else {
			self::$enable_post_type_review = true;
			$enable                        = true;
		}
		endwhile;
		wp_reset_postdata();
		$post = $originalpost; // to fix override post data

		//enable schema from settings
		if (! $enable) {
			$post_type            = self::getPostTypes(true, false);
			$rtrs_schema_settings = get_option('rtrs_schema_settings');

			$setting_post_type = isset($rtrs_schema_settings['post_type']) ? array_column($rtrs_schema_settings['post_type'], 'post_type') : [];

			if ($setting_post_type) {
				$post_type = $setting_post_type;
			}

			if (in_array($p_type, $post_type)) {
				self::$enable_post_type_review = true;
				$enable                        = true;
			}
		}

		return $enable;
	}

	/**
	 *  Check review enable.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	private static $enable_post_type = null;

	public static function isEnableByPostType($p_type = null) {
		if (self::$enable_post_type != null) {
			return self::$enable_post_type;
		}

		global $post;
		$originalpost = $post; // to fix override post data

		$args = [
			'posts_per_page' => -1,
			'post_type'      => rtrs()->getPostType(),
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => 'rtrs_post_type',
					'value'   => $p_type,
					'compare' => '=',
				],
				[
					'key'     => 'rtrs_support',
					'value'   => 'schema',
					'compare' => '!=',
				],
			],
		];
		$query = new \WP_Query($args);

		$enable  = false;
		$post_id = is_object( $post ) && isset(  $post->ID ) ? $post->ID : null ;

		while ($query->have_posts()): $query->the_post();

		if ($p_type == 'page') {
			$page_id = get_post_meta(get_the_ID(), 'rtrs_page_id', false);

			if ($page_id) {
				foreach ($page_id as $page) {
					if ($post_id == $page) {
						self::$enable_post_type = true;
						$enable                 = true;
						break 2; // break parent loop also
					}
				}
			} else {
				self::$enable_post_type = true;
				$enable                 = true;
				break;
			}
		} else {
			self::$enable_post_type = true;
			$enable                 = true;
		}
		endwhile;
		wp_reset_postdata();
		$post = $originalpost; // to fix override post data

		return $enable;
	}

	/**
	 *  Get all post meta.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	private static $post_meta = null;

	public static function getMetaByPostType($p_type = null) {
		if (self::$post_meta != null) {
			return self::$post_meta;
		}

		$args = [
			'posts_per_page' => 1,
			'post_type'      => rtrs()->getPostType(),
			'meta_query'     => [
				[
					'key'     => 'rtrs_post_type',
					'value'   => $p_type,
					'compare' => '=',
				],
			],
		];
		$query     = new \WP_Query($args);
		$all_metas = null;
		while ($query->have_posts()): $query->the_post();
		$all_metas          = get_post_meta(get_the_ID());
		$all_metas['sc_id'] =  get_the_ID();
		self::$post_meta    = $all_metas;
		endwhile;
		wp_reset_postdata();

		return $all_metas;
	}

	/**
	 *  Get Criteria.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function getCriteriaByPostType($p_type = null) {
		if (! $p_type) {
			$p_type = get_post_type();
		}

		$args = [
			'posts_per_page' => 1,
			'post_type'      => rtrs()->getPostType(),
			'meta_query'     => [
				[
					'key'     => 'rtrs_post_type',
					'value'   => $p_type,
					'compare' => '=',
				],
			],
		];

		$query          = new \WP_Query($args);
		$multi_criteria = [];
		while ($query->have_posts()): $query->the_post();
		$multi_criteria = get_post_meta(get_the_ID(), 'multi_criteria', true);
		endwhile;
		wp_reset_postdata();

		return $multi_criteria;
	} 
	 
	/**
	 *  Default settings schema
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	private static $default_setting_schema = null;
	public static function default_setting_schema() {
		if (self::$default_setting_schema != null) {
			return self::$default_setting_schema;
		}

		$new_default   = [];
		$post_type     = Functions::getPostTypes(false, false);
		foreach (array_keys($post_type) as $value) {
			switch ($value) {
				case 'post':
					$new_default[] = [
						'post_type'   => $value,
						'schema_type' => 'blog_posting',
					];
					break;

				case 'page':
					$new_default[] = [
						'post_type'   => $value,
						'schema_type' => 'article',
					];
					break;

				case 'product':
				case 'download':
					$new_default[] = [
						'post_type'   => $value,
						'schema_type' => 'product',
					];
					break;

				default:
					$new_default[] = [
						'post_type'   => $value,
						'schema_type' => 'article',
					];

					break;
			}
		}
		 
		return self::$default_setting_schema = $new_default;
	}

	/**
	 *  String to slug convert.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function slugify($string) {
		if ( self::is_english( $string ) ) {
			return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
		} else {
			return sanitize_title( $string );
		}
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $str text.
	 * @return boolean
	 */
	public static function is_english( $str ) {
		if (strlen($str) != strlen(utf8_decode($str))) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Sanitize out put.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public function sanitizeOutPut($value, $type = 'text') {
		$newValue = null;
		if ($value) {
			if ($type == 'text') {
				$newValue = esc_html(stripslashes($value));
			} elseif ($type == 'url') {
				$newValue = esc_url(stripslashes($value));
			} elseif ($type == 'textarea') {
				$newValue = esc_textarea(stripslashes($value));
			} else {
				$newValue = esc_html(stripslashes($value));
			}
		}

		return $newValue;
	}

	/**
	 * Image information.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public function imageInfo($attachment_id) {
		$data           = [];
		$imgData        = wp_get_attachment_metadata($attachment_id);
		$data['id']     = $attachment_id;
		$data['url']    = wp_get_attachment_url($attachment_id);
		$data['width']  = ! empty($imgData['width']) ? absint($imgData['width']) : 0;
		$data['height'] = ! empty($imgData['height']) ? absint($imgData['height']) : 0;

		return $data;
	}

	/**
	 *  Google rich snippet auto category.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function rich_snippet_auto_cats() {
		$pro_label = '';
		if (! function_exists('rtrsp')) {
			$pro_label = ' [Pro]'; //don't need to translate
		}

		$auto_cat = [
			''             => esc_html__('Select', 'review-schema'),
			'article'      => esc_html__('Article', 'review-schema'),
			'news_article' => esc_html__('News article', 'review-schema'),
			'blog_posting' => esc_html__('Blog posting', 'review-schema'),
			'product'      => esc_html__('Product', 'review-schema') . $pro_label,
		];

		if (is_plugin_active('learnpress/learnpress.php')) {
			$auto_cat['course'] = esc_html__('Course', 'review-schema') . $pro_label;
		}

		return apply_filters('rtrs_rich_snippet_auto_cats', $auto_cat);
	}

	/**
	 *  Google rich snippet category.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function rich_snippet_cats() {
		$pro_label = '';
		if (! function_exists('rtrsp')) {
			$pro_label = ' [Pro]'; //don't need to translate
		}

		return apply_filters('rtrs_rich_snippet_cats', [
			'article'              => esc_html__('Article', 'review-schema'),
			'tech_article'         => esc_html__('Tech Article', 'review-schema'),
			'news_article'         => esc_html__('News article', 'review-schema'),
			'blog_posting'         => esc_html__('Blog posting', 'review-schema'),
			'event'                => esc_html__('Event', 'review-schema'),
			'local_business'       => esc_html__('Local business', 'review-schema'),
			'faq'                  => esc_html__('Faq', 'review-schema'),
			'service'              => esc_html__('Service', 'review-schema'),
			'question_answer'      => esc_html__('Q&A', 'review-schema'),
			'how_to'               => esc_html__('How to', 'review-schema'),
			'about'                => esc_html__('About', 'review-schema'),
			'contact'              => esc_html__('Contact', 'review-schema'),
			'person'               => esc_html__('Person', 'review-schema'),
			'movie'                => esc_html__('Movie', 'review-schema'),
			'audio'                => esc_html__('Audio', 'review-schema'),
			'video'                => esc_html__('Video', 'review-schema'),
			'breadcrumb'           => esc_html__('Breadcrumb', 'review-schema'),
			'itemlist'             => esc_html__('ItemList', 'review-schema'),
			'product'              => esc_html__('Product', 'review-schema') . $pro_label,
			'book'                 => esc_html__('Book', 'review-schema') . $pro_label,
			'real_state_listing'   => esc_html__('Real Estate Listing', 'review-schema') . $pro_label,
			'course'               => esc_html__('Course', 'review-schema') . $pro_label,
			'job_posting'          => esc_html__('Job posting', 'review-schema') . $pro_label,
			'recipe'               => esc_html__('Recipe', 'review-schema') . $pro_label,
			'software_app'         => esc_html__('Software App', 'review-schema') . $pro_label,
			'image_license'        => esc_html__('Image License', 'review-schema') . $pro_label,
			'special_announcement' => esc_html__('Special announcement', 'review-schema') . $pro_label,
			'mosque'  			   => esc_html__('Mosque', 'review-schema'),
			'church'  			   => esc_html__('Church', 'review-schema'),
			'hindutemple'  		   => esc_html__('Hindu Temple', 'review-schema'),
			'buddhisttemple'  	   => esc_html__('Buddhist Temple', 'review-schema'),
			'medical_webpage'  	   => esc_html__('Medical Webpage', 'review-schema'),
			'collection_page'  	   => esc_html__('Collection Page', 'review-schema') . $pro_label,
		]);
	}

	/**
	 * Get all custom post types.
	 *
	 * @param none
	 *
	 * @return array
	 */
	public static function getPostTypes($key_only = false, $select_option = true) {
		global $wp_post_types;

		$pre_post_types = $data = [];
		if ($select_option) {
			$data[] = esc_html__('Select', 'review-schema');
		}
		foreach ($wp_post_types as $key => $post_type) {
			$pre_post_types[$key] = $post_type->label;
		}

		// Remove some post type
		$post_type_remove = [
			'rtrs',
			'rtrs_affiliate',
			//'page',
			'attachment',
			'nav_menu_item',
			'customize_changeset',
			'revision',
			'custom_css',
			'oembed_cache',
			'user_request',
			'wp_block',
			'product_variation',
			'shop_order',
			'shop_order_refund',
			'shop_coupon',
			//extra
			'edd_log',
			'edd_payment',
			'edd_discount',
			//tlp
			'rtcl_cfg',
			'rtcl_cf',
			'rtcl_payment',
			'rtcl_pricing',
			//elementor
			'elementor_library',
			'e-landing-page',
			'wp_template',
			//lp
			'lp_order',
		];

		foreach ($pre_post_types as $key => $posttype):
			if (in_array($key, $post_type_remove)) {
				continue;
			}
		if ($key_only) {
			$data[] = $key;
		} else {
			$data[$key] = $posttype;
		}
		endforeach;

		return apply_filters('rtrs_post_type', $data);
	}

	/**
	 * Check purchased user.
	 *
	 * @param comment_id
	 *
	 * @return mixed
	 */
	public static function purchased_user($comment_details = null) {
		$varified = false;

		$user_id    = $comment_details->user_id;
		$user_email = $comment_details->comment_author_email;
		$post_id    = $comment_details->comment_post_ID;
		$post_type  = get_post_type($post_id);

		if ($post_type == 'product') {
			$varified = wc_customer_bought_product($user_email, $user_id, $post_id);
		} elseif ($post_type == 'download') {
			$varified = edd_has_user_purchased($user_id, $post_id);
		}

		return $varified;
	}

	/**
	 *  Review Schema Star Icon.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function review_stars($rating, $dash_icon = false) {
		ob_start();
		for ($x = 0; $x < 5; $x++) {
			if (floor($rating) - $x >= 1) {
				if ($dash_icon) {
					echo '<i class="dashicons dashicons-star-filled"></i>';
				} else {
					echo '<i class="rtrs-star"></i>';
				}
			} elseif ($rating - $x > 0) {
				if ($dash_icon) {
					echo '<i class="dashicons dashicons-star-half"></i>';
				} else {
					echo '<i class="rtrs-star-half-alt"></i>';
				}
			} else {
				if ($dash_icon) {
					echo '<i class="dashicons dashicons-star-empty"></i>';
				} else {
					echo '<i class="rtrs-star-empty"></i>';
				}
			}
		}

		return ob_get_clean();
	}

	/**
	 *  Review Schema Entity Star Icon.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function review_entity_stars($rating) {
		ob_start();
		foreach ([1, 2, 3, 4, 5] as $val) {
			$score = $rating - $val;
			if ($score >= 0) {
				echo '&#9733;';
			} elseif ($score > -1 && $score < 0) {
				// half star will show full star in url
				echo '&#9733;';
			} else {
				echo '&#9734;';
			}
		}

		return ob_get_clean();
	}

	public static function get_same_as($value) {
		$sameAs = null;
		if ($value) {
			$sameAsRaw = preg_split('/\r\n|\r|\n/', $value);
			$sameAsRaw = ! empty($sameAsRaw) ? array_filter($sameAsRaw) : [];
			if (! empty($sameAsRaw) && is_array($sameAsRaw)) {
				if (1 < count($sameAsRaw)) {
					$sameAs = $sameAsRaw;
				} else {
					$sameAs = $sameAsRaw[0];
				}
			}
		}

		return $sameAs;
	}

	public static function filter_content($content, $limit = 0) {
		$content = preg_replace('#\[[^\]]+\]#', '', wp_strip_all_tags($content));
		$content = self::characterToHTMLEntity($content);
		if ($limit && strlen($content) > $limit) {
			$content = mb_substr($content, 0, $limit, 'utf-8');
			$content = preg_replace('/\W\w+\s*(\W*)$/', '$1', $content);
		}

		return $content;
	}

	public static function array_insert(&$array, $position, $insert_array) {
		$first_array = array_splice($array, 0, $position + 1);
		$array       = array_merge($first_array, $insert_array, $array);
	}

	public static function add_notice($message, $notice_type = 'success', $notice_id = null) {
		if (! did_action('rtrs_init')) {
			self::doing_it_wrong(__FUNCTION__, esc_html__('This function should not be called before rtrs_init.', 'review-schema'), '1.0.0');

			return;
		}

		$notices = rtrs()->session->get('rtrs_notices', []);

		$notices[$notice_type][] = apply_filters('rtrs_add_notice_' . $notice_type, $message, $notice_id);

		rtrs()->session->set('rtrs_notices', $notices);
	}

	public static function characterToHTMLEntity($str) {
		$replace = [
			"'", '&', '<', '>', '€', '‘', '’', '“', '”', '–', '—', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½', '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ', 'Œ', 'œ', '‚', '„', '…', '™', '•', '˜',
		];

		$search = [
			'&#8217;', '&amp;', '&lt;', '&gt;', '&euro;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&ndash;', '&mdash;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&OElig;', '&oelig;', '&sbquo;', '&bdquo;', '&hellip;', '&trade;', '&bull;', '&asymp;',
		];

		//REPLACE VALUES
		$str = str_replace($search, $replace, $str);

		//RETURN FORMATED STRING
		return $str;
	}

	/**
	 *  Format bye.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public static function format_bytes($bytes) {
		$label = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		for ($i = 0; $bytes >= 1024 && $i < (count($label) - 1); $bytes /= 1024, $i++);

		return  round($bytes, 2) . ' ' . $label[$i];
	}

	/**
	 * Generate ShortCode CSS.
	 *
	 * @param int $scID
	 *
	 * @return void
	 */  

	public static function generatorShortCodeCss($scID, $post_type) {
		global $wp_filesystem;
		// Initialize the WP filesystem, no more using 'file-put-contents' function
		if ( empty($wp_filesystem) ) {
			require_once (ABSPATH . '/wp-admin/includes/file.php');
			WP_Filesystem();
		}
		
		$upload_dir = wp_upload_dir(); 
		$upload_basedir = $upload_dir['basedir'] ;
		$cssFile = $upload_basedir . '/review-schema/sc.css'; 
		if ( $css = rtrs()->render($post_type . '-sc-css', compact('scID'), true) ) { 
			$css = sprintf('/*' . $post_type . '-sc-%2$d-start*/%1$s/*' . $post_type . '-sc-%2$d-end*/', $css, $scID);
			if ( file_exists($cssFile) && ($oldCss = $wp_filesystem->get_contents($cssFile)) ) {
				if ( strpos($oldCss, '/*' . $post_type . '-sc-' . $scID . '-start') !== false ) {
					$oldCss = preg_replace('/\/\*' . $post_type . '-sc-' . $scID . '-start[\s\S]+?' . $post_type . '-sc-' . $scID . '-end\*\//', '', $oldCss);
					$oldCss = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $oldCss);
				}
				$css = $oldCss . $css;				
			} else if ( ! file_exists( $cssFile ) ) {
				$upload_basedir_trailingslashit = trailingslashit( $upload_basedir ); 
				$wp_filesystem->mkdir( $upload_basedir_trailingslashit. 'review-schema' );
			}
			if( ! $wp_filesystem->put_contents( $cssFile, $css  ) ){
				error_log(print_r('Review Schema: Error Generated css file ',true));
			}
		} 
	}

	/**
	 * Remove Generate ShortCode CSS.
	 *
	 * @param int $scID
	 *
	 * @return void
	 */
	public static function removeGeneratorShortCodeCss($scID, $post_type) {
		global $wp_filesystem;
		// Initialize the WP filesystem, no more using 'file-put-contents' function
		if (empty($wp_filesystem)) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$upload_dir = wp_upload_dir(); 
		$upload_basedir = $upload_dir['basedir'];
		$cssFile = $upload_basedir . '/review-schema/sc.css';

		if (file_exists($cssFile) && ($oldCss = $wp_filesystem->get_contents($cssFile)) && strpos($oldCss, '/*' . $post_type . '-sc-' . $scID . '-start') !== false) {
			$css = preg_replace('/\/\*' . $post_type . '-sc-' . $scID . '-start[\s\S]+?' . $post_type . '-sc-' . $scID . '-end\*\//', '', $oldCss);
			$css = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", '', $css);

			$wp_filesystem->put_contents($cssFile, $css);
		}
	} 

	public static function getSiteTypes() {
		$siteTypes = [
			'Organization',
			'LocalBusiness'  => [
				'AnimalShelter',
				'AutomotiveBusiness' => [
					'AutoBodyShop',
					'AutoDealer',
					'AutoPartsStore',
					'AutoRental',
					'AutoRepair',
					'AutoWash',
					'GasStation',
					'MotorcycleDealer',
					'MotorcycleRepair',
				],
				'ChildCare',
				'DryCleaningOrLaundry',
				'EmergencyService',
				'EmploymentAgency',
				'EntertainmentBusiness' => [
					'AdultEntertainment',
					'AmusementPark',
					'ArtGallery',
					'Casino',
					'ComedyClub',
					'MovieTheater',
					'NightClub',

				],
				'FinancialService'    => [
					'AccountingService',
					'AutomatedTeller',
					'BankOrCreditUnion',
					'InsuranceAgency',
				],
				'FoodEstablishment'   => [
					'Bakery',
					'BarOrPub',
					'Brewery',
					'CafeOrCoffeeShop',
					'FastFoodRestaurant',
					'IceCreamShop',
					'Restaurant',
					'Winery',
				],
				'GovernmentOffice',
				'HealthAndBeautyBusiness' => [
					'BeautySalon',
					'DaySpa',
					'HairSalon',
					'HealthClub',
					'NailSalon',
					'TattooParlor',
				],
				'HomeAndConstructionBusiness' => [
					'Electrician',
					'GeneralContractor',
					'HVACBusiness',
					'HousePainter',
					'Locksmith',
					'MovingCompany',
					'Plumber',
					'RoofingContractor',
				],
				'InternetCafe',
				'LegalService'    => [
					'Attorney',
					'Notary',
				],
				'Library',
				'MedicalBusiness'   => [
					// 'CommunityHealth',
					'Dentist',
					// 'Dermatology',
					// 'DietNutrition',
					// 'Emergency',
					// 'Geriatric',
					// 'Gynecologic',
					'MedicalClinic',
					// 'Midwifery',
					// 'Nursing',
					// 'Obstetric',
					// 'Oncologic',
					'Optician',
					// 'Optometric',
					// 'Otolaryngologic',
					// 'Pediatric',
					'Pharmacy',
					'Physician',
					// 'Physiotherapy',
					// 'PlasticSurgery',
					// 'Podiatric',
					// 'PrimaryCare',
					// 'Psychiatric',
					// 'PublicHealth',
				],
				'LodgingBusiness'  => [
					'BedAndBreakfast',
					'Campground',
					'Hostel',
					'Hotel',
					'Motel',
					'Resort',
				],
				'ProfessionalService',
				'RadioStation',
				'RealEstateAgent',
				'RecyclingCenter',
				'SelfStorage',
				'ShoppingCenter',
				'SportsActivityLocation' => [
					'BowlingAlley',
					'ExerciseGym',
					'GolfCourse',
					'HealthClub',
					'PublicSwimmingPool',
					'SkiResort',
					'SportsClub',
					'StadiumOrArena',
					'TennisComplex',
				],
				'Store'   => [
					'AutoPartsStore',
					'BikeStore',
					'BookStore',
					'ClothingStore',
					'ComputerStore',
					'ConvenienceStore',
					'DepartmentStore',
					'ElectronicsStore',
					'Florist',
					'FurnitureStore',
					'GardenStore',
					'GroceryStore',
					'HardwareStore',
					'HobbyShop',
					'HomeGoodsStore',
					'JewelryStore',
					'LiquorStore',
					'MensClothingStore',
					'MobilePhoneStore',
					'MovieRentalStore',
					'MusicStore',
					'OfficeEquipmentStore',
					'OutletStore',
					'PawnShop',
					'PetStore',
					'ShoeStore',
					'SportingGoodsStore',
					'TireShop',
					'ToyStore',
					'WholesaleStore',
				],
				'TelevisionStation',
				'TouristInformationCenter',
				'TravelAgency',
				'TaxiService',
			],
			'NGO',
			'CivicStructure' => [
				'Museum',
			],
		];

		return apply_filters('rtseo_site_types', $siteTypes);
	}

	public static function getCountryList() {
		$countryList = [
			''   => 'Select Country',
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia, Plurinational State of',
			'BQ' => 'Bonaire, Sint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Congo, the Democratic Republic of the',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Côte d Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curaçao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (Malvinas)',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'VA' => 'Holy See (Vatican City State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran, Islamic Republic of',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KP' => "Korea, Democratic People's Republic of",
			'KR' => 'Korea, Republic of,',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao Peoples Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia, the former Yugoslav Republic of',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia, Federated States of',
			'MD' => 'Moldova, Republic of',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestine, State of',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthélemy',
			'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin (French part)',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten (Dutch part)',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan, Province of China',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania, United Republic of',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela, Bolivarian Republic of',
			'VN' => 'Viet Nam',
			'VG' => 'Virgin Islands, British',
			'VI' => 'Virgin Islands, U.S.',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		];

		return apply_filters('rtseo_country_list', $countryList);
	}

	public static function getLanguageList() {
		$language_list = [
			'Akan',
			'Amharic',
			'Arabic',
			'Assamese',
			'Awadhi',
			'Azerbaijani',
			'Balochi',
			'Belarusian',
			'Bengali',
			'Bhojpuri',
			'Burmese',
			'Cantonese',
			'Cebuano',
			'Chewa',
			'Chhattisgarhi',
			'Chittagonian',
			'Czech',
			'Deccan',
			'Dhundhari',
			'Dutch',
			'English',
			'French',
			'Fula',
			'Gan',
			'German',
			'Greek',
			'Gujarati',
			'Haitian Creole',
			'Hakka',
			'Haryanvi',
			'Hausa',
			'Hiligaynon',
			'Hindi / Urdu',
			'Hmong',
			'Hungarian',
			'Igbo',
			'Ilokano',
			'Italian',
			'Japanese',
			'Javanese',
			'Jin',
			'Kannada',
			'Kazakh',
			'Khmer',
			'Kinyarwanda',
			'Kirundi',
			'Konkani',
			'Korean',
			'Kurdish',
			'Madurese',
			'Magahi',
			'Maithili',
			'Malagasy',
			'Malay/Indonesian',
			'Malayalam',
			'Mandarin',
			'Marathi',
			'Marwari',
			'Min Bei',
			'Min Dong',
			'Min Nan',
			'Mossi',
			'Nepali',
			'Oriya',
			'Oromo',
			'Pashto',
			'Persian',
			'Polish',
			'Portuguese',
			'Punjabi',
			'Quechua',
			'Romanian',
			'Russian',
			'Saraiki',
			'Serbo-Croatian',
			'Shona',
			'Sindhi',
			'Sinhalese',
			'Somali',
			'Spanish',
			'Sundanese',
			'Swahili',
			'Swedish',
			'Sylheti',
			'Tagalog',
			'Tamil',
			'Telugu',
			'Thai',
			'Turkish',
			'Ukrainian',
			'Uyghur',
			'Uzbek',
			'Vietnamese',
			'Wu',
			'Xhosa',
			'Xiang',
			'Yoruba',
			'Zulu',
		];

		$language_with_key = [];

		foreach ($language_list as $value) {
			$language_with_key[$value] = $value;
		}

		return apply_filters('rtseo_language_list', $language_with_key);
	}
}
