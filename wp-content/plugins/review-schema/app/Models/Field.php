<?php

namespace Rtrs\Models;

use Rtrs\Helpers\Functions;

class Field {
	private $type;

	private $name;

	private $value;

	private $default;

	private $label;

	private $id;

	private $is_pro;

	private $doc;

	private $data;

	private $accordion;

	private $has_prefix;

	private $class;

	private $holderClass;

	private $desc;

	private $options;

	private $fields;

	private $option;

	private $attr;

	private $multiple;

	private $duplicate;

	private $alignment;

	private $placeholder;

	private $required;

	private $recommended;

	public function __construct() {
	}

	private function setArgument($attr, $group, $index) {
		$this->type     = isset($attr['type']) ? ($attr['type'] ? sanitize_text_field($attr['type']) : 'text') : 'text';
		$this->multiple = isset($attr['multiple']) ? ($attr['multiple'] ? sanitize_text_field($attr['multiple']) : false) : false;

		$this->duplicate = isset($attr['duplicate']) && $attr['duplicate'] == false ? false : true;

		$this->name = isset($attr['name']) ? ($attr['name'] ? sanitize_text_field($attr['name']) : null) : null;
		$name       = $this->name;
		$this->name = $group ? $group . '[' . $index . ']' . '[' . $this->name . ']' : $this->name;

		$this->default = isset($attr['default']) ? ($attr['default'] ? $attr['default'] : null) : null;
		$this->value   = isset($attr['value']) ? ($attr['value'] ? $attr['value'] : null) : null;

		if (! $this->value) {
			if ($this->multiple) {
				$v = get_post_meta(get_the_ID(), $this->name);
			} else {
				if ($group) {
					$field_name = explode('[', $this->name);
					$v          = get_post_meta(get_the_ID(), $field_name[0], true);

					preg_match_all('#\[(.*?)\]#', $this->name, $match);

					$match_fields = $match[1];

					foreach ($match_fields as $arg) {
						if (! empty($v[$arg])) {
							$v = $v[$arg];
						} else {
							$v = null;
						}
					}
				} else {
					$v = get_post_meta(get_the_ID(), $this->name, true);
				}
			}
			$this->value = ($v ? $v : $this->default);
		}

		$this->label = isset($attr['label']) ? ($attr['label'] ? sanitize_text_field($attr['label']) : null) : null;

		$this_name_id = str_replace('][', '_', $this->name);
		$this_name_id = str_replace('[', '_', $this_name_id);
		$this_name_id = str_replace(']', '', $this_name_id);

		$this->id          = isset($attr['id']) ? ($attr['id'] ? sanitize_text_field($attr['id']) : null) : $this_name_id;
		$this->is_pro      = isset($attr['is_pro']) ? ($attr['is_pro'] ? sanitize_text_field($attr['is_pro']) : null) : null;
		$this->doc         = isset($attr['doc']) ? ($attr['doc'] ? sanitize_text_field($attr['doc']) : null) : null;
		$this->data        = isset($attr['data']) ? ($attr['data'] ? sanitize_text_field($attr['data']) : null) : null;
		$this->required    = isset($attr['required']) ? ($attr['required'] ? sanitize_text_field($attr['required']) : null) : null;
		$this->recommended = isset($attr['recommended']) ? ($attr['recommended'] ? sanitize_text_field($attr['recommended']) : null) : null;
		$this->accordion   = isset($attr['accordion']) ? ($attr['accordion'] ? sanitize_text_field($attr['accordion']) : null) : null;
		if (function_exists('rtrsp')) {
			$this->is_pro = false;
		}
		$this->has_prefix  = isset($attr['has_prefix']) ? ($attr['has_prefix'] ? rest_sanitize_boolean($attr['has_prefix']) : null) : null;
		$this->class       = isset($attr['class']) ? ($attr['class'] ? sanitize_text_field($attr['class']) : null) : null;
		$this->holderClass = isset($attr['holderClass']) ? ($attr['holderClass'] ? sanitize_text_field($attr['holderClass']) : null) : null;
		$this->placeholder = isset($attr['placeholder']) ? ($attr['placeholder'] ? sanitize_text_field($attr['placeholder']) : null) : null;
		$this->desc        = isset($attr['desc']) ? ($attr['desc'] ? ($attr['desc']) : null) : null;
		$this->options     = isset($attr['options']) ? ($attr['options'] ? $attr['options'] : []) : [];
		$this->fields      = isset($attr['fields']) ? ($attr['fields'] ? $attr['fields'] : []) : [];
		$this->option      = isset($attr['option']) ? ($attr['option'] ? sanitize_text_field($attr['option']) : null) : null;
		$this->attr        = isset($attr['attr']) ? ($attr['attr'] ? sanitize_text_field($attr['attr']) : null) : null;
		$this->alignment   = isset($attr['alignment']) ? ($attr['alignment'] ? sanitize_text_field($attr['alignment']) : null) : null;
		$this->class       = $this->class ? sanitize_text_field($this->class) . ' rt-form-control' : 'rt-form-control';
	}

