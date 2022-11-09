<?php

namespace Rtrs\Controllers\Admin\Meta;

use Rtrs\Helpers\Functions;

class SingleMetaOptions {
	private $prefix = 'rtrs_';

	/**
	 * Marge all meta field.
	 *
	 * @return array
	 */
	public function allMetaFields() {
		$fields  = [];
		$fieldsA = array_merge(
			$this->sectionSchemaFields(),
			$this->sectionReviewFields()
		);
		foreach ($fieldsA as $field) {
			$fields[] = $field;
		}

		return $fields;
	}

	public function sectionReviewFields() {
		$post_type     = get_post_type();
		$review_fields = [
			[
				'type'  => 'info',
				'label' => esc_html__('Total rating', 'review-schema'),
				'doc'   => esc_html__('Total rating of this', 'review-schema') . ' ' . $post_type,
				'data'  => 'total_rating',
			],
			[
				'type'  => 'info',
				'label' => esc_html__('Average rating', 'review-schema'),
				'doc'   => esc_html__('Average rating of this', 'review-schema') . ' ' . $post_type,
				'data'  => 'avg_rating',
			],
			[
				'type'  => 'info',
				'label' => esc_html__('Best rating', 'review-schema'),
				'doc'   => esc_html__('Best rating of this', 'review-schema') . ' ' . $post_type,
				'data'  => 'best_rating',
			],
			[
				'type'  => 'info',
				'label' => esc_html__('Worst rating', 'review-schema'),
				'doc'   => esc_html__('Worst rating of this', 'review-schema') . ' ' . $post_type,
				'data'  => 'worst_rating',
			],
		];

		return apply_filters('rtrs_affiliate_review_fields', $review_fields);
	}

