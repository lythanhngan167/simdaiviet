<?php

namespace Rtrs\Controllers\Admin\Meta;

use Rtrs\Helpers\Functions;

class MetaOptions {
	/**
	 * Marge all meta field.
	 *
	 * @return array
	 */
	public function allMetaFields() {
		$fields  = array();
		$fieldsA = array_merge(
			$this->sectionConditionalFields(),
			$this->sectionReviewFields(),
			$this->sectionSchemaFields(),
			$this->sectionSettingFields(),
			$this->sectionStyleFields()
		);
		foreach ( $fieldsA as $field ) {
			$fields[] = $field;
		}

		return $fields;
	}

	/**
	 * Get all post meta in array.
	 *
	 * @return array
	 */
	public static function metaValue( $sc_id ) {
		$sc_meta = array();
		// layout tab
		$sc_meta['rtrs_post_type'] = isset( $_REQUEST['rtrs_post_type'] ) ? sanitize_text_field( $_REQUEST['rtrs_post_type'] ) : get_post_meta( $sc_id, 'rtrs_post_type', true );

		return $sc_meta;
	}

	public function filterOptions() {
		$business_info_field = array(
			'top_rated'    => esc_html__( 'Top Rated', 'review-schema' ),
			'low_rated'    => esc_html__( 'Lowest Rating', 'review-schema' ),
			'latest_first' => esc_html__( 'Latest First', 'review-schema' ),
			'oldest_first' => esc_html__( 'Oldest First', 'review-schema' ),
			// TODO: do it later
			// 'recommended'  => esc_html__('Recommended', 'review-schema'),
			// 'highlighted'  => esc_html__('Highlighted', 'review-schema'),
		);

		return apply_filters( 'rtrs_business_info_field', $business_info_field );
	}

	public function reviewFields() {
		$review_field = array(
			'img'         => esc_html__( 'Author Image', 'review-schema' ),
			'name'        => esc_html__( 'Author Name', 'review-schema' ),
			'rating_star' => esc_html__( 'Rating Star', 'review-schema' ),
			'time'        => esc_html__( 'Time', 'review-schema' ),
			'review'      => esc_html__( 'Review', 'review-schema' ),
		);

		return apply_filters( 'rtrs_review_field', $review_field );
	}

	public function sectionConditionalFields() {
		$section_conditional = array(
			array(
				'type'     => 'select2',
				'name'     => 'rtrs_post_type',
				'label'    => esc_html__( 'Select post type', 'review-schema' ),
				'default'  => '',
				'required' => true,
				'id'       => 'rtrs-post-type',
				'options'  => $this->postType(),
			),
			array(
				'type'        => 'select2',
				'name'        => 'rtrs_page_id',
				'holderClass' => 'rtrs-hidden',
				'label'       => esc_html__( 'Choose pages', 'review-schema' ),
				'desc'        => '<span style="color: #b70000; font-weight: 500;">' . esc_html__( 'You can choose individual pages otherwise it will applied for all pages', 'review-schema' ) . '</span>',
				'id'          => 'rtrs-page-id',
				'multiple'    => true,
				'options'     => $this->allPages(),
			),
			array(
				'type'      => 'radio',
				'name'      => 'rtrs_support',
				'label'     => esc_html__( 'Support', 'review-schema' ),
				'id'        => 'rtrs-support',
				'default'   => 'review-schema',
				'alignment' => 'vertical',
				'options'   => array(
					'review-schema' => esc_html__( 'Review with Schema JSON-LD', 'review-schema' ),
					'review'        => esc_html__( 'Only Review', 'review-schema' ),
					'schema'        => esc_html__( 'Only Schema JSON-LD', 'review-schema' ),
				),
			),
		);

		return apply_filters( 'rtrs_section_conditional_fields', $section_conditional );
	}