	public function Field($attr, $group = null, $index = null) {
		$this->setArgument($attr, $group, $index);
		$holderId  = $this->name . '_holder';
		$html      = null;
		$pro_label = $this->is_pro ? '<span class="rtrs-pro rtrs-tooltip">' . esc_html__('[Pro]', 'review-schema') . '<span class="rtrs-tooltiptext">' . esc_html__('This is premium field', 'review-schema') . '</span></span>' : '';

		$tooltip = $this->doc ? '<div class="rtrs-tooltip rtrs-tooltip-doc"> 
                <i class="dashicons dashicons-editor-help">
                    <span class="rtrs-tooltiptext">' . $this->doc . '</span>
                </i></div>' : '';

		$required = $this->required ? '<div class="rtrs-tooltip rtrs-tooltip-required"> 
                <i class="dashicons dashicons-star-filled">
                    <span class="rtrs-tooltiptext">' . esc_html__('Required', 'review-schema') . '</span>
                </i></div>' : '';

		$recommended = $this->recommended ? '<div class="rtrs-tooltip rtrs-tooltip-recommended"> 
                <i class="dashicons dashicons-star-filled">
                    <span class="rtrs-tooltiptext">' . esc_html__('Recommended', 'review-schema') . '</span>
                </i></div>' : '';

		$pro_class = $this->is_pro && ($this->id != 'rtrs-affiliate_criteria') ? 'pro-field' : '';
		$pro_label = apply_filters('rtrs_pro_label', $pro_label);
		if ($this->name == 'rating_criteria') {
			$pro_label = '';
		}

		//TODO: do it later
		$hidden = ($this->name == 'recommendation' || $this->name == 'highlight_review') ? 'rtrs-hidden' : '';

		$html .= sprintf("<div class='rtrs-field-wrapper %s %s' id='%s'>", esc_attr($hidden), esc_attr($this->holderClass), esc_attr($holderId));
		$html .= sprintf(
			'<div class="rtrs-label">%s</div>',
			$this->label ? sprintf(
				'<label for="">%s %s %s %s %s</label>',
				esc_html($this->label),
				wp_kses($pro_label, ['div' => ['class' => []], 'span' => ['class' => []]]),
				wp_kses($tooltip, ['div' => ['class' => []], 'i' => ['class' => []], 'span' => ['class' => []]]),
				wp_kses($required, ['div' => ['class' => []], 'i' => ['class' => []], 'span' => ['class' => []]]),
				wp_kses($recommended, ['div' => ['class' => []], 'i' => ['class' => []], 'span' => ['class' => []]])
			)
			: ''
		);

		$html .= "<div class='rtrs-field " . $pro_class . "'>";
		$html .= ($this->is_pro) && ! function_exists('rtrsp') && ($this->id != 'rtrs-affiliate_criteria') ? '<div class="pro-field-overlay"></div>' : '';
		switch ($this->type) {
			case 'text':
				$html .= $this->text($group);
				break;

			case 'url':
				$html .= $this->url($group);
				break;

			case 'number':
				$html .= $this->number($group);
				break;

			case 'float':
				$html .= $this->float($group);
				break;

			case 'select':
				$html .= $this->select($group);
				break;

			case 'select2':
				$html .= $this->select2($group);
				break;

			case 'schema_type':
				$html .= $this->schema_type($group);
				break;

			case 'textarea':
				$html .= $this->textArea($group);
				break;

			case 'checkbox':
				$html .= $this->checkbox($group);
				break;

			case 'switch':
				$html .= $this->rt_switch($group);
				break;

			case 'auto-fill':
				$html .= $this->auto_fill($group);
				break;

			case 'tab':
				$html .= $this->tab($group);
				break;

			case 'repeater':
				$html .= $this->repeater($group);
				break;

			case 'image':
				$html .= $this->image($group);
				break;

			case 'gallery':
				$html .= $this->gallery($group);
				break;

			case 'group':
				$html .= $this->group($group);
				break;

			case 'radio':
				$html .= $this->radio($group);
				break;

			case 'radio-image':
				$html .= $this->radioImage($group);
				break;

			case 'color':
				$html .= $this->color($group);
				break;

			case 'info':
				$html .= $this->info($group);
				break;

			case 'button':
				$html .= $this->button($group);
				break;

			case 'style':
				$html .= $this->smartStyle($group);
				break;
		}

		if ($this->desc) {
			$html .= "<p class='description'>" . wp_kses($this->desc, ['a' => ['href' => []], 'br' => [], 'strong' => [], 'span' => ['style' => []]]) . '</p>';
			$this->desc = ''; // reset description
		}
		$html .= '</div>'; // field
		$html .= '</div>'; // field holder

		return $html;
	}

