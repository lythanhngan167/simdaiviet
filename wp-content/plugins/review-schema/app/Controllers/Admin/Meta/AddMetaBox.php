<?php

namespace Rtrs\Controllers\Admin\Meta;

use Rtrs\Helpers\Functions;

class AddMetaBox {
	public function __construct() {
		//actions
		add_action('admin_head', [$this, 'add_meta_boxes']);
		add_action('save_post', [$this, 'save_meta_data'], 10, 2);
		add_action('pre_post_update', [$this, 'before_update_post']);
		add_action('before_delete_post', [$this, 'before_delete_post'], 10, 2);

		if (Functions::is_edit_page() || (isset($_GET['page']) && $_GET['page'] == 'rtrs-settings')) {
			add_action('admin_footer', [$this, 'pro_alert_html']);
		}

		//rtrs post type
		add_filter('manage_edit-rtrs_columns', [$this, 'rtrs_columns_title_arrange']);
		add_action('manage_rtrs_posts_custom_column', [$this, 'rtrs_columns_data_arrange'], 10, 2);

		//rtrs affiliate post type
		add_action('edit_form_after_title', [$this, 'rtrs_sc_after_title']);
		add_filter('manage_edit-rtrs_affiliate_columns', [$this, 'rtrs_affiliate_columns_title_arrange']);
		add_action('manage_rtrs_affiliate_posts_custom_column', [$this, 'rtrs_affiliate_columns_data_arrange'], 10, 2);

		add_filter('preprocess_comment', [$this, 'modify_comment_type']);
	}

	/**
	 * Comment type review
	 * when post any comment, add comment type as review.
	 *
	 * @package Review Schema
	 *
	 * @since 1.0
	 */
	public function modify_comment_type($commentdata) {
		$post_type = get_post_type($commentdata['comment_post_ID']);
		if (Functions::isEnableByPostType($post_type)) {
			$commentdata['comment_type'] = 'review';
		}

		return $commentdata;
	}

	public function rtrs_columns_title_arrange($columns) {
		$shortcode = [
			'post_type' => esc_html__('Post Type', 'review-schema'),
			'support'   => esc_html__('Support', 'review-schema'),
		];

		return array_slice($columns, 0, 2, true) + $shortcode + array_slice($columns, 1, null, true);
	}

	public function rtrs_columns_data_arrange($column) {
		switch ($column) {
			case 'post_type':
				if ($post_type = get_post_meta(get_the_ID(), 'rtrs_post_type', true)) {
					echo ucfirst(esc_html($post_type));
				}
				break;

			case 'support':
				$support = get_post_meta(get_the_ID(), 'rtrs_support', true);
				switch ($support) {
					case 'review-schema':
						esc_html_e('Review with Schema JSON-LD', 'review-schema');
						break;

					case 'review':
						esc_html_e('Only Review', 'review-schema');
						break;

					case 'schema':
						esc_html_e('Only Schema JSON-LD', 'review-schema');
						break;
				}
				break;

			default:
				break;
		}
	}

	public function rtrs_affiliate_columns_title_arrange($columns) {
		$shortcode = [
			'shortcode' => esc_html__('Shortcode', 'review-schema'),
		];

		return array_slice($columns, 0, 2, true) + $shortcode + array_slice($columns, 1, null, true);
	}

