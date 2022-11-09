<?php

namespace Rtrs\Models;

use Rtrs\Helpers\Functions;

abstract class SettingsAPI {
	/**
	 * The plugin ID. Used for option names.
	 *
	 * @var string
	 */
	public $plugin_id = 'rtrs_';

	/**
	 * ID of the class extending the settings API. Used in option names.
	 *
	 * @var string
	 */
	public $option = '';

	/**
	 * Setting values.
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Form option fields.
	 *
	 * @var array
	 */
	public $form_fields = [];

	/**
	 * The posted settings data. When empty, $_POST data will be used.
	 *
	 * @var array
	 */
	protected $data = [];

	public static $messages = [];

	public static $errors = [];

	/**
	 * Add a message.
	 *
	 * @param string $text
	 */
	public static function add_message($text) {
		self::$messages[] = $text;
	}

	/**
	 * Output messages + errors.
	 */
	public static function show_messages() {
		if (count(self::$errors) > 0) {
			foreach (self::$errors as $error) {
				echo '<div id="message" class="error inline"><p><strong>' . esc_html($error) . '</strong></p></div>';
			}
		} elseif (count(self::$messages) > 0) {
			foreach (self::$messages as $message) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html($message) . '</strong></p></div>';
			}
		}
	}

	/**
	 * Get the form fields after they are initialized.
	 *
	 * @return array of options
	 */
	public function get_form_fields() {
		return apply_filters(
			'rtrs_settings_api_form_fields_' . $this->option,
			array_map([$this, 'set_defaults'], $this->form_fields)
		);
	}

	/**
	 * Set default required properties for each field.
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function set_defaults($field) {
		if (! isset($field['default'])) {
			$field['default'] = '';
		}

		return $field;
	}

	/**
	 * Output the admin options table.
	 */
	public function admin_options() {
		echo '<table class="form-table">' . $this->generate_settings_html($this->get_form_fields()) . '</table>';
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @return string
	 *
	 * @since 2.6.0
	 */
	public function get_option_key() {
		return $this->plugin_id . $this->option;
	}

	/**
	 * Get a fields type. Defaults to "text" if not set.
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function get_field_type($field) {
		return empty($field['type']) ? 'text' : $field['type'];
	}

	/**
	 * Get a fields default value. Defaults to "" if not set.
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function get_field_default($field) {
		return empty($field['default']) ? '' : $field['default'];
	}

	/**
	 * Get a field's posted and validated value.
	 *
	 * @param string $key
	 * @param array $field
	 * @param array $post_data
	 *
	 * @return string
	 */
	public function get_field_value($key, $field, $post_data = []) {
		$type = $this->get_field_type($field);

		// Note: this $_POST data satitize by following function, after the lines
		$post_data = empty($post_data) ? (! empty($_POST[$this->get_option_key()]) ? ($_POST[$this->get_option_key()]) : []) : $post_data;
		$value     = isset($post_data[$key]) ? $post_data[$key] : null;

		// Look for a validate_FIELDID_field method for special handling
		if (is_callable([$this, 'validate_' . $key . '_field'])) {
			return $this->{'validate_' . $key . '_field'}($key, $value);
		}

		// Look for a validate_FIELDTYPE_field method
		if (is_callable([$this, 'validate_' . $type . '_field'])) {
			return $this->{'validate_' . $type . '_field'}($key, $value);
		}

		// Fallback to text
		return $this->validate_text_field($key, $value);
	}

	/**
	 * Sets the POSTed data. This method can be used to set specific data, instead
	 * of taking it from the $_POST array.
	 *
	 * @param array data
	 */
	public function set_post_data($data = []) {
		$this->data = $data;
	}

	/**
	 * Returns the POSTed data, to be used to dd the settings.
	 *
	 * @return array
	 */
	public function get_post_data() {
		if (! empty($this->data) && is_array($this->data)) {
			return $this->data;
		}

		return isset($_POST[$this->get_option_key()]) ? ($_POST[$this->get_option_key()]) : [];
	}

	/**
	 * Processes and dds options.
	 * If there is an error thrown, will continue to dd and validate fields, but will leave the erroring field out.
	 *
	 * @return bool was anything ddd?
	 */
	public function process_admin_options() {
		$this->init_settings();

		$post_data = $this->get_post_data();

		foreach ($this->get_form_fields() as $key => $field) {
			if ('title' !== $this->get_field_type($field)) {
				try {
					$this->settings[$key] = $this->get_field_value($key, $field, $post_data);
				} catch (\Exception $e) {
					self::add_error($e->getMessage());
				}
			} else {
				unset($this->settings[$key]);
			}
		}

		$sanitized_new_settings = apply_filters('rtrs_settings_api_sanitized_fields_' . $this->option, $this->settings, $this);
		do_action('rtrs_admin_settings_before_ddd_' . $this->option, $sanitized_new_settings, Functions::get_option($this->get_option_key()), $this);

		return update_option($this->get_option_key(), $sanitized_new_settings);
	}

	/**
	 * Add an error message for display in admin on dd.
	 *
	 * @param string $error
	 */
	public static function add_error($error) {
		self::$errors[] = $error;
	}

	/**
	 * Get admin error messages.
	 */
	public function get_errors() {
		return self::$errors;
	}

	/**
	 * Display admin error messages.
	 */
	public function display_errors() {
		if ($this->get_errors()) {
			echo '<div class="error notice is-dismissible">';
			foreach ($this->get_errors() as $error) {
				echo '<p>' . wp_kses_post($error) . '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Initialise Settings.
	 *
	 * Store all settings in a single database entry
	 * and make sure the $settings array is either the default
	 * or the settings stored in the database.
	 *
	 * @since 1.0.0
	 *
	 * @uses  get_option(), add_option()
	 */
	public function init_settings() {
		$this->settings = get_option($this->get_option_key(), null);

		// If there are no settings defined, use defaults.
		if (! is_array($this->settings)) {
			$form_fields    = $this->get_form_fields();
			$this->settings = array_merge(
				array_fill_keys(array_keys($form_fields), ''),
				wp_list_pluck($form_fields, 'default')
			);
		}
	}

	/**
	 * get_option function.
	 *
	 * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param string $key
	 * @param mixed $empty_value
	 *
	 * @return string The value specified for the option or a default value for the option.
	 */
	public function get_option($key, $empty_value = null, $group_name = null, $index = null) {
		if (empty($this->settings)) {
			$this->init_settings();
		}

		// Get option default if unset.
		if (! isset($this->settings[$key])) {
			$form_fields  = $this->get_form_fields();
			if ($group_name) {
				return isset($this->settings[$group_name][$index][$key]) ? $this->settings[$group_name][$index][$key] : '';
			} else {
				$this->settings[$key] = isset($form_fields[$key]) ? $this->get_field_default($form_fields[$key]) : '';
			}
		}

		if (! is_null($empty_value) && '' === $this->settings[$key]) {
			$this->settings[$key] = $empty_value;
		}

		return $this->settings[$key];
	}

	/**
	 * Prefix key for settings.
	 *
	 * @param mixed $key
	 *
	 * @return string
	 */
	public function get_field_key($key, $group_name = null, $index = null) {
		if ($group_name) {
			return $this->plugin_id . $this->option . '[' . $group_name . '][' . $index . '][' . $key . ']';
		} else {
			return $this->plugin_id . $this->option . '[' . $key . ']';
		}
	}

	public function get_field_id($key) {
		return $this->plugin_id . $this->option . '-' . $key;
	}

	public function get_group_id($key) {
		return $this->plugin_id . $this->option . '[' . $key . ']';
	}

	/**
	 * Generate Settings HTML.
	 *
	 * Generate the HTML for the fields on the "settings" screen.
	 *
	 * @param array $form_fields (default: array())
	 * @param bool $echo
	 *
	 * @return string the html for the settings
	 *
	 * @since  1.0.0
	 *
	 * @uses   method_exists()
	 */
	public function generate_settings_html($form_fields = []) {
		if (empty($form_fields)) {
			$form_fields = $this->get_form_fields();
		}

		$html = '';
		foreach ($form_fields as $k => $v) {
			$type = $this->get_field_type($v);

			if (method_exists($this, 'generate_' . $type . '_html')) {
				$html .= $this->{'generate_' . $type . '_html'}($k, $v);
			} else {
				$html .= $this->generate_text_html($k, $v);
			}
		}

		return $html;
	}

	/**
	 * Get HTML for tooltips.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_tooltip_html($data) {
		if (true === $data['desc_tip']) {
			$tip = esc_html($data['description']);
		} elseif (! empty($data['desc_tip'])) {
			$tip = esc_html($data['desc_tip']);
		} else {
			$tip = '';
		}

		return $tip;
	}

	/**
	 * Get HTML for descriptions.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_description_html($data) {
		if (true === $data['desc_tip']) {
			$description = '';
		} elseif (! empty($data['desc_tip'])) {
			$description = $data['description'];
		} elseif (! empty($data['description'])) {
			$description = $data['description'];
		} else {
			$description = '';
		}

		return $description ? '<p class="description">' . wp_kses_post($description) . '</p>' . "\n" : '';
	}

	/**
	 * Get custom attributes.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_custom_attribute_html($data) {
		$custom_attributes = [];

		if (! empty($data['custom_attributes']) && is_array($data['custom_attributes'])) {
			foreach ($data['custom_attributes'] as $attribute => $attribute_value) {
				$custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
			}
		}

		return implode(' ', $custom_attributes);
	}

	/**
	 * Generate Text Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_text_html($key, $data, $group_name = null, $index = null) {
		$field_key     = $this->get_field_key($key, $group_name, $index);
		$id            = $this->get_field_id($key);
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <input class="input-text regular-input <?php echo esc_attr($data['class']); ?>"
                           type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field_key); ?>"
                           id="<?php echo esc_attr($id); ?>" style="<?php echo esc_attr($data['css']); ?>"
                           value="<?php echo esc_attr($this->get_option($key, null, $group_name, $index)); ?>"
                           placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?> />
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate group Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_group_html($key, $data) {
		$field_key     = $this->get_field_key($key);
		$id            = $this->get_field_id($key);

		$defaults        = $this->get_placeholder_data();
		$data            = wp_parse_args($data, $defaults);
		$wrapper_class   = implode(' ', [$id, $data['wrapper_class']]);
		$depends         = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		ob_start();

		$form_fields = $data['fields'];

		$is_pro = (isset($data['is_pro']) && $data['is_pro'] == true);

		$group_name = $this->get_group_id($key);

		$loop_count = 1;
		if ($this->get_option($key)) {
			$loop_count = count($this->get_option($key));
		}

		$html = '</table><div id=' . esc_attr($id) . '>';
		for ($i=0; $i < $loop_count; $i++) {
			$html .= '<div class="rtrs-setting-group-wrap"><table class="form-table ">';
			foreach ($form_fields as $k => $v) {
				$type = $this->get_field_type($v);

				if (method_exists($this, 'generate_' . $type . '_html')) {
					$html .= $this->{'generate_' . $type . '_html'}($k, $v, $key, $i);
				} else {
					$html .= $this->generate_text_html($k, $v);
				}
			}
			$html .= '</table><i data-id=' . esc_attr($id) . ' data-name=' . esc_attr($group_name) . ' class="rtrs-group-remove dashicons dashicons-dismiss"></i></div>';
		}
		$html .= '</div>';

		$pro_label = $data_pro = '';
		if ($is_pro && ! function_exists('rtrsp')) {
			$data_pro  = 'yes';
			$pro_label = '<span class="rtrs-pro">' . esc_html__('[Pro]', 'review-schema') . '</span>';
			$pro_label = apply_filters('rtrs_pro_label', $pro_label);
		}

		$html .= '<div class="rtrs-group-add"><a href="#" data-pro="' . esc_attr($data_pro) . '" data-id="' . esc_attr($id) . '" data-name="' . esc_attr($group_name) . '" class="button button-primary">' . esc_html__('Add New', 'review-schema') . ' ' . $data['title'] . '</a>' . wp_kses($pro_label, ['span' => ['class' => []]]) . '</div><table class="form-table">';

		echo $html;

		return ob_get_clean();
	}

	public function generate_map_center_html($key, $data) {
		$field_key     = $this->get_field_key($key);
		$id            = $this->get_field_id($key);
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		$latLng        = wp_parse_args($this->get_option($key), [
			'address' => '',
			'lat'     => 0,
			'lng'     => 0,
		]);
		ob_start(); ?>
		<tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo $depends; ?>>
			<th scope="row" class="title-desc">
				<?php echo $this->get_tooltip_html($data); ?>
				<label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
			</th>
			<td class="form-input">
				<div class="rtcl-geo-address-field" style="max-width: 400px">
					<input id="<?php echo esc_attr($id); ?>" type="text"
						   name="<?php echo esc_attr($field_key); ?>[address]" autocomplete="off"
						   value="<?php echo esc_attr($latLng['address']); ?>"
						   placeholder="Select a location"
						   class="form-control rtcl-geo-address-input rtcl_geo_address_input"
						   aria-invalid="false">
					<i class="rtcl-get-location rtcl-icon rtcl-icon-target rtcl_geo_address_location"></i>
					<input type="hidden" class="latitude" name="<?php echo esc_attr($field_key); ?>[lat]"
						   value="<?php echo esc_attr($latLng['lat']); ?>">
					<input type="hidden" class="longitude" name="<?php echo esc_attr($field_key); ?>[lng]"
						   value="<?php echo esc_attr($latLng['lng']); ?>">
				</div>
				<div class="rtcl-map-wrap">
					<div class="rtcl-map" data-type="input">
						<div class="marker" data-latitude="<?php echo esc_attr($latLng['lat']); ?>"
							 data-longitude="<?php echo esc_attr($latLng['lng']); ?>"></div>
					</div>
				</div>
				<?php echo $this->get_description_html($data); ?>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate group Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_auto_schema_html($key, $data) {
		$field_key     = $this->get_field_key($key);
		$id            = $this->get_field_id($key);
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		if (! $data['label']) {
			$data['label'] = $data['title'];
		}

		$values      = $this->get_option($key);

		$new_default = [];
		$post_type   = Functions::getPostTypes(false, false);
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
		$values = is_array($values) ? $values : $new_default;

		$query = new \WP_Query(
			[
				'posts_per_page' => -1,
				'post_type'      => rtrs()->getPostType(),
				'post_status'    => 'publish',
			]
		);
		$custom_review_schema = [];
		while ($query->have_posts()): $query->the_post();
		$custom_review_schema[get_the_ID()] = get_post_meta(get_the_ID(), 'rtrs_post_type', true);
		endwhile;
		wp_reset_postdata();

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
					<style>
						.rtrs-auto-schema td {
							padding: 3px 10px 5px 0px;
						}
					</style>
					<table class="rtrs-auto-schema"> 
						<tr> 
							<td><strong><?php esc_html_e('Post Type', 'review-schema'); ?></strong></td>
							<td><strong><?php esc_html_e('Schema Type', 'review-schema'); ?></strong></td>
							<td><strong><?php esc_html_e('Overridden?', 'review-schema'); ?></strong></td> 
						</tr>
						<?php
							$i = 0;
		foreach ((array) $data['options'] as $option_key => $option_value) :
																	$pro_field = $disabled =false;

		if (in_array($option_key, $custom_review_schema)) {
			$disabled = true;
		}

		if (
																			(
																				$option_key == 'product' ||
																				$option_key == 'download'
																			)
																			&&
																			! function_exists('rtrsp')
																		) {
			$pro_field = true;
		} ?>
						<tr class="<?php if ($pro_field) {
			echo 'schema-pro-field';
		} ?>">  
							<td>
								<label for="<?php echo esc_attr($id . '-' . $option_key); ?>"> 
									<input <?php if ($pro_field || $disabled) {
			echo 'disabled';
		} ?> class="<?php echo esc_attr($data['class']); ?>" type="checkbox"
										name="<?php echo esc_attr($field_key); ?>[<?php echo esc_attr($i); ?>][post_type]"
										id="<?php echo esc_attr($id . '-' . $option_key); ?>"
										value="<?php echo esc_attr($option_key); ?>"
										<?php checked(isset($values[$i]['post_type'])); ?> />
									<?php
									echo  $option_value;

		if ($pro_field) {
			echo ' <span class="rtrs-pro">[Pro]</span>';
		} ?>  
								</label> 
							</td> 
							<td>
								<select <?php if ($pro_field || $disabled) {
			echo 'disabled';
		} ?> name="<?php echo esc_attr($field_key); ?>[<?php echo esc_attr($i); ?>][schema_type]">
									<option value=""><?php esc_html_e('Select', 'review-schema'); ?></option>
									<?php
									foreach (Functions::rich_snippet_cats() as $key => $value) {
										$selected = isset($values[$i]['schema_type']) && $values[$i]['schema_type'] == $key ? 'selected' : '';
										printf(
											'<option value="%1$s" %3$s>%2$s</option>',
											$key,
											$value,
											$selected
										);
									} ?> 
								</select>
							</td>
							<td>
								<span title="<?php esc_attr_e('From Review Schema Generator', 'review-schema'); ?>">
								<?php
									if ($key = array_search($option_key, $custom_review_schema)) {
										esc_html_e('Yes', 'review-schema');
										printf(
											'&nbsp;&nbsp;<a target="_blank" href="%s">%s</a>',
											get_edit_post_link($key),
											esc_html__('(Edit)', 'review-schema')
										);
									} ?>
								</span>
							</td>
						</tr>
						<?php
						$i++;
		endforeach; ?>
					</table>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Text Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_button_html($key, $data) {
		$field_key     = $key;
		$id            = $this->get_field_id($key);
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <input class="button rtrs-import-schema <?php echo esc_attr($data['class']); ?>"
                           type="<?php echo esc_attr($data['type']); ?>" data-id="<?php echo esc_attr($field_key); ?>"
                           style="<?php echo esc_attr($data['css']); ?>"
                           value="<?php esc_attr_e('Import', 'review-schema'); ?>" <?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?> /><span class="rtrs-import-loader"><i class="dashicons dashicons-update spin"></i></span>
						<div class="rtrs-import-info"></div>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	public function generate_image_size_html($key, $data) {
		$field_key     = $this->get_field_key($key);
		$id            = $this->get_field_id($key);
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		$size          = $this->get_option($key);
		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input rtrs-image-size-wrap">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
					<?php foreach ((array) $data['options'] as $option_key => $option_value) : ?>
                        <div class='rtrs-image-size-item'>
							<?php
							if ($option_key == 'crop'): ?>
                                <label for="<?php echo esc_attr($id) . '-' . $option_key; ?>">
                                    <input type="checkbox"
                                           name="<?php echo esc_attr($field_key); ?>[<?php echo esc_attr($option_key); ?>]"
                                           id="<?php echo esc_attr($id) . '-' . $option_key; ?>"
                                           value="yes" <?php checked(isset($size[$option_key]) ? $size[$option_key] : null, 'yes'); ?> />
									<?php echo wp_kses_post($option_value); ?>
                                </label><br/>
							<?php else:
								$value = ! empty($size[$option_key]) ? absint(esc_attr($size[$option_key])) : null; ?>
                                <label for='<?php echo esc_attr($id) . '-' . $option_key; ?>'><?php echo wp_kses_post($option_value); ?></label>
                                <input type='number'
                                       name='<?php echo esc_attr($field_key); ?>[<?php echo esc_attr($option_key); ?>]'
                                       id="<?php echo esc_attr($id) . '-' . $option_key; ?>"
                                       value="<?php echo esc_attr($value); ?>"
                                />
							<?php endif; ?>
                        </div>
					<?php endforeach; ?>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	public function generate_image_html($key, $data) {
		$field_key       = $this->get_field_key($key);
		$id              = $this->get_field_id($key);
		$defaults        = $this->get_placeholder_data();
		$data            = wp_parse_args($data, $defaults);
		$wrapper_class   = implode(' ', [$id, $data['wrapper_class']]);
		$depends         = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		$value           = absint($this->get_option($key));
		$placeholder_url = Functions::get_default_placeholder_url();
		$image_src       = $value ? wp_get_attachment_thumb_url($value) : $placeholder_url;
		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <div class="rtrs-setting-image-wrap">
                        <input type="hidden" id="<?php echo esc_attr($id); ?>" class="rtrs-setting-image-id"
                               value="<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($field_key); ?>"/>
                        <div class="image-preview-wrapper"
                             data-placeholder="<?php echo esc_url($placeholder_url); ?>">
                            <img src="<?php echo esc_url($image_src); ?>"/>
                        </div>
                        <input type="button" class="button button-secondary rtrs-add-image"
                               value="<?php esc_attr_e('Add Image', 'review-schema'); ?>"/>

						<?php if ($value) { ?>
							<input type="button" class="button button-secondary rtrs-remove-image"
                               value="<?php esc_attr_e('Remove Image', 'review-schema'); ?>"/>
						<?php } ?>

                    </div>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Password Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_password_html($key, $data) {
		$data['type'] = 'password';

		return $this->generate_text_html($key, $data);
	}

	/**
	 * Generate Color Picker Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_color_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <input class="rtrs-color <?php echo esc_attr($data['class']); ?>" type="text"
                           name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($id); ?>"
                           style="<?php echo esc_attr($data['css']); ?>"
                           value="<?php echo esc_attr($this->get_option($key)); ?>"
						<?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?> />
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Textarea HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_textarea_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <textarea rows="4" cols="20" class="input-text wide-input <?php echo esc_attr($data['class']); ?>"
                              type="<?php echo esc_attr($data['type']); ?>"
                              name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($id); ?>"
                              style="<?php echo esc_attr($data['css']); ?>"
                              placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?>><?php echo esc_textarea($this->get_option($key)); ?></textarea>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	public function generate_wysiwyg_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
					<?php
					wp_editor(
			htmlspecialchars_decode($this->get_option($key)),
			$id,
			[
							'textarea_name' => esc_attr($field_key),
							'media_buttons' => false,
							'quicktags'     => true,
							'editor_height' => 250,
						]
		);

		echo '<pre>' . $this->get_description_html($data) . '</pre>'; ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Checkbox HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_checkbox_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		if (! $data['label']) {
			$data['label'] = $data['title'];
		}

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <label for="<?php echo esc_attr($id); ?>">
                        <input <?php disabled($data['disabled'], true); ?>
                                class="<?php echo esc_attr($data['class']); ?>" type="checkbox"
                                name="<?php echo esc_attr($field_key); ?>"
                                id="<?php echo esc_attr($id); ?>"
                                style="<?php echo esc_attr($data['css']); ?>"
                                value="yes" <?php checked(
			$this->get_option($key),
			'yes'
		); ?> <?php echo $this->get_custom_attribute_html($data); ?> /> <?php echo wp_kses_post($data['label']); ?>
                    </label><br/>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Checkbox HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_multi_checkbox_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		if (! $data['label']) {
			$data['label'] = $data['title'];
		}
		$values = $this->get_option($key);
		$values = is_array($values) ? $values : [];
		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
					<?php foreach ((array) $data['options'] as $option_key => $option_value) : ?>
                        <label for="<?php echo esc_attr($id . '-' . $option_key); ?>">
                            <input class="<?php echo esc_attr($data['class']); ?>" type="checkbox"
                                   name="<?php echo esc_attr($field_key); ?>[]"
                                   id="<?php echo esc_attr($id . '-' . $option_key); ?>"
                                   value="<?php echo esc_attr($option_key); ?>"
								<?php checked(in_array($option_key, $values)); ?> />
							<?php echo  $option_value; ?>
                        </label><br/>
					<?php endforeach; ?>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Select HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_select_html($key, $data, $group_name = null, $index = null) {
		$field_key = $this->get_field_key($key, $group_name, $index);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <select class="select <?php echo esc_attr($data['class']); ?>"
                            name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($id); ?>"
                            style="<?php echo esc_attr($data['css']); ?>" <?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?>>
						<?php if (! empty($data['blank'])): ?>
                            <option value="<?php echo esc_attr($data['blank_value']); ?>"><?php echo esc_html($data['blank_text']); ?></option>
						<?php endif; ?>
						<?php foreach ((array) $data['options'] as $option_key => $option_value) : ?>
                            <option value="<?php echo esc_attr($option_key); ?>" <?php selected(
			$option_key,
			esc_attr($this->get_option($key, null, $group_name, $index))
		); ?>><?php echo esc_html($option_value); ?></option>
						<?php endforeach; ?>
                    </select>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Select HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_schema_type_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <select class="select <?php echo esc_attr($data['class']); ?>"
                            name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($id); ?>"
                            style="<?php echo esc_attr($data['css']); ?>" <?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?>>
						<?php if (! empty($data['blank'])): ?>
                            <option value="<?php echo esc_attr($data['blank_value']); ?>"><?php echo esc_html($data['blank_text']); ?></option>
						<?php endif; ?>
						<?php
							$siteType = ! empty($this->get_option($key)) ? $this->get_option($key) : null;
		foreach ($data['options'] as $key => $site) {
			if (is_array($site)) {
				$slt = ($key == $siteType ? 'selected' : null);
				echo "<option value='" . esc_attr($key) . "' " . esc_attr($slt) . '>&nbsp;&nbsp;&nbsp;' . esc_html($key) . '</option>';
				foreach ($site as $inKey => $inSite) {
					if (is_array($inSite)) {
						$slt = ($inKey == $siteType ? 'selected' : null);
						echo "<option value='" . esc_attr($inKey) . "' " . esc_attr($slt) . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . esc_attr($inKey) . '</option>';
						foreach ($inSite as $inInKey => $inInSite) {
							if (is_array($inInSite)) {
								$slt = ($inInKey == $siteType ? 'selected' : null);
								echo "<option value='" . esc_attr($inInKey) . "' " . esc_attr($slt) . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html($inInKey) . '</option>';
								foreach ($inInSite as $iSite) {
									$slt = ($iSite == $siteType ? 'selected' : null);
									echo "<option value='" . esc_attr($iSite) . "' " . esc_attr($slt) . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html($iSite) . '</option>';
								}
							} else {
								$slt = ($inInSite == $siteType ? 'selected' : null);
								echo "<option value='" . esc_attr($inInSite) . "' " . esc_attr($slt) . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html($inInSite) . '</option>';
							}
						}
					} else {
						$slt = ($inSite == $siteType ? 'selected' : null);
						echo "<option value='" . esc_attr($inSite) . "' " . esc_attr($slt) . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html($inSite) . '</option>';
					}
				}
			} else {
				$slt = ($site == $siteType ? 'selected' : null);
				echo "<option value='" . esc_attr($site) . "' " . esc_attr($slt) . '>' . esc_html($site) . '</option>';
			}
		} ?>
                    </select>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Radio HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_radio_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
					<?php foreach ((array) $data['options'] as $option_key => $option_value) : ?>
                        <label><input type="radio" name="<?php echo esc_attr($field_key); ?>"
                                      value="<?php echo esc_attr($option_key) ?>"
								<?php checked(
			$option_key,
			$this->get_option($key)
		) ?> > <?php echo wp_kses_post($option_value) ?></label>
                        <br>
					<?php endforeach; ?>
					<?php echo $this->get_description_html($data); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Multiselect HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_multiselect_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args($data, $defaults);
		$wrapper_class = implode(' ', [$id, $data['wrapper_class']]);
		$depends       = empty($data['dependency']) ? '' : "data-rt-depends='" . wp_json_encode($data['dependency']) . "'";
		$value         = (array) $this->get_option($key, []);

		ob_start(); ?>
        <tr valign="top" class="<?php echo esc_attr($wrapper_class); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html($this->get_tooltip_html($data)); ?>
                <label for="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                    </legend>
                    <select multiple="multiple" class="multiselect <?php echo esc_attr($data['class']); ?>"
                            name="<?php echo esc_attr($field_key); ?>[]" id="<?php echo esc_attr($id); ?>"
                            style="<?php echo esc_attr($data['css']); ?>" <?php disabled(
			$data['disabled'],
			true
		); ?> <?php echo $this->get_custom_attribute_html($data); ?>>
						<?php foreach ((array) $data['options'] as $option_key => $option_value) : ?>
                            <option value="<?php echo esc_attr($option_key); ?>" <?php selected(in_array(
			$option_key,
			$value
		), true); ?>><?php echo esc_html($option_value); ?></option>
						<?php endforeach; ?>
                    </select>
					<?php echo $this->get_description_html($data); ?>
					<?php if ($data['select_buttons']) : ?>
                        <br/><a class="select_all button"
                                href="#"><?php esc_html_e('Select all', 'review-schema'); ?></a> <a
                                class="select_none button"
                                href="#"><?php esc_html_e('Select none', 'review-schema'); ?></a>
					<?php endif; ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Title HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function generate_title_html($key, $data) {
		$field_key = $this->get_field_key($key);
		$id        = $this->get_field_id($key);
		$defaults  = [
			'title' => '',
			'class' => '',
		];

		$data = wp_parse_args($data, $defaults);

		ob_start(); ?>
        </table>
        <h3 class="rtrs-settings-sub-title <?php echo esc_attr($data['class']); ?>"
            id="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($data['title']); ?></h3>
		<?php if (! empty($data['description'])) : ?>
            <p><?php echo wp_kses_post($data['description']); ?></p>
		<?php endif; ?>
        <table class="form-table">
		<?php

		return ob_get_clean();
	}

	/**
	 * Validate Text Field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 *
	 * @param string $key Field key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_text_field($key, $value) {
		$value = is_null($value) ? '' : $value;
		if (is_array($value)) {
			return $value;
		}

		return wp_kses_post(trim(stripslashes($value)));
	}

	/**
	 * Validate Password Field. No input sanitization is used to avoid corrupting passwords.
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_password_field($key, $value) {
		$value = is_null($value) ? '' : $value;

		return trim(stripslashes($value));
	}

	/**
	 * Validate Textarea Field.
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_textarea_field($key, $value) {
		$value = is_null($value) ? '' : $value;

		return wp_kses(
			trim(stripslashes($value)),
			array_merge(
				[
					'iframe' => ['src' => true, 'style' => true, 'id' => true, 'class' => true],
				],
				wp_kses_allowed_html('post')
			)
		);
	}

	public function validate_wysiwyg_field($key, $value) {
		$value = is_null($value) ? '' : $value;

		return wp_kses(
			trim(stripslashes($value)),
			array_merge(
				[
					'iframe' => ['src' => true, 'style' => true, 'id' => true, 'class' => true],
				],
				wp_kses_allowed_html('post')
			)
		);
	}

	/**
	 * Validate Checkbox Field.
	 *
	 * If not set, return "no", otherwise return "yes".
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_checkbox_field($key, $value) {
		return ! is_null($value) ? 'yes' : 'no';
	}

	/**
	 * Validate Select Field.
	 *
	 * @param string $key
	 * @param string $value Posted Value
	 *
	 * @return string
	 */
	public function validate_select_field($key, $value) {
		$value = is_null($value) ? '' : $value;

		return Functions::clean(stripslashes($value));
	}

	/**
	 * Validate Multiselect Field.
	 *
	 * @param string $key
	 * @param string $value Posted Value
	 *
	 * @return string|array
	 */
	public function validate_multiselect_field($key, $value) {
		return is_array($value) ? array_map(
			[Functions::class, 'clean'],
			array_map('stripslashes', $value)
		) : '';
	}

	public function validate_image_size_field($key, $value) {
		return is_array($value) ? array_map(
			[Functions::class, 'clean'],
			array_map('stripslashes', $value)
		) : '';
	}

	public function validate_image_field($key, $value) {
		return $value = is_null($value) ? '' : absint($value);
	}

	/**
	 * Validate Multiselect Field.
	 *
	 * @param string $key
	 * @param string $value Posted Value
	 *
	 * @return string|array
	 */
	public function validate_multi_checkbox_field($key, $value) {
		return is_array($value) ? array_map(
			[Functions::class, 'clean'],
			array_map('stripslashes', $value)
		) : '';
	}

	private function get_placeholder_data() {
		return [
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'blank'             => true,
			'blank_text'        => esc_html__('Select one', 'review-schema'),
			'blank_value'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => [],
			'wrapper_class'     => '',
			'options'           => [],
			'select_buttons'    => false,
			'dependency'        => '',
		];
	}
}