	private function text($group = null) {
		$h = null;
		$h .= sprintf("<input
        type='text'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value), esc_attr($this->name), esc_attr($this->placeholder));

		return $h;
	}

	private function url() {
		$h = null;
		$h .= sprintf("<input
        type='url'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_url($this->value), esc_attr($this->name), esc_attr($this->placeholder));

		return $h;
	}

	private function number() {
		$h = null;
		$h .= sprintf("<input
        type='number'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value), esc_attr($this->name), esc_attr($this->placeholder));

		return $h;
	}

	private function float() {
		$h = null;
		$h .= sprintf("<input
        type='number'
        step='any'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value), esc_attr($this->name), esc_attr($this->placeholder));

		return $h;
	}

	private function select() {
		$h = null;
		if ($this->multiple) {
			$this->attr  = " style='min-width:160px;'";
			$this->name  = $this->name . '[]';
			$this->attr  = $this->attr . " multiple='multiple'";
			$this->value = (is_array($this->value) && ! empty($this->value) ? $this->value : []);
		} else {
			$this->value = [$this->value];
		}

		$h .= sprintf("<select name='%s' id='%s' class='%s' %s>", esc_attr($this->name), esc_attr($this->id), esc_attr($this->class), esc_html($this->attr));
		if (is_array($this->options) && ! empty($this->options)) {
			foreach ($this->options as $key => $value) {
				$slt = (in_array($key, $this->value) ? 'selected' : null);
				$h .= sprintf("<option %s value='%s'>%s</option>", esc_attr($slt), esc_attr($key), esc_html($value));
			}
		}
		$h .= '</select>';

		return $h;
	}

	private function select2() {
		$h = null;
		if ($this->multiple) {
			$this->attr  = " style='min-width:160px;'";
			$this->name  = $this->name . '[]';
			$this->attr  = $this->attr . " multiple='multiple'";
			$this->value = (is_array($this->value) && ! empty($this->value) ? $this->value : []);
		} else {
			$this->value = [$this->value];
		}

		$h .= sprintf("<select name='%s' id='%s' class='rtrs-select2 %s' %s>", esc_attr($this->name), esc_attr($this->id), esc_attr($this->class), esc_html($this->attr));
		if (is_array($this->options) && ! empty($this->options)) {
			foreach ($this->options as $key => $value) {
				$slt = (in_array($key, $this->value) ? 'selected' : null);
				$h .= sprintf("<option %s value='%s'>%s</option>", esc_attr($slt), esc_attr($key), esc_html($value));
			}
		}
		$h .= '</select>';

		return $h;
	}

	private function schema_type() {
		$h           = null;
		$this->value = [$this->value];

		$h .= sprintf("<select name='%s' id='%s' class='rtrs-select2 %s' %s>", esc_attr($this->name), esc_attr($this->id), esc_attr($this->class), esc_html($this->attr));
		if (is_array($this->options) && ! empty($this->options)) {
			foreach ($this->options as $key => $site) {
				if (is_array($site)) {
					$slt = (in_array($key, $this->value) ? 'selected' : null);
					$h .= "<option value='$key' $slt>&nbsp;&nbsp;&nbsp;$key</option>";
					foreach ($site as $inKey => $inSite) {
						if (is_array($inSite)) {
							$slt = (in_array($inKey, $this->value) ? 'selected' : null);
							$h .= "<option value='$inKey' $slt>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$inKey</option>";
							foreach ($inSite as $inInKey => $inInSite) {
								if (is_array($inInSite)) {
									$slt = (in_array($inInKey, $this->value) ? 'selected' : null);
									$h .= "<option value='$inInKey' $slt>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$inInKey</option>";
									foreach ($inInSite as $iSite) {
										$slt = (in_array($iSite, $this->value) ? 'selected' : null);
										$h .= "<option value='$iSite' $slt>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$iSite</option>";
									}
								} else {
									$slt = (in_array($inInSite, $this->value) ? 'selected' : null);
									$h .= "<option value='$inInSite' $slt>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$inInSite</option>";
								}
							}
						} else {
							$slt = (in_array($inSite, $this->value) ? 'selected' : null);
							$h .= "<option value='$inSite' $slt>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$inSite</option>";
						}
					}
				} else {
					$slt = (in_array($site, $this->value) ? 'selected' : null);
					$h .= "<option value='$site' $slt>$site</option>";
				}
			}
		}
		$h .= '</select>';

		return $h;
	}

	private function textArea() {
		$h = null;
		$h .= sprintf("<textarea
            class='%s rt-textarea'
            id='%s'
            name='%s'
            placeholder='%s' 
            >%s</textarea>", esc_attr($this->class), esc_attr($this->id), esc_attr($this->name), esc_attr($this->placeholder), esc_html($this->value));

		return $h;
	}

	private function rt_switch() { // TODO: Change the function name
		$h       = null;
		$checked = ($this->value ? 'checked' : null);
		if ($this->is_pro && ! function_exists('rtrsp')) {
			$checked = null;
		}

		$h .= sprintf(
			"<label class='rtrs-switch'><input type='checkbox' %s id='%s' name='%s' value='1' /><span class='rtrs-switch-slider round'></span></label>",
			esc_attr($checked),
			esc_attr($this->id),
			esc_attr($this->name),
			esc_html($this->option)
		);

		return $h;
	}

	private function checkbox() {
		$h = null;
		if ($this->multiple) {
			$this->name  = $this->name . '[]';
			$this->value = (is_array($this->value) && ! empty($this->value) ? $this->value : []);
		}
		if ($this->multiple) {
			$h .= sprintf("<div class='checkbox-group %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));
			if (is_array($this->options) && ! empty($this->options)) {
				foreach ($this->options as $key => $value) {
					$checked = (in_array($key, $this->value) ? 'checked' : null);
					$h .= sprintf(
						'<label for="%1$s-%2$s">
                        <input type="checkbox" id="%1$s-%2$s" %3$s name="%4$s" value="%2$s">%5$s
                        </label>',
						esc_attr($this->id),
						esc_attr($key),
						esc_attr($checked),
						esc_attr($this->name),
						esc_html($value)
					);
				}
			}
			$h .= '</div>';
		} else {
			$checked = ($this->value ? 'checked' : null);
			$h .= sprintf(
				"<label><input type='checkbox' %s id='%s' name='%s' value='1' />%s</label>",
				esc_attr($checked),
				esc_attr($this->id),
				esc_attr($this->name),
				esc_html($this->option)
			);
		}

		return $h;
	}

	private function tab() {
		$h = null;
		$h .= "<div class='rtrs-tab'>";
		foreach ($this->options as $key => $value) {
			$checked = ($this->value == $key ? 'checked' : null);
			if (! $checked) {
				$checked = ($this->default == $key ? 'checked' : null);
			}
			$h .= sprintf(
				'<input type="radio" id="%1$s-tab-%2$s" name="%1$s" value="%2$s" %4$s><label for="%1$s-tab-%2$s">%3$s</label>',
				esc_attr($this->name),
				esc_attr($key),
				esc_attr($value),
				esc_attr($checked)
			);
		}
		$h .= '</div>';

		return $h;
	}

	private function auto_fill() {
		$h = null;
		$h .= "<div class='rtrs-auto_fill'>";
		$h .= '<button class="button button-primary" data-type="' . $this->id . '">' . esc_attr($this->label) . '</button>';
		$h .= '</div>';

		return $h;
	}

	private function repeater() {
		$h           = null;
		$this->name  = $this->name . '[]';
		$this->value = (is_array($this->value) && ! empty($this->value) ? $this->value : []);
		$h .= sprintf("<div class='rtrs-repeater checkbox-group %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));

		if ($this->value) {
			$this->options = $this->value;
		}

		if (is_array($this->options) && ! empty($this->options)) {
			foreach ($this->options as $key => $value) {
				$h .= sprintf(
					'<label for="%1$s-%2$s">
                <input type="text" id="%1$s-%2$s" name="%3$s" value="%4$s"><i class="dashicons dashicons-move"></i> <i class="remove dashicons dashicons-dismiss"></i>
                </label>',
					esc_attr($this->id),
					esc_attr($key),
					esc_attr($this->name),
					esc_html($value)
				);
			}
		}
		$h .= '</div>';

		$pro_label = '';
		if (! function_exists('rtrsp')) {
			$hidden_class = '';
			if (count($this->options) < 3) {
				$hidden_class = 'rtrs-hidden';
			}
			$pro_label = '<span class="rtrs-pro ' . $hidden_class . '">' . esc_html__('[Pro]', 'review-schema') . '</span>';
			$pro_label = apply_filters('rtrs_pro_label', $pro_label);
		}
		$has_prefix = $this->has_prefix ? 'data-single="true"' : false;
		$h .= "<a href='#' " . $has_prefix . "><i class='dashicons dashicons-insert'></i> " . esc_html__('Add New', 'review-schema') . ' ' . wp_kses($pro_label, ['span' => ['class' => []]]) . '</a>';

		return $h;
	}

	private function image() {
		$h           = null;
		$this->value = $this->value ? $this->value : null;

		$h .= sprintf("<div class='rtrs-image %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));
		$h .= sprintf("<div class='rtrs-form-group'><div class='rtrs-preview-imgs %s'>", esc_attr($this->id));

		if ($value = $this->value) {
			$img_url = '';
			$img_src = wp_get_attachment_url($value);
			if ($img_src) {
				$img_url = $img_src;
			}

			$h .= "<div class='rtrs-preview-img'><img src='" . $img_url . "' /><input type='hidden' name='" . $this->name . "' value='" . $value . "'><button class='rtrs-file-remove' data-id='" . $value . "'>x</button></div>";
		} else {
			$h .= "<div class='rtrs-preview-img'><input type='hidden' name='" . $this->name . "' value='0'></div>";
		}

		$h .= sprintf(
			"</div>
                        <button data-name='%s' data-field='image' type='button' class='rtrs-upload-box'>
                            <i class='rtrs-picture'></i>
                            <span>%s</span>
                        </button>
                    </div>",
			$this->name,
			esc_html__('Upload Image', 'review-schema')
		);
		$h .= '</div>';

		return $h;
	}

	private function gallery() {
		$h           = null;
		$this->value = (is_array($this->value) && ! empty($this->value) ? $this->value : []);

		$h .= sprintf("<div class='rtrs-gallery %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));
		$h .= "<div class='rtrs-form-group'>
            <div class='rtrs-preview-imgs'>";

		if ($this->value) {
			foreach ($this->value as $value) {
				if (! $value) {
					continue;
				}
				$img_url = '';
				$img_src = wp_get_attachment_url($value);
				if ($img_src) {
					$img_url = $img_src;
				}

				$h .= "<div class='rtrs-preview-img'><img src='" . esc_url($img_url) . "' /><input type='hidden' name='" . esc_attr($this->name) . "[]' value='" . esc_attr($value) . "'><button class='rtrs-file-remove' data-id='" . esc_attr($value) . "'>x</button></div>";
			}
		} else {
			$h .= "<div class='rtrs-preview-img'><input type='hidden' name='" . esc_attr($this->name) . "' value='0'></div>";
		}

		$h .= sprintf(
			"</div>
                        <button data-name='%s' data-field='gallery' type='button' class='rtrs-upload-box'>
                            <i class='rtrs-picture'></i>
                            <span>%s</span>
                        </button>
                    </div>",
			esc_attr($this->name),
			esc_html__('Upload Image', 'review-schema')
		);
		$h .= '</div>';

		return $h;
	}

	private function group() {
		$h          = null;
		$group_name = $this->name;
		$duplicate  = $this->duplicate;
		$is_pro     = $this->is_pro;

		$this->value = (is_array($this->value) && ! empty($this->value) ? $this->value : []);

		$group_fields = $this->fields;
		$loop_count   = 1;

		if ($this->value) {
			$loop_count = count($this->value);
		}

		$label     = $this->label;
		$accordion = $this->accordion;

		$group_id = str_replace('][', '_', $group_name);
		$group_id = str_replace('[', '_', $group_id);
		$group_id = str_replace(']', '', $group_id);

		$h .= sprintf("<div class='rtrs-group-wrap' id='%s'>", esc_attr($group_id));
		for ($i=0; $i < $loop_count; $i++) {
			$h .= "<div class='rtrs-accordion-wrap'>";
			//accordion label
			$h .= sprintf(
				"<div class='rtrs-accordion-label'>%s 
                <span class='rtrs-accordion-counter'>%s
                </span> <a class='rtrs-accordion-remove %s' data-id='%s' 
                href='#'><i class='dashicons dashicons-dismiss'></i></a> <span class='rtrs-accordion-arrow'><i class='dashicons dashicons-arrow-down-alt2'></i></span></div>",
				esc_html($label),
				$duplicate ? esc_html($i + 1) : '',
				$duplicate ? '' : 'rtrs-hidden',
				esc_attr($group_id)
			);
			// accordion body field
			$h .= sprintf("<div class='rtrs-group rtrs-accordion-body %s'>", esc_attr($this->alignment));
			foreach ($group_fields as $key => $attr) {
				$h .= $this->Field($attr, $group_name, $i);
			}
			$h .= '</div>';
			$h .= '</div>'; //rtrs-accordion-wrap
		}
		$h .= '</div>';

		$pro_label = $data_pro = '';
		if ($is_pro && ! function_exists('rtrsp')) {
			$data_pro  = 'yes';
			$hidden_class = '';
			if ( $group_name == 'rating_criteria' ) {
				if ( $loop_count < 3) {
					$hidden_class = 'rtrs-hidden';
				}
			}
			$pro_label = '<span class="rtrs-pro ' . $hidden_class . '">' . esc_html__('[Pro]', 'review-schema') . '</span>';
			$pro_label = apply_filters('rtrs_pro_label', $pro_label);
		}
		$has_prefix = $this->has_prefix ? 'data-single="true"' : false;

		if ($duplicate) {
			$h .= "<a href='#' data-pro='" . esc_attr($data_pro) . "' data-id='" . esc_attr($group_id) . "' data-name='" . esc_attr($group_name) . "' " . $has_prefix . "><i class='dashicons dashicons-insert'></i> " . esc_html__('Add New', 'review-schema') . ' ' . wp_kses($pro_label, ['span' => ['class' => []]]) . '</a>';
		}

		return $h;
	}

	private function radio() {
		$h = null;
		$h .= sprintf("<div class='radio-group %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));
		if (is_array($this->options) && ! empty($this->options)) {
			foreach ($this->options as $key => $value) {
				$checked = ($key == $this->value ? 'checked' : null);
				$h .= sprintf(
					'<label for="%1$s-%2$s">
                <input type="radio" id="%1$s-%2$s" %3$s name="%4$s" value="%2$s">%5$s
                </label> ',
					esc_attr($this->id),
					esc_attr($key),
					esc_attr($checked),
					esc_attr($this->name),
					esc_html($value)
				);
			}
		}
		$h .= '</div>';

		return $h;
	}

	private function radioImage() {
		$h = null;
		$h .= sprintf("<div class='rtrs-radio-image %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));

		if (is_array($this->options) && ! empty($this->options)) {
			foreach ($this->options as $key => $value) {
				$checked     = ($value['value'] == $this->value ? 'checked' : null);
				$is_pro      = (isset($value['is_pro']) && $value['is_pro'] && ! function_exists('rtrsp') ? '<div class="rtrs-ribbon"><span>' . esc_html__('Pro', 'review-schema') . '</span></div>' : '');
				$is_data_pro = (isset($value['is_pro']) && $value['is_pro'] && ! function_exists('rtrsp') ? 'yes' : '');
				$h .= sprintf(
					'<label for="%1$s-%2$s">
                <input type="radio" id="%1$s-%2$s" %3$s name="%4$s" value="%2$s" data-pro="%7$s">
                <div class="rtrs-radio-image-pro-wrap">
                    <img src="%5$s" alt="%2$s">
                    %6$s
                    <div class="rtrs-checked"><span class="dashicons dashicons-yes"></span></div>
                </div>
                </label>',
					esc_attr($this->id),
					esc_attr($value['value']),
					esc_attr($checked),
					esc_attr($this->name),
					esc_url($value['img']),
					$is_pro,
					esc_attr($is_data_pro)
				);
			}
		}
		$h .= '</div>';

		return $h;
	}

	private function color($group = null) {
		$h    = null;
		$name = $group ? $group . '[]' . '[' . $this->name . ']' : $this->name;
		$h .= sprintf("<input
        type='text'
        class='rt-color %s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value), esc_attr($name), esc_attr($this->placeholder));

		return $h;
	}

	private function info($group = null) {
		$h       = null;
		$post_id = get_the_ID();
		$h .= '<div class="rtrs-review-info">';
		switch ($this->data) {
			case 'total_rating':
				$total_rating = Review::getTotalRatings($post_id);
				$total_rating = ($total_rating) ? esc_attr($total_rating) : 0;
				$h .= '<b>' . $total_rating . '</b>';
				break;

			case 'avg_rating':
				$avg_rating = Review::getAvgRatings($post_id);
				if ($avg_rating) {
					$h .= Functions::review_stars($avg_rating, true) . '(' . $avg_rating . ')';
				}
				break;

			case 'best_rating':
				$best_rating = Review::getBestRating($post_id);
				if ($best_rating) {
					$h .= Functions::review_stars($best_rating, true) . '(' . $best_rating . ')';
				}
				break;

			case 'worst_rating':
				$worst_rating = Review::getWorstRating($post_id);
				if ($worst_rating) {
					$h .= Functions::review_stars($worst_rating, true) . '(' . $worst_rating . ')';
				}
				break;

			case 'total_recommended':
				$total_recommended = Review::getTotalRecommendation($post_id);
				$h .= '<b>' . $total_recommended . '</b>';
				break;

			default:
				break;
		}
		$h .= '</div>';

		return $h;
	}

	private function button($group = null) {
		$h = null;
		$h .= sprintf("<input
        type='button'
        class='%s rtrs-reload-btn button button-primary button-large'
        id='%s'
        value='%s'   
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value));

		return $h;
	}

	private function smartStyle($group = null) {
		$h       = null;
		$sColor  = ! empty($this->value['color']) ? esc_attr($this->value['color']) : null;
		$sSize   = ! empty($this->value['size']) ? esc_attr($this->value['size']) : null;
		$sWeight = ! empty($this->value['weight']) ? esc_attr($this->value['weight']) : null;
		$sAlign  = ! empty($this->value['align']) ? esc_attr($this->value['align']) : null;
		$h .= "<div class='rt-multiple-field-container'>";
		// color
		$h .= "<div class='rt-inner-field rt-col-4'>";
		$h .= "<div class='rt-inner-field-container size'>";
		$h .= "<span class='label'>" . esc_html__('Color', 'review-schema') . '</span>';
		$h .= "<input type='text' value='" . esc_attr($sColor) . "' class='rt-color' name='" . esc_attr($this->name) . "[color]'>";
		$h .= '</div>';
		$h .= '</div>';

		// Font size
		$h .= "<div class='rt-inner-field rt-col-4'>";
		$h .= "<div class='rt-inner-field-container size'>";
		$h .= "<span class='label'>" . esc_html__('Font size', 'review-schema') . '</span>';
		$h .= "<select name='" . esc_attr($this->name) . "[size]' class='rtrs-select2'>";
		$fSizes = $this->fontSize();
		$h .= "<option value=''>" . esc_html__('Default', 'review-schema') . '</option>';
		foreach ($fSizes as $size => $label) {
			$sSlt = ($size == $sSize ? 'selected' : null);
			$h .= sprintf("<option value='%s' %s>%s</option>", esc_attr($size), esc_attr($sSlt), esc_html($label));
		}
		$h .= '</select>';
		$h .= '</div>';
		$h .= '</div>';

		// Weight
		$h .= "<div class='rt-inner-field rt-col-4'>";
		$h .= "<div class='rt-inner-field-container weight'>";
		$h .= "<span class='label'>" . esc_html__('Weight', 'review-schema') . '</span>';
		$h .= "<select name='" . esc_attr($this->name) . "[weight]' class='rtrs-select2'>";
		$h .= "<option value=''>" . esc_html__('Default', 'review-schema') . '</option>';
		$weights = $this->textWeight();
		foreach ($weights as $weight => $label) {
			$wSlt = ($weight == $sWeight ? 'selected' : null);
			$h .= sprintf("<option value='%s' %s>%s</option>", esc_attr($weight), esc_attr($wSlt), esc_html($label));
		}
		$h .= '</select>';
		$h .= '</div>';
		$h .= '</div>';

		// Alignment
		$h .= "<div class='rt-inner-field rt-col-4'>";
		$h .= "<div class='rt-inner-field-container alignment'>";
		$h .= "<span class='label'>" . esc_html__('Alignment', 'review-schema') . '</span>';
		$h .= "<select name='" . esc_attr($this->name) . "[align]' class='rtrs-select2'>";
		$h .= "<option value=''>" . esc_html__('Default', 'review-schema') . '</option>';
		$aligns = $this->alignment();
		foreach ($aligns as $align => $label) {
			$aSlt = ($align == $sAlign ? 'selected' : null);
			$h .= sprintf("<option value='%s' %s>%s</option>", esc_attr($align), esc_attr($aSlt), esc_html($label));
		}
		$h .= '</select>';
		$h .= '</div>';
		$h .= '</div>';
		$h .= '</div>';

		return $h;
	}

	private function fontSize() {
		$num = [];
		for ($i = 10; $i <= 60; $i++) {
			$num[$i] = $i . 'px';
		}

		return $num;
	}

	private function alignment() {
		return [
			'left'    => esc_html__('Left', 'review-schema'),
			'right'   => esc_html__('Right', 'review-schema'),
			'center'  => esc_html__('Center', 'review-schema'),
			'justify' => esc_html__('Justify', 'review-schema'),
		];
	}

	private function textWeight() {
		return [
			'normal'  => esc_html__('Normal', 'review-schema'),
			'bold'    => esc_html__('Bold', 'review-schema'),
			'bolder'  => esc_html__('Bolder', 'review-schema'),
			'lighter' => esc_html__('Lighter', 'review-schema'),
			'inherit' => esc_html__('Inherit', 'review-schema'),
			'initial' => esc_html__('Initial', 'review-schema'),
			'unset'   => esc_html__('Unset', 'review-schema'),
			100       => '100',
			200       => '200',
			300       => '300',
			400       => '400',
			500       => '500',
			600       => '600',
			700       => '700',
			800       => '800',
			900       => '900',
		];
	}
}