	public function rtrs_affiliate_columns_data_arrange($column) {
		switch ($column) {
			case 'shortcode':
				echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[rtrs-affiliate id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">';
				break;

			default:
				break;
		}
	}

	public function add_meta_boxes() {
		add_meta_box(
			'rtrs_meta',
			esc_html__('Review Schema Generator', 'review-schema'),
			[$this, 'rtrs_meta_settings'],
			rtrs()->getPostType(),
			'normal',
			'high'
		);

		add_meta_box(
			'rt_plugin_sc_pro_information',
			esc_html__('Documentation', 'review-schema'),
			[$this, 'rt_plugin_sc_pro_information'],
			rtrs()->getPostType(),
			'side',
			'low'
		);

		add_meta_box(
			'rtrs_meta',
			esc_html__('Affiliate Shortcode Generator', 'review-schema'),
			[$this, 'rtrs_affiliate_settings'],
			'rtrs_affiliate',
			'normal',
			'high'
		);

		if (Functions::is_edit_page()) {
			global $post;
			$post_type = $post->post_type;
			if (Functions::isEnableByPostTypeReview($post_type)) {
				add_meta_box(
					'rtrs_meta',
					esc_html__('Review & Schema Settings', 'review-schema'),
					[$this, 'rtrs_single_meta_settings'],
					[$post_type],
					'normal',
					'low'
				);
			}
		}
	}

	public function rt_plugin_sc_pro_information($post) {
		$html = '';

		$html .= sprintf(
			'<div class="rt-document-box">
				<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
				<div class="rt-box-content">
					<h3 class="rt-box-title">%1$s</h3>
						<p>%2$s</p>
						<a href="https://www.radiustheme.com/docs/review-schema/review-schema/" target="_blank" class="rt-admin-btn">%1$s</a>
				</div>
			</div>',
			esc_html__('Documentation', 'review-schema'),
			esc_html__('Get started by spending some time with the documentation we included step by step process with screenshots with video.', 'review-schema')
		);

		$html .= '<div class="rt-document-box">
                        <div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
                        <div class="rt-box-content">
                            <h3 class="rt-box-title">' . esc_html__('Need Help?', 'review-schema') . '</h3>
                                <p>' . esc_html__('Stuck with something? Please create a', 'review-schema') . ' 
                    <a href="https://www.radiustheme.com/contact/">' . esc_html__('ticket here', 'review-schema') . '</a> ' . esc_html__('or post on ', 'review-schema') . '<a href="https://www.facebook.com/groups/234799147426640/">facebook group</a>. ' . esc_html__('For emergency case join our', 'review-schema') . ' <a href="https://www.radiustheme.com/">' . esc_html__('live chat', 'review-schema') . '</a>.</p>
                                <a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">' . esc_html__('Get Support', 'review-schema') . '</a>
                        </div>
                    </div>';

		$html .= '<div class="rt-document-box">
                <div class="rt-box-icon"><i class="dashicons dashicons-smiley"></i></div>
                <div class="rt-box-content">
                    <h3 class="rt-box-title">Happy Our Work?</h3>
                    <p>Thank you for choosing Review Schema. If you have found our plugin useful and makes you smile, please consider giving us a 5-star rating on WordPress.org. It will help us to grow.</p>
                    <a target="_blank" href="https://wordpress.org/support/plugin/review-schema/reviews/?filter=5#new-post" class="rt-admin-btn">Yes, You Deserve It</a>
                </div>
            </div>';

		echo $html;
	}

	public function pro_alert_html() {
		$html = '';
		if (! function_exists('rtrsp')) {
			$html .= '<div class="rt-document-box rt-alert rtrs-pro-alert">
                    <div class="rt-box-icon"><i class="dashicons dashicons-lock"></i></div>
                    <div class="rt-box-content">
                        <h3 class="rt-box-title">' . esc_html__('Pro field alert!', 'review-schema') . '</h3>
                        <p><span></span>' . esc_html__('Sorry! this is a pro field. To use this field, you need to use pro plugin.', 'review-schema') . '</p>
                        <a href="https://www.radiustheme.com/downloads/wordpress-review-structure-data-schema-plugin/?utm_source=WordPress&utm_medium=reviewschema&utm_campaign=pro_click" target="_blank" class="rt-admin-btn">' . esc_html__('Upgrade to pro', 'review-schema') . '</a>
                        <a href="#" target="_blank" class="rt-alert-close rtrs-pro-alert-close">x</a>
                    </div>
                </div>';
		}

		$html .= '<div class="rt-document-box rt-alert rtrs-post-type">
            <div class="rt-box-icon"><i class="dashicons dashicons-lock"></i></div>
            <div class="rt-box-content">
                <h3 class="rt-box-title">' . esc_html__('Already exist alert!', 'review-schema') . '</h3>
                <p>' . esc_html__('Sorry! this post type already exist, you need to choose new one.', 'review-schema') . '</p> 
                <a href="#" target="_blank" class="rt-alert-close rtrs-post-type-close">x</a>
            </div>
        </div>';

		echo $html;
	}

	public function rtrs_sc_after_title($post) {
		if (rtrs()->getPostTypeAffiliate() !== $post->post_type) {
			return;
		}
		$html = null;
		$html .= '<div class="postbox rt-after-title" style="margin-bottom: 0;"><div class="inside">';
		$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[rtrs-affiliate id=&quot;' . esc_attr($post->ID) . '&quot; title=&quot;' . esc_attr($post->post_title) . '&quot;]" class="large-text code rt-code-sc">
        <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[rtrs-affiliate id=&quot;' . esc_attr($post->ID) . '&quot; title=&quot;' . esc_attr($post->post_title) . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
        </p>';
		$html .= '</div></div>';
		echo $html;
	}

	public function postType() {
		return apply_filters('rtrs_post_type', Functions::getPostTypes());
	}

	public function rtrs_meta_settings($post) {
		$post = [
			'post' => $post,
		];
		wp_nonce_field(rtrs()->getNonceText(), rtrs()->getNonceId());

		//auto select tab
		$tab = get_post_meta(get_the_ID(), '_rtrs_sc_tab', true);
		if (! $tab) {
			$tab = 'review';
		}
		$review_tab  = ($tab == 'review') ? 'active' : '';
		$schema_tab  = ($tab == 'schema') ? 'active' : '';
		$setting_tab = ($tab == 'setting') ? 'active' : '';
		$style_tab   = ($tab == 'style') ? 'active' : '';
		$preview_tab = ($tab == 'preview') ? 'active' : '';

		$html = null;

		$html .= '<div id="rt-conditional-wrap" class="rtrs-tab-content" style="display: block;">';
		$html .= rtrs()->render('metas.sc.conditional', $post, true);
		$html .= '</div>';

		//meta tab
		$html .= '<div id="sc-tabs" class="rtrs-tab-container">';
		$html .= '<ul class="rtrs-tab-nav rt-back">
                <li class="review-tab ' . esc_attr($review_tab) . '"><a href="#sc-review"><i class="dashicons dashicons-star-filled"></i>' . esc_html__('Review', 'review-schema') . '</a></li> 
                <li class="' . esc_attr($setting_tab) . '"><a href="#sc-settings"><i class="dashicons dashicons-admin-tools"></i>' . esc_html__('Settings', 'review-schema') . '</a></li>
                <li class="schema-tab ' . esc_attr($schema_tab) . '"><a href="#sc-schema"><i class="dashicons dashicons-editor-table"></i>' . esc_html__('Schema', 'review-schema') . '</a></li>
                <li class="' . esc_attr($style_tab) . '"><a href="#sc-style"><i class="dashicons dashicons-admin-customizer"></i>' . esc_html__('Style', 'review-schema') . '</a></li></ul>';

		$review_tab  = ($tab == 'review') ? 'display: block' : '';
		$schema_tab  = ($tab == 'schema') ? 'display: block' : '';
		$setting_tab = ($tab == 'setting') ? 'display: block' : '';
		$style_tab   = ($tab == 'style') ? 'display: block' : '';
		$preview_tab = ($tab == 'preview') ? 'display: block' : '';

		$html .= '<input type="hidden" id="_rtrs_sc_tab" name="_rtrs_sc_tab" value="' . esc_attr($tab) . '" />';

		$html .= '<div id="sc-review" class="rtrs-tab-content" style="' . esc_attr($review_tab) . '">';
		$html .= rtrs()->render('metas.sc.review', $post, true);
		$html .= '</div>';

		$html .= '<div id="sc-schema" class="rtrs-tab-content" style="' . esc_attr($schema_tab) . '">';
		$html .= rtrs()->render('metas.sc.schema', $post, true);
		$html .= '</div>';

		$html .= '<div id="sc-settings" class="rtrs-tab-content" style="' . esc_attr($setting_tab) . '">';
		$html .= rtrs()->render('metas.sc.settings', $post, true);
		$html .= '</div>';

		$html .= '<div id="sc-style" class="rtrs-tab-content" style="' . esc_attr($style_tab) . '">';
		$html .= rtrs()->render('metas.sc.style', $post, true);
		$html .= '</div>';
		echo $html;

		echo '</div>'; //wrap div
	}

	public function rtrs_affiliate_settings($post) {
		$post = [
			'post' => $post,
		];
		wp_nonce_field(rtrs()->getNonceText(), rtrs()->getNonceId());

		//auto select tab
		$tab = get_post_meta(get_the_ID(), '_rtrs_sc_tab', true);
		if (! $tab) {
			$tab = 'affiliate';
		}

		$affiliate_tab = ($tab == 'affiliate') ? 'active' : '';
		$schema_tab    = ($tab == 'schema') ? 'active' : '';
		$style_tab     = ($tab == 'style') ? 'active' : '';
		$preview_tab   = ($tab == 'preview') ? 'active' : '';

		$html = null;

		$html .= '<div id="sc-tabs" class="rtrs-tab-container">';
		$html .= '<ul class="rtrs-tab-nav">
                <li class="' . esc_attr($affiliate_tab) . '"><a href="#sc-affiliate"><i class="dashicons dashicons-megaphone"></i>' . esc_html__('Affiliate', 'review-schema') . '</a></li>
                <li class="' . esc_attr($schema_tab) . '"><a href="#sc-schema"><i class="dashicons dashicons-editor-table"></i>' . esc_html__('Schema', 'review-schema') . '</a></li>
                <li class="' . esc_attr($style_tab) . '"><a href="#sc-style"><i class="dashicons dashicons-admin-customizer"></i>' . esc_html__('Style', 'review-schema') . '</a></li></ul>';

		$affiliate_tab = ($tab == 'affiliate') ? 'display: block' : '';
		$schema_tab    = ($tab == 'schema') ? 'display: block' : '';
		$style_tab     = ($tab == 'style') ? 'display: block' : '';
		$preview_tab   = ($tab == 'preview') ? 'display: block' : '';

		$html .= '<input type="hidden" id="_rtrs_sc_tab" name="_rtrs_sc_tab" value="' . esc_attr($tab) . '" />';

		$html .= '<div id="sc-affiliate" class="rtrs-tab-content" style="' . esc_attr($affiliate_tab) . '">';
		$html .= rtrs()->render('metas.affiliate.affiliate', $post, true);
		$html .= '</div>';

		$html .= '<div id="sc-schema" class="rtrs-tab-content" style="' . esc_attr($schema_tab) . '">';
		$html .= rtrs()->render('metas.affiliate.schema', $post, true);
		$html .= '</div>';

		$html .= '<div id="sc-style" class="rtrs-tab-content" style="' . esc_attr($style_tab) . '">';
		$html .= rtrs()->render('metas.affiliate.style', $post, true);
		$html .= '</div>';
		echo $html;

		echo '</div>'; //wrap div
	}

	public function rtrs_single_meta_settings($post) {
		$post = [
			'post' => $post,
		];
		wp_nonce_field(rtrs()->getNonceText(), rtrs()->getNonceId());
		$post_type = $post['post']->post_type;

		//auto select tab
		$tab = get_post_meta(get_the_ID(), '_rtrs_sc_tab', true);

		if (! $tab) {
			$tab = (Functions::isEnableByPostType($post_type)) ? 'review' : 'schema';
		} else {
			if (Functions::isEnableByPostTypeSchema($post_type) && $tab == 'review') {
				$tab = 'schema';
			}
		}

		$review_tab  = ($tab == 'review') ? 'active' : '';
		$schema_tab  = ($tab == 'schema') ? 'active' : '';
		$preview_tab = ($tab == 'preview') ? 'active' : '';

		$html = null;
		$html .= '<div id="sc-tabs" class="rtrs-tab-container">';
		$html .= '<ul class="rtrs-tab-nav">';
		if (Functions::isEnableByPostType($post_type)) {
			$html .= '<li class="' . esc_attr($review_tab) . '"><a href="#sc-review"><i class="dashicons dashicons-star-filled"></i>' . esc_html__('Review', 'review-schema') . '</a></li>';
		}

		if (Functions::isEnableByPostTypeSchema($post_type)) {
			$html .= '<li class="' . esc_attr($schema_tab) . '"><a href="#sc-schema"><i class="dashicons dashicons-editor-table"></i>' . esc_html__('Schema', 'review-schema') . '</a></li>';
		}
		$html .= '</ul>';

		$review_tab  = ($tab == 'review') ? 'display: block' : '';
		$schema_tab  = ($tab == 'schema') ? 'display: block' : '';
		$preview_tab = ($tab == 'preview') ? 'display: block' : '';

		$html .= '<input type="hidden" id="_rtrs_sc_tab" name="_rtrs_sc_tab" value="' . esc_attr($tab) . '" />';
		if (Functions::isEnableByPostType($post_type)) {
			$html .= '<div id="sc-review" class="rtrs-tab-content" style="' . esc_attr($review_tab) . '">';
			$html .= rtrs()->render('metas.single.review', $post, true);
			$html .= '</div>';
		}

		if (Functions::isEnableByPostTypeSchema($post_type)) {
			$html .= '<div id="sc-schema" class="rtrs-tab-content" style="' . esc_attr($schema_tab) . '">';
			$html .= rtrs()->render('metas.single.schema', $post, true);
			$html .= '</div>';
		}

		$html .= '<div id="sc-preview" class="rtrs-tab-content" style="' . esc_attr($preview_tab) . '">';
		$html .= '</div>';

		$html .= '</div>'; //wrap div
		echo $html;
	}

	public function sanitize_field($type, $value) {
		$fValue = '';
		switch ($type) {
			case 'text':
			case 'select':
			case 'tab':
			case 'radio-image':
				$fValue = isset($value) ? sanitize_text_field($value) : null;
				break;

			case 'url':
				$fValue = isset($value) ? esc_url_raw($value) : null;
				break;

			case 'number':
			case 'switch':
			case 'checkbox':
			case 'image':
				$fValue = isset($value) ? absint($value) : null;
				break;

			case 'float':
				$fValue = isset($value) ? floatval($value) : null;
				break;

			case 'gallery':
				$fValue = isset($value) && is_array($value) ? array_map('absint', $value) : null;
				break;

			case 'repeater':
				$fValue = isset($value) && is_array($value) ? array_map('sanitize_text_field', array_filter($value)) : null;
				break;

			case 'color':
				$fValue = isset($value) ? sanitize_hex_color($value) : null;
				break;

			case 'style':
				$fValue = isset($value) ? array_map('sanitize_text_field', $value) : null;
				break;

			default:
				$fValue = isset($value) ? sanitize_text_field($value) : null;
				break;
		}

		return $fValue;
	}

	public function searchArray($value, $key, $array) {
		foreach ($array as $k => $val) {
			if ( !empty( $val[$key] ) && $val[$key] == $value) {
				return $k;
			}
		}

		return null;
	}

	public function save_meta_data($post_id, $post) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		if (! Functions::verify_nonce()) {
			return $post_id;
		}

		$meta_options = null;
		if (rtrs()->getPostType() == $post->post_type) {
			$meta_options = new MetaOptions();
			$meta_options = $meta_options->allMetaFields();
		} elseif ('rtrs_affiliate' == $post->post_type) {
			$meta_options = new AffiliateOptions();
			$meta_options = $meta_options->allMetaFields();
		} else {
			$meta_options = new SingleMetaOptions();
			$meta_options = $meta_options->allMetaFields();

			$selected_schema = [
				$meta_options[0],
				$meta_options[1],
			];

			if (isset($_POST['_rtrs_rich_snippet_cat']) && is_array($_POST['_rtrs_rich_snippet_cat'])) {
				foreach ($_POST['_rtrs_rich_snippet_cat'] as $value) {
					$index = $this->searchArray('rtrs_' . $value . '_schema', 'name', $meta_options);
					if ($index != null) {
						$selected_schema[] = $meta_options[$index];
					}
				}
			}

			$meta_options = $selected_schema;
		}

		foreach ($meta_options as $field) {
			if ($field['type'] == 'heading' || $field['type'] == 'auto-fill') {
				continue;
			}

			if ($field['type'] == 'group') {
				//escape pro field
				if ($field['name'] != 'rating_criteria') {
					if (isset($field['is_pro']) && ! function_exists('rtrsp')) {
						continue;
					}
				}

				//save group field
				$groupValue = [];

				//remove heading type from groups field
				foreach ($field['fields'] as $key => $single_meta) {
					if ($single_meta['type'] == 'heading' || $single_meta['type'] == 'auto-fill') {
						unset($field['fields'][$key]);
					}
				}

				//after remove heading type sort again
				$field['fields'] = array_values($field['fields']);

				foreach ($_REQUEST[$field['name']] as $key => $group_fields) {
					$i = 0;
					foreach ($group_fields as $group_key => $group_field) {
						// if 1st nested group
						if ($field['fields'][$i]['type'] == 'group') {
							foreach ($group_field as $group_two_key => $group_two_field) {
								foreach ($group_two_field as $group_three_key => $group_three_field) {
									$nested_index = array_search($group_three_key, array_column($field['fields'][$i]['fields'], 'name'));
									//if 2nd nested group
									if ($field['fields'][$i]['fields'][$nested_index]['type'] == 'group') {
										foreach ($group_three_field as $group_four_key => $group_four_field) {
											foreach ($group_four_field as $group_five_key => $group_five_field) {
												$second_nested_index                                                                              = array_search($group_five_key, array_column($field['fields'][$i]['fields'][$nested_index]['fields'], 'name'));
												$groupValue[$key][$group_key][$group_two_key][$group_three_key][$group_four_key][$group_five_key] = $this->sanitize_field($field['fields'][$i]['fields'][$nested_index]['fields'][$second_nested_index]['type'], $group_five_field);
											}
										}
									} else {
										$groupValue[$key][$group_key][$group_two_key][$group_three_key] = $this->sanitize_field($field['fields'][$i]['fields'][$nested_index]['type'], $group_three_field);
									}
								}
							}
						} else {
							$groupValue[$key][$group_key] = $this->sanitize_field($field['fields'][$i]['type'], $group_field);
						}
						$i++;
					}
				}

				update_post_meta($post_id, $field['name'], $groupValue);
			} else {
				if (isset($field['multiple'])) {
					if ($field['multiple']) {
						delete_post_meta($post_id, $field['name']);
						$mValueA = isset($_REQUEST[$field['name']]) ? array_map('sanitize_text_field', $_REQUEST[$field['name']]) : [];
						if (is_array($mValueA) && ! empty($mValueA)) {
							foreach ($mValueA as $item) {
								add_post_meta($post_id, $field['name'], trim($item));
							}
						}
					}
				} else {
					//escape pro field
					if (isset($field['is_pro']) && ! function_exists('rtrsp')) {
						continue;
					}

					if (isset($_REQUEST[$field['name']])) {
						$fValue = $this->sanitize_field($field['type'], $_REQUEST[$field['name']]);
						update_post_meta($post_id, $field['name'], $fValue);
					} elseif ($field['type'] == 'switch' || $field['type'] == 'checkbox') {
						update_post_meta($post_id, $field['name'], null);
					}
				}
			}
		}

		// save current tab
		$sc_tab = isset($_REQUEST['_rtrs_sc_tab']) ? sanitize_text_field($_REQUEST['_rtrs_sc_tab']) : '';
		update_post_meta($post_id, '_rtrs_sc_tab', $sc_tab);

		// generate shortcode
		if (rtrs()->getPostType() == $post->post_type) {
			Functions::generatorShortCodeCss($post_id, 'review');
		} elseif ('rtrs_affiliate' == $post->post_type) {
			Functions::generatorShortCodeCss($post_id, 'affiliate');
		}
	} // end function

	/**
	 * Check if post type already exists before save.
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function before_update_post($post_id) {
		$allowed_html = [
			'a' => [
				'href'  => [],
				'title' => [],
			],
			'b' => [],
			'p' => [],
		];

		if (isset($_POST['rtrs_post_type']) && ! $_POST['rtrs_post_type']) {
			wp_die(wp_kses(__('<b>ERROR:</b> Please choose a post type', 'review-schema'), $allowed_html) . "<p><a href='javascript:history.back()'>" . esc_html__('« Back', 'review-schema') . '</a></p>');
		} else {
			$post_type = isset($_POST['rtrs_post_type']) ? sanitize_text_field($_POST['rtrs_post_type']) : '';
			$scPostIds = get_posts([
				'post_type'      => rtrs()->getPostType(),
				'posts_per_page' => -1,
				'post_status'    => ['publish', 'draft'],
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'     => 'rtrs_post_type',
						'value'   => $post_type,
						'compare' => '=',
					],
				],
			]);

			$current_post_type = get_post_meta($post_id, 'rtrs_post_type', true);

			if (($current_post_type != $post_type) && ! empty($scPostIds)) {
				wp_die(wp_kses(__('<b>ERROR:</b> Sorry! this post type already exist, you need to choose new one.', 'review-schema'), $allowed_html) . "<p><a href='javascript:history.back()'>" . esc_html__('« Back', 'review-schema') . '</a></p>');
			}
		}
	}

	/**
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public function before_delete_post($post_id, $post) {
		if (rtrs()->getPostType() == $post->post_type) {
			Functions::removeGeneratorShortCodeCss($post_id, 'review');
		} elseif ('rtrs_affiliate' == $post->post_type) {
			Functions::removeGeneratorShortCodeCss($post_id, 'affiliate');
		}
	}
}