	public function sectionReviewFields() {
		$section_layout = array(
			array(
				'type'    => 'radio-image',
				'name'    => 'criteria',
				'label'   => esc_html__( 'Criteria?', 'review-schema' ),
				'doc'     => esc_html__( 'If you want you can enable or disable single or criteria based rating from here', 'review-schema' ),
				'id'      => 'rtrs-criteria',
				'default' => 'single',
				'options' => array(
					array(
						'value' => 'single',
						'img'   => RTRS_URL . '/assets/imgs/single-criteria.jpg',
					),
					array(
						'value' => 'multi',
						'img'   => RTRS_URL . '/assets/imgs/multi-criteria.jpg',
					),
				),
			),
			array(
				'type'      => 'repeater',
				'name'      => 'multi_criteria',
				'label'     => esc_html__( 'Multi criteria', 'review-schema' ),
				'id'        => 'rtrs-multi-criteria',
				'alignment' => 'vertical',
				'default'   => $this->multiCriteria(),
				'options'   => $this->multiCriteria(),
			),
			array(
				'type'    => 'radio-image',
				'name'    => 'summary_layout',
				'label'   => esc_html__( 'Review summary layout', 'review-schema' ),
				'default' => 'one',
				'id'      => 'rtrs-summary_layout',
				'options' => array(
					array(
						'value' => 'one',
						'img'   => RTRS_URL . '/assets/imgs/summary-one.jpg',
					),
					array(
						'value' => 'two',
						'img'   => RTRS_URL . '/assets/imgs/summary-two.jpg',
					),
					array(
						'value'  => 'three',
						'img'    => RTRS_URL . '/assets/imgs/summary-three.jpg',
						'is_pro' => true,
					),
					array(
						'value'  => 'four',
						'img'    => RTRS_URL . '/assets/imgs/summary-four.jpg',
						'is_pro' => true,
					),
				),
			),
			array(
				'type'    => 'radio-image',
				'name'    => 'review_layout',
				'label'   => esc_html__( 'Review layout', 'review-schema' ),
				'default' => 'one',
				'id'      => 'rtrs-review_layout',
				'options' => array(
					array(
						'value' => 'one',
						'img'   => RTRS_URL . '/assets/imgs/review-one.jpg',
					),
					array(
						'value' => 'two',
						'img'   => RTRS_URL . '/assets/imgs/review-two.jpg',
					),
					array(
						'value'  => 'three',
						'img'    => RTRS_URL . '/assets/imgs/review-three.jpg',
						'is_pro' => true,
					),
					array(
						'value'  => 'four',
						'img'    => RTRS_URL . '/assets/imgs/review-two.jpg',
						'is_pro' => true,
					),
				),
			),
			array(
				'type'    => 'select2',
				'name'    => 'pagination_type',
				'label'   => esc_html__( 'Pagination type', 'review-schema' ),
				'id'      => 'rtrs-pagination_type',
				'default' => 'normal',
				'options' => $this->pagination_type(),
			),
		);

		return apply_filters( 'rtrs_section_layout_fields', $section_layout );
	}

	public function sectionSchemaFields() {
		$section_schema = array(
			array(
				'name'        => 'page_schema',
				'type'        => 'heading',
				'holderClass' => 'rtrs-page-schema',
				'label'       => esc_html__( 'Google rich snippet?', 'review-schema' ),
				'desc'        => wp_kses( __( "Google auto rich snippet not support in page, you need to set it manually from single page. <a href='#'> Help Documentation</a>", 'review-schema' ), array( 'a' => array( 'href' => array() ) ) ),
			),
			array(
				'type'  => 'switch',
				'name'  => 'rich_snippet',
				'id'    => 'rtrs-auto_rich_snippet',
				'label' => esc_html__( 'Structured data (rich snippet)?', 'review-schema' ),
				'desc'  => esc_html__( 'Auto generate Structured data schema (rich snippet). If you want, you can add custom structured data from single post/page', 'review-schema' ),
			),
			array(
				'type'    => 'select2',
				'name'    => 'rich_snippet_cat',
				'label'   => esc_html__( 'Structured data type', 'review-schema' ),
				'desc'    => '<span style="color: #b70000; font-weight: 500;">' . esc_html__( 'By default only some type is possible to auto generate, but you can generate all type from single post', 'review-schema' ) . '</span>',
				'default' => '',
				'id'      => 'rtrs-rich_snippet_cat_back',
				'options' => Functions::rich_snippet_auto_cats(),
			),
		);

		return apply_filters( 'rtrs_section_schema_fields', $section_schema );
	}