	public function sectionSchemaFields() {
		$settings_fields = [
			[
				'type'    => 'checkbox',
				'name'    => '_rtrs_custom_rich_snippet',
				'id'      => 'rtrs-rich_snippet',
				'label'   => esc_html__('Custom rich snippet? (Manual)', 'review-schema'),
				'desc'    => esc_html__('If you enable this option. Schema JSON-LD data will generate from here.', 'review-schema'),
				'option'  => esc_html__('Enable', 'review-schema'),
			],
			[
				'type'     => 'select2',
				'name'     => '_rtrs_rich_snippet_cat',
				'label'    => esc_html__('Rich snippet type', 'review-schema'),
				'multiple' => true,
				'id'       => 'rtrs-rich_snippet_cat',
				'options'  => Functions::rich_snippet_cats(),
			],
			//article
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'article_schema',
				'id'          => 'rtrs-article_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Article schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'article',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'headline',
						'type'     => 'text',
						'label'    => esc_html__('Headline', 'review-schema'),
						'desc'     => esc_html__('Article title', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'mainEntityOfPage',
						'type'     => 'url',
						'label'    => esc_html__('Page URL', 'review-schema'),
						'desc'     => esc_html__('The canonical URL of the article page', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author',
						'type'     => 'text',
						'label'    => esc_html__('Author Name', 'review-schema'),
						'desc'     => esc_html__('Author display name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author_url',
						'type'     => 'text',
						'label'    => esc_html__('Author URL', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'image',
						'type'     => 'image',
						'label'    => esc_html__('Article Feature Image', 'review-schema'),
						'required' => true,
						'desc'     => wp_kses(__('Images should be at least 696 pixels wide.<br>Images should be in .jpg, .png, or. gif format.', 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'datePublished',
						'type'     => 'text',
						'label'    => esc_html__('Published date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'dateModified',
						'type'     => 'text',
						'label'    => esc_html__('Modified date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'publisher',
						'type'     => 'text',
						'label'    => esc_html__('Publisher', 'review-schema'),
						'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'publisherImage',
						'type'     => 'image',
						'label'    => esc_html__('Publisher Logo', 'review-schema'),
						'desc'     => wp_kses(__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.<br>', 'review-schema'), ['br' => []]),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
						'desc'  => esc_html__('Short description', 'review-schema'),
					],
					[
						'name'  => 'articleBody',
						'type'  => 'textarea',
						'label' => esc_html__('Article body', 'review-schema'),
						'desc'  => esc_html__('Article content', 'review-schema'),
					],
					[
						'name'  => 'alternativeHeadline',
						'type'  => 'text',
						'label' => esc_html__('Alternative headline', 'review-schema'),
						'desc'  => esc_html__('A secondary headline for the article.', 'review-schema'),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'video',
						'label'     => esc_html__('Video Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
							],
							[
								'name'  => 'description',
								'type'  => 'textarea',
								'label' => esc_html__('Description', 'review-schema'),
							],
							[
								'name'  => 'thumbnailUrl',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'     => 'contentUrl',
								'type'     => 'url',
								'label'    => esc_html__('Content URL', 'review-schema'),
							],
							[
								'name'        => 'embedUrl',
								'type'        => 'url',
								'label'       => esc_html__('Embed URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format.', 'review-schema'),
							],
							[
								'name'     => 'uploadDate',
								'type'     => 'text',
								'label'    => esc_html__('Upload Date', 'review-schema'),
								'class'    => 'rtrs-date',
								'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
							],
							[
								'name'     => 'duration',
								'type'     => 'text',
								'label'    => esc_html__('Duration', 'review-schema'),
								'desc'     => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
							],
						],
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'audio',
						'label'     => esc_html__('Audio Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'desc'     => esc_html__('The title of the audio', 'review-schema'),
							],
							[
								'name'     => 'description',
								'type'     => 'textarea',
								'label'    => esc_html__('Description', 'review-schema'),
								'desc'     => esc_html__('The short description of the audio', 'review-schema'),
							],
							[
								'name'  => 'duration',
								'type'  => 'text',
								'label' => esc_html__('Duration', 'review-schema'),
								'desc'  => esc_html__('The duration of the audio in ISO 8601 format.(PT1M33S)', 'review-schema'),
							],
							[
								'name'        => 'contentUrl',
								'type'        => 'url',
								'label'       => esc_html__('Content URL', 'review-schema'),
								'placeholder' => esc_html__('URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual audio media file. This file should be in .mp3, .wav, .mpc or other audio file format.', 'review-schema'),
							],
							[
								'name'  => 'encodingFormat',
								'type'  => 'text',
								'label' => esc_html__('Encoding Format', 'review-schema'),
								'desc'  => esc_html__("The encoding format of audio like: 'audio/mpeg'", 'review-schema'),
							],
						],
					],
				],
			],
			//news_article
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'news_article_schema',
				'id'          => 'rtrs-news_article_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('News article schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'news_article',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'type'      => 'tab',
						'name'      => 'status',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'headline',
						'type'     => 'text',
						'label'    => esc_html__('Headline', 'review-schema'),
						'desc'     => esc_html__('Article title', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'mainEntityOfPage',
						'type'     => 'url',
						'label'    => esc_html__('Page URL', 'review-schema'),
						'desc'     => esc_html__('The canonical URL of the article page', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author',
						'type'     => 'text',
						'label'    => esc_html__('Author Name', 'review-schema'),
						'desc'     => esc_html__('Author display name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author_url',
						'type'     => 'text',
						'label'    => esc_html__('Author URL', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'image',
						'type'     => 'image',
						'label'    => esc_html__('Article Feature Image', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('Images should be at least 696 pixels wide.<br>Images should be in .jpg, .png, or. gif format.', 'review-schema'),
					],
					[
						'name'     => 'datePublished',
						'type'     => 'text',
						'label'    => esc_html__('Published date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'dateModified',
						'type'     => 'text',
						'label'    => esc_html__('Modified date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'publisher',
						'type'     => 'text',
						'label'    => esc_html__('Publisher', 'review-schema'),
						'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'publisherImage',
						'type'     => 'image',
						'label'    => esc_html__('Publisher Logo', 'review-schema'),
						'desc'     => wp_kses(__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.<br>', 'review-schema'), ['br' => []]),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
						'desc'  => esc_html__('Short description', 'review-schema'),
					],
					[
						'name'  => 'articleBody',
						'type'  => 'textarea',
						'label' => esc_html__('Article body', 'review-schema'),
						'desc'  => esc_html__('Article content', 'review-schema'),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'video',
						'label'     => esc_html__('Video Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
							],
							[
								'name'  => 'description',
								'type'  => 'textarea',
								'label' => esc_html__('Description', 'review-schema'),
							],
							[
								'name'  => 'thumbnailUrl',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'     => 'contentUrl',
								'type'     => 'url',
								'label'    => esc_html__('Content URL', 'review-schema'),
							],
							[
								'name'        => 'embedUrl',
								'type'        => 'url',
								'label'       => esc_html__('Embed URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format.', 'review-schema'),
							],
							[
								'name'     => 'uploadDate',
								'type'     => 'text',
								'label'    => esc_html__('Upload Date', 'review-schema'),
								'class'    => 'rtrs-date',
								'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
							],
							[
								'name'     => 'duration',
								'type'     => 'text',
								'label'    => esc_html__('Duration', 'review-schema'),
								'desc'     => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
							],
						],
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'audio',
						'label'     => esc_html__('Audio Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'desc'     => esc_html__('The title of the audio', 'review-schema'),
							],
							[
								'name'     => 'description',
								'type'     => 'textarea',
								'label'    => esc_html__('Description', 'review-schema'),
								'desc'     => esc_html__('The short description of the audio', 'review-schema'),
							],
							[
								'name'  => 'duration',
								'type'  => 'text',
								'label' => esc_html__('Duration', 'review-schema'),
								'desc'  => esc_html__('The duration of the audio in ISO 8601 format.(PT1M33S)', 'review-schema'),
							],
							[
								'name'        => 'contentUrl',
								'type'        => 'url',
								'label'       => esc_html__('Content URL', 'review-schema'),
								'placeholder' => esc_html__('URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual audio media file. This file should be in .mp3, .wav, .mpc or other audio file format.', 'review-schema'),
							],
							[
								'name'  => 'encodingFormat',
								'type'  => 'text',
								'label' => esc_html__('Encoding Format', 'review-schema'),
								'desc'  => esc_html__("The encoding format of audio like: 'audio/mpeg'", 'review-schema'),
							],
						],
					],
				],
			],
			//blog_posting
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'blog_posting_schema',
				'id'          => 'rtrs-blog_posting_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Blog posting schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'blog_posting',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'type'      => 'tab',
						'name'      => 'status',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'headline',
						'type'     => 'text',
						'label'    => esc_html__('Headline', 'review-schema'),
						'desc'     => esc_html__('Blog posting title', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'mainEntityOfPage',
						'type'     => 'url',
						'label'    => esc_html__('Page URL', 'review-schema'),
						'desc'     => esc_html__('The canonical URL of the article page', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author',
						'type'     => 'text',
						'label'    => esc_html__('Author name', 'review-schema'),
						'desc'     => esc_html__('Author display name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author_url',
						'type'     => 'text',
						'label'    => esc_html__('Author URL', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'image',
						'type'     => 'image',
						'label'    => esc_html__('Feature Image', 'review-schema'),
						'desc'     => esc_html__('The representative image of the article. Only a marked-up image that directly belongs to the article should be specified.<br> Images should be at least 696 pixels wide. <br>Images should be in .jpg, .png, or. gif format.', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'datePublished',
						'type'     => 'text',
						'label'    => esc_html__('Published date', 'review-schema'),
						'class'    => 'rtrs-date',
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'dateModified',
						'type'     => 'text',
						'label'    => esc_html__('Modified date', 'review-schema'),
						'class'    => 'rtrs-date',
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'publisher',
						'type'     => 'text',
						'label'    => esc_html__('Publisher', 'review-schema'),
						'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'publisherImage',
						'type'     => 'image',
						'label'    => esc_html__('Publisher Logo', 'review-schema'),
						'desc'     => esc_html__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.<br>', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
						'desc'  => esc_html__('Short description', 'review-schema'),
					],
					[
						'name'  => 'articleBody',
						'type'  => 'textarea',
						'label' => esc_html__('Article body', 'review-schema'),
						'desc'  => esc_html__('Article content', 'review-schema'),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'video',
						'label'     => esc_html__('Video Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
							],
							[
								'name'  => 'description',
								'type'  => 'textarea',
								'label' => esc_html__('Description', 'review-schema'),
							],
							[
								'name'  => 'thumbnailUrl',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'     => 'contentUrl',
								'type'     => 'url',
								'label'    => esc_html__('Content URL', 'review-schema'),
							],
							[
								'name'        => 'embedUrl',
								'type'        => 'url',
								'label'       => esc_html__('Embed URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format.', 'review-schema'),
							],
							[
								'name'     => 'uploadDate',
								'type'     => 'text',
								'label'    => esc_html__('Upload Date', 'review-schema'),
								'class'    => 'rtrs-date',
								'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
							],
							[
								'name'     => 'duration',
								'type'     => 'text',
								'label'    => esc_html__('Duration', 'review-schema'),
								'desc'     => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
							],
						],
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'audio',
						'label'     => esc_html__('Audio Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'desc'     => esc_html__('The title of the audio', 'review-schema'),
							],
							[
								'name'     => 'description',
								'type'     => 'textarea',
								'label'    => esc_html__('Description', 'review-schema'),
								'desc'     => esc_html__('The short description of the audio', 'review-schema'),
							],
							[
								'name'  => 'duration',
								'type'  => 'text',
								'label' => esc_html__('Duration', 'review-schema'),
								'desc'  => esc_html__('The duration of the audio in ISO 8601 format.(PT1M33S)', 'review-schema'),
							],
							[
								'name'        => 'contentUrl',
								'type'        => 'url',
								'label'       => esc_html__('Content URL', 'review-schema'),
								'placeholder' => esc_html__('URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual audio media file. This file should be in .mp3, .wav, .mpc or other audio file format.', 'review-schema'),
							],
							[
								'name'  => 'encodingFormat',
								'type'  => 'text',
								'label' => esc_html__('Encoding Format', 'review-schema'),
								'desc'  => esc_html__("The encoding format of audio like: 'audio/mpeg'", 'review-schema'),
							],
						],
					],
				],
			],
			//event
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'event_schema',
				'id'          => 'rtrs-event_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Event schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'event',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The name of the event.', 'review-schema'),
					],
					[
						'name'     => 'locationName',
						'type'     => 'text',
						'label'    => esc_html__('Location name', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('Event Location name', 'review-schema'),
					],
					[
						'name'     => 'locationAddress',
						'type'     => 'text',
						'label'    => esc_html__('Location address', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The location of for example where the event is happening, an organization is located, or where an action takes place.', 'review-schema'),
					],
					[
						'name'     => 'startDate',
						'type'     => 'text',
						'label'    => esc_html__('Start date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Event start date Like : 2020-10-16 4:00 AM', 'review-schema'),
					],
					[
						'name'        => 'endDate',
						'type'        => 'text',
						'label'       => esc_html__('End date', 'review-schema'),
						'recommended' => true,
						'class'       => 'rtrs-date',
						'desc'        => esc_html__('Event end date Like : 2020-10-16 4:00 AM', 'review-schema'),
					],
					[
						'name'        => 'description',
						'type'        => 'textarea',
						'label'       => esc_html__('Description', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('Event description', 'review-schema'),
					],
					[
						'name'        => 'performerName',
						'type'        => 'text',
						'label'       => esc_html__('Performer Name', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__("The performer's name.", 'review-schema'),
					],
					[
						'name'        => 'image',
						'type'        => 'image',
						'label'       => esc_html__('Image', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('Image or logo for the event or tour', 'review-schema'),
					],
					[
						'name'        => 'price',
						'type'        => 'number',
						'label'       => esc_html__('Price', 'review-schema'),
						'recommended' => true,
						'desc'        => wp_kses(__("This is highly recommended. The lowest available price, including service charges and fees, of this type of ticket. <span class='required'>Not required but (Recommended)</span>", 'review-schema'), ['span' => []]),
					],
					[
						'name'  => 'priceCurrency',
						'type'  => 'text',
						'label' => esc_html__('Price currency', 'review-schema'),
						'desc'  => esc_html__('The 3-letter currency code. (USD)', 'review-schema'),
					],
					[
						'name'          => 'availability',
						'type'          => 'select',
						'label'         => esc_html__('Availability', 'review-schema'),
						'recommended'   => true,
						'default'       => 'http://schema.org/InStock',
						'options'       => [
							'http://schema.org/InStock'  => esc_html__('InStock', 'review-schema'),
							'http://schema.org/SoldOut'  => esc_html__('SoldOut', 'review-schema'),
							'http://schema.org/PreOrder' => esc_html__('PreOrder', 'review-schema'),
						],
					],
					[
						'name'          => 'eventStatus',
						'type'          => 'select',
						'label'         => esc_html__('Event status', 'review-schema'),
						'recommended'   => true,
						'default'       => 'https://schema.org/EventScheduled',
						'options'       => [
							'https://schema.org/EventCancelled'    => esc_html__('Cancelled', 'review-schema'),
							'https://schema.org/EventMovedOnline'  => esc_html__('Moved Online', 'review-schema'),
							'https://schema.org/EventPostponed'    => esc_html__('Postponed', 'review-schema'),
							'https://schema.org/EventRescheduled'  => esc_html__('Rescheduled', 'review-schema'),
							'https://schema.org/EventScheduled'    => esc_html__('Scheduled', 'review-schema'),
						],
					],
					[
						'name'          => 'eventAttendanceMode',
						'type'          => 'select',
						'label'         => esc_html__('Attendance mode', 'review-schema'),
						'recommended'   => true,
						'default'       => 'https://schema.org/OfflineEventAttendanceMode',
						'options'       => [
							'https://schema.org/OfflineEventAttendanceMode'  => esc_html__('Offline', 'review-schema'),
							'https://schema.org/OnlineEventAttendanceMode'   => esc_html__('Online', 'review-schema'),
							'https://schema.org/MixedEventAttendanceMode'    => esc_html__('Mixed', 'review-schema'),
						],
					],
					[
						'name'        => 'validFrom',
						'type'        => 'text',
						'label'       => esc_html__('Valid From', 'review-schema'),
						'recommended' => true,
						'class'       => 'rtrs-date',
						'desc'        => sprintf("The date and time when tickets go on sale (only required on date-restricted offers), in <a href='%s' target='_blank'>ISO-8601 format</a>", 'https://en.wikipedia.org/wiki/ISO_8601'),
					],
					[
						'name'        => 'url',
						'type'        => 'url',
						'recommended' => true,
						'label'       => esc_html__('URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'desc'        => wp_kses(__("A link to the event's details page. <span class='required'>Not required but (Recommended)</span>", 'review-schema'), ['span' => []]),
					],
					[
						'name'        => 'organizerName',
						'type'        => 'text',
						'recommended' => true,
						'label'       => esc_html__('Organization Name', 'review-schema'),
					],
					[
						'name'        => 'organizerUrl',
						'type'        => 'url',
						'recommended' => true,
						'label'       => esc_html__('Organization URL', 'review-schema'),
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Review Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'         => 'review_author_sameAs',
						'type'         => 'textarea',
						'label'        => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'  => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'     => true,
						'desc'         => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'        => 'review_ratingValue',
						'type'        => 'number',
						'label'       => esc_html__('Rating avg value', 'review-schema'),
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//job_posting
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'job_posting_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-job_posting_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Job posting schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'job_posting',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'  => 'title',
						'type'  => 'text',
						'label' => esc_html__('Title', 'review-schema'),
					],
					[
						'name'  => 'salaryAmount',
						'type'  => 'number',
						'label' => esc_html__('Base Salary', 'review-schema'),
						'desc'  => esc_html__('50.00', 'review-schema'),
					],
					[
						'name'  => 'currency',
						'type'  => 'text',
						'label' => esc_html__('Currency', 'review-schema'),
						'desc'  => esc_html__('USD', 'review-schema'),
					],
					[
						'name'    => 'salaryAt',
						'type'    => 'select',
						'label'   => esc_html__('Salary at', 'review-schema'),
						'options' => [
							'MONTH'   => esc_html__('MONTH', 'review-schema'),
							'HOUR'    => esc_html__('HOUR', 'review-schema'),
							'WEEK'    => esc_html__('WEEK', 'review-schema'),
							'YEAR'    => esc_html__('YEAR', 'review-schema'),
						],
					],
					[
						'name'  => 'datePosted',
						'type'  => 'text',
						'label' => esc_html__('Job posted date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'validThrough',
						'type'  => 'text',
						'label' => esc_html__('Valid date through', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'    => 'employmentType',
						'type'    => 'select',
						'label'   => esc_html__('Employment Type', 'review-schema'),
						'options' => [
							'full-time'  => esc_html__('Full Time', 'review-schema'),
							'part-time'  => esc_html__('Part Time', 'review-schema'),
							'contract'   => esc_html__('Contract', 'review-schema'),
							'temporary'  => esc_html__('Temporary', 'review-schema'),
							'seasonal'   => esc_html__('Seasonal', 'review-schema'),
							'internship' => esc_html__('Internship', 'review-schema'),
						],
					],
					[
						'name'  => 'workHours',
						'type'  => 'text',
						'label' => esc_html__('working Hours', 'review-schema'),
						'desc'  => esc_html__('40 hours per week', 'review-schema'),
					],
					[
						'name'  => 'hiringOrganization',
						'type'  => 'text',
						'label' => esc_html__('Hiring Organization', 'review-schema'),
					],
					[
						'name'  => 'addressLocality',
						'type'  => 'text',
						'label' => esc_html__('Job location address', 'review-schema'),
						'desc'  => esc_html__('City (i.e Melbourne)', 'review-schema'),
					],
					[
						'name'  => 'addressRegion',
						'type'  => 'text',
						'label' => esc_html__('Job location region', 'review-schema'),
						'desc'  => esc_html__('State (i.e. Victoria)', 'review-schema'),
					],
					[
						'name'  => 'postalCode',
						'type'  => 'text',
						'label' => esc_html__('Location Postal code', 'review-schema'),
					],
					[
						'name'  => 'streetAddress',
						'type'  => 'text',
						'label' => esc_html__('Location street Address', 'review-schema'),
					],
					[
						'name'  => 'jobBenefits',
						'type'  => 'text',
						'label' => esc_html__('Job Benefits', 'review-schema'),
						'desc'  => esc_html__('Medical, Life, Dental', 'review-schema'),
					],
					[
						'name'  => 'educationRequirements',
						'type'  => 'text',
						'label' => esc_html__('Education Requirement. Like: Bachelor Degree', 'review-schema'),
					],
					[
						'name'  => 'experienceRequirements',
						'type'  => 'number',
						'label' => esc_html__('Experience Requirements', 'review-schema'),
						'desc'  => esc_html__('Value will be number of month, Like: 2 years = 24', 'review-schema'),
					],
					[
						'name'  => 'incentiveCompensation',
						'type'  => 'text',
						'label' => esc_html__('Incentive Compensation', 'review-schema'),
					],
					[
						'name'  => 'industry',
						'type'  => 'text',
						'label' => esc_html__('Industry', 'review-schema'),
					],
					[
						'name'  => 'occupationalCategory',
						'type'  => 'text',
						'label' => esc_html__('Occupational Category', 'review-schema'),
					],
					[
						'name'  => 'qualifications',
						'type'  => 'textarea',
						'label' => esc_html__('Qualifications', 'review-schema'),
					],
					[
						'name'  => 'responsibilities',
						'type'  => 'textarea',
						'label' => esc_html__('Responsibilities', 'review-schema'),
					],
					[
						'name'  => 'skills',
						'type'  => 'textarea',
						'label' => esc_html__('Skills', 'review-schema'),
					],
				],
			],
			//local_business
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'local_business_schema',
				'id'          => 'rtrs-local_business_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Local business schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'local_business',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'category',
						'label'    => esc_html__('Category', 'review-schema'),
						'type'     => 'schema_type',
						'required' => true,
						'options'  => Functions::getSiteTypes(),
						'empty'    => esc_html__('Select One', 'review-schema'),
						'desc'     => esc_html__('Use the most appropriate schema category for local business', 'review-schema'),
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'     => 'image',
						'type'     => 'gallery',
						'label'    => esc_html__('Images', 'review-schema'),
					],
					[
						'name'     => 'logo',
						'type'     => 'image',
						'label'    => esc_html__('Business Logo', 'review-schema'),
						'desc'     => esc_html__('This is only for Organization category and The image must be 112x112px, at minimum.', 'review-schema'),
					],
					[
						'name'        => 'priceRange',
						'type'        => 'text',
						'label'       => esc_html__('Price Range', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('The price range of the business, for example $$$.', 'review-schema'),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'address',
						'label'     => esc_html__('Address', 'review-schema'),
						'fields'    => [
							[
								'name'  => 'streetAddress',
								'type'  => 'text',
								'label' => esc_html__('Street address', 'review-schema'),
							],
							[
								'name'  => 'addressLocality',
								'type'  => 'text',
								'label' => esc_html__('Address locality', 'review-schema'),
								'desc'  => esc_html__('City (i.e Melbourne)', 'review-schema'),
							],
							[
								'name'  => 'addressRegion',
								'type'  => 'text',
								'label' => esc_html__('Address region', 'review-schema'),
								'desc'  => esc_html__('State (i.e. Victoria)', 'review-schema'),
							],
							[
								'name'  => 'postalCode',
								'type'  => 'text',
								'label' => esc_html__('Postal code', 'review-schema'),
							],
							[
								'name'     => 'addressCountry',
								'label'    => esc_html__('Country', 'review-schema'),
								'type'     => 'select2',
								'options'  => Functions::getCountryList(),
								'empty'    => esc_html__('Select One', 'review-schema'),
							],
						],
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'geo',
						'label'     => esc_html__('GEO', 'review-schema'),
						'fields'    => [
							[
								'name'  => 'latitude',
								'type'  => 'text',
								'label' => esc_html__('Latitude', 'review-schema'),
							],
							[
								'name'  => 'longitude',
								'type'  => 'text',
								'label' => esc_html__('Longitude', 'review-schema'),
							],
						],
					],
					[
						'type'    => 'group',
						'name'    => 'opening_hours',
						'label'   => esc_html__('Opening Hours By Day', 'review-schema'),
						'fields'  => [
							[
								'name'    => 'day',
								'type'    => 'select',
								'label'   => esc_html__('Day', 'review-schema'),
								'empty'   => esc_html__('Select one', 'review-schema'),
								'options' => [
									'Monday'      => esc_html__('Monday', 'review-schema'),
									'Tuesday'     => esc_html__('Tuesday', 'review-schema'),
									'Wednesday'   => esc_html__('Wednesday', 'review-schema'),
									'Thursday'    => esc_html__('Thursday', 'review-schema'),
									'Friday'      => esc_html__('Friday', 'review-schema'),
									'Saturday'    => esc_html__('Saturday', 'review-schema'),
									'Sunday'      => esc_html__('Sunday', 'review-schema'),
								],
								'desc'    => esc_html__("Don't add same day multiple time", 'review-schema'),
							],
							[
								'name'  => 'opens',
								'type'  => 'text',
								'label' => esc_html__('Opens Time', 'review-schema'),
								'desc'  => esc_html__('Times are specified using 24:00 time. For example, 3PM is specified as 15:00.', 'review-schema'),
							],
							[
								'name'  => 'closes',
								'type'  => 'text',
								'label' => esc_html__('Closes Time', 'review-schema'),
							],
						],
					],
					[
						'name'        => 'telephone',
						'type'        => 'text',
						'label'       => esc_html__('Telephone', 'review-schema'),
						'recommended' => true,
					],
					[
						'name'        => 'url',
						'type'        => 'url',
						'label'       => esc_html__('Web URL', 'review-schema'),
						'recommended' => true,
					],
					[
						'name'     => 'servesCuisine',
						'type'     => 'text',
						'label'    => esc_html__('Serves Cuisine', 'review-schema'),
						'desc'     => esc_html__('This is only for Restaurant category', 'review-schema'),
					],
					[
						'type'    => 'group',
						'name'    => 'menu_sections',
						'label'   => esc_html__('Restaurant Menu Sections', 'review-schema'),
						'desc'    => esc_html__('This is only for Restaurant category', 'review-schema'),
						'fields'  => [
							[
								'name'  => 'name',
								'type'  => 'text',
								'label' => esc_html__('Name', 'review-schema'),
							],
							[
								'name'  => 'desc',
								'type'  => 'textarea',
								'label' => esc_html__('Description', 'review-schema'),
							],
							[
								'name'     => 'images',
								'type'     => 'gallery',
								'label'    => esc_html__('Images', 'review-schema'),
							],
							[
								'name'  => 'availabilityStarts',
								'type'  => 'text',
								'label' => esc_html__('Availability Starts', 'review-schema'),
								'desc'  => esc_html__('Like: 2021-03-02T08:22:00', 'review-schema'),
							],
							[
								'name'  => 'availabilityEnds',
								'type'  => 'text',
								'label' => esc_html__('Availability Ends', 'review-schema'),
								'desc'  => esc_html__('Like: 2021-03-02T08:22:00', 'review-schema'),
							],
							[
								'type'    => 'group',
								'name'    => 'menu_items',
								'label'   => esc_html__('Menu Items', 'review-schema'),
								'desc'    => esc_html__('This is only for Restaurant category', 'review-schema'),
								'fields'  => [
									[
										'name'  => 'name',
										'type'  => 'text',
										'label' => esc_html__('Name', 'review-schema'),
									],
									[
										'name'     => 'image',
										'type'     => 'image',
										'label'    => esc_html__('Image', 'review-schema'),
									],
									[
										'name'  => 'desc',
										'type'  => 'textarea',
										'label' => esc_html__('Description', 'review-schema'),
									],
									[
										'name'  => 'price',
										'type'  => 'text',
										'label' => esc_html__('Price', 'review-schema'),
									],
									[
										'name'  => 'priceCurrency',
										'type'  => 'text',
										'label' => esc_html__('Price Currency', 'review-schema'),
										'desc'  => esc_html__('Like: USD', 'review-schema'),
									],
									[
										'name'  => 'calories',
										'type'  => 'text',
										'label' => esc_html__('Calories', 'review-schema'),
										'desc'  => esc_html__('Like: 170 calories', 'review-schema'),
									],
									[
										'name'  => 'fatContent',
										'type'  => 'text',
										'label' => esc_html__('Fat Content', 'review-schema'),
										'desc'  => esc_html__('Like: 3 grams', 'review-schema'),
									],
									[
										'name'  => 'fiberContent',
										'type'  => 'text',
										'label' => esc_html__('Fiber Content', 'review-schema'),
									],
									[
										'name'  => 'proteinContent',
										'type'  => 'text',
										'label' => esc_html__('Protein Content', 'review-schema'),
									],
									[
										'name'  => 'suitableForDiet',
										'type'  => 'text',
										'label' => esc_html__('Suitable For Diet', 'review-schema'),
										'desc'  => esc_html__('Example: https://schema.org/GlutenFreeDiet', 'review-schema'),
									],
								],
							],
						],
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Review Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'            => 'review_author_sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'     => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'        => true,
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'review_ratingValue',
						'type'  => 'number',
						'label' => esc_html__('Rating avg value', 'review-schema'),
						'desc'  => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//software_app
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'software_app_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-software_app_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Software app schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'software_app',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'price',
						'type'  => 'number',
						'label' => esc_html__('Price', 'review-schema'),
						'desc'  => esc_html__('The lowest available price, including service charges and fees, of this type of ticket.', 'review-schema'),
					],
					[
						'name'  => 'priceCurrency',
						'type'  => 'text',
						'label' => esc_html__('Price currency', 'review-schema'),
						'desc'  => esc_html__('The 3-letter currency code.', 'review-schema'),
					],
					[
						'name'        => 'applicationCategory',
						'type'        => 'select',
						'label'       => esc_html__('Application Category', 'review-schema'),
						'recommended' => true,
						'options'     => self::getApplicationCategoryList(),
						'desc'        => esc_html__('The type of app (for example, BusinessApplication or GameApplication). The value must be a supported app type.', 'review-schema'),
					],
					[
						'name'  => 'operatingSystem',
						'type'  => 'text',
						'label' => esc_html__('Operating System', 'review-schema'),
						'desc'  => esc_html__('The operating(s) required to use the app (for example, Windows 7, OSX 10.6, Android 1.6)', 'review-schema'),
					],
					[
						'type'  => 'heading',
						'label' => esc_html__('Aggregate Rating', 'review-schema'),
					],
					[
						'name'        => 'aggregate_ratingValue',
						'type'        => 'number',
						'label'       => esc_html__('Rating avg value', 'review-schema'),
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'aggregate_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('A numerical quality rating for the item.', 'review-schema'),
					],
					[
						'name'  => 'aggregate_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('A numerical quality rating for the item.', 'review-schema'),
					],
					[
						'name'  => 'aggregate_ratingCount',
						'type'  => 'number',
						'label' => esc_html__('Rating Count', 'review-schema'),
						'desc'  => esc_html__('A numerical quality rating for the item.', 'review-schema'),
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Review Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'            => 'review_author_sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'     => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'        => true,
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2020-12-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'review_ratingValue',
						'type'  => 'number',
						'label' => esc_html__('Rating avg value', 'review-schema'),
						'desc'  => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//book
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'book_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-book_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Book schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'book',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'datePublished',
						'type'     => 'text',
						'label'    => esc_html__('Published date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'            => 'author_sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'     => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'        => true,
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'bookFormat',
						'type'     => 'select',
						'label'    => esc_html__('Book Format', 'review-schema'),
						'options'  => ['EBook', 'Hardcover', 'Paperback', 'AudioBook'],
						'required' => true,
					],
					[
						'name'     => 'isbn',
						'type'     => 'text',
						'label'    => esc_html__('ISBN', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The ISBN of the tome. Use the ISBN of the print book instead if there is no ISBN for that edition, such as for a Kindle edition.', 'review-schema'),
					],
					[
						'name'     => 'workExample',
						'type'     => 'text',
						'label'    => esc_html__('Work Example', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'url',
						'type'     => 'url',
						'label'    => esc_html__('URL', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('URL to the page on your site about the book. The page may list all available editions.', 'review-schema'),
					],
					[
						'name'     => 'sameAs',
						'type'     => 'textarea',
						'label'    => esc_html__('Same As', 'review-schema'),
						'attr'     => 'placeholder="https://example.com/example&#10;https://example.com/example"',
						'required' => true,
						'desc'     => esc_html__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'),
					],
					[
						'name'  => 'publisher',
						'type'  => 'text',
						'label' => esc_html__('Publisher', 'review-schema'),
					],
					[
						'name'  => 'numberOfPages',
						'type'  => 'number',
						'label' => esc_html__('Number of Pages', 'review-schema'),
					],
					[
						'name'  => 'copyrightHolder',
						'type'  => 'text',
						'label' => esc_html__('Copyright Holder', 'review-schema'),
						'desc'  => esc_html__('Holt, Rinehart and Winston', 'review-schema'),
					],
					[
						'name'  => 'copyrightYear',
						'type'  => 'number',
						'label' => esc_html__('Copyright Year', 'review-schema'),
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'genre',
						'type'  => 'text',
						'label' => esc_html__('Genre', 'review-schema'),
						'desc'  => esc_html__('Educational Materials', 'review-schema'),
					],
					[
						'name'  => 'inLanguage',
						'type'  => 'text',
						'label' => esc_html__('Language', 'review-schema'),
						'desc'  => esc_html__('en-US', 'review-schema'),
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Review status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'            => 'review_author_sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'     => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'        => true,
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'review_ratingValue',
						'type'  => 'number',
						'label' => esc_html__('Rating avg value', 'review-schema'),
						'desc'  => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//real_state_listing
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'real_state_listing_schema',
				'id'          => 'rtrs-real_state_listing_schema',
				'is_pro'      => true,
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Real state listing schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'real_state_listing',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'headline',
						'type'     => 'text',
						'label'    => esc_html__('Title', 'review-schema'), 
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
						'desc'  => esc_html__('Short description', 'review-schema'),
					],
					[
						'name'     => 'mainEntityOfPage',
						'type'     => 'url',
						'label'    => esc_html__('Page URL', 'review-schema'),
						'desc'     => esc_html__('The canonical URL of the listing page', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author',
						'type'     => 'text',
						'label'    => esc_html__('Author Name', 'review-schema'),
						'desc'     => esc_html__('Author display name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'author_url',
						'type'     => 'text',
						'label'    => esc_html__('Author URL', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'image',
						'type'     => 'image',
						'label'    => esc_html__('Feature Image', 'review-schema'),
						'required' => true
					],
					[
						'name'     => 'datePosted',
						'type'     => 'text',
						'label'    => esc_html__('Published date', 'review-schema'),
						'class'    => 'rtrs-date',
						'required' => true,
						'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					], 
					[
						'name'     => 'publisher',
						'type'     => 'text',
						'label'    => esc_html__('Publisher', 'review-schema'),
						'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'publisherImage',
						'type'     => 'image',
						'label'    => esc_html__('Publisher Logo', 'review-schema'),
						'desc'     => wp_kses(__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.<br>', 'review-schema'), ['br' => []]),
						'required' => true,
					], 
					[
						'name'  => 'award',
						'label' => esc_html__('Award', 'review-schema'),
						'type'  => 'text',
						'desc'  => esc_html__('An award won by or for this listing.', 'review-schema'),
					],
					[
						'name'  => 'price',
						'type'  => 'number',
						'label' => esc_html__('Price', 'review-schema'),
					],
					[
						'name'  => 'priceCurrency',
						'type'  => 'text',
						'label' => esc_html__('Price Currency', 'review-schema'),
						'desc'  => esc_html__('USD', 'review-schema'),
					],
					[
						'name'    => 'availability',
						'type'    => 'select',
						'label'   => esc_html__('Availability', 'review-schema'),
						'empty'   => esc_html__('Select one', 'review-schema'),
						'options' => [
							'http://schema.org/InStock'             => esc_html__('InStock', 'review-schema'),
							'http://schema.org/InStoreOnly'         => esc_html__('InStoreOnly', 'review-schema'),
							'http://schema.org/OutOfStock'          => esc_html__('OutOfStock', 'review-schema'),
							'http://schema.org/SoldOut'             => esc_html__('SoldOut', 'review-schema'),
							'http://schema.org/OnlineOnly'          => esc_html__('OnlineOnly', 'review-schema'),
							'http://schema.org/LimitedAvailability' => esc_html__('LimitedAvailability', 'review-schema'),
							'http://schema.org/Discontinued'        => esc_html__('Discontinued', 'review-schema'),
							'http://schema.org/PreOrder'            => esc_html__('PreOrder', 'review-schema'),
						],
						'desc'    => esc_html__('Select a availability type', 'review-schema'),
					],
					[
						'name'        => 'sameAs',
						'type'        => 'textarea',
						'label'       => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder' => 'https://facebook.com/example&#10;https://twitter.com/example',
						'desc'        => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'video',
						'label'     => esc_html__('Video Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
							],
							[
								'name'  => 'description',
								'type'  => 'textarea',
								'label' => esc_html__('Description', 'review-schema'),
							],
							[
								'name'  => 'thumbnailUrl',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'     => 'contentUrl',
								'type'     => 'url',
								'label'    => esc_html__('Content URL', 'review-schema'),
							],
							[
								'name'        => 'embedUrl',
								'type'        => 'url',
								'label'       => esc_html__('Embed URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format.', 'review-schema'),
							],
							[
								'name'     => 'uploadDate',
								'type'     => 'text',
								'label'    => esc_html__('Upload Date', 'review-schema'),
								'class'    => 'rtrs-date',
								'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
							],
							[
								'name'     => 'duration',
								'type'     => 'text',
								'label'    => esc_html__('Duration', 'review-schema'),
								'desc'     => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
							],
						],
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'audio',
						'label'     => esc_html__('Audio Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'desc'     => esc_html__('The title of the audio', 'review-schema'),
							],
							[
								'name'     => 'description',
								'type'     => 'textarea',
								'label'    => esc_html__('Description', 'review-schema'),
								'desc'     => esc_html__('The short description of the audio', 'review-schema'),
							],
							[
								'name'  => 'duration',
								'type'  => 'text',
								'label' => esc_html__('Duration', 'review-schema'),
								'desc'  => esc_html__('The duration of the audio in ISO 8601 format.(PT1M33S)', 'review-schema'),
							],
							[
								'name'        => 'contentUrl',
								'type'        => 'url',
								'label'       => esc_html__('Content URL', 'review-schema'),
								'placeholder' => esc_html__('URL', 'review-schema'),
								'desc'        => esc_html__('A URL pointing to the actual audio media file. This file should be in .mp3, .wav, .mpc or other audio file format.', 'review-schema'),
							],
							[
								'name'  => 'encodingFormat',
								'type'  => 'text',
								'label' => esc_html__('Encoding Format', 'review-schema'),
								'desc'  => esc_html__("The encoding format of audio like: 'audio/mpeg'", 'review-schema'),
							],
						],
					],
				],
			],
			//course
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'course_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-course_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Course schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'course',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'provider',
						'type'  => 'text',
						'label' => esc_html__('Provider', 'review-schema'),
					],
					[
						'name'         => 'courseMode',
						'type'         => 'textarea',
						'label'        => esc_html__('Course Mode', 'review-schema'),
						'placeholder'  => esc_html__('One item per line like bellow', 'review-schema'),
						'desc'         => wp_kses(__('MOOC<br>online', 'review-schema'), ['br' => []]),
					],
					[
						'name'  => 'startDate',
						'type'  => 'text',
						'label' => esc_html__('Start Date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('2020-10-16', 'review-schema'),
					],
					[
						'name'  => 'endDate',
						'type'  => 'text',
						'label' => esc_html__('End Date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('2020-10-16', 'review-schema'),
					],
					[
						'name'     => 'locationName',
						'type'     => 'text',
						'label'    => esc_html__('Location name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'locationAddress',
						'type'     => 'text',
						'label'    => esc_html__('Location address', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'image',
						'type'  => 'image',
						'label' => esc_html__('Course image', 'review-schema'),
					],
					[
						'name'  => 'price',
						'type'  => 'number',
						'label' => esc_html__('Price', 'review-schema'),
					],
					[
						'name'  => 'priceCurrency',
						'type'  => 'text',
						'label' => esc_html__('Price Currency', 'review-schema'),
						'desc'  => esc_html__('USD', 'review-schema'),
					],
					[
						'name'    => 'availability',
						'type'    => 'select',
						'label'   => esc_html__('Availability', 'review-schema'),
						'empty'   => esc_html__('Select one', 'review-schema'),
						'options' => [
							'http://schema.org/InStock'             => esc_html__('InStock', 'review-schema'),
							'http://schema.org/InStoreOnly'         => esc_html__('InStoreOnly', 'review-schema'),
							'http://schema.org/OutOfStock'          => esc_html__('OutOfStock', 'review-schema'),
							'http://schema.org/SoldOut'             => esc_html__('SoldOut', 'review-schema'),
							'http://schema.org/OnlineOnly'          => esc_html__('OnlineOnly', 'review-schema'),
							'http://schema.org/LimitedAvailability' => esc_html__('LimitedAvailability', 'review-schema'),
							'http://schema.org/Discontinued'        => esc_html__('Discontinued', 'review-schema'),
							'http://schema.org/PreOrder'            => esc_html__('PreOrder', 'review-schema'),
						],
						'desc'    => esc_html__('Select a availability type', 'review-schema'),
					],
					[
						'name'  => 'url',
						'type'  => 'url',
						'label' => esc_html__('Course Url', 'review-schema'),
					],
					[
						'name'  => 'validFrom',
						'type'  => 'text',
						'label' => esc_html__('Valid From', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('The date when the item becomes valid. Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'    => 'performerType',
						'type'    => 'select',
						'label'   => 'Performer Type',
						'options' => ['Organization', 'Person'],
					],
					[
						'name'  => 'performerName',
						'type'  => 'text',
						'label' => esc_html__('Performer Name', 'review-schema'),
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'        => 'review_author_sameAs',
						'type'        => 'textarea',
						'label'       => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder' => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'    => true,
						'desc'        => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'review_ratingValue',
						'type'  => 'number',
						'label' => esc_html__('Rating avg value', 'review-schema'),
						'desc'  => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//product
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'product_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-product_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Product schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'product',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'type'      => 'tab',
						'name'      => 'status',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'image',
						'type'  => 'image',
						'label' => esc_html__('Image', 'review-schema'),
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
						'desc'  => esc_html__('Product description.', 'review-schema'),
					],
					[
						'name'  => 'identifier_section',
						'type'  => 'heading',
						'label' => esc_html__('Product Identifier', 'review-schema'),
						// 'desc'  => esc_html__("Add Product unique Identifier.", 'review-schema')
					],
					[
						'name'        => 'sku',
						'type'        => 'text',
						'label'       => esc_html__('SKU', 'review-schema'),
						'recommended' => true,
					],
					[
						'name'     => 'brand',
						'type'     => 'text',
						'label'    => esc_html__('Brand', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The brand of the product (Used globally).', 'review-schema'),
					],
					[
						'name'     => 'identifier_type',
						'type'     => 'select',
						'label'    => esc_html__('Identifier Type', 'review-schema'),
						'required' => true,
						'options'  => [
							'mpn'    => esc_html__('MPN', 'review-schema'),
							'isbn'   => esc_html__('ISBN', 'review-schema'),
							'gtin8'  => esc_html__('GTIN-8 (UPC, JAN)', 'review-schema'),
							'gtin12' => esc_html__('GTIN-12 (UPC)', 'review-schema'),
							'gtin13' => esc_html__('GTIN-13 (EAN,JAN)', 'review-schema'),
						],
						'desc' => wp_kses(
							__('<strong>MPN</strong><br>
                                &#8594; MPN(Manufacturer Part Number) Used globally, Alphanumeric digits (various lengths)<br>
                                <strong>GTIN</strong><br>
                                &#8594; UPC(Universal Product Code) Used in primarily North America. 12 numeric digits. eg. 892685001003.<br>
                                &#8594; EAN(European Article Number) Used primarily outside of North America. Typically 13 numeric digits (can occasionally be either eight or 14 numeric digits). eg. 4011200296908<br>
                                &#8594; ISBN(International Standard Book Number) Used globally, ISBN-13 (recommended), 13 numeric digits 978-0747595823<br>
                                &#8594; JAN(Japanese Article Number) Used only in Japan, 8 or 13 numeric digits.', 'review-schema'),
							['br' => [], 'strong' => []]
						),
					],
					[
						'name'     => 'identifier',
						'type'     => 'text',
						'label'    => esc_html__('Identifier Value', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('Enter product unique identifier', 'review-schema'),
					],
					[
						'name'  => 'rating_section',
						'type'  => 'heading',
						'label' => esc_html__('Product Review & Rating', 'review-schema'),
					],
					[
						'name'        => 'reviewRatingValue',
						'type'        => 'number',
						'label'       => esc_html__('Review rating avg value', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'        => 'reviewBestRating',
						'type'        => 'number',
						'label'       => esc_html__('Review Best rating', 'review-schema'),
						'recommended' => true,
					],
					[
						'name'        => 'reviewWorstRating',
						'type'        => 'number',
						'label'       => esc_html__('Review Worst rating', 'review-schema'),
						'recommended' => true,
					],
					[
						'name'  => 'reviewAuthor',
						'type'  => 'text',
						'label' => esc_html__('Review author', 'review-schema'),
					],
					[
						'name'        => 'ratingValue',
						'type'        => 'number',
						'label'       => esc_html__('Aggregate Rating value', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'reviewCount',
						'type'  => 'number',
						'label' => esc_html__('Aggregate Total review count', 'review-schema'),
						'desc'  => wp_kses(__("Review Count. <span class='required'>This is required if (Rating value) is given</span>", 'review-schema'), ['span' => []]),
					],
					[
						'name'  => 'pricing_section',
						'type'  => 'heading',
						'label' => esc_html__('Product Pricing', 'review-schema'),
					],
					[
						'name'  => 'priceCurrency',
						'type'  => 'text',
						'label' => esc_html__('Price currency', 'review-schema'),
						'desc'  => esc_html__('The 3-letter currency code.', 'review-schema'),
					],
					[
						'name'  => 'price',
						'type'  => 'number',
						'label' => esc_html__('Price', 'review-schema'),
						'desc'  => esc_html__('The lowest available price, including service charges and fees, of this type of ticket.', 'review-schema'),
					],
					[
						'name'        => 'priceValidUntil',
						'type'        => 'text',
						'label'       => esc_html__('PriceValidUntil', 'review-schema'),
						'recommended' => true,
						'class'       => 'rtrs-date',
						'desc'        => esc_html__('The date (in ISO 8601 date format) after which the price will no longer be available. Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'    => 'availability',
						'type'    => 'select',
						'label'   => esc_html__('Availability', 'review-schema'),
						'empty'   => esc_html__('Select one', 'review-schema'),
						'options' => [
							'http://schema.org/InStock'             => esc_html__('InStock', 'review-schema'),
							'http://schema.org/InStoreOnly'         => esc_html__('InStoreOnly', 'review-schema'),
							'http://schema.org/OutOfStock'          => esc_html__('OutOfStock', 'review-schema'),
							'http://schema.org/SoldOut'             => esc_html__('SoldOut', 'review-schema'),
							'http://schema.org/OnlineOnly'          => esc_html__('OnlineOnly', 'review-schema'),
							'http://schema.org/LimitedAvailability' => esc_html__('LimitedAvailability', 'review-schema'),
							'http://schema.org/Discontinued'        => esc_html__('Discontinued', 'review-schema'),
							'http://schema.org/PreOrder'            => esc_html__('PreOrder', 'review-schema'),
						],
						'desc'    => esc_html__('Select a availability type', 'review-schema'),
					],
					[
						'name'    => 'itemCondition',
						'type'    => 'select',
						'label'   => esc_html__('Product condition', 'review-schema'),
						'empty'   => esc_html__('Select one', 'review-schema'),
						'options' => [
							'http://schema.org/NewCondition'         => esc_html__('NewCondition', 'review-schema'),
							'http://schema.org/UsedCondition'        => esc_html__('UsedCondition', 'review-schema'),
							'http://schema.org/DamagedCondition'     => esc_html__('DamagedCondition', 'review-schema'),
							'http://schema.org/RefurbishedCondition' => esc_html__('RefurbishedCondition', 'review-schema'),
						],
						'desc'    => esc_html__('Select a condition', 'review-schema'),
					],
					[
						'name'  => 'url',
						'type'  => 'url',
						'label' => esc_html__('Product URL', 'review-schema'),
						'desc'  => esc_html__("A URL to the product web page (that includes the Offer). (Don't use offerURL for markup that appears on the product page itself.)", 'review-schema'),
					],
				],
			],
			//recipe
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'recipe_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-recipe_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Recipe schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'recipe',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'  => 'name',
						'type'  => 'text',
						'label' => esc_html__('Name', 'review-schema'),
					],
					[
						'name'  => 'author',
						'type'  => 'text',
						'label' => esc_html__('Author', 'review-schema'),
					],
					[
						'name'  => 'datePublished',
						'type'  => 'text',
						'label' => esc_html__('Published Date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'image',
						'type'     => 'image',
						'label'    => esc_html__('Image', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'        => 'keywords',
						'type'        => 'text',
						'label'       => esc_html__('Recipe keywords', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('Pizza, Nice, Testy', 'review-schema'),
					],
					[
						'name'        => 'recipeCategory',
						'type'        => 'text',
						'label'       => esc_html__('Recipe Category', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('example, appetizer, entree, etc.', 'review-schema'),
					],
					[
						'name'        => 'recipeCuisine',
						'type'        => 'text',
						'label'       => esc_html__('Recipe Cuisine', 'review-schema'),
						'recommended' => true,
						'desc'        => esc_html__('example, French or Ethiopian', 'review-schema'),
					],
					[
						'name'  => 'prepTime',
						'type'  => 'text',
						'label' => esc_html__('Prepare Time', 'review-schema'),
						'desc'  => esc_html__('PT15M', 'review-schema'),
					],
					[
						'name'  => 'cookTime',
						'type'  => 'text',
						'label' => esc_html__('Cook Time', 'review-schema'),
						'desc'  => esc_html__('PT1H', 'review-schema'),
					],
					[
						'type'    => 'group',
						'name'    => 'ingredient',
						'label'   => esc_html__('Recipe Ingredient', 'review-schema'),
						'fields'  => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'required' => true,
							],
						],
					],
					[
						'type'    => 'group',
						'name'    => 'instructions',
						'label'   => esc_html__('Recipe Instructions', 'review-schema'),
						'fields'  => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
							],
							[
								'name'     => 'text',
								'type'     => 'textarea',
								'label'    => esc_html__('Text', 'review-schema'),
							],
							[
								'name'  => 'image',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'     => 'url',
								'type'     => 'url',
								'label'    => esc_html__('URL', 'review-schema'),
							],
						],
					],
					[
						'name'  => 'calories',
						'type'  => 'text',
						'label' => esc_html__('Nutrition: calories', 'review-schema'),
						'desc'  => esc_html__('240 calories', 'review-schema'),
					],
					[
						'name'  => 'fatContent',
						'type'  => 'text',
						'label' => esc_html__('Nutrition: Fat Content', 'review-schema'),
						'desc'  => esc_html__('9 grams fat', 'review-schema'),
					],
					[
						'name'  => 'userInteractionCount',
						'type'  => 'number',
						'label' => esc_html__('User Interaction Count', 'review-schema'),
					],
					[
						'name'        => 'ratingValue',
						'type'        => 'number',
						'label'       => esc_html__('Rating avg value', 'review-schema'),
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'reviewCount',
						'type'  => 'number',
						'label' => esc_html__('Review Count', 'review-schema'),
					],
					[
						'name'  => 'bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
					],
					[
						'name'  => 'worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
					],
					[
						'name'  => 'recipeYield',
						'type'  => 'text',
						'label' => esc_html__('Recipe Yield', 'review-schema'),
					],
					[
						'name'  => 'suitableForDiet',
						'type'  => 'text',
						'label' => esc_html__('Suitable ForDiet', 'review-schema'),
						'desc'  => esc_html__('http://schema.org/LowFatDiet', 'review-schema'),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'video',
						'label'     => esc_html__('Video Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'description',
								'type'     => 'textarea',
								'label'    => esc_html__('Description', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'thumbnailUrl',
								'type'     => 'image',
								'label'    => esc_html__('Image', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'contentUrl',
								'type'     => 'url',
								'label'    => esc_html__('Content URL', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'embedUrl',
								'type'     => 'url',
								'label'    => esc_html__('Embed URL', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'uploadDate',
								'type'     => 'text',
								'label'    => esc_html__('Upload Date', 'review-schema'),
								'class'    => 'rtrs-date',
								'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'duration',
								'type'     => 'text',
								'label'    => esc_html__('Duration', 'review-schema'),
								'desc'     => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
								'required' => true,
							],
						],
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Review Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'        => 'review_author_sameAs',
						'type'        => 'textarea',
						'label'       => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder' => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'    => true,
						'desc'        => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'        => 'review_ratingValue',
						'type'        => 'number',
						'label'       => esc_html__('Rating avg value', 'review-schema'),
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//faq
			[
				'type'        => 'group',
				'duplicate'   => false,
				'name'        => $this->prefix . 'faq_schema',
				'id'          => 'rtrs-faq_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Faq schema', 'review-schema'),
				'fields'      => [
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'type'    => 'group',
						'name'    => 'faqs',
						'id'      => 'rtrs-faq_schema',
						'label'   => esc_html__('Faqs', 'review-schema'),
						'fields'  => [
							[
								'name'   => 'ques',
								'type'   => 'text',
								'label'  => esc_html__('Question', 'review-schema'),
							],
							[
								'name'   => 'ans',
								'type'   => 'textarea',
								'label'  => esc_html__('Answer', 'review-schema'),
							],
						],
					],
				],
			],
			//service
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'service_schema',
				'id'          => 'rtrs-service_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Service schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'service',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'label'    => esc_html__('Name', 'review-schema'),
						'type'     => 'text',
						'required' => true,
						'desc'     => esc_html__('The name of the Service.', 'review-schema'),
					],
					[
						'name'     => 'serviceType',
						'label'    => esc_html__('Service type', 'review-schema'),
						'type'     => 'text',
						'required' => true,
						'desc'     => esc_html__("The type of service being offered, e.g. veterans' benefits, emergency relief, etc.", 'review-schema'),
					],
					[
						'name'        => 'additionalType',
						'label'       => esc_html__('Additional type(URL)', 'review-schema'),
						'type'        => 'url',
						'placeholder' => esc_html__('URL', 'review-schema'),
						'desc'        => esc_html__('An additional type for the service, typically used for adding more specific types from external vocabularies in microdata syntax.', 'review-schema'),
					],
					[
						'name'  => 'award',
						'label' => esc_html__('Award', 'review-schema'),
						'type'  => 'text',
						'desc'  => esc_html__('An award won by or for this service.', 'review-schema'),
					],
					[
						'name'  => 'category',
						'label' => esc_html__('Category', 'review-schema'),
						'type'  => 'text',
						'desc'  => esc_html__('A category for the service.', 'review-schema'),
					],
					[
						'name'  => 'providerMobility',
						'label' => esc_html__('Provider mobility', 'review-schema'),
						'type'  => 'text',
						'desc'  => esc_html__("Indicates the mobility of a provided service (e.g. 'static', 'dynamic').", 'review-schema'),
					],
					[
						'name'    => 'description',
						'label'   => esc_html__('Description', 'review-schema'),
						'type'    => 'textarea',
						'require' => true,
						'desc'    => esc_html__('A short description of the service. New line is not supported.', 'review-schema'),
					],
					[
						'name'    => 'image',
						'label'   => esc_html__('Image', 'review-schema'),
						'type'    => 'image',
						'require' => false,
						'desc'    => esc_html__('An image of the service', 'review-schema'),
					],
					[
						'name'    => 'mainEntityOfPage',
						'label'   => esc_html__('Main entity of page URL', 'review-schema'),
						'type'    => 'url',
						'require' => false,
						'desc'    => esc_html__('Indicates a page (or other CreativeWork) for which this thing is the main entity being described.', 'review-schema'),
					],
					[
						'name'        => 'sameAs',
						'label'       => esc_html__('Same as URL', 'review-schema'),
						'type'        => 'url',
						'placeholder' => 'URL',
						'desc'        => esc_html__("URL of a reference Web page that unambiguously indicates the service's identity. E.g. the URL of the service's Wikipedia page, Freebase page, or official website.", 'review-schema'),
					],
					[
						'name'        => 'url',
						'label'       => esc_html__('Url of the service', 'review-schema'),
						'type'        => 'url',
						'placeholder' => 'URL',
						'desc'        => esc_html__('URL of the service.', 'review-schema'),
					],
					[
						'name'  => 'alternateName',
						'label' => esc_html__('Alternate name', 'review-schema'),
						'type'  => 'text',
						'desc'  => esc_html__('An alias for the service.', 'review-schema'),
					],
				],
			],
			//how_to
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'how_to_schema',
				'id'          => 'rtrs-how_to_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('How to schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'how_to',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'image',
						'type'  => 'image',
						'label' => esc_html__('Image', 'review-schema'),
					],
					[
						'name'  => 'priceCurrency',
						'type'  => 'text',
						'label' => esc_html__('Price currency', 'review-schema'),
						'desc'  => esc_html__('The 3-letter currency code.', 'review-schema'),
					],
					[
						'name'  => 'price',
						'type'  => 'number',
						'label' => esc_html__('Price', 'review-schema'),
					],
					[
						'type'    => 'group',
						'name'    => 'supply',
						'label'   => esc_html__('Supply', 'review-schema'),
						'fields'  => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'required' => true,
							],
						],
					],
					[
						'type'    => 'group',
						'name'    => 'tool',
						'label'   => esc_html__('Tool', 'review-schema'),
						'fields'  => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'required' => true,
							],
						],
					],
					[
						'type'    => 'group',
						'name'    => 'step',
						'label'   => esc_html__('Step', 'review-schema'),
						'fields'  => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
							],
							[
								'name'     => 'text',
								'type'     => 'textarea',
								'label'    => esc_html__('Text', 'review-schema'),
							],
							[
								'name'  => 'image',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'     => 'url',
								'type'     => 'url',
								'label'    => esc_html__('URL', 'review-schema'),
							],
							[
								'name'     => 'clipId',
								'type'     => 'text',
								'label'    => esc_html__('Video Clip ID', 'review-schema'),
								'desc'     => esc_html__('Video Clip ID, this need to match with Video Info Clip ID', 'review-schema'),
							],
							[
								'type'    => 'group',
								'name'    => 'direction',
								'label'   => esc_html__('Direction', 'review-schema'),
								'fields'  => [
									[
										'name'     => 'text',
										'type'     => 'textarea',
										'label'    => esc_html__('Description', 'review-schema'),
										'required' => true,
									],
								],
							],
						],
					],
					[
						'name'     => 'totalTime',
						'type'     => 'text',
						'label'    => esc_html__('Total time', 'review-schema'),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'video',
						'label'     => esc_html__('Video Info', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'name',
								'type'     => 'text',
								'label'    => esc_html__('Name', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'description',
								'type'     => 'textarea',
								'label'    => esc_html__('Description', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'thumbnailUrl',
								'type'     => 'image',
								'label'    => esc_html__('Image', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'contentUrl',
								'type'     => 'url',
								'label'    => esc_html__('Content URL', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'embedUrl',
								'type'     => 'url',
								'label'    => esc_html__('Embed URL', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'uploadDate',
								'type'     => 'text',
								'label'    => esc_html__('Upload Date', 'review-schema'),
								'class'    => 'rtrs-date',
								'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
								'required' => true,
							],
							[
								'name'     => 'duration',
								'type'     => 'text',
								'label'    => esc_html__('Duration', 'review-schema'),
								'desc'     => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
								'required' => true,
							],
							[
								'type'    => 'group',
								'name'    => 'clip',
								'label'   => esc_html__('Clip', 'review-schema'),
								'fields'  => [
									[
										'name'     => 'name',
										'type'     => 'text',
										'label'    => esc_html__('Name', 'review-schema'),
									],
									[
										'name'     => 'id',
										'type'     => 'text',
										'label'    => esc_html__('Clip ID', 'review-schema'),
										'desc'     => esc_html__('Like: Clip1, Clip2 etc', 'review-schema'),
									],
									[
										'name'     => 'startOffset',
										'type'     => 'number',
										'label'    => esc_html__('Start Offset', 'review-schema'),
									],
									[
										'name'     => 'endOffset',
										'type'     => 'number',
										'label'    => esc_html__('End Offset', 'review-schema'),
									],
									[
										'name'     => 'url',
										'type'     => 'url',
										'label'    => esc_html__('URL', 'review-schema'),
									],
								],
							],
						],
					],
				],
			],
			//about
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'about_schema',
				'id'          => 'rtrs-about_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('About page schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'about',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'image',
						'type'  => 'image',
						'label' => esc_html__('Image', 'review-schema'),
					],
					[
						'name'     => 'url',
						'type'     => 'url',
						'label'    => esc_html__('URL', 'review-schema'),
					],
					[
						'name'        => 'sameAs',
						'type'        => 'textarea',
						'label'       => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder' => 'https://facebook.com/example&#10;https://twitter.com/example',
						'desc'        => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
				],
			],
			//contact
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'contact_schema',
				'id'          => 'rtrs-contact_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Contact page schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'contact',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'image',
						'type'  => 'image',
						'label' => esc_html__('Image', 'review-schema'),
					],
					[
						'name'     => 'url',
						'type'     => 'url',
						'label'    => esc_html__('URL', 'review-schema'),
					],
					[
						'name'        => 'video',
						'type'        => 'url',
						'label'       => esc_html__('Video URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'desc'        => esc_html__('A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format.', 'review-schema'),
					],
					[
						'name'            => 'sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'     => 'https://facebook.com/example&#10;https://twitter.com/example',
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
				],
			],
			//person
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'person_schema',
				'id'          => 'rtrs-person_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Person page schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'person',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'image',
						'type'  => 'image',
						'label' => esc_html__('Image', 'review-schema'),
					],
					[
						'name'     => 'telephone',
						'type'     => 'text',
						'label'    => esc_html__('Phone', 'review-schema'),
					],
					[
						'name'     => 'email',
						'type'     => 'text',
						'label'    => esc_html__('Email', 'review-schema'),
					],
					[
						'name'     => 'url',
						'type'     => 'url',
						'label'    => esc_html__('URL', 'review-schema'),
					],
					[
						'name'     => 'jobTitle',
						'type'     => 'text',
						'label'    => esc_html__('Job Title', 'review-schema'),
					],
					[
						'name'  => 'description',
						'type'  => 'textarea',
						'label' => esc_html__('Description', 'review-schema'),
					],
					[
						'name'  => 'birthPlace',
						'type'  => 'text',
						'label' => esc_html__('Birth Place', 'review-schema'),
					],
					[
						'name'  => 'birthDate',
						'type'  => 'text',
						'label' => esc_html__('Birth Date', 'review-schema'),
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'  => 'height',
						'type'  => 'text',
						'label' => esc_html__('Height', 'review-schema'),
					],
					[
						'name'  => 'gender',
						'type'  => 'text',
						'label' => esc_html__('Gender', 'review-schema'),
					],
					[
						'name'  => 'nationality',
						'type'  => 'text',
						'label' => esc_html__('Nationality', 'review-schema'),
					],
					[
						'name'        => 'sameAs',
						'type'        => 'textarea',
						'label'       => esc_html__('Same As profile link', 'review-schema'),
						'placeholder' => 'https://facebook.com/example&#10;https://twitter.com/example',
						'desc'        => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'type'      => 'group',
						'duplicate' => false,
						'name'      => 'addresses',
						'label'     => esc_html__('Address', 'review-schema'),
						'fields'    => [
							[
								'name'  => 'streetAddress',
								'type'  => 'text',
								'label' => esc_html__('Street address', 'review-schema'),
							],
							[
								'name'  => 'addressLocality',
								'type'  => 'text',
								'label' => esc_html__('Address locality', 'review-schema'),
								'desc'  => esc_html__('City (i.e Melbourne)', 'review-schema'),
							],
							[
								'name'  => 'addressRegion',
								'type'  => 'text',
								'label' => esc_html__('Address region', 'review-schema'),
								'desc'  => esc_html__('State (i.e. Victoria)', 'review-schema'),
							],
							[
								'name'  => 'postalCode',
								'type'  => 'text',
								'label' => esc_html__('Postal code', 'review-schema'),
							],
							[
								'name'     => 'addressCountry',
								'label'    => esc_html__('Country', 'review-schema'),
								'type'     => 'select2',
								'options'  => Functions::getCountryList(),
								'empty'    => esc_html__('Select One', 'review-schema'),
							],
						],
					],
				],
			],
			//question_answer
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'question_answer_schema',
				'id'          => 'rtrs-question_answer_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Q&A schema', 'review-schema'),
				'fields'      => [
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'   => 'name',
						'type'   => 'text',
						'label'  => esc_html__('Question title', 'review-schema'),
					],
					[
						'name'   => 'text',
						'type'   => 'textarea',
						'label'  => esc_html__('Question description', 'review-schema'),
					],
					[
						'name'   => 'answerCount',
						'type'   => 'number',
						'label'  => esc_html__('Question total answer', 'review-schema'),
					],
					[
						'name'   => 'upvoteCount',
						'type'   => 'number',
						'label'  => esc_html__('Question total votes', 'review-schema'),
					],
					[
						'name'  => 'dateCreated',
						'type'  => 'text',
						'label' => esc_html__('Question created Date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'   => 'author',
						'type'   => 'text',
						'label'  => esc_html__('Question author', 'review-schema'),
					],
					[
						'type'    => 'group',
						'name'    => 'answers',
						'id'      => 'rtrs-question_answer_schema',
						'label'   => esc_html__('Answers', 'review-schema'),
						'fields'  => [
							[
								'name'   => 'text',
								'type'   => 'textarea',
								'label'  => esc_html__('Answer', 'review-schema'),
							],
							[
								'name'  => 'dateCreated',
								'type'  => 'text',
								'label' => esc_html__('Created Date', 'review-schema'),
								'class' => 'rtrs-date',
								'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
							],
							[
								'name'   => 'upvoteCount',
								'type'   => 'number',
								'label'  => esc_html__('Total votes', 'review-schema'),
							],
							[
								'name'   => 'url',
								'type'   => 'url',
								'label'  => esc_html__('URL', 'review-schema'),
							],
							[
								'name'   => 'author',
								'type'   => 'text',
								'label'  => esc_html__('Author', 'review-schema'),
							],
							[
								'name'      => 'answerType',
								'type'      => 'tab',
								'label'     => esc_html__('Answer type', 'review-schema'),
								'desc'      => esc_html__('Only one accepted answer will be in this list.', 'review-schema'),
								'default'   => 'normal',
								'options'   => [
									'normal'   => esc_html__('General', 'review-schema'),
									'accepted' => esc_html__('Accepted', 'review-schema'),
								],
							],
						],
					],
				],
			],
			//breadcrumb
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'breadcrumb_schema',
				'id'          => 'rtrs-breadcrumb_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Breadcrumb schema', 'review-schema'),
				'fields'      => [
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'type'    => 'group',
						'name'    => 'items',
						'id'      => 'rtrs-breadcrumb_schema',
						'label'   => esc_html__('Items', 'review-schema'),
						'fields'  => [
							[
								'name'   => 'name',
								'type'   => 'text',
								'label'  => esc_html__('Name', 'review-schema'),
							],
							[
								'name'   => 'item',
								'type'   => 'url',
								'label'  => esc_html__('URL', 'review-schema'),
							],
						],
					],
				],
			],
			//itemlist
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'itemlist_schema',
				'id'          => 'rtrs-itemlist_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Itemlist schema', 'review-schema'),
				'fields'      => [
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'type'    => 'group',
						'name'    => 'items',
						'id'      => 'rtrs-itemlist_schema',
						'label'   => esc_html__('Items', 'review-schema'),
						'fields'  => [
							[
								'name'   => 'url',
								'type'   => 'url',
								'label'  => esc_html__('URL', 'review-schema'),
							],
						],
					],
				],
			],
			//movie
			[
				'type'    => 'group',
				'name'    => $this->prefix . 'movie_schema',
				// "is_pro"  => true,
				'id'          => 'rtrs-movie_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Movie schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'movie',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'description',
						'type'     => 'textarea',
						'label'    => esc_html__('Description', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'duration',
						'type'  => 'text',
						'label' => esc_html__('Duration', 'review-schema'),
						'desc'  => esc_html__('Runtime of the movie in ISO 8601 format (for example, "PT2H22M" (142 minutes)).', 'review-schema'),
					],
					[
						'name'  => 'dateCreated',
						'type'  => 'text',
						'label' => esc_html__('Created Date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'image',
						'type'     => 'image',
						'label'    => esc_html__('Image', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'director',
						'type'  => 'text',
						'label' => esc_html__('Director', 'review-schema'),
					],
					[
						'name'        => 'author',
						'type'        => 'textarea',
						'label'       => esc_html__('Author', 'review-schema'),
						'placeholder' => esc_html__('One item per line like bellow', 'review-schema'),
						'desc'        => wp_kses(__('Ted Elliott<br>Terry Rossio', 'review-schema'), ['br' => []]),
					],
					[
						'name'        => 'actor',
						'type'        => 'textarea',
						'label'       => esc_html__('Actor', 'review-schema'),
						'placeholder' => esc_html__('One item per line like bellow', 'review-schema'),
						'desc'        => wp_kses(__('Johnny Depp<br>Penelope Cruz<br>Ian McShane', 'review-schema'), ['br' => []]),
					],
					[
						'name'  => 'review_section',
						'type'  => 'heading',
						'label' => esc_html__('Review', 'review-schema'),
						'desc'  => esc_html__('To add review schema for this type, complete fields below and enable, others live blank.', 'review-schema'),
					],
					[
						'name'      => 'review_active',
						'type'      => 'tab',
						'label'     => esc_html__('Review status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'review_author',
						'type'     => 'text',
						'label'    => esc_html__('Author', 'review-schema'),
						'required' => true,
					],
					[
						'name'            => 'review_author_sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Author Same As profile link', 'review-schema'),
						'placeholder'     => 'https://facebook.com/example&#10;https://twitter.com/example',
						'required'        => true,
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'     => 'review_body',
						'type'     => 'textarea',
						'label'    => esc_html__('Review body', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The actual body of the review.', 'review-schema'),
					],
					[
						'name'  => 'review_datePublished',
						'type'  => 'text',
						'label' => esc_html__('Date of Published', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
					[
						'name'     => 'review_publisher',
						'type'     => 'text',
						'label'    => esc_html__('Publisher', 'review-schema'),
						'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
						'required' => true,
					],
					[
						'name'  => 'review_publisherImage',
						'type'  => 'image',
						'label' => esc_html__('Publisher Logo', 'review-schema'),
						'desc'  => wp_kses(__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.', 'review-schema'), ['br' => []]),
					],
					[
						'name'            => 'review_sameAs',
						'type'            => 'textarea',
						'label'           => esc_html__('Review same as link', 'review-schema'),
						'placeholder'     => 'https://example.com/example&#10;https://example.com/example',
						'required'        => true,
						'desc'            => wp_kses(__("A reference page that unambiguously indicates the item's identity; for example, the URL of the item's Wikipedia page, Freebase page, or official website.<br> Enter new line for every entry", 'review-schema'), ['br' => []]),
					],
					[
						'name'        => 'review_ratingValue',
						'type'        => 'number',
						'label'       => esc_html__('Rating avg value', 'review-schema'),
						'desc'        => esc_html__('Rating value. (1, 2.5, 3, 5 etc)', 'review-schema'),
					],
					[
						'name'  => 'review_bestRating',
						'type'  => 'number',
						'label' => esc_html__('Best rating', 'review-schema'),
						'desc'  => esc_html__('The highest value allowed in this rating.', 'review-schema'),
					],
					[
						'name'  => 'review_worstRating',
						'type'  => 'number',
						'label' => esc_html__('Worst rating', 'review-schema'),
						'desc'  => esc_html__('The lowest value allowed in this rating. * Required if the rating is not on a 5-point scale. If worstRating is omitted, 1 is assumed.', 'review-schema'),
					],
				],
			],
			//audio
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'audio_schema',
				'id'          => 'rtrs-audio_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Video schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'audio',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The title of the audio', 'review-schema'),
					],
					[
						'name'     => 'description',
						'type'     => 'textarea',
						'label'    => esc_html__('Description', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The short description of the audio', 'review-schema'),
					],
					[
						'name'     => 'duration',
						'type'     => 'text',
						'required' => true,
						'label'    => esc_html__('Duration', 'review-schema'),
						'desc'     => esc_html__('The duration of the audio in ISO 8601 format.(PT1M33S)', 'review-schema'),
					],
					[
						'name'        => 'contentUrl',
						'type'        => 'url',
						'required'    => true,
						'label'       => esc_html__('Content URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'desc'        => esc_html__('A URL pointing to the actual audio media file. This file should be in .mp3, .wav, .mpc or other audio file format.', 'review-schema'),
					],
					[
						'name'  => 'encodingFormat',
						'type'  => 'text',
						'label' => esc_html__('Encoding Format', 'review-schema'),
						'desc'  => esc_html__("The encoding format of audio like: 'audio/mpeg'", 'review-schema'),
					],
				],
			],
			//video
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'video_schema',
				'id'          => 'rtrs-video_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Video schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'video',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The title of the video', 'review-schema'),
					],
					[
						'name'     => 'description',
						'type'     => 'textarea',
						'label'    => esc_html__('Description', 'review-schema'),
						'required' => true,
						'desc'     => esc_html__('The description of the video', 'review-schema'),
					],
					[
						'name'        => 'thumbnailUrl',
						'type'        => 'url',
						'label'       => esc_html__('Thumbnail URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'required'    => true,
						'desc'        => esc_html__('A URL pointing to the video thumbnail image file. Images must be at least 160x90 pixels and at most 1920x1080 pixels.', 'review-schema'),
					],
					[
						'name'  => 'uploadDate',
						'type'  => 'text',
						'label' => esc_html__('Upload Date', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('2020-02-05T08:00:00+08:00', 'review-schema'),
					],
					[
						'name'  => 'duration',
						'type'  => 'text',
						'label' => esc_html__('Duration', 'review-schema'),
						'desc'  => esc_html__('The duration of the video in ISO 8601 format.(PT1M33S)', 'review-schema'),
					],
					[
						'name'        => 'contentUrl',
						'type'        => 'url',
						'label'       => esc_html__('Content URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'desc'        => esc_html__('A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format.', 'review-schema'),
					],
					[
						'name'        => 'embedUrl',
						'type'        => 'url',
						'label'       => esc_html__('Embed URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'desc'        => esc_html__('A URL pointing to a player for the specific video. Usually this is the information in the src element of an &lt;embed> tag.Example: Dailymotion: http://www.dailymotion.com/swf/x1o2g.', 'review-schema'),
					],
					[
						'name'  => 'interactionCount',
						'type'  => 'text',
						'label' => esc_html__('Interaction count', 'review-schema'),
						'desc'  => esc_html__('The number of times the video has been viewed.', 'review-schema'),
					],
					[
						'name'  => 'expires',
						'type'  => 'text',
						'label' => esc_html__('Expires', 'review-schema'),
						'class' => 'rtrs-date',
						'desc'  => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
					],
				],
			],
			//image_license
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'image_license_schema',
				'is_pro'      => true,
				'id'          => 'rtrs-image_license_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Image License schema', 'review-schema'),
				'fields'      => [
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'        => 'contentUrl',
						'type'        => 'url',
						'label'       => esc_html__('Content URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'required'    => true,
						'desc'        => esc_html__('Image URL', 'review-schema'),
					],
					[
						'name'        => 'license',
						'type'        => 'url',
						'label'       => esc_html__('License URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'required'    => true,
					],
					[
						'name'        => 'acquireLicensePage',
						'type'        => 'url',
						'label'       => esc_html__('Acquire license URL', 'review-schema'),
						'placeholder' => esc_html__('URL', 'review-schema'),
						'required'    => true,
					],
				],
			],
			//special_announcement
			[
				'type'        => 'group',
				'name'        => $this->prefix . 'special_announcement_schema',
				'id'          => 'rtrs-special_announcement_schema',
				'holderClass' => 'rtrs-hidden rtrs-schema-field',
				'label'       => esc_html__('Special announcement schema', 'review-schema'),
				'fields'      => [
					[
						'id'        => 'special_announcement',
						'type'      => 'auto-fill',
						'is_pro'    => true,
						'label'     => esc_html__('Auto Fill', 'review-schema'),
					],
					[
						'name'      => 'status',
						'type'      => 'tab',
						'label'     => esc_html__('Status', 'review-schema'),
						'default'   => 'show',
						'options'   => [
							'show' => esc_html__('Show', 'review-schema'),
							'hide' => esc_html__('Hide', 'review-schema'),
						],
					],
					[
						'name'     => 'name',
						'type'     => 'text',
						'label'    => esc_html__('Name', 'review-schema'),
						'desc'     => esc_html__('SpecialAnnouncement.name: Name of the announcement. This text should be present on the underlying page.', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'url',
						'type'     => 'url',
						'label'    => esc_html__('Page URL', 'review-schema'),
						'desc'     => esc_html__('SpecialAnnouncement.url: URL of the page containing the announcements. If present, this must match the URL of the page containing the information.', 'review-schema'),
						'required' => true,
					],
					[
						'name'     => 'datePublished',
						'type'     => 'text',
						'label'    => esc_html__('Published date', 'review-schema'),
						'class'    => 'rtrs-date',
						'desc'     => esc_html__('Like this: 2020-12-25 14:20:00', 'review-schema'),
						'required' => true,
					],
					[
						'name'        => 'expires',
						'type'        => 'text',
						'label'       => esc_html__('Expires date', 'review-schema'),
						'class'       => 'rtrs-date',
						'desc'        => esc_html__('Like this: 2020-12-25 14:20:00', 'review-schema'),
						'recommended' => true,
					],
					[
						'name'     => 'text',
						'type'     => 'textarea',
						'label'    => esc_html__('Text', 'review-schema'),
						'desc'     => esc_html__('SpecialAnnouncement.text: Text of the announcements.', 'review-schema'),
						'required' => true,
					],
					[
						'name'      => 'locations',
						'type'      => 'group',
						'label'     => esc_html__('Announcement Locations', 'review-schema'),
						'fields'    => [
							[
								'name'     => 'type',
								'type'     => 'select',
								'label'    => esc_html__('Type', 'review-schema'),
								'options'  => self::announcementLocationTypes(),
								'required' => true,
							],
							[
								'name'        => 'name',
								'type'        => 'text',
								'label'       => esc_html__('Name', 'review-schema'),
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.name: ', 'review-schema'),
								'recommended' => true,
							],
							[
								'name'        => 'url',
								'type'        => 'url',
								'label'       => esc_html__('URL', 'review-schema'),
								'recommended' => true,
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.url: URL', 'review-schema'),
							],
							[
								'name'        => 'address_street',
								'type'        => 'text',
								'label'       => esc_html__('Address: Street', 'review-schema'),
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.address.streetAddress: The street address. For example, 1600 Amphitheatre Pkwy.', 'review-schema'),
								'recommended' => true,
							],
							[
								'name'        => 'address_locality',
								'type'        => 'text',
								'label'       => esc_html__('Address: Locality', 'review-schema'),
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.address.addressLocality: The locality in which the street address is, and which is in the region. For example, Mountain View.', 'review-schema'),
								'recommended' => true,
							],
							[
								'name'        => 'address_post_code',
								'type'        => 'text',
								'label'       => esc_html__('Address: Post Code', 'review-schema'),
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.address.postalCode: The postal code. For example, 94043.', 'review-schema'),
								'recommended' => true,
							],
							[
								'name'        => 'address_region',
								'type'        => 'text',
								'label'       => esc_html__('Address: Region', 'review-schema'),
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.address.addressRegion: The region in which the locality is, and which is in the country. For example, California.', 'review-schema'),
								'recommended' => true,
							],
							[
								'name'        => 'address_country',
								'type'        => 'text',
								'label'       => esc_html__('Address: Country', 'review-schema'),
								'desc'        => esc_html__('SpecialAnnouncement.announcementLocation.address.addressCountry: The country. For example, USA. You can also provide the two-letter ISO 3166-1 alpha-2 country code.', 'review-schema'),
								'recommended' => true,
							],
							[
								'name'  => 'id',
								'type'  => 'text',
								'label' => esc_html__('ID', 'review-schema'),
								'desc'  => esc_html__('SpecialAnnouncement.announcementLocation.@id: An optional unique identifier so that you can reference pre-existing structured data for this location.', 'review-schema'),
							],
							[
								'name'  => 'image',
								'type'  => 'image',
								'label' => esc_html__('Image', 'review-schema'),
							],
							[
								'name'        => 'priceRange',
								'type'        => 'text',
								'label'       => 'Price Range (Recommended)',
								'recommended' => true,
								'desc'        => esc_html__('The price range of the business, for example $$$.', 'review-schema'),
							],
							[
								'name'        => 'telephone',
								'type'        => 'text',
								'label'       => esc_html__('Telephone (Recommended)', 'review-schema'),
								'recommended' => true,
							],
						],
					],
				],
			],
		];
		$settings_fields[] = $this->TechAtcicleSchemaFields();
		$settings_fields[] = $this->mosque_schema_fields();
		$settings_fields[] = $this->church_schema_fields();
		$settings_fields[] = $this->hindutemple_schema_fields();
		$settings_fields[] = $this->buddhisttemple_schema_fields();
		$settings_fields[] = $this->MedicalWebPageSchemaFields();
		// $settings_fields[] = $this->CollectionPageSchemaFields(); Pro
		
		return apply_filters('rtrs_section_schema_fields', $settings_fields);
	}
	/**
	 * Tech Atcicle Schema Fields
	 */
	public function TechAtcicleSchemaFields() {
		//Tech article
		$settings_fields = [
			'type'        => 'group',
			'name'        => $this->prefix . 'tech_article_schema',
			'id'          => 'rtrs-tech_article_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__('Tech Article schema', 'review-schema'),
			'fields'      => [
				[
					'id'        => 'tech_article',
					'type'      => 'auto-fill',
					'is_pro'    => true,
					'label'     => esc_html__('Auto Fill', 'review-schema'),
				],
				[
					'name'      => 'status',
					'type'      => 'tab',
					'label'     => esc_html__('Status', 'review-schema'),
					'default'   => 'show',
					'options'   => [
						'show' => esc_html__('Show', 'review-schema'),
						'hide' => esc_html__('Hide', 'review-schema'),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__('Headline', 'review-schema'),
					'desc'     => esc_html__('Article title', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'mainEntityOfPage',
					'type'     => 'url',
					'label'    => esc_html__('Page URL', 'review-schema'),
					'desc'     => esc_html__('The canonical URL of the article page', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'author_type',
					'label'       => __('Author Type', 'review-schema'),
					'type'        => 'select',
					'recommended' => true,
					'empty'       => __("Select one", 'review-schema'),
					'options'     => array(
						'Person'  => 'Person',
						'Organization'  => 'Organization'
					),
				],
				[
					'name'     => 'author',
					'type'     => 'text',
					'label'    => esc_html__('Author Name', 'review-schema'),
					'desc'     => esc_html__('Author display name', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'author_url',
					'type'     => 'text',
					'label'    => esc_html__('Author URL', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'auth_description',
					'label' => __('Author Description', "review-schema"),
					'type'  => 'textarea',
					'desc'  => __('Short description. New line is not supported.', "review-schema")
				],
				
				[
					'name'     => 'image',
					'type'     => 'image',
					'label'    => esc_html__('Feature Image', 'review-schema'),
					'required' => true,
					'desc'     => wp_kses(__('Images should be at least 696 pixels wide.<br>Images should be in .jpg, .png, or. gif format.', 'review-schema'), ['br' => []]),
				],
				[
					'name'     => 'datePublished',
					'type'     => 'text',
					'label'    => esc_html__('Published date', 'review-schema'),
					'class'    => 'rtrs-date',
					'required' => true,
					'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
				],
				[
					'name'     => 'dateModified',
					'type'     => 'text',
					'label'    => esc_html__('Modified date', 'review-schema'),
					'class'    => 'rtrs-date',
					'required' => true,
					'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
				],
				[
					'name'     => 'publisher',
					'type'     => 'text',
					'label'    => esc_html__('Publisher', 'review-schema'),
					'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'publisherImage',
					'type'     => 'image',
					'label'    => esc_html__('Publisher Logo', 'review-schema'),
					'desc'     => wp_kses(__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.<br>', 'review-schema'), ['br' => []]),
					'required' => true,
				],
				[
					'name'  => 'description',
					'type'  => 'textarea',
					'label' => esc_html__('Description', 'review-schema'),
					'desc'  => esc_html__('Short description', 'review-schema'),
				],
				
				[
					'name'  => 'articleBody',
					'type'  => 'textarea',
					'label' => esc_html__('Article body', 'review-schema'),
					'desc'  => esc_html__('Article content', 'review-schema'),
				],
				[
					'name'  => 'keywords',
					'type'  => 'text',
					'label' => esc_html__('Keywords', 'review-schema'),
				],
			],
		];
		return $settings_fields;
	}
	/**
	 * Medical WebPage Schema Fields
	 */
	public function MedicalWebPageSchemaFields() {
		// Medical Webpage
		$settings_fields = [
			'type'        => 'group',
			'name'        => $this->prefix . 'medical_webpage_schema',
			'id'          => 'rtrs-medical_webpage_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__('Medical Webpage', 'review-schema'),
			'fields'      => [
				[
					'id'        => 'medical_webpage',
					'type'      => 'auto-fill',
					'is_pro'    => true,
					'label'     => esc_html__('Auto Fill', 'review-schema'),
				],
				[
					'name'      => 'status',
					'type'      => 'tab',
					'label'     => esc_html__('Status', 'review-schema'),
					'default'   => 'show',
					'options'   => [
						'show' => esc_html__('Show', 'review-schema'),
						'hide' => esc_html__('Hide', 'review-schema'),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__('Headline', 'review-schema'),
					'desc'     => esc_html__('Title', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'webpage_url',
					'label'    => __('Webpage url', 'review-schema'),
					'type'     => 'url',
					'desc'     => __('Web Page Url', 'review-schema'),
				],
				[
					'name'     => 'specialty_url',
					'label'    => __('Specialty url', 'review-schema'),
					'type'     => 'url',
					'desc'     => __('Specialty Url', 'review-schema'),
				],
				[
					'name'     => 'image',
					'label'    => __('Image', 'review-schema'),
					'type'     => 'image',
					'required' => true,
				],
				[
					'name'     => 'datePublished',
					'type'     => 'text',
					'label'    => esc_html__('Published date', 'review-schema'),
					'class'    => 'rtrs-date',
					'required' => true,
					'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
				],
				[
					'name'     => 'dateModified',
					'type'     => 'text',
					'label'    => esc_html__('Modified date', 'review-schema'),
					'class'    => 'rtrs-date',
					'required' => true,
					'desc'     => esc_html__('Like this: 2021-08-25 14:20:00', 'review-schema'),
				],
				[
					'name'     => 'publisher',
					'type'     => 'text',
					'label'    => esc_html__('Publisher', 'review-schema'),
					'desc'     => esc_html__('Publisher name or Organization name', 'review-schema'),
					'required' => true,
				],
				[
					'name'     => 'publisherImage',
					'type'     => 'image',
					'label'    => esc_html__('Publisher Logo', 'review-schema'),
					'desc'     => wp_kses(__('Logos should have a wide aspect ratio, not a square icon.<br>Logos should be no wider than 600px, and no taller than 60px.<br>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.<br>', 'review-schema'), ['br' => []]),
					'required' => true,
				],
				[
					'name'     => 'lastreviewed',
					'label'    => __('Last Reviewed', 'review-schema'),
					'type'     => 'text',
					'class'    => 'rtrs-date',
					'desc'     => __('Like this: 2021-12-25', 'review-schema')
				],
				[
					'name'     => 'maincontentofpage',
					'label'    => __('Main Content of Page', 'review-schema'),
					'type'     => 'text'
				],
				[
					'name'     => 'about',
					'label'    => __('About', 'review-schema'),
					'type'     => 'textarea'
				],
				[
					'name'     => 'description',
					'label' => __('Description', 'review-schema'),
					'type'  => 'textarea',
					'desc'  => __('Short description. New line is not supported.', 'review-schema')
				],
				[
					'name'     => 'keywords',
					'label'    => __('Keywords', 'review-schema'),
					'type'     => 'text',
				],
			]
		];
		return $settings_fields;
	}

	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function mosque_schema_fields() {
		$prefix        = 'rtrs_';
		$schema_fields = [
			'type'        => 'group',
			'name'        => $prefix . 'mosque_schema',
			'id'          => 'rtrs-mosque_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__( 'Mosque schema', 'review-schema' ),
			'fields'      => [
				[
					'id'     => 'mosque',
					'type'   => 'auto-fill',
					'is_pro' => true,
					'label'  => esc_html__( 'Auto Fill', 'review-schema' ),
				],
				[
					'name'    => 'status',
					'type'    => 'tab',
					'label'   => esc_html__( 'Status', 'review-schema' ),
					'default' => 'show',
					'options' => [
						'show' => esc_html__( 'Show', 'review-schema' ),
						'hide' => esc_html__( 'Hide', 'review-schema' ),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__( 'Name', 'review-schema' ),
					'required' => true,
				],
				[
					'name'     => 'description',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Description', 'review-schema' ),
					'required' => true,
				],
				[
					'name'  => 'image',
					'type'  => 'image',
					'label' => esc_html__( 'Image', 'review-schema' ),
				],
				[
					'name'        => 'url',
					'label'       => esc_html__( 'URL', 'review-schema' ),
					'type'        => 'url',
					'placeholder' => esc_html__( 'URL', 'review-schema' ),
				],
				[
					'name'        => 'capacity',
					'label'       => esc_html__( 'Maximum Capacity', 'review-schema' ),
					'type'        => 'number',
					'placeholder' => esc_html__( 'Maximum Capacity', 'review-schema' ),
				],
				[
					'name'  => 'hasMap',
					'label' => esc_html__( 'Has Map', 'review-schema' ),
					'type'  => 'text',
				],
				[
					'type'      => 'group',
					'duplicate' => false,
					'name'      => 'address',
					'label'     => esc_html__( 'Address Info', 'review-schema' ),
					'fields'    => [
						[
							'name'  => 'address-country',
							'type'  => 'text',
							'label' => esc_html__( 'Country', 'review-schema' ),
						],
						[
							'name'  => 'address-locality',
							'type'  => 'text',
							'label' => esc_html__( 'Locality', 'review-schema' ),
						],
						[
							'name'  => 'address-region',
							'type'  => 'text',
							'label' => esc_html__( 'Region', 'review-schema' ),
						],
						[
							'name'  => 'postal-code',
							'type'  => 'text',
							'label' => esc_html__( 'Postal Code', 'review-schema' ),
						],
					],
				],

			],
		];
		return $schema_fields;
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function church_schema_fields() {
		$prefix        = 'rtrs_';
		$schema_fields = [
			'type'        => 'group',
			'name'        => $prefix . 'church_schema',
			'id'          => 'rtrs-church_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__( 'Church schema', 'review-schema' ),
			'fields'      => [
				[
					'id'     => 'church',
					'type'   => 'auto-fill',
					'is_pro' => true,
					'label'  => esc_html__( 'Auto Fill', 'review-schema' ),
				],
				[
					'name'    => 'status',
					'type'    => 'tab',
					'label'   => esc_html__( 'Status', 'review-schema' ),
					'default' => 'show',
					'options' => [
						'show' => esc_html__( 'Show', 'review-schema' ),
						'hide' => esc_html__( 'Hide', 'review-schema' ),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__( 'Name', 'review-schema' ),
					'required' => true,
				],
				[
					'name'     => 'description',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Description', 'review-schema' ),
					'required' => true,
				],
				[
					'name'  => 'image',
					'type'  => 'image',
					'label' => esc_html__( 'Image', 'review-schema' ),
				],
				[
					'name'        => 'url',
					'label'       => esc_html__( 'URL', 'review-schema' ),
					'type'        => 'url',
					'placeholder' => esc_html__( 'URL', 'review-schema' ),
				],
				[
					'name'        => 'capacity',
					'label'       => esc_html__( 'Maximum Capacity', 'review-schema' ),
					'type'        => 'number',
					'placeholder' => esc_html__( 'Maximum Capacity', 'review-schema' ),
				],
				[
					'name'  => 'hasMap',
					'label' => esc_html__( 'Has Map', 'review-schema' ),
					'type'  => 'text',
				],
				[
					'type'      => 'group',
					'duplicate' => false,
					'name'      => 'address',
					'label'     => esc_html__( 'Address Info', 'review-schema' ),
					'fields'    => [
						[
							'name'  => 'address-country',
							'type'  => 'text',
							'label' => esc_html__( 'Country', 'review-schema' ),
						],
						[
							'name'  => 'address-locality',
							'type'  => 'text',
							'label' => esc_html__( 'Locality', 'review-schema' ),
						],
						[
							'name'  => 'address-region',
							'type'  => 'text',
							'label' => esc_html__( 'Region', 'review-schema' ),
						],
						[
							'name'  => 'postal-code',
							'type'  => 'text',
							'label' => esc_html__( 'Postal Code', 'review-schema' ),
						],
					],
				],

			],
		];
		return $schema_fields;
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function hindutemple_schema_fields() {
		$prefix        = 'rtrs_';
		$schema_fields = [
			'type'        => 'group',
			'name'        => $prefix . 'hindutemple_schema',
			'id'          => 'rtrs-hindutemple_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__( 'Hindu temple schema', 'review-schema' ),
			'fields'      => [
				[
					'id'     => 'hindutemple',
					'type'   => 'auto-fill',
					'is_pro' => true,
					'label'  => esc_html__( 'Auto Fill', 'review-schema' ),
				],
				[
					'name'    => 'status',
					'type'    => 'tab',
					'label'   => esc_html__( 'Status', 'review-schema' ),
					'default' => 'show',
					'options' => [
						'show' => esc_html__( 'Show', 'review-schema' ),
						'hide' => esc_html__( 'Hide', 'review-schema' ),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__( 'Name', 'review-schema' ),
					'required' => true,
				],
				[
					'name'     => 'description',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Description', 'review-schema' ),
					'required' => true,
				],
				[
					'name'  => 'image',
					'type'  => 'image',
					'label' => esc_html__( 'Image', 'review-schema' ),
				],
				[
					'name'        => 'url',
					'label'       => esc_html__( 'URL', 'review-schema' ),
					'type'        => 'url',
					'placeholder' => esc_html__( 'URL', 'review-schema' ),
				],
				[
					'name'        => 'capacity',
					'label'       => esc_html__( 'Maximum Capacity', 'review-schema' ),
					'type'        => 'number',
					'placeholder' => esc_html__( 'Maximum Capacity', 'review-schema' ),
				],
				[
					'name'  => 'hasMap',
					'label' => esc_html__( 'Has Map', 'review-schema' ),
					'type'  => 'text',
				],
				[
					'type'      => 'group',
					'duplicate' => false,
					'name'      => 'address',
					'label'     => esc_html__( 'Address Info', 'review-schema' ),
					'fields'    => [
						[
							'name'  => 'address-country',
							'type'  => 'text',
							'label' => esc_html__( 'Country', 'review-schema' ),
						],
						[
							'name'  => 'address-locality',
							'type'  => 'text',
							'label' => esc_html__( 'Locality', 'review-schema' ),
						],
						[
							'name'  => 'address-region',
							'type'  => 'text',
							'label' => esc_html__( 'Region', 'review-schema' ),
						],
						[
							'name'  => 'postal-code',
							'type'  => 'text',
							'label' => esc_html__( 'Postal Code', 'review-schema' ),
						],
					],
				],
			],
		];
		return $schema_fields;
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function buddhisttemple_schema_fields() {
		$prefix        = 'rtrs_';
		$schema_fields = [
			'type'        => 'group',
			'name'        => $prefix . 'buddhisttemple_schema',
			'id'          => 'rtrs-buddhisttemple_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__( 'Buddhist temple schema', 'review-schema' ),
			'fields'      => [
				[
					'id'     => 'buddhisttemple',
					'type'   => 'auto-fill',
					'is_pro' => true,
					'label'  => esc_html__( 'Auto Fill', 'review-schema' ),
				],
				[
					'name'    => 'status',
					'type'    => 'tab',
					'label'   => esc_html__( 'Status', 'review-schema' ),
					'default' => 'show',
					'options' => [
						'show' => esc_html__( 'Show', 'review-schema' ),
						'hide' => esc_html__( 'Hide', 'review-schema' ),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__( 'Name', 'review-schema' ),
					'required' => true,
				],
				[
					'name'     => 'description',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Description', 'review-schema' ),
					'required' => true,
				],
				[
					'name'  => 'image',
					'type'  => 'image',
					'label' => esc_html__( 'Image', 'review-schema' ),
				],
				[
					'name'        => 'url',
					'label'       => esc_html__( 'URL', 'review-schema' ),
					'type'        => 'url',
					'placeholder' => esc_html__( 'URL', 'review-schema' ),
				],
				[
					'name'        => 'capacity',
					'label'       => esc_html__( 'Maximum Capacity', 'review-schema' ),
					'type'        => 'number',
					'placeholder' => esc_html__( 'Maximum Capacity', 'review-schema' ),
				],
				[
					'name'  => 'hasMap',
					'label' => esc_html__( 'Has Map', 'review-schema' ),
					'type'  => 'text',
				],
				[
					'type'      => 'group',
					'duplicate' => false,
					'name'      => 'address',
					'label'     => esc_html__( 'Address Info', 'review-schema' ),
					'fields'    => [
						[
							'name'  => 'address-country',
							'type'  => 'text',
							'label' => esc_html__( 'Country', 'review-schema' ),
						],
						[
							'name'  => 'address-locality',
							'type'  => 'text',
							'label' => esc_html__( 'Locality', 'review-schema' ),
						],
						[
							'name'  => 'address-region',
							'type'  => 'text',
							'label' => esc_html__( 'Region', 'review-schema' ),
						],
						[
							'name'  => 'postal-code',
							'type'  => 'text',
							'label' => esc_html__( 'Postal Code', 'review-schema' ),
						],
					],
				],
			],
		];
		return $schema_fields;
	}

	public static function getApplicationCategoryList() {
		$list = [
			'GameApplication',
			'SocialNetworkingApplication',
			'TravelApplication',
			'ShoppingApplication',
			'SportsApplication',
			'LifestyleApplication',
			'BusinessApplication',
			'DesignApplication',
			'DeveloperApplication',
			'DriverApplication',
			'EducationalApplication',
			'HealthApplication',
			'FinanceApplication',
			'SecurityApplication',
			'BrowserApplication',
			'CommunicationApplication',
			'DesktopEnhancementApplication',
			'EntertainmentApplication',
			'MultimediaApplication',
			'HomeApplication',
			'UtilitiesApplication',
			'ReferenceApplication',
		];

		$new_list = [];
		foreach ($list as $value) {
			$new_list[$value] = $value;
		}

		return apply_filters('rtseo_application_category_list', $new_list);
	}

	public static function announcementLocationTypes() {
		$location_types = [
			'Airport',
			'Aquarium',
			'Beach',
			'Bridge',
			'BuddhistTemple',
			'BusStation',
			'BusStop',
			'Campground',
			'CatholicChurch',
			'Cemetery',
			'Church',
			'CivicStructure',
			'CityHall',
			'CollegeOrUniversity',
			'Courthouse',
			'CovidTestingFacility',
			'Crematorium',
			'DefenceEstablishment',
			'EducationalOrganization',
			'ElementarySchool',
			'Embassy',
			'EventVenue',
			'FireStation',
			'GovernmentBuilding',
			'HighSchool',
			'HinduTemple',
			'Hospital',
			'LegislativeBuilding',
			'MiddleSchool',
			'Mosque',
			'MovieTheater',
			'Museum',
			'MusicVenue',
			'Park',
			'ParkingFacility',
			'PerformingArtsTheater',
			'PlaceOfWorship',
			'Playground',
			'PoliceStation',
			'Preschool',
			'RVPark',
			'School',
			'StadiumOrArena',
			'SubwayStation',
			'Synagogue',
			'TaxiStand',
			'TrainStation',
			'Zoo',
		];

		$new_location_types = [];
		foreach ($location_types as $value) {
			$new_location_types[$value] = $value;
		}

		return apply_filters('rtseo_announcement_location_types', $new_location_types);
	}
}