	public function sectionSettingFields() {
		$settings_fields = array(
			array(
				'type'   => 'switch',
				'name'   => 'title',
				'id'     => 'rtrs-title',
				'label'  => esc_html__( 'Review title disable?', 'review-schema' ),
				'option' => esc_html__( 'Disable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'human-time-diff',
				'id'     => 'rtrs-human-time-diff',
				'label'  => esc_html__( 'Disable human readable time format ?', 'review-schema' ),
				'option' => esc_html__( 'Disable', 'review-schema' ),
				'desc'   => esc_html__( 'By default review time is human readable format such as "1 hour ago", "5 mins ago", "2 days ago " Or ', 'review-schema' ) . ' <a href=' . admin_url( 'options-general.php' ) . '>' . esc_html__( 'Go to General Settings for change date formate', 'review-schema' ) . '</a>',
			),
			array(
				'type'   => 'switch',
				'name'   => 'website',
				'id'     => 'rtrs-website',
				'label'  => esc_html__( 'Review website url disable?', 'review-schema' ),
				'option' => esc_html__( 'Disable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'image_review',
				'id'     => 'rtrs-image-review',
				'label'  => esc_html__( 'Allow image review?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'video_review',
				'is_pro' => true,
				'id'     => 'rtrs-video-review',
				'label'  => esc_html__( 'Allow video review?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'pros_cons',
				'id'     => 'rtrs-pros_cons',
				'label'  => esc_html__( 'Allow pros cons?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'name'    => 'pros_cons_limit',
				'type'    => 'number',
				'default' => 3,
				'is_pro'  => true,
				'label'   => esc_html__( 'Pros cons limit', 'review-schema' ),
				'desc'    => esc_html__( 'How many field field you want to allow', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'recommendation',
				'is_pro' => true,
				'id'     => 'rtrs-recommendation',
				'class'  => 'rtrs-hidden',
				'label'  => esc_html__( 'Allow recommendation?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'highlight_review',
				'is_pro' => true,
				'id'     => 'rtrs-highlight_review',
				'class'  => 'rtrs-hidden',
				'label'  => esc_html__( 'Highlight review?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'social_share',
				'is_pro' => true,
				'id'     => 'rtrs-social-share',
				'label'  => esc_html__( 'Social share?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'like',
				'is_pro' => true,
				'id'     => 'rtrs-like',
				'label'  => esc_html__( 'Allow review like?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'dislike',
				'is_pro' => true,
				'id'     => 'rtrs-dislike',
				'label'  => esc_html__( 'Allow review dislike?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'anonymous_review',
				'is_pro' => true,
				'id'     => 'rtrs-anonymous_review',
				'label'  => esc_html__( 'Allow anonymous review?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'email',
				'is_pro' => true,
				'id'     => 'rtrs-email',
				'label'  => esc_html__( 'Email field disable?', 'review-schema' ),
				'option' => esc_html__( 'Disable', 'review-schema' ),
				'desc'   => ' <a href=' . admin_url( 'options-discussion.php' ) . '>' . esc_html__( 'Got To Discussion', 'review-schema' ) . '</a>' . sprintf( ' %s <strong> %s </strong>', esc_html__( '. You have to uncheck the settings.', 'review-schema' ), esc_html__( 'Comment author must fill out name and email', 'review-schema' ) ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'author',
				'id'     => 'rtrs-author',
				'is_pro' => true,
				'label'  => esc_html__( 'Author field disable?', 'review-schema' ),
				'option' => esc_html__( 'Disable', 'review-schema' ),
				'desc'   => ' <a href=' . admin_url( 'options-discussion.php' ) . '>' . esc_html__( 'Got To Discussion', 'review-schema' ) . '</a>' . sprintf( ' %s <strong> %s </strong>', esc_html__( '. You have to uncheck the settings.', 'review-schema' ), esc_html__( 'Comment author must fill out name and email', 'review-schema' ) ),
			),
			array(
				'type'   => 'switch',
				'name'   => 'purchased_badge',
				'is_pro' => true,
				'id'     => 'rtrs-purchased_badge',
				'label'  => esc_html__( 'Show purchase badge?', 'review-schema' ),
				'option' => esc_html__( 'Enable', 'review-schema' ),
				'desc'   => esc_html__( 'It will show WC, EDD purchased badge', 'review-schema' ),
			),
			array(
				'type'  => 'switch',
				'name'  => 'recaptcha',
				'id'    => 'rtrs-recaptcha',
				'label' => esc_html__( 'Allow google recaptcha?', 'review-schema' ),
				'desc'  => esc_html__( 'When you enable google captcha, you must need to fill up Google captcha v3 credential from', 'review-schema' ) . ' <a href=' . menu_page_url( 'rtrs-settings', false ) . '>' . esc_html__( 'Settings', 'review-schema' ) . '</a>',
			),
			array(
				'type'        => 'switch',
				'name'        => 'filter',
				'label'       => esc_html__( 'Filter?', 'review-schema' ),
				'holderClass' => 'rtrs-filter',
				'id'          => 'rtrs-filter',
				'option'      => esc_html__( 'Enable', 'review-schema' ),
			),
			array(
				'type'      => 'checkbox',
				'name'      => 'filter_option',
				'label'     => esc_html__( 'Filter Options', 'review-schema' ),
				'id'        => 'rtrs-filter-options',
				'multiple'  => true,
				'alignment' => 'vertical',
				'default'   => array_keys( $this->filterOptions() ),
				'options'   => $this->filterOptions(),
			),
		);

		return apply_filters( 'rtrs_section_setting_fields', $settings_fields );
	}

	public function sectionStyleFields() {
		$style_fields = array(
			array(
				'name'  => 'parent_class',
				'type'  => 'text',
				'label' => 'Parent class',
				'id'    => 'rtrs-parent-class',
				'class' => 'medium-text',
				'desc'  => esc_html__( 'Parent class for adding custom css', 'review-schema' ),
			),
			array(
				'name'  => 'width',
				'id'    => 'rtrs-width',
				'type'  => 'text',
				'class' => 'small-width',
				'label' => esc_html__( 'Width', 'review-schema' ),
				'desc'  => esc_html__( 'Layout width, Like: 400px or 50% etc', 'review-schema' ),
			),
			array(
				'name'  => 'margin',
				'id'    => 'rtrs-margin',
				'type'  => 'text',
				'class' => 'small-width',
				'label' => esc_html__( 'Margin', 'review-schema' ),
				'desc'  => esc_html__( 'Layout margin, Like: 50px', 'review-schema' ),
			),
			array(
				'name'  => 'padding',
				'id'    => 'rtrs-padding',
				'type'  => 'text',
				'class' => 'small-width',
				'label' => esc_html__( 'Padding', 'review-schema' ),
				'desc'  => esc_html__( 'Layout padding, Like: 50px', 'review-schema' ),
			),
			array(
				'name'  => 'review_title',
				'type'  => 'style',
				'label' => esc_html__( 'Review title', 'review-schema' ),
			),
			array(
				'name'  => 'review_text',
				'type'  => 'style',
				'label' => esc_html__( 'Review text', 'review-schema' ),
			),
			array(
				'name'  => 'date_text',
				'type'  => 'style',
				'label' => esc_html__( 'Date text', 'review-schema' ),
			),
			array(
				'name'  => 'author_name',
				'type'  => 'style',
				'label' => esc_html__( 'Author name', 'review-schema' ),
			),
			array(
				'name'  => 'author_name_hover',
				'type'  => 'style',
				'label' => esc_html__( 'Author name hover', 'review-schema' ),
			),
			/*
			 array(
				"name"        => "reply_btn_color",
				'type'        => 'style',
				'label'       => esc_html__( 'Reply button', 'review-schema' ),
			), */
			array(
				'type'    => 'color',
				'name'    => 'star_color',
				'id'      => 'rtrs-star-color',
				'default' => '#ffb300',
				'label'   => esc_html__( 'Star color', 'review-schema' ),
			),
			array(
				'type'    => 'color',
				'name'    => 'meta_icon_color',
				'id'      => 'rtrs-meta_icon_color',
				'default' => '#646464',
				'label'   => esc_html__( 'Meta icon color', 'review-schema' ),
			),
		);

		return apply_filters( 'rtrs_section_style_fields', $style_fields );
	}

	public function summary_layouts() {
		return apply_filters(
			'rtrs_summary_layouts',
			array(
				'one'   => esc_html__( 'Layout 1', 'review-schema' ),
				'two'   => esc_html__( 'Layout 2', 'review-schema' ),
				'three' => esc_html__( 'Layout 3', 'review-schema' ),
				'four'  => esc_html__( 'Layout 4', 'review-schema' ),
			)
		);
	}

	public function review_layouts() {
		return apply_filters(
			'rtrs_review_layouts',
			array(
				'one'   => esc_html__( 'Layout 1', 'review-schema' ),
				'two'   => esc_html__( 'Layout 2', 'review-schema' ),
				'three' => esc_html__( 'Layout 3', 'review-schema' ),
				'four'  => esc_html__( 'Layout 4', 'review-schema' ),
			)
		);
	}

	public function pagination_type() {
		$pro_label = '';
		if ( ! function_exists( 'rtrsp' ) ) {
			$pro_label = ' [Pro]'; // don't need to translate
		}

		return apply_filters(
			'rtrs_pagination_type',
			array(
				'number'      => esc_html__( 'Number', 'review-schema' ),
				'number-ajax' => esc_html__( 'Number Ajax', 'review-schema' ) . $pro_label,
				'load-more'   => esc_html__( 'Load More', 'review-schema' ) . $pro_label,
				'auto-scroll' => esc_html__( 'Auto Scroll', 'review-schema' ) . $pro_label,
			)
		);
	}

	public function postType() {
		$post_type = Functions::getPostTypes();

		return apply_filters( 'rtrs_post_type', $post_type );
	}

	public function allPages() {
		$page_array    = array();
		$page_array[0] = esc_html__( 'Select', 'review-schema' );
		$all_pages     = get_pages();
		foreach ( $all_pages as $page ) {
			$page_array[ $page->ID ] = $page->post_title;
		}

		return apply_filters( 'rtrs_pages', $page_array );
	}

	public function multiCriteria() {
		return apply_filters(
			'rtrs_multi_criteria',
			array(
				esc_html__( 'Chất lượng', 'review-schema' ),
				esc_html__( 'Giá bán', 'review-schema' ),
				esc_html__( 'Dịch vụ', 'review-schema' ),
			)
		);
	}

	public function prosCons() {
		return apply_filters( 'rtrs_pros_cons', array( '' ) );
	}
}
